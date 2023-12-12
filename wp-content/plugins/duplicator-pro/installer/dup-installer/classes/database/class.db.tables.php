<?php
/**
 * Original installer files manager
 *
 * Standard: PSR-2
 * @link http://www.php-fig.org/psr/psr-2 Full Documentation
 *
 * @package SC\DUPX\U
 *
 */
defined('ABSPATH') || defined('DUPXABSPATH') || exit;

/**
 * Original installer files manager
 * singleton class
 */
final class DUPX_DB_Tables
{

    /**
     * 
     * @var self
     */
    private static $instance = null;

    /**
     * 
     * @var DUPX_DB_Table_item[]
     */
    private $tables = array();

    /**
     *
     * @return self
     */
    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    private function __construct()
    {
        $confTables = (array) DUPX_ArchiveConfig::getInstance()->dbInfo->tablesList;
        foreach ($confTables as $tableName => $tableInfo) {
            $rows = ($tableInfo->insertedRows === false ? $tableInfo->inaccurateRows : $tableInfo->insertedRows);

            $this->tables[$tableName] = new DUPX_DB_Table_item($tableName, $rows, $tableInfo->size);
        }

        DUPX_Log::info('CONSTRUCT TABLES: '.DUPX_log::varToString($this->tables), DUPX_Log::LV_HARD_DEBUG);
    }

    /**
     * 
     * @return DUPX_DB_Table_item[]
     */
    public function getTables()
    {
        return $this->tables;
    }

    /**
     * 
     * @return [string]
     */
    public function getTablesNames()
    {
        return array_keys($this->tables);
    }

    /**
     * 
     * @return [string]
     */
    public function getNewTablesNames()
    {
        $result = array();

        foreach ($this->tables as $tableObj) {
            if ($tableObj->extract()) {
                $result[] = $tableObj->getNewName();
            }
        }

        return $result;
    }

    /**
     * 
     * @return [string]
     */
    public function getReplaceTablesNames()
    {
        $result = array();

        foreach ($this->tables as $tableObj) {
            if ($tableObj->replaceEngine()) {
                $result[] = $tableObj->getNewName();
            }
        }

        return $result;
    }

    /**
     * 
     * @param id $subsiteId
     * @return string
     */
    public function getSubsiteTablesNewNames($subsiteId)
    {
        $result = array();

        foreach ($this->tables as $tableObj) {
            if ($tableObj->extract() && $tableObj->getSubsisteId() === $subsiteId) {
                $result[] = $tableObj->getNewName();
            }
        }

        return $result;
    }

    /**
     * return list of current standalone site tables without prefix
     * 
     * @return [string]
     */
    public function getStandaoneTablesWithoutPrefix()
    {
        $standaloneTables = null;

        if (is_null($standaloneTables)) {
            $standaloneTables = array();
            $standaloneId     = DUPX_Paramas_Manager::getInstance()->getValue(DUPX_Paramas_Manager::PARAM_SUBSITE_ID);

            foreach ($this->tables as $tableObj) {
                if ($tableObj->getSubsisteId() === $standaloneId) {
                    $standaloneTables[] = $tableObj->getNameWithoutPrefix();
                }
            }
        }

        return $standaloneTables;
    }

    public function getTablesToSkip()
    {
        $result = array();

        foreach ($this->tables as $tableObj) {
            if (!$tableObj->extract()) {
                $result[] = $tableObj->getOriginalName();
            }
        }

        return $result;
    }

    /**
     * 
     * @param type $table
     * @return DUPX_DB_Table_item // false if table don't exists
     * 
     * @throws Exception
     */
    public function getTableObjByName($table)
    {
        if (!isset($this->tables[$table])) {
            throw new Exception('TABLE '.$table.' Isn\'t in list');
        }

        return $this->tables[$table];
    }

    /**
     * 
     * @return array
     */
    public function getRenameTablesMapping()
    {
        $mapping      = array(
            'standalone' => array(),
            'prefix'     => array()
        );
        $standaloneId = DUPX_Paramas_Manager::getInstance()->getValue(DUPX_Paramas_Manager::PARAM_SUBSITE_ID);

        foreach ($this->tables as $tableObj) {
            if (!$tableObj->extract()) {
                // skip stable not extracted
                continue;
            }

            if (DUPX_MU::newSiteAction() === DUPX_MultisiteMode::Standalone && $standaloneId > 1 && $tableObj->getSubsisteId() === $standaloneId) {
                $mapping['standalone'][$tableObj->getOriginalName()] = $tableObj->getNameWithoutPrefix();
                continue;
            }

            if ($tableObj->havePrefix() && $tableObj->getOriginalName() !== $tableObj->getNewName()) {
                $mapping['prefix'][$tableObj->getOriginalName()] = $tableObj->getNameWithoutPrefix(true);
            }
        }

        return $mapping;
    }

    /**
     * return param table default
     * 
     * @return array
     */
    public function getDefaultParamValue()
    {
        $result = array();

        foreach ($this->tables as $table) {
            $result[$table->getOriginalName()] = DUPX_Param_item_form_tables::getParamItemValueFromData($table->getOriginalName(), $table->canBeExctracted(), $table->canBeExctracted());
        }

        return $result;
    }

    /**
     * return general tables names without prefix
     * 
     * @return [string]
     */
    public static function getGeneralTablesNames()
    {
        return array(
            'commentmeta',
            'comments',
            'links',
            'options',
            'postmeta',
            'posts',
            'term_relationships',
            'term_taxonomy',
            'terms',
            'termmeta'
        );
    }

    /**
     * return multisite general tables without prefix
     * 
     * @return [string]
     */
    public static function getMultisiteTables()
    {
        return array(
            'blogmeta',
            'blogs',
            'blog_versions',
            'registration_log',
            'signups',
            'site',
            'sitemeta'
        );
    }
}