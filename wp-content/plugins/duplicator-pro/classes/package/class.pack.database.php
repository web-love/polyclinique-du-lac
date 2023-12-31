<?php
/**
 * Classes for building the package database file
 *
 * @copyright (c) 2017, Snapcreek LLC
 * @license	https://opensource.org/licenses/GPL-3.0 GNU Public License
 */
defined('ABSPATH') || defined('DUPXABSPATH') || exit;

require_once DUPLICATOR_PRO_PLUGIN_PATH.'/classes/entities/class.global.entity.php';

require_once dirname(__FILE__).'/class.pack.database.build.progress.php';
require_once dirname(__FILE__).'/class.pack.database.build.iterator.php';
require_once dirname(__FILE__).'/class.pack.database.info.php';

/**
 * Class used to do the actual working of building the database file
 * There are currently three modes: PHP, MYSQLDUMP, PHPCHUNKING
 * PHPCHUNKING and PHP will eventually be combined as one routine
 */
class DUP_PRO_Database
{

    //IDE HELPERS
    /* @var $global DUP_PRO_Global_Entity  */

    //CONSTANTS
    /**
     * Marks the end of the CREATEs in the SQL file which have to be
     * run together in one chunk during install
     */
    const TABLE_CREATION_END_MARKER = "/* DUPLICATOR PRO TABLE CREATION END */\n";

    /**
     * Updating the percentage of progression in the serialized structure in the database is a heavy action so every TOT entries are made. 
     * 
     */
    const ROWS_NUM_TO_UPDATE_PROGRESS = 10000;

    /**
     * prefix of the file used to save the offsets of the inserted tables
     */
    const STORE_DB_PROGRESS_FILE_PREFIX = 'dup_pro_db_export_progress_';

    /**
     * 
     */
    const CLOSE_INSERT_QUERY = ";\n\n";

    /**
     * 
     */
    const PHP_DUMP_CHUNK_WORKER_TIME = 5; // in sec

    //PUBLIC
    /**
     *
     * @var DUP_PRO_DatabaseInfo
     */
    public $info;
    //PUBLIC: Legacy Style
    public $Type     = 'MySQL';
    public $Size;
    public $File;
    public $FilterTables;
    public $FilterOn;
    public $DBMode;
    public $Compatible;
    public $Comments = '';
    public $dbStorePathPublic;
    //PRIVATE
    private $endFileMarker;
    private $traceLogEnabled;

    /**
     *
     * @var DUP_PRO_Package 
     */
    private $Package;
    private $throttleDelayInUs = 0;

    /**
     *
     * @global wpdb $wpdb
     * @param DUP_PRO_Package $package
     */
    public function __construct(DUP_PRO_Package $package)
    {
        global $wpdb;

        $this->Package         = $package;
        $this->endFileMarker   = '';
        $this->traceLogEnabled = DUP_PRO_Log::isTraceLogEnabled();
        $this->info            = new DUP_PRO_DatabaseInfo();
        $global                = DUP_PRO_Global_Entity::get_instance();
        if (!($global instanceof DUP_PRO_Global_Entity)) {
            if (is_admin()) {
                add_action('admin_notices', array('DUP_PRO_UI_Alert', 'showTablesCorrupted'));
                add_action('network_admin_notices', array('DUP_PRO_UI_Alert', 'showTablesCorrupted'));
            }
            throw new Exception("Global Entity is null!");
        }
        $this->throttleDelayInUs = $global->getMicrosecLoadReduction();
        $wpdb->query("SET SESSION wait_timeout = ".DUPLICATOR_PRO_DB_MAX_TIME);
    }

    public function __destruct()
    {
        $this->Package = null;
    }

    public function __clone()
    {
        DUP_PRO_LOG::trace("CLONE ".__CLASS__);

        $this->info = clone $this->info;
    }

    /**
     * Runs the build process for the database
     *
     * @return null
     */
    public function build()
    {
        DUP_PRO_LOG::trace("BUILDING DATABASE");

        do_action('duplicator_pro_build_database_before_start', $this->Package);

        $global = DUP_PRO_Global_Entity::get_instance();

        $this->Package->db_build_progress->startTime = DUP_PRO_U::getMicrotime();
        $this->Package->set_status(DUP_PRO_PackageStatus::DBSTART);

        $this->dbStorePathPublic = "{$this->Package->StorePath}/{$this->File}";
        $mysqlDumpPath           = DUP_PRO_DB::getMySqlDumpPath();
        $mode                    = DUP_PRO_DB::getBuildMode(); //($mysqlDumpPath && $global->package_mysqldump) ? 'MYSQLDUMP' : 'PHP';

        $mysqlDumpSupport = ($mysqlDumpPath) ? 'Is Supported' : 'Not Supported';

        $log = "\n********************************************************************************\n";
        $log .= "DATABASE:\n";
        $log .= "********************************************************************************\n";
        $log .= "BUILD MODE:   {$mode} ";

        if (($mode == 'MYSQLDUMP') && strlen($this->Compatible)) {
            $log .= " (Legacy SQL)";
        }

        $log .= "(query limit - {$global->package_mysqldump_qrylimit})\n";

        $log .= "MYSQLDUMP:    {$mysqlDumpSupport}\n";
        $log .= "MYSQLTIMEOUT: ".DUPLICATOR_PRO_DB_MAX_TIME;
        DUP_PRO_Log::info($log);
        $log = null;

        do_action('duplicator_pro_build_database_start', $this->Package);

        switch ($mode) {
            case 'MYSQLDUMP':
                $this->runMysqlDump($mysqlDumpPath);
                break;
            case 'PHP' :
                $this->runPHPDump();
                $this->validateStage1();
                break;
        }

        $this->doFinish();
    }

    /**
     * Gets the database.sql file path and name
     *
     * @return string	Returns the full file path and file name of the database.sql file
     */
    public function getSafeFilePath()
    {
        return DupProSnapLibIOU::safePath(DUPLICATOR_PRO_SSDIR_PATH."/{$this->File}");
    }

    /**
     * @return string Returns the URL to the sql file
     */
    public function getURL()
    {
        return DUPLICATOR_PRO_SSDIR_URL."/{$this->File}";
    }

    protected function getStoreProgressFile()
    {
        return trailingslashit(DUPLICATOR_PRO_SSDIR_PATH_TMP).self::STORE_DB_PROGRESS_FILE_PREFIX.$this->Package->Hash.'.json';
    }

    /**
     *  Gets all the scanner information about the database
     *
     * 	@return array Returns an array of information about the database
     */
    public function getScanData()
    {
        global $wpdb;
        $filterTables         = isset($this->FilterTables) ? explode(',', $this->FilterTables) : null;
        $tblBaseCount         = 0;
        $tblFinalCount        = 0;
        $muFilteredTableCount = 0;

        $tables                     = $wpdb->get_results("SHOW TABLE STATUS", ARRAY_A);
        $views                      = $wpdb->get_results("SHOW FULL TABLES WHERE Table_Type = 'VIEW'", ARRAY_A);
        $procs                      = $wpdb->get_results("SHOW PROCEDURE STATUS", ARRAY_A);
        $info                       = array();
        $info['Status']['Success']  = is_null($tables) ? false : true;
        $info['Status']['Size']     = 'Good';
        $info['Status']['Rows']     = 'Good';

        $info['Size']       = 0;
        $info['Rows']       = 0;
        $info['TableCount'] = 0;
        $info['TableList']  = array();
        $tblCaseFound       = 0;

        $ms_tables_to_filter    = $this->Package->Multisite->getTablesToFilter();
        $this->info->tablesList = array();

        //Only return what we really need
        foreach ($tables as $table) {
            $tblBaseCount++;
            $name = DUP_PRO_DB::updateCaseSensitivePrefix($table["Name"]);
            if (in_array($name, $ms_tables_to_filter)) {
                $muFilteredTableCount++;
                continue;
            }

            if ($this->FilterOn && is_array($filterTables)) {
                if (in_array($name, $filterTables)) {
                    continue;
                }
            }

            $size = ($table["Data_length"] + $table["Index_length"]);

            $info['Size']                      += $size;
            $info['Rows']                      += ($table["Rows"]);
            $info['TableList'][$name]['Case']  = preg_match('/[A-Z]/', $name) ? 1 : 0;
            $info['TableList'][$name]['Rows']  = empty($table["Rows"]) ? '0' : number_format($table["Rows"]);
            $info['TableList'][$name]['Size']  = DUP_PRO_U::byteSize($size);
            $info['TableList'][$name]['USize'] = $size;
            $tblFinalCount++;

            $this->info->addTableInList($name, $table["Rows"], $size);

            //Table Uppercase
            if ($info['TableList'][$name]['Case']) {
                if (!$tblCaseFound) {
                    $tblCaseFound = 1;
                }
            }
        }

        $this->info->addTriggers();
        $info['Status']['Size']     = ($info['Size'] > DUPLICATOR_PRO_SCAN_DB_ALL_SIZE) ? 'Warn' : 'Good';
        $info['Status']['Rows']     = ($info['Rows'] > DUPLICATOR_PRO_SCAN_DB_ALL_ROWS) ? 'Warn' : 'Good';
        $info['Status']['Triggers'] = count($this->info->triggerList) > 0 ? 'Warn' : 'Good';
        $info['TableCount']     = $tblFinalCount;

        $this->info->name                 = $wpdb->dbname;
        $this->info->isNameUpperCase      = preg_match('/[A-Z]/', $wpdb->dbname) ? 1 : 0;
        $this->info->isTablesUpperCase    = $tblCaseFound;
        $this->info->tablesBaseCount      = $tblBaseCount;
        $this->info->tablesFinalCount     = $tblFinalCount;
        $this->info->muFilteredTableCount = $muFilteredTableCount;
        $this->info->tablesRowCount       = $info['Rows'];
        $this->info->tablesSizeOnDisk     = $info['Size'];
        $this->info->dbEngine             = DupProSnapLibDB::getDBEngine($wpdb->dbh);
        $this->info->version              = DUP_PRO_DB::getVersion();
        $this->info->versionComment       = DUP_PRO_DB::getVariable('version_comment');
        $this->info->varLowerCaseTables   = DUP_PRO_DB::getVariable('lower_case_table_names');
        $tables                           = $this->getFilteredTables();
        $this->info->charSetList          = DUP_PRO_DB::getTableCharSetList($tables);
        $this->info->collationList        = DUP_PRO_DB::getTableCollationList($filterTables);
        $this->info->buildMode            = DUP_PRO_DB::getBuildMode();
        $this->info->viewCount            = count($views);
        $this->info->procCount            = count($procs);

        return $info;
    }

    /**
     * Runs the mysqldump process to build the database.sql script
     *
     * @param string $exePath The path to the mysqldump executable
     *
     * @return bool	Returns true if the mysqldump process ran without issues
     */
    private function runMysqlDump($exePath)
    {
        DUP_PRO_LOG::trace("RUN MYSQL DUMP");
        $sql_header = "/* DUPLICATOR-PRO (MYSQL-DUMP BUILD MODE) MYSQL SCRIPT CREATED ON : ".@date("Y-m-d H:i:s")." */\n\n";

        if (file_put_contents($this->dbStorePathPublic, $sql_header, FILE_APPEND) === false) {
            DUP_PRO_Log::error("file_put_content failed", "file_put_content failed while writing to {$this->dbStorePathPublic}", false);
            return false;
        }

        if ($this->mysqlDumpWriteCreates($exePath) != true) {
            DUP_PRO_Log::trace("Mysqldump error while writing CREATE queries");
            return false;
        }

        if ($this->mysqlDumpWriteInserts($exePath) != true) {
            DUP_PRO_Log::trace("Mysqldump error while writing INSERT queries");
            return false;
        }

        return true;
    }

    /**
     * @param string $exePath The path to the mysqldump executable
     * @return bool returns true if successful
     */
    private function mysqlDumpWriteCreates($exePath)
    {
        DUP_PRO_LOG::trace("START WRITING CREATES TO SQL FILE");
        $cmd         = $this->getMysqlDumpCmd($exePath, array('--no-data', '--routines', '--skip-triggers'));
        $mysqlResult = $this->mysqlDumpWriteCmd($cmd, $exePath);
        if (file_put_contents($this->dbStorePathPublic, self::TABLE_CREATION_END_MARKER."\n", FILE_APPEND) === false) {
            DUP_PRO_Log::error("file_put_content failed", "file_put_content failed while writing to {$this->dbStorePathPublic}", false);
            return false;
        }
        return $this->mysqlDumpEvaluateResult($mysqlResult);
    }

    /**
     * @param string $exePath The path to the mysqldump executable
     * @return bool returns true if successful
     */
    private function mysqlDumpWriteInserts($exePath)
    {
        DUP_PRO_LOG::trace("START WRITING INSERTS TO SQL FILE");
        $cmd         = $this->getMysqlDumpCmd($exePath, array('--no-create-info'));
        $mysqlResult = $this->mysqlDumpWriteCmd($cmd, $exePath);
        $sql_footer  = "\n\n/* Duplicator WordPress Timestamp: ".date("Y-m-d H:i:s")."*/\n";
        $sql_footer  .= "/* ".DUPLICATOR_PRO_DB_EOF_MARKER." */\n";
        if (file_put_contents($this->dbStorePathPublic, $sql_footer, FILE_APPEND) === false) {
            DUP_PRO_Log::error("file_put_content failed", "file_put_content failed while writing to {$this->dbStorePathPublic}", false);
            return false;
        }
        return $this->mysqlDumpEvaluateResult($mysqlResult);
    }

    /**
     * @param string $cmd The mysqldump command to be run
     * @param string $exePath The path to the mysqldump executable
     * @return int The result of the mysql dump
     */
    private function mysqlDumpWriteCmd($cmd, $exePath)
    {
        DUP_PRO_LOG::trace("WRITING CREATES TO SQL FILE");
        $mysqlResult = 0;

        if (DUP_PRO_Shell_U::isPopenEnabled()) {
            $tables              = $this->getFilteredTables(true);
            $caseSensitiveTables = array_map(array('DUP_PRO_DB', 'updateCaseSensitivePrefix'), $tables);

            $findReplaceTableNames = array();
            foreach ($tables as $index => $tableName) {
                $csTable = $caseSensitiveTables[$index];
                if ($tableName !== $csTable) {
                    $findReplaceTableNames[$tableName] = $csTable;
                }
            }
            $needToRewrite = count($findReplaceTableNames) > 0;

            $firstLine = '';
            DUP_PRO_LOG::trace("POPEN mysqldump: $cmd");
            $handle    = popen($cmd, "r");
            if ($handle) {
                while (!feof($handle)) {
                    $line = fgets($handle); //get only one line
                    if ($line) {
                        if (empty($firstLine)) {
                            $firstLine = $line;
                            if (false !== stripos($line, 'Using a password on the command line interface can be insecure')) {
                                continue;
                            }
                        }

                        if ($needToRewrite) {
                            $replaceCount = 1;

                            if (preg_match('/CREATE TABLE `(.*?)`/', $line, $matches)) {
                                $tableName = $matches[1];
                                if (isset($findReplaceTableNames[$tableName])) {
                                    $rewriteTableAs = $findReplaceTableNames[$tableName];
                                    $line           = str_replace('CREATE TABLE `'.$tableName.'`', 'CREATE TABLE `'.$rewriteTableAs.'`', $line, $replaceCount);
                                }
                            } elseif (preg_match('/INSERT INTO `(.*?)`/', $line, $matches)) {
                                $tableName = $matches[1];
                                if (isset($findReplaceTableNames[$tableName])) {
                                    $rewriteTableAs = $findReplaceTableNames[$tableName];
                                    $line           = str_replace('INSERT INTO `'.$tableName.'`', 'INSERT INTO `'.$rewriteTableAs.'`', $line, $replaceCount);
                                }
                            } elseif (preg_match('/LOCK TABLES `(.*?)`/', $line, $matches)) {
                                $tableName = $matches[1];
                                if (isset($findReplaceTableNames[$tableName])) {
                                    $rewriteTableAs = $findReplaceTableNames[$tableName];
                                    $line           = str_replace('LOCK TABLES `'.$tableName.'`', 'LOCK TABLES `'.$rewriteTableAs.'`', $line, $replaceCount);
                                }
                            }
                        }

                        if (file_put_contents($this->dbStorePathPublic, $line, FILE_APPEND) === false) {
                            DUP_PRO_Log::error("file_put_content failed", "file_put_content failed while writing to {$this->dbStorePathPublic}", false);
                            //return mysql result warning value
                            $mysqlResult = 1;
                            return $mysqlResult;
                        }
                        $output = "Ran from {$exePath}";
                    }
                }
                $mysqlResult = pclose($handle);
            } else {
                $output = '';
            }

            // Password bug > 5.6 (@see http://bugs.mysql.com/bug.php?id=66546)
            if (empty($output) && trim($firstLine) === 'Warning: Using a password on the command line interface can be insecure.') {
                $output = '';
            }
        } else {
            DUP_PRO_LOG::trace("SHELL_EXEC mysqldump: $cmd");
            //$output = shell_exec($cmd);
            exec($cmd, $output, $mysqlResult);
            $output = implode("\n", $output);

            // Password bug > 5.6 (@see http://bugs.mysql.com/bug.php?id=66546)
            if (trim($output) === 'Warning: Using a password on the command line interface can be insecure.') {
                $output = '';
            }
            $output = (strlen($output)) ? $output : "Ran from {$exePath}";
            DUP_PRO_Log::info("RESPONSE: {$output}");
        }

        return $mysqlResult;
    }

    /**
     * @param int $mysqlResult The result of the mysql dump
     * @return bool returns true if the result was valid
     */
    private function mysqlDumpEvaluateResult($mysqlResult)
    {
        if ($mysqlResult !== 0) {
            /**
             * -1 error command shell
             * mysqldump return
             * 0 - Success
             * 1 - Warning
             * 2 - Exception
             */
            DUP_PRO_LOG::infoTrace('MYSQL DUMP ERROR '.print_r($mysqlResult, true));
            DUP_PRO_Log::error(DUP_PRO_U::__('Shell mysql dump failed. Last 10 lines of dump file below.'),
                implode("\n", DupProSnapLibIOU::getLastLinesOfFile($this->dbStorePathPublic, DUPLICATOR_PRO_DB_MYSQLDUMP_ERROR_CONTAINING_LINE_COUNT, DUPLICATOR_PRO_DB_MYSQLDUMP_ERROR_CHARS_IN_LINE_COUNT)), false);
            $this->setError(DUP_PRO_U::__('Shell mysql dump error. Take a look at the package log for details.'), DUP_PRO_U::__('Change SQL engine to PHP'),
                                            array(
                                                'global' => array(
                                                                'package_mysqldump' => 0
                                                            )
                                                )
                                );
            return false;
        }
        DUP_PRO_Log::trace("Operation was successful");
        return true;
    }

    /**
     * @param string $exePath The path to the mysqldump executable
     * @param array $extraFlags Extra flags to be added to the command
     * @return string The command to be executed
     */
    private function getMysqlDumpCmd($exePath, $extraFlags = array())
    {
        $global = DUP_PRO_Global_Entity::get_instance();

        $host           = explode(':', DB_HOST);
        $host           = reset($host);
        $port           = strpos(DB_HOST, ':') ? end(explode(':', DB_HOST)) : '';
        $name           = DB_NAME;
        $mysqlcompat_on = isset($this->Compatible) && strlen($this->Compatible);

        //Build command
        $cmd = escapeshellarg($exePath);
        $cmd .= ' --no-create-db';
        $cmd .= ' --single-transaction';
        $cmd .= ' --hex-blob';
        $cmd .= ' --skip-add-drop-table';
        $cmd .= ' --quote-names';
        $cmd .= ' --skip-comments';
        $cmd .= ' --skip-set-charset';
        $cmd .= ' --allow-keywords';
        $cmd .= ' --net_buffer_length='.DupProSnapLibUtil::getIntBetween($global->package_mysqldump_qrylimit, DUP_PRO_Constants::MYSQL_DUMP_CHUNK_SIZE_MIN_LIMIT, DUP_PRO_Constants::MYSQL_DUMP_CHUNK_SIZE_MAX_LIMIT);
        $cmd .= ' --no-tablespaces';

        if (!empty($extraFlags)) {
            foreach ($extraFlags as $flag) {
                $cmd .= ' '.$flag;
            }
        }

        //Compatibility mode
        if ($mysqlcompat_on) {
            DUP_PRO_Log::info("COMPATIBLE: [{$this->Compatible}]");
            $cmd .= " --compatible={$this->Compatible}";
        }

        // get excluded table list
        $tables = $this->getFilteredTables(true);

        foreach ($tables as $val) {
            $cmd .= " --ignore-table={$name}.{$val} ";
        }

        $cmd .= ' -u '.escapeshellarg(DB_USER);
        $cmd .= (DB_PASSWORD) ?
            ' -p'.DUP_PRO_Shell_U::escapeshellargWindowsSupport(DB_PASSWORD) : '';
        $cmd .= ' -h '.escapeshellarg($host);
        $cmd .= (!empty($port) && is_numeric($port)) ?
            ' -P '.$port : '';

        $cmd .= ' '.escapeshellarg(DB_NAME);
        $cmd .= ' 2>&1';

        if (!DUP_PRO_Shell_U::isPopenEnabled()) {
            // >> makes sure that the result is appended to the file and doesn't overwrite it
            $cmd .= ' >> '.escapeshellarg($this->dbStorePathPublic);
        }

        return $cmd;
    }

    /**
     * return a tables list. 
     * If $getExcludedTables is false return the included tables list else return the filtered table list
     * 
     * @global wpdb $wpdb
     * @param bool $getExcludedTables
     * @return string[]
     */
    private function getFilteredTables($getExcludedTables = false)
    {
        global $wpdb;

        $result = array();

        // ALL TABLES
        $allTables       = $wpdb->get_col("SHOW FULL TABLES WHERE Table_Type != 'VIEW'");
        // MANUAL FILTER TABLE 
        $filterTables    = ($this->FilterOn && isset($this->FilterTables)) ? explode(',', $this->FilterTables) : array();
        // SUB SITE FILTER TABLE
        $muFilterTables  = $this->Package->Multisite->getTablesToFilter();
        // TOTAL FILTER TABLES
        $allFilterTables = array_unique(array_merge($filterTables, $muFilterTables));

        $allTablesCount = count($allTables);
        $allFilterCount = count($allFilterTables);
        $createCount    = $allTablesCount - $allFilterCount;

        DUP_PRO_Log::infoTrace("TABLES: total: ".$allTablesCount." | filtered:".$allFilterCount." | create:".$createCount);
        if (!empty($filterTables)) {
            DUP_PRO_Log::infoTrace("MANUAL FILTER TABLES: \n\t".implode("\n\t", $filterTables));
        }
        if (!empty($muFilterTables)) {
            DUP_PRO_Log::infoTrace("MU SITE FILTER TABLES: \n\t".implode("\n\t", $muFilterTables));
        }

        if ($getExcludedTables) {
            $result = $allFilterTables;
        } else {
            if (empty($allFilterTables)) {
                $result = $allTables;
            } else {
                foreach ($allTables as $val) {
                    if (!in_array($val, $allFilterTables)) {
                        $result[] = $val;
                    }
                }
            }
        }

        return $result;
    }

    /**
     * Callback called in the insert iterator at the beginning of the current table dump.
     * 
     * @param DUP_PRO_DB_Build_Iterator $iterator
     */
    public function startTableIteratorCallback(DUP_PRO_DB_Build_Iterator $iterator)
    {
        $this->Package->db_build_progress->tableCountStart($iterator->current());
    }

    /**
     * Callback called in the insert iterator at the end of the current table dump.
     * 
     * @param DUP_PRO_DB_Build_Iterator $iterator
     */
    public function endTableIteratorCallback(DUP_PRO_DB_Build_Iterator $iterator)
    {
        $this->Package->db_build_progress->tableCountEnd($iterator->current(), $iterator->getCurrentOffset());
    }

    /**
     * Creates the database.sql script using PHP code
     *
     * @return null
     */
    private function runPHPDump()
    {
        DUP_PRO_LOG::trace("RUN PHP DUMP");
        global $wpdb;
        $global = DUP_PRO_Global_Entity::get_instance();

        $wpdb->query("SET session wait_timeout = ".DUPLICATOR_PRO_DB_MAX_TIME);
        $this->doFiltering();
        $this->writeCreates();

        $handle           = @fopen($this->dbStorePathPublic, 'a');
        $dbInsertIterator = $this->getDbBuildIterator();

        //BUILD INSERTS:
        for (; $dbInsertIterator->valid(); $dbInsertIterator->next()) {
            if ($dbInsertIterator->getCurrentRows() <= 0) {
                continue;
            }

            $table = $dbInsertIterator->current();

            $dbInsertIterator->addFileSize(DupProSnapLibIOU::fwrite($handle, "\n/* INSERT TABLE DATA: {$table} */\n"));

            $row_offset       = 0;
            $currentQuerySize = 0;
            $firstInsert      = true;
            $insertQueryLine  = true;

            do {
                $result = DupProSnapLibDB::selectUsingPrimaryKeyAsOffset(
                        $wpdb->dbh,
                        'SELECT * FROM `'.$table.'` WHERE 1',
                        $table,
                        $row_offset,
                        DUP_PRO_Constants::PHP_DUMP_READ_PAGE_SIZE,
                        $row_offset);

                if ($result === false) {
                    $this->setErrorSelectFrom($table);
                    return;
                }

                if (($lastSelectNumRows = DupProSnapLibDB::numRows($result)) > 0) {
                    while (($row = DupProSnapLibDB::fetchAssoc($result))) {
                        if ($currentQuerySize >= $global->package_mysqldump_qrylimit) {
                            $insertQueryLine = true;
                        }

                        if ($insertQueryLine) {
                            $line             = ($firstInsert ? '' : self::CLOSE_INSERT_QUERY).'INSERT INTO `'.$table.'` VALUES '."\n";
                            $insertQueryLine  = $firstInsert      = false;
                            $currentQuerySize = 0;
                        } else {
                            $line = ",\n";
                        }
                        $line       .= '('.implode(',', array_map(array('DUP_PRO_DB', 'escValueToQueryString'), $row)).')';
                        $lineSize   = DupProSnapLibIOU::fwriteChunked($handle, $line);
                        $totalCount = $dbInsertIterator->nextRow(0, $lineSize);

                        $currentQuerySize += $lineSize;

                        if (0 == ($totalCount % self::ROWS_NUM_TO_UPDATE_PROGRESS)) {
                            $this->setProgressPer($totalCount);
                        }
                    }

                    if ($this->throttleDelayInUs > 0) {
                        usleep($this->throttleDelayInUs * DUP_PRO_Constants::PHP_DUMP_READ_PAGE_SIZE);
                    }
                } else if ($insertQueryLine == false) {
                    // if false exists a insert to close
                    $dbInsertIterator->addFileSize(DupProSnapLibIOU::fwrite($handle, self::CLOSE_INSERT_QUERY));
                }

                DupProSnapLibDB::freeResult($result);
            } while ($lastSelectNumRows > 0);
        }

        $this->writeSQLFooter($handle);
        $wpdb->flush();
        DupProSnapLibIOU::fclose($handle);
    }

    /**
     * Initialize the build iterator, based on the phpdumpmode, the storeprogress file is used or not.
     * 
     * @staticvar type $iterator
     * @return \DUP_PRO_DB_Build_Iterator
     */
    private function getDbBuildIterator()
    {
        static $iterator = null;

        if (is_null($iterator)) {
            $iterator = new DUP_PRO_DB_Build_Iterator(
                $this->Package->db_build_progress->tablesToProcess,
                (DUP_PRO_DB::getBuildMode() === DUP_PRO_DB::BUILD_MODE_PHP_MULTI_THREAD ? $this->getStoreProgressFile() : null),
                array($this, 'startTableIteratorCallback'),
                array($this, 'endTableIteratorCallback'));
        }
        return $iterator;
    }

    private function setError($message, $fix, $quickFix = false)
    {
        DUP_PRO_Log::trace($message);

        $this->Package->build_progress->failed = true;
        DUP_PRO_LOG::trace('Database: buildInChunks Failed');
        $this->Package->update();

        DUP_PRO_Log::error("**RECOMMENDATION:  $fix.", $message, false);

        $system_global = DUP_PRO_System_Global_Entity::get_instance();
        if ($quickFix === false) {
            $system_global->add_recommended_text_fix($message, $fix);
        } else {
            $system_global->add_recommended_quick_fix($message, $fix, $quickFix);
        }
        $system_global->save();
    }

    /**
     * Manage the error if it comes from a SELECT on the table
     * 
     * @global wpdb $wpdb
     * @param string $table
     */
    private function setErrorSelectFrom($table)
    {
        global $wpdb;
        DUP_PRO_Log::info($wpdb->last_error);
        if (false !== stripos($wpdb->last_error, 'is marked as crashed and should be repaired')) {
            $repair_query = "REPAIR TABLE `".$table."`;";
            $fix          = sprintf(DUP_PRO_U::__('Detected that database table %1$s is corrupt. Run repair tool or execute SQL command %2$s'), $table, $repair_query);
        } else {
            $fix = DUP_PRO_U::__('Please contact your DataBase administrator to fix the error.');
        }
        $this->setError($wpdb->last_error, $fix);
    }

    /**
     * Uses PHP to build the SQL file in chunks over multiple http requests
     *
     * @return void
     */
    public function buildInChunks()
    {
        DUP_PRO_LOG::trace("Database: buildInChunks Start");
        if ($this->Package->db_build_progress->wasInterrupted) {
            $this->Package->db_build_progress->failureCount++;
            $log_msg = 'Database: buildInChunks failure count increased to  '.$this->Package->db_build_progress->failureCount;
            DUP_PRO_LOG::trace($log_msg);
            error_log($log_msg);
        }

        if ($this->Package->db_build_progress->errorOut || $this->Package->db_build_progress->failureCount > DUPLICATOR_PRO_SQL_SCRIPT_PHP_CODE_MULTI_THREADED_MAX_RETRIES) {
            $this->Package->build_progress->failed = true;
            DUP_PRO_LOG::trace('Database: buildInChunks Failed');
            $this->Package->update();
            return;
        }

        $this->Package->db_build_progress->wasInterrupted = true;
        $this->Package->update();
        //TODO: See where else it needs to directly error out
        if (!$this->Package->db_build_progress->doneInit) {
            DUP_PRO_LOG::trace("Database: buildInChunks Init");
            $this->doInit();
            $this->Package->db_build_progress->doneInit = true;
        } elseif (!$this->Package->db_build_progress->doneFiltering) {
            DUP_PRO_LOG::trace("Database: buildInChunks Filtering");
            $this->doFiltering();
            $this->Package->db_build_progress->doneFiltering = true;
        } elseif (!$this->Package->db_build_progress->doneCreates) {
            DUP_PRO_LOG::trace("Database: buildInChunks WriteCreates");
            $this->writeCreates();
            $this->Package->db_build_progress->doneCreates = true;
        } elseif (!$this->Package->db_build_progress->completed) {
            DUP_PRO_LOG::trace("Database: buildInChunks WriteInsertChunk");
            $this->writeInsertChunk();
        }

        $this->Package->build_progress->database_script_built = false;
        if ($this->Package->db_build_progress->completed) {
            if (!$this->Package->db_build_progress->validationStage1) {
                $this->validateStage1();
            } else {
                DUP_PRO_LOG::trace("Database: buildInChunks completed");
                $this->Package->build_progress->database_script_built = true;
                $this->doFinish();
            }
        }

        DUP_PRO_LOG::trace("Database: buildInChunks End");
        // Resetting failure count since we if it recovers after a single failure we won't count it against it.
        $this->Package->db_build_progress->failureCount   = 0;
        $this->Package->db_build_progress->wasInterrupted = false;
        $this->Package->update();
    }

    /**
     * Performs validation of the values entered based on build progress counts
     * 
     * @throws Exception
     */
    protected function validateStage1()
    {
        DUP_PRO_LOG::trace("DB VALIDATION 1");
        $isValid = true;

        // SEARCH END MARKER
        $lastLines = DUP_PRO_U::tailFile($this->dbStorePathPublic, 3);
        if (strpos($lastLines, DUPLICATOR_PRO_DB_EOF_MARKER) === false) {
            DUP_PRO_LOG::infoTrace('DB VALIDATION 1: can\'t find SQL EOR MARKER in sql file');
            $isValid = false;
        }

        foreach ($this->Package->db_build_progress->countCheckData['tables'] as $table => $tableInfo) {
            $minVal = min($tableInfo['start'], $tableInfo['end']);
            $maxVal = max($tableInfo['start'], $tableInfo['end']);

            // The rows entered must be between the start value of the dump on the table and the end value.
            if ($tableInfo['count'] < $minVal || $tableInfo['count'] > $maxVal) {
                DUP_PRO_LOG::infoTrace('DB VALIDATION 1 FAIL: count check table "'.$table.'" START: '.$tableInfo['start'].' END: '.$tableInfo['end'].' COUNT: '.$tableInfo['count']);
                $isValid = false;
            } else {
                $this->info->addInsertedRowsInTableList($table, $tableInfo['count']);
                DUP_PRO_LOG::trace('DB VALIDATION 1 SUCCESS: count check table "'.$table.'" START: '.$tableInfo['start'].' END: '.$tableInfo['end'].' COUNT: '.$tableInfo['count']);
            }
        }

        $dbInsertIterator = $this->getDbBuildIterator();
        clearstatcache();
        if (filesize($this->dbStorePathPublic) !== $dbInsertIterator->getFileSize()) {
            DUP_PRO_LOG::infoTrace('SQL FILE SIZE CHECK FAILED, EXPECTED: '.$dbInsertIterator->getFileSize().' FILE SIZE: '.filesize($this->dbStorePathPublic).' OF FILE '.$this->dbStorePathPublic);
            $isValid = false;
        } else {
            DUP_PRO_LOG::infoTrace('SQL FILE SIZE CHECK OK, SIZE: '.$dbInsertIterator->getFileSize());
        }

        $dbInsertIterator->removeCounterFile();

        if ($isValid) {
            DUP_PRO_LOG::trace("DB VALIDATION 1: succeffully");
            $this->Package->db_build_progress->validationStage1 = true;
            $this->Package->update();
        } else {
            DUP_PRO_LOG::infoTrace("DB VALIDATION 1: failed to validate");
            throw new Exception("DB VALIDATION 1: failed to validate");
        }
    }

    /**
     * Used to initialize the PHP chunking logic
     *     *
     * @return void
     */
    private function doInit()
    {
        $global = DUP_PRO_Global_Entity::get_instance();

        do_action('duplicator_pro_build_database_before_start', $this->Package);

        $this->Package->db_build_progress->startTime = DUP_PRO_U::getMicrotime();
        $this->Package->set_status(DUP_PRO_PackageStatus::DBSTART);
        $this->dbStorePathPublic                     = "{$this->Package->StorePath}/{$this->File}";

        $log = "\n********************************************************************************\n";
        $log .= "DATABASE:\n";
        $log .= "********************************************************************************\n";
        $log .= "BUILD MODE:   PHP + CHUNKING ";
        $log .= "(query size limit - {$global->package_mysqldump_qrylimit} )\n";

        DUP_PRO_Log::info($log);

        do_action('duplicator_pro_build_database_start', $this->Package);
        $this->Package->update();
    }

    /**
     * Initialize the table to be processed for the dump.
     * 
     * @global wpdb $wpdb
     */
    private function doFiltering()
    {
        global $wpdb;
        $wpdb->query("SET session wait_timeout = ".DUPLICATOR_PRO_DB_MAX_TIME);
        $tables                                            = $this->getFilteredTables();
        $this->Package->db_build_progress->tablesToProcess = array_map(array('DUP_PRO_DB', 'updateCaseSensitivePrefix'), $tables);
        $this->Package->db_build_progress->countCheckSetStart();
        $this->Package->db_build_progress->doneFiltering   = true;
        $this->Package->update();

        // MAKE SURE THE ITERATOR IS RESET
        $dbInsertIterator = $this->getDbBuildIterator();
        $dbInsertIterator->rewind();
    }

    /**
     * Dumps the structure of the view table and procedures.
     * 
     * @global wpdb $wpdb
     */
    private function writeCreates()
    {
        global $wpdb;

        $handle = @fopen($this->dbStorePathPublic, 'a');

        //Added 'NO_AUTO_VALUE_ON_ZERO' at plugin version 3.4.8 to fix :
        //**ERROR** database error write 'Invalid default value for for older mysql versions
        $sql_header = "/* DUPLICATOR-PRO (";
        $sql_header .= (DUP_PRO_DB::getBuildMode() === DUP_PRO_DB::BUILD_MODE_PHP_MULTI_THREAD ? 'PHP MULTI-THREADED BUILD MODE' : 'PHP SINGLE-THREAD BUILD MODE');
        $sql_header .= ") MYSQL SCRIPT CREATED ON : ".date("Y-m-d H:i:s")." */\n\n";
        $sql_header .= "/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;\n";
        $sql_header .= "/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;\n";
        $sql_header .= "/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;\n\n";
        DupProSnapLibIOU::fwrite($handle, $sql_header);

        //BUILD CREATES:
        //All creates must be created before inserts do to foreign key constraints
        foreach ($this->Package->db_build_progress->tablesToProcess as $table) {
            $create             = $wpdb->get_row("SHOW CREATE TABLE `{$table}`", ARRAY_N);
            $count              = 1;
            // UPDATE CASE SENSITIVE TABLE PREFIX NAME
            $create_table_query = str_ireplace($table, $table, $create[1], $count);

            DupProSnapLibIOU::fwrite($handle, "{$create_table_query};\n\n");
            DUP_PRO_LOG::trace("DATABASE CREATE TABLE: ".$table." OK");
        }

        $procedures = $wpdb->get_col("SHOW PROCEDURE STATUS WHERE `Db` = '{$wpdb->dbname}'", 1);
        if (count($procedures)) {
            foreach ($procedures as $procedure) {
                DupProSnapLibIOU::fwrite($handle, "DELIMITER ;;\n");
                $create = $wpdb->get_row("SHOW CREATE PROCEDURE `{$procedure}`", ARRAY_N);
                DupProSnapLibIOU::fwrite($handle, "{$create[2]} ;;\n");
                DupProSnapLibIOU::fwrite($handle, "DELIMITER ;\n\n");
            }
        }

        $views = $wpdb->get_col("SHOW FULL TABLES WHERE Table_Type = 'VIEW'");
        if (count($views)) {
            foreach ($views as $view) {
                $create = $wpdb->get_row("SHOW CREATE VIEW `{$view}`", ARRAY_N);
                DupProSnapLibIOU::fwrite($handle, "{$create[1]};\n\n");
            }
        }
        DupProSnapLibIOU::fwrite($handle, self::TABLE_CREATION_END_MARKER);

        $dbInsertIterator = $this->getDbBuildIterator();
        $fileStat         = fstat($handle);
        $dbInsertIterator->addFileSize($fileStat['size']);
        DupProSnapLibIOU::fclose($handle);

        $this->Package->db_build_progress->errorOut    = true;
        $this->Package->db_build_progress->doneCreates = true;
        $this->Package->update();
        $this->Package->db_build_progress->errorOut    = false;
    }

    /**
     * 
     * @global wpdb $wpdb
     * @return void
     * 
     * @throws Exception
     */
    private function writeInsertChunk()
    {
        global $wpdb;
        $startTime   = microtime(true);
        $elapsedTime = 0;
        $global      = DUP_PRO_Global_Entity::get_instance();

        $dbInsertIterator = $this->getDbBuildIterator();
        $dbBuildProgress  = $this->Package->db_build_progress;

        $this->truncateSqlFileOnExpectedSize($dbInsertIterator->getFileSize());

        if (($handle = fopen($this->dbStorePathPublic, 'a')) === false) {
            $msg = print_r(error_get_last(), true);
            throw new Exception("FILE READ ERROR: Could not open file {$this->dbStorePathPublic} {$msg}");
        }

        if (!$dbInsertIterator->lastIsCompleteInsert()) {
            $dbInsertIterator->setLastIsCompleteInsert(
                DupProSnapLibIOU::fwrite($handle, self::CLOSE_INSERT_QUERY)
            );
        }

        for (; $dbInsertIterator->valid(); $dbInsertIterator->next()) {
            $table = $dbInsertIterator->current();

            if ($this->traceLogEnabled) {
                $table_number  = $dbInsertIterator->key() + 1;
                DUP_PRO_Log::trace("------------ DB SCAN CHUNK LOOP ------------");
                DUP_PRO_Log::trace("table: ".$table." (".$table_number." of ".$dbInsertIterator->count().")");
                DUP_PRO_Log::trace("worker_time: ".$elapsedTime." Max worker time: ".self::PHP_DUMP_CHUNK_WORKER_TIME);
                DUP_PRO_Log::trace("row_offset: ".$dbInsertIterator->getCurrentOffset()." of ".$dbInsertIterator->getCurrentRows());
                if (($primaryColumn = DupProSnapLibDB::getUniqueIndexColumn($wpdb->dbh, $table)) === false) {
                    DUP_PRO_Log::trace("no key column found, use normal offset ");
                } else {
                    DUP_PRO_Log::trace("primary column for offset system: ".DupProSnapLibLogger::varToString($primaryColumn));
                }
                DUP_PRO_Log::trace("last_index_offset: ".DupProSnapLibLogger::varToString($dbInsertIterator->getLastIndexOffset()));

                DUP_PRO_Log::trace("query size limit: ".$global->package_mysqldump_qrylimit);
            }

            if ($dbInsertIterator->getCurrentRows() <= 0) {
                continue;
            }

            $currentQuerySize = 0;
            $indexColumns     = DupProSnapLibDB::getUniqueIndexColumn($wpdb->dbh, $table);
            $firstInsert      = true;
            $insertQueryLine  = true;

            do {
                $result = DupProSnapLibDB::selectUsingPrimaryKeyAsOffset(
                        $wpdb->dbh,
                        'SELECT * FROM `'.$table.'` WHERE 1',
                        $table,
                        $dbInsertIterator->getLastIndexOffset(),
                        DUP_PRO_Constants::PHP_DUMP_READ_PAGE_SIZE);

                if ($result === false) {
                    $this->setErrorSelectFrom($table);
                    return;
                }

                if (($lastSelectNumRows = DupProSnapLibDB::numRows($result)) > 0) {
                    while (($row = DupProSnapLibDB::fetchAssoc($result))) {
                        if ($currentQuerySize >= $global->package_mysqldump_qrylimit) {
                            $insertQueryLine = true;
                        }

                        if ($insertQueryLine) {
                            $line             = ($firstInsert ? '' : self::CLOSE_INSERT_QUERY).'INSERT INTO `'.$table.'` VALUES '."\n";
                            $insertQueryLine  = $firstInsert      = false;
                            $currentQuerySize = 0;
                        } else {
                            $line = ",\n";
                        }
                        $line     .= '('.implode(',', array_map(array('DUP_PRO_DB', 'escValueToQueryString'), $row)).')';
                        $lineSize = DupProSnapLibIOU::fwriteChunked($handle, $line);

                        /* TEST INTERRUPTION START *** */
                        /* mt_srand((double) microtime() * 1000000);
                          if (mt_rand(1, 1000) > 997) {
                          die();
                          } */
                        /* TEST INTERRUPTION END *** */

                        $totalCount = $dbInsertIterator->nextRow(
                            DupProSnapLibDB::getOffsetFromRowAssoc($row, $indexColumns, $dbInsertIterator->getLastIndexOffset()),
                            $lineSize
                        );

                        $currentQuerySize += $lineSize;

                        if (0 == ($totalCount % self::ROWS_NUM_TO_UPDATE_PROGRESS)) {
                            $this->setProgressPer($totalCount);
                        }

                        if (($elapsedTime = microtime(true) - $startTime) >= self::PHP_DUMP_CHUNK_WORKER_TIME) {
                            break;
                        }
                    }

                    if ($this->throttleDelayInUs > 0) {
                        usleep($this->throttleDelayInUs * DUP_PRO_Constants::PHP_DUMP_READ_PAGE_SIZE);
                    }

                    if ($elapsedTime >= self::PHP_DUMP_CHUNK_WORKER_TIME) {
                        break 2;
                    }
                } else if ($insertQueryLine == false) {
                    // if false exists a insert to close
                    $dbInsertIterator->setLastIsCompleteInsert(
                        DupProSnapLibIOU::fwrite($handle, self::CLOSE_INSERT_QUERY)
                    );
                }

                DupProSnapLibDB::freeResult($result);
            } while ($lastSelectNumRows > 0);
        }

        if (($dbBuildProgress->completed = !$dbInsertIterator->valid())) {
            $this->writeSQLFooter($handle);
            $this->Package->update();
        } else {
            $this->setProgressPer($totalCount);
        }

        DupProSnapLibIOU::fclose($handle);
    }

    /**
     * 
     * @param int $size
     * @return boolean
     * @throws Exception
     */
    private function truncateSqlFileOnExpectedSize($size)
    {
        clearstatcache();
        if (filesize($this->dbStorePathPublic) === $size) {
            return true;
        }

        $handle = @fopen($this->dbStorePathPublic, 'r+');
        if ($handle === false) {
            $msg = print_r(error_get_last(), true);
            throw new Exception("FILE READ ERROR: Could not open file {$this->dbStorePathPublic} {$msg}");
        }

        if (ftruncate($handle, $size)) {
            DUP_PRO_LOG::trace("SQL FILE DON'T MATCH SIZE, TRUNCATE AT ".$size);
        } else {
            throw new Exception("FILE TRUNCATE ERROR: Could not truncate to file size ".$size);
        }

        DupProSnapLibIOU::fclose($handle);
    }

    private function writeSQLFooter($fileHandle)
    {
        $sql_footer       = "\n/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;\n";
        $sql_footer       .= "/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;\n";
        $sql_footer       .= "/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;\n\n";
        $sql_footer       .= "/* Duplicator WordPress Timestamp: ".date("Y-m-d H:i:s")."*/\n";
        $sql_footer       .= "/* ".DUPLICATOR_PRO_DB_EOF_MARKER." */\n";
        $dbInsertIterator = $this->getDbBuildIterator();
        $dbInsertIterator->addFileSize(DupProSnapLibIOU::fwrite($fileHandle, $sql_footer));
    }

    private function setProgressPer($offset)
    {
        $per = DupProSnapLibUtil::getWorkPercent(
                DUP_PRO_PackageStatus::DBSTART,
                DUP_PRO_PackageStatus::DBDONE,
                $this->Package->db_build_progress->countCheckData['impreciseTotalRows'],
                $offset);
        $this->Package->set_status($per);
    }

    private function doFinish()
    {
        DUP_PRO_Log::info("SQL CREATED: {$this->File}");
        $time_end     = DUP_PRO_U::getMicrotime();
        $elapsed_time = DUP_PRO_U::elapsedTime($time_end, $this->Package->db_build_progress->startTime);

        $sql_file_size = filesize($this->dbStorePathPublic);
        if ($sql_file_size <= 0) {
            DUP_PRO_Log::error("SQL file generated zero bytes.", "No data was written to the sql file.  Check permission on file and parent directory at [{$this->dbStorePathPublic}]");
        }
        DUP_PRO_Log::info("SQL FILE SIZE: ".DUP_PRO_U::byteSize($sql_file_size));
        DUP_PRO_Log::info("SQL FILE TIME: ".date("Y-m-d H:i:s"));
        DUP_PRO_Log::info("SQL RUNTIME: {$elapsed_time}");
        DUP_PRO_Log::info("MEMORY STACK: ".DUP_PRO_Server::getPHPMemory());

        $this->Size = @filesize($this->dbStorePathPublic);
        $this->Package->set_status(DUP_PRO_PackageStatus::DBDONE);
        do_action('duplicator_pro_build_database_completed', $this->Package);
    }

    private function getIntFieldsStruct($table)
    {
        if (!isset($this->intFieldsStruct[$table])) {
            $table_structure             = $GLOBALS['wpdb']->get_results("DESCRIBE `$table`");
            $int_fields_struct_for_table = array();
            foreach ($table_structure as $struct) {
                if ((0 === strpos($struct->Type, 'tinyint')) || (0 === strpos(strtolower($struct->Type), 'smallint')) || (0 === strpos(strtolower($struct->Type), 'mediumint')) || (0 === strpos(strtolower($struct->Type), 'int')) || (0 === strpos(strtolower($struct->Type), 'bigint'))
                ) {
                    $int_fields_struct_for_table[$struct->Field] = (null === $struct->Default ) ? 'NULL' : $struct->Default;
                }
            }
            $this->intFieldsStruct[$table] = $int_fields_struct_for_table;
        }
        return $this->intFieldsStruct[$table];
    }
}