<?php
/**
 * Utility class managing th emigration data
 *
 * Standard: PSR_2
 * @link http://www.php_fig.org/psr/psr_2
 * @copyright (c) 2017, Snapcreek LLC
 * @license	https://opensource.org/licenses/GPL_3.0 GNU Public License
 *
 */
defined('ABSPATH') || defined('DUPXABSPATH') || exit;

class DUP_PRO_Migration
{

    const HOOK_FIRST_LOGIN_AFTER_INSTALL = 'duplicator_pro_hook_first_login_after_install';
    const HOOK_BOTTOM_MIGRATION_MESSAGE  = 'duplicator_pro_hook_bottom_migration_message';
    const FIRST_LOGIN_OPTION             = 'duplicator_pro_first_login_after_install';
    const MIGRATION_DATA_OPTION          = 'duplicator_pro_migration_data';

    protected static $migrationCleanupReport = array(
        'removed' => array(),
        'stored'  => array()
    );

    public static function init()
    {
        add_action('admin_init', array(__CLASS__, 'adminInit'));
        add_action(self::HOOK_FIRST_LOGIN_AFTER_INSTALL, array(__CLASS__, 'removeFirstLoginOption'));
        add_action(self::HOOK_FIRST_LOGIN_AFTER_INSTALL, array(__CLASS__, 'storeMigrationFiles'));
        add_action(self::HOOK_FIRST_LOGIN_AFTER_INSTALL, array(__CLASS__, 'setDupSettingsAfterInstall'));

        // LAST BEACAUSE MAKE A WP_REDIRECT
        add_action(self::HOOK_FIRST_LOGIN_AFTER_INSTALL, array(__CLASS__, 'autoCleanFileAfterInstall'), 99999);

        add_filter(self::HOOK_BOTTOM_MIGRATION_MESSAGE, array(__CLASS__, 'preventMigrationLiteMessagesDisplay'));
    }

    public static function adminInit()
    {
        if (self::isFirstLoginAfterInstall()) {
            add_action('current_screen', array(__CLASS__, 'wpAdminHook'), 99999);
            update_option(DUP_PRO_UI_Notice::OPTION_KEY_MIGRATION_SUCCESS_NOTICE, true);
            do_action(self::HOOK_FIRST_LOGIN_AFTER_INSTALL, self::getMigrationData());
        }
    }

    /**
     * 
     */
    public static function wpAdminHook()
    {
        $screen = get_current_screen();
        if (!is_null($screen) && $screen->id != 'duplicator-pro_page_duplicator-pro-tools') {
            $adminPage = '/admin.php?page=duplicator-pro-tools';
            $adminUrl  = is_multisite() ? network_admin_url($adminPage) : admin_url($adminPage);

            wp_redirect($adminUrl);
            exit;
        }
    }

    /**
     * 
     * @return boolean
     */
    public static function isFirstLoginAfterInstall()
    {
        if (is_user_logged_in() && get_option(self::FIRST_LOGIN_OPTION, false)) {
            if (is_multisite()) {
                if (is_super_admin()) {
                    return true;
                }
            } else {
                if (current_user_can('manage_options')) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * prevent lite migration message
     * @todo remove this when the refactoring is finished
     * 
     */
    public static function preventMigrationLiteMessagesDisplay($html)
    {
        return $html.'<style>#dup-global-error-reserved-files { display: none !important; } </style>';
    }

    /**
     * 
     * @return string
     */
    public static function getCleanFilesAcrtionUrl()
    {
        $adminPage = '/admin.php?page=duplicator-pro-tools&tab=diagnostics&action=installer';
        return (is_multisite() ? network_admin_url($adminPage) : admin_url($adminPage));
    }

    /**
     * 
     * @param array $migrationData
     * @return void
     */
    public static function autoCleanFileAfterInstall($migrationData)
    {
        if ($migrationData == false || $migrationData['cleanInstallerFiles'] == false) {
            return;
        }

        wp_redirect(self::getCleanFilesAcrtionUrl());
        exit;
    }

    public static function setDupSettingsAfterInstall($migrationData)
    {
        $global = DUP_PRO_Global_Entity::get_instance();
        if (!($global instanceof DUP_PRO_Global_Entity)) {
            return;
        }

        $global->lock_mode                 = DUP_PRO_Global_Entity::get_lock_type();
        $global->ajax_protocol             = DUPLICATOR_PRO_DEFAULT_AJAX_PROTOCOL;
        $global->server_kick_off_sslverify = DUP_PRO_Global_Entity::get_server_kick_sslverify_flag();
        if ($global->archive_build_mode !== DUP_PRO_Archive_Build_Mode::DupArchive) {
            $global->set_build_mode();
        }
        $global->save();
        flush_rewrite_rules(true);

        // remove point in database but not files.
        DUP_PRO_Package_Recover::resetRecoverPackage();
    }

    public static function removeFirstLoginOption($migrationData)
    {
        delete_option(self::FIRST_LOGIN_OPTION);
    }

    /**
     * 
     * @staticvar array $migrationData
     * 
     * @param string|null $key
     * @return mixed
     */
    public static function getMigrationData($key = null)
    {
        static $migrationData = null;
        if (is_null($migrationData)) {
            $migrationData = get_option(self::MIGRATION_DATA_OPTION, false);
            if (is_string($migrationData)) {
                $migrationData = (array) json_decode($migrationData);
            }
        }

        if (is_null($key)) {
            return $migrationData;
        } else if (isset($migrationData[$key])) {
            return $migrationData[$key];
        } else {
            return false;
        }
    }

    /**
     * 
     * @return string
     */
    public static function getSaveModeWarning()
    {
        switch (self::getMigrationData('safeMode')) {
            case 1:
                //safe_mode basic
                return DUP_PRO_U::__('NOTICE: Safe mode (Basic) was enabled during install, be sure to re-enable all your plugins.');
            case 2:
                //safe_mode advance
                return DUP_PRO_U::__('NOTICE: Safe mode (Advanced) was enabled during install, be sure to re-enable all your plugins.');
            case 0:
            default:
                return '';
        }
    }

    /**
     * 
     * @param array $migrationData
     */
    public static function storeMigrationFiles($migrationData)
    {
        wp_mkdir_p(DUPLICATOR_PRO_SSDIR_PATH_INSTALLER);
        DupProSnapLibIOU::emptyDir(DUPLICATOR_PRO_SSDIR_PATH_INSTALLER);

        $filesToMove = array(
            $migrationData['installerLog'],
            $migrationData['installerBootLog'],
            $migrationData['origFileFolderPath']
        );

        foreach ($filesToMove as $path) {
            if (file_exists($path)) {
                if (DupProSnapLibIOU::rcopy($path, DUPLICATOR_PRO_SSDIR_PATH_INSTALLER.'/'.basename($path), true)) {
                    self::$migrationCleanupReport['stored'] = "<div class='success'>"
                        ."<i class='fa fa-check'></i> "
                        .DUP_PRO_U::__('Original files folder moved in installer backup directory')." - ".esc_html($path).
                        "</div>";
                } else {
                    self::$migrationCleanupReport['stored'] = "<div class='success'>"
                        .'<i class="fa fa-exclamation-triangle"></i> '
                        .sprintf(DUP_PRO_U::__('Can\'t move %s to %s'), esc_html($path), DUPLICATOR_PRO_SSDIR_PATH_INSTALLER)
                        .'</div>';
                }
            }
        }
    }

    /**
     * 
     * @return array
     */
    public static function getStoredMigrationLists()
    {
        if (($migrationData = self::getMigrationData()) == false) {
            $filesToCheck = array();
        } else {
            $filesToCheck = array(
                $migrationData['installerLog']       => DUP_PRO_U::__('Installer log'),
                $migrationData['installerBootLog']   => DUP_PRO_U::__('Installer boot log'),
                $migrationData['origFileFolderPath'] => DUP_PRO_U::__('Original files folder')
            );
        }

        $result = array();

        foreach ($filesToCheck as $path => $label) {
            $storedPath = DUPLICATOR_PRO_SSDIR_PATH_INSTALLER.'/'.basename($path);
            if (!file_exists($storedPath)) {
                continue;
            }
            $result[$storedPath] = $label;
        }

        return $result;
    }

    /**
     * 
     * @return bool
     */
    public static function haveFileToClean()
    {
        return count(self::checkInstallerFilesList()) > 0;
    }

    /**
     * Gets a list of all the installer files and directory by name and full path
     *
     * @remarks
     *  FILES:		installer.php, installer-backup.php, dup-installer-bootlog__[HASH].txt
     * 	DIRS:		dup-installer
     * 	Last set is for lazy developer cleanup files that a developer may have
     *  accidentally left around lets be proactive for the user just in case.
     *
     * @return [string] // [file_name]
     */
    public static function getGenericInstallerFiles()
    {
        return array(
            'installer.php',
            '[HASH]installer-backup.php',
            'dup-installer',
            'dup-installer[HASH]',
            'dup-installer-bootlog__[HASH].txt',
            '[HASH]_archive.zip|daf'
        );
    }

    /**
     * 
     * @return string[]
     */
    public static function checkInstallerFilesList()
    {
        $migrationData = self::getMigrationData();

        $foldersToChkeck = array(
            DupProSnapLibIOU::safePathTrailingslashit(ABSPATH),
            DupProSnapLibUtilWp::getHomePath(),
        );

        $result = array();

        if (!empty($migrationData)) {
            if (file_exists($migrationData['archivePath']) &&
                !DUP_PRO_Archive::isBackupPathChild($migrationData['archivePath']) &&
                !DUP_PRO_Package_Importer::isImportPath($migrationData['archivePath']) &&
                !DUP_PRO_Package_Recover::isRecoverPath($migrationData['archivePath'])) {
                $result[] = $migrationData['archivePath'];
            }
            if (file_exists($migrationData['installerPath']) &&
                !DUP_PRO_Archive::isBackupPathChild($migrationData['archivePath']) &&
                !DUP_PRO_Package_Recover::isRecoverPath($migrationData['archivePath'])) {
                $result[] = $migrationData['installerPath'];
            }
            if (file_exists($migrationData['installerBootLog'])) {
                $result[] = $migrationData['installerBootLog'];
            }
            if (file_exists($migrationData['dupInstallerPath'])) {
                $result[] = $migrationData['dupInstallerPath'];
            }
        }

        foreach ($foldersToChkeck as $folder) {
            $result = array_merge($result, DupProSnapLibIOU::regexGlob($folder, array(
                    'regexFile'   => array(
                        DUPLICATOR_PRO_ARCHIVE_REGEX_PATTERN,
                        DUPLICATOR_PRO_INSTALLER_REGEX_PATTERN,
                        DUPLICATOR_PRO_DUP_INSTALLER_BOOTLOG_REGEX_PATTERN
                    ),
                    'regexFolder' => array(
                        DUPLICATOR_PRO_DUP_INSTALLER_FOLDER_REGEX_PATTERN
                    )
            )));
        }

        $result = array_map(array('DupProSnapLibIOU', 'safePathUntrailingslashit'), $result);
        return array_unique($result);
    }

    public static function cleanMigrationFiles()
    {
        $cleanList = self::checkInstallerFilesList();

        $result = array();

        foreach ($cleanList as $path) {
            try {
                $success = (DupProSnapLibIOU::rrmdir($path) !== false);
            }
            catch (Exception $ex) {
                $success = false;
            }
            catch (Error $ex) {
                $success = false;
            }

            $result[$path] = $success;
        }

        return $result;
    }
}