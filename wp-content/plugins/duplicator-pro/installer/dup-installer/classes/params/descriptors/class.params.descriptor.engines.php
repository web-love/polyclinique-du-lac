<?php
/**
 * Installer params manager
 *
 * Standard: PSR-2
 * @link http://www.php-fig.org/psr/psr-2 Full Documentation
 *
 * @final class DUPX_Paramas_Descriptor_urls_paths
  {
  package SC\DUPX\U
 *
 */
defined('ABSPATH') || defined('DUPXABSPATH') || exit;

/**
 * class where all parameters are initialized. Used by the param manager
 */
final class DUPX_Paramas_Descriptor_engines implements DUPX_Interface_Paramas_Descriptor
{

    /**
     *
     * @param DUPX_Param_item[] $params
     */
    public static function init(&$params)
    {
        $archiveConfig = DUPX_ArchiveConfig::getInstance();

        $params[DUPX_Paramas_Manager::PARAM_RESTORE_BACKUP_MODE] = new DUPX_Param_item_form(
            DUPX_Paramas_Manager::PARAM_RESTORE_BACKUP_MODE,
            DUPX_Param_item_form::TYPE_BOOL,
            DUPX_Param_item_form::FORM_TYPE_SWITCH,
            array(
            'default' => DUPX_InstallerState::getInstance()->isInstallerCreatedInThisLocation() && !$archiveConfig->isPartialNetwork()
            ),
            array(
            'status' => function($paramObj) {
                if (DUPX_ArchiveConfig::getInstance()->isPartialNetwork()) {
                    return DUPX_Param_item_form::STATUS_SKIP;
                } else if (DUPX_InstallerState::getInstance()->isInstallerCreatedInThisLocation()) {
                    return DUPX_Param_item_form::STATUS_ENABLED;
                } else {
                    return DUPX_Param_item_form::STATUS_SKIP;
                }
            },
            'label'          => 'Backup:',
            'checkboxLabel'  => 'Enable restore backup mode.',
            'wrapperClasses' => array('revalidate-on-change'),
            'subNote'        => '<b>When this option is enabled, all search and replacement actions are excluded from the installation process.</b> '
            .'This option can only be activated if both the old path and URL match the new ones. '
            )
        );

        $statusRemoveActions = (DUPX_ArchiveConfig::getInstance()->exportOnlyDB ? DUPX_Param_item_form_option::OPT_DISABLED : DUPX_Param_item_form_option::OPT_ENABLED);

        $params[DUPX_Paramas_Manager::PARAM_ARCHIVE_ACTION] = new DUPX_Param_item_form(
            DUPX_Paramas_Manager::PARAM_ARCHIVE_ACTION,
            DUPX_Param_item_form::TYPE_STRING,
            DUPX_Param_item_form::FORM_TYPE_SELECT,
            array(
            'default'      => DUP_PRO_Extraction::ACTION_DO_NOTHING,
            'acceptValues' => array(
                DUP_PRO_Extraction::ACTION_DO_NOTHING,
                DUP_PRO_Extraction::ACTION_REMOVE_WP_FILES,
                DUP_PRO_Extraction::ACTION_REMOVE_ALL_FILES
            )
            ),
            array(
            'label'          => 'Archive Action:',
            'options'        => array(
                new DUPX_Param_item_form_option(DUP_PRO_Extraction::ACTION_DO_NOTHING, 'Extract files over current files'),
                new DUPX_Param_item_form_option(DUP_PRO_Extraction::ACTION_REMOVE_WP_FILES, 'Remove WP core and content and extract', $statusRemoveActions),
                new DUPX_Param_item_form_option(DUP_PRO_Extraction::ACTION_REMOVE_ALL_FILES, 'Remove all files except addon sites and extract', $statusRemoveActions)
            ),
            'wrapperClasses' => array('revalidate-on-change'),
            'subNote'        => function ($param) {
                return dupxTplRender('parts/params/archive-action-notes', array(
                'currentAction' => $param->getValue()
                ), false);
            }
            )
        );

        $params[DUPX_Paramas_Manager::PARAM_ARCHIVE_ENGINE_SKIP_WP_FILES] = new DUPX_Param_item_form(
            DUPX_Paramas_Manager::PARAM_ARCHIVE_ENGINE_SKIP_WP_FILES,
            DUPX_Param_item_form::TYPE_STRING,
            DUPX_Param_item_form::FORM_TYPE_SELECT,
            array(
            'default'      => DUP_PRO_Extraction::FILTER_NONE,
            'acceptValues' => array(
                DUP_PRO_Extraction::FILTER_NONE,
                DUP_PRO_Extraction::FILTER_SKIP_WP_CORE,
                DUP_PRO_Extraction::FILTER_SKIP_CORE_PLUG_THEMES,
                DUP_PRO_Extraction::FILTER_ONLY_MEDIA_PLUG_THEMES
            )
            ),
            array(
            'label'          => 'Skip Files:',
            'options'        => array(
                new DUPX_Param_item_form_option(DUP_PRO_Extraction::FILTER_NONE, 'Extract all files'),
                new DUPX_Param_item_form_option(DUP_PRO_Extraction::FILTER_SKIP_WP_CORE, 'Skip extraction of WP core files'),
                new DUPX_Param_item_form_option(DUP_PRO_Extraction::FILTER_SKIP_CORE_PLUG_THEMES, 'Skip extraction of WP core files and plugins/themes existing on the host'),
                new DUPX_Param_item_form_option(DUP_PRO_Extraction::FILTER_ONLY_MEDIA_PLUG_THEMES, 'Extract only media files and new plugins and themes')
            ),
            'wrapperClasses' => array('revalidate-on-change'),
            'subNote'        => dupxTplRender('parts/params/extract-skip-notes', array(
                'currentSkipMode' => DUP_PRO_Extraction::FILTER_NONE
                ), false)
            )
        );


        $engineOptions = self::getArchiveEngineOptions();

        $params[DUPX_Paramas_Manager::PARAM_ARCHIVE_ENGINE] = new DUPX_Param_item_form(
            DUPX_Paramas_Manager::PARAM_ARCHIVE_ENGINE,
            DUPX_Param_item_form::TYPE_STRING,
            DUPX_Param_item_form::FORM_TYPE_SELECT,
            array(
            'default'          => $engineOptions['default'],
            'acceptValues'     => $engineOptions['acceptValues'],
            'sanitizeCallback' => function ($value) {
                if (DUPX_Paramas_Manager::getInstance()->getValue(DUPX_Paramas_Manager::PARAM_ARCHIVE_ENGINE_SKIP_WP_FILES) !== DUP_PRO_Extraction::FILTER_NONE && $value === DUP_PRO_Extraction::ENGINE_ZIP_SHELL) {
                    return DUP_PRO_Extraction::ENGINE_ZIP_CHUNK;
                }
                return $value;
            },
            ),
                                                                  array(
            'label'   => 'Extraction Mode:',
            'options' => $engineOptions['options'],
            'size'    => 0,
            'subNote' => $engineOptions['subNote'],
            'attr'    => array(
                'onchange' => 'DUPX.onSafeModeSwitch();'
            ))
        );

        $params[DUPX_Paramas_Manager::PARAM_DB_ACTION] = new DUPX_Param_item_form(
            DUPX_Paramas_Manager::PARAM_DB_ACTION,
            DUPX_Param_item_form::TYPE_STRING,
            DUPX_Param_item_form::FORM_TYPE_SELECT,
            array(
            'default'      => 'empty',
            'acceptValues' => array(
                DUPX_DBInstall::DBACTION_CREATE,
                DUPX_DBInstall::DBACTION_EMPTY,
                DUPX_DBInstall::DBACTION_RENAME,
                DUPX_DBInstall::DBACTION_MANUAL,
                DUPX_DBInstall::DBACTION_ONLY_CONNECT
            )
            ),
            array(
            'label'          => 'Database Action:',
            'wrapperClasses' => array('revalidate-on-change'),
            'options'        => array(
                new DUPX_Param_item_form_option(DUPX_DBInstall::DBACTION_CREATE,
                                                'Create New Database',
                                                function () {
                        if (DUPX_InstallerState::getInstance()->getMode() === DUPX_InstallerState::MODE_STD_INSTALL) {
                            return DUPX_Param_item_form_option::OPT_ENABLED;
                        } else {
                            return DUPX_Param_item_form_option::OPT_DISABLED;
                        }
                    }),
                new DUPX_Param_item_form_option(DUPX_DBInstall::DBACTION_EMPTY, 'Connect and Remove All Data'),
                new DUPX_Param_item_form_option(DUPX_DBInstall::DBACTION_RENAME, 'Connect and Backup Any Existing Data'),
                new DUPX_Param_item_form_option(DUPX_DBInstall::DBACTION_ONLY_CONNECT, 'Connect and Do Nothing (Advanced)'),
                new DUPX_Param_item_form_option(DUPX_DBInstall::DBACTION_MANUAL, 'Manual SQL Execution (Advanced)')
            )
            )
        );

        $params[DUPX_Paramas_Manager::PARAM_DB_ENGINE] = new DUPX_Param_item_form(
            DUPX_Paramas_Manager::PARAM_DB_ENGINE,
            DUPX_Param_item_form::TYPE_STRING,
            DUPX_Param_item_form::FORM_TYPE_SELECT,
            array(
            'default'      => DUPX_DBInstall::ENGINE_CHUNK,
            'acceptValues' => array(
                DUPX_DBInstall::ENGINE_CHUNK,
                DUPX_DBInstall::ENGINE_NORMAL
            )),
            array(
            'label'   => 'Database Mode:',
            'size'    => 0,
            'options' => array(
                new DUPX_Param_item_form_option(DUPX_DBInstall::ENGINE_CHUNK, 'Chunking mode'),
                new DUPX_Param_item_form_option(DUPX_DBInstall::ENGINE_NORMAL, 'Single step')
            ))
        );

        $params[DUPX_Paramas_Manager::PARAM_DB_CHUNK] = new DUPX_Param_item(
            DUPX_Paramas_Manager::PARAM_DB_CHUNK,
            DUPX_Param_item_form::TYPE_BOOL,
            array(
            'default' => ($params[DUPX_Paramas_Manager::PARAM_DB_ENGINE]->getValue() === DUPX_DBInstall::ENGINE_CHUNK)
            )
        );

        if ($params[DUPX_Paramas_Manager::PARAM_RESTORE_BACKUP_MODE]->getValue()) {
            $default = DUPX_S3_Funcs::MODE_SKIP;
        } else if ($params[DUPX_Paramas_Manager::PARAM_DB_ENGINE]->getValue() === DUPX_DBInstall::ENGINE_CHUNK) {
            $default = DUPX_S3_Funcs::MODE_CHUNK;
        } else {
            $default = DUPX_S3_Funcs::MODE_NORMAL;
        }
        $params[DUPX_Paramas_Manager::PARAM_REPLACE_ENGINE] = new DUPX_Param_item(
            DUPX_Paramas_Manager::PARAM_REPLACE_ENGINE,
            DUPX_Param_item_form::TYPE_INT,
            array(
            'default'      => $default,
            'acceptValues' => array(
                DUPX_S3_Funcs::MODE_NORMAL,
                DUPX_S3_Funcs::MODE_CHUNK,
                DUPX_S3_Funcs::MODE_SKIP,
            ))
        );


        $params[DUPX_Paramas_Manager::PARAM_SKIP_PATH_REPLACE] = new DUPX_Param_item(
            DUPX_Paramas_Manager::PARAM_SKIP_PATH_REPLACE,
            DUPX_Param_item_form::TYPE_BOOL,
            array(
            'default' => false
            )
        );
    }

    /**
     * 
     * @param DUPX_Param_item[] $params
     */
    public static function updateParamsAfterOverwrite(&$params)
    {
        if ($params[DUPX_Paramas_Manager::PARAM_RESTORE_BACKUP_MODE]->getStatus() !== DUPX_Param_item::STATUS_OVERWRITE) {
            $restoreBk = (DUPX_InstallerState::getInstance()->isInstallerCreatedInThisLocation() && !DUPX_ArchiveConfig::getInstance()->isPartialNetwork());
            $params[DUPX_Paramas_Manager::PARAM_RESTORE_BACKUP_MODE]->setValue($restoreBk);
        }

        if ($params[DUPX_Paramas_Manager::PARAM_ARCHIVE_ACTION]->getStatus() !== DUPX_Param_item::STATUS_OVERWRITE &&
            $params[DUPX_Paramas_Manager::PARAM_RESTORE_BACKUP_MODE]->getValue()) {
            $params[DUPX_Paramas_Manager::PARAM_ARCHIVE_ACTION]->setValue(DUP_PRO_Extraction::ACTION_REMOVE_WP_FILES);
        }

        if ($params[DUPX_Paramas_Manager::PARAM_SKIP_PATH_REPLACE]->getStatus() !== DUPX_Param_item::STATUS_OVERWRITE) {
            if (strlen($params[DUPX_Paramas_Manager::PARAM_PATH_OLD]->getValue()) === 0) {
                $params[DUPX_Paramas_Manager::PARAM_SKIP_PATH_REPLACE]->setValue(true);
            }
        }

        self::setPramasOnRestoreBackupMode($params);
    }

    public static function getDbChunkFromParams()
    {
        return DUPX_Paramas_Manager::getInstance()->getValue(DUPX_Paramas_Manager::PARAM_DB_ENGINE) === DUPX_DBInstall::ENGINE_CHUNK;
    }

    public static function getReplaceEngineModeFromParams()
    {
        $paramsManager = DUPX_Paramas_Manager::getInstance();
        if ($paramsManager->getValue(DUPX_Paramas_Manager::PARAM_RESTORE_BACKUP_MODE)) {
            return DUPX_S3_Funcs::MODE_SKIP;
        } else if ($paramsManager->getValue(DUPX_Paramas_Manager::PARAM_DB_ENGINE) === DUPX_DBInstall::ENGINE_CHUNK) {
            return DUPX_S3_Funcs::MODE_CHUNK;
        } else {
            return DUPX_S3_Funcs::MODE_NORMAL;
        }
    }

    /**
     * 
     * @param DUPX_Param_item[] $params
     */
    private static function setPramasOnRestoreBackupMode($params)
    {
        if (!$params[DUPX_Paramas_Manager::PARAM_RESTORE_BACKUP_MODE]->getValue()) {
            return;
        }

        if (DUPX_Custom_Host_Manager::getInstance()->isManaged()) {
            $params[DUPX_Paramas_Manager::PARAM_WP_CONFIG]->setValue('nothing');
            $params[DUPX_Paramas_Manager::PARAM_HTACCESS_CONFIG]->setValue('nothing');
            $params[DUPX_Paramas_Manager::PARAM_OTHER_CONFIG]->setValue('nothing');
        } else {
            $params[DUPX_Paramas_Manager::PARAM_WP_CONFIG]->setValue('modify');
            $params[DUPX_Paramas_Manager::PARAM_HTACCESS_CONFIG]->setValue('original');
            $params[DUPX_Paramas_Manager::PARAM_OTHER_CONFIG]->setValue('original');
        }

        $params[DUPX_Paramas_Manager::PARAM_EMPTY_SCHEDULE_STORAGE]->setValue(false);
    }

    private static function getArchiveEngineOptions()
    {
        $archiveConfig = DUPX_ArchiveConfig::getInstance();

        $acceptValues = array();
        $subNote      = null;
        if (($manualEnable = DUPX_Conf_Utils::isManualExtractFilePresent()) === true) {
            $acceptValues[] = DUP_PRO_Extraction::ENGINE_MANUAL;
        } else {
            $subNote = <<<SUBNOTEHTML
* Option enabled when archive has been pre-extracted
<a href="https://snapcreek.com/duplicator/docs/faqs-tech/#faq-installer-015-q" target="_blank">[more info]</a>               
SUBNOTEHTML;
        }
        if (($zipEnable = ($archiveConfig->isZipArchive() && DUPX_Conf_Utils::archiveExists() && DUPX_Conf_Utils::classZipArchiveEnable())) === true) {
            $acceptValues[] = DUP_PRO_Extraction::ENGINE_ZIP;
            $acceptValues[] = DUP_PRO_Extraction::ENGINE_ZIP_CHUNK;
        }
        if (($shellZipEnable = ($archiveConfig->isZipArchive() && DUPX_Conf_Utils::archiveExists() && DUPX_Conf_Utils::shellExecUnzipEnable())) === true) {
            $acceptValues[] = DUP_PRO_Extraction::ENGINE_ZIP_SHELL;
        }
        if (($dupEnable = (!$archiveConfig->isZipArchive() && DUPX_Conf_Utils::archiveExists())) === true) {
            $acceptValues[] = DUP_PRO_Extraction::ENGINE_DUP;
        }

        $options   = array();
        $options[] = new DUPX_Param_item_form_option(DUP_PRO_Extraction::ENGINE_MANUAL,
                                                     'Manual Archive Extraction',
                                                     $manualEnable ? DUPX_Param_item_form_option::OPT_ENABLED : DUPX_Param_item_form_option::OPT_DISABLED);

        if ($archiveConfig->isZipArchive()) {
            //ZIP-ARCHIVE
            $options[] = new DUPX_Param_item_form_option(DUP_PRO_Extraction::ENGINE_ZIP,
                                                         'PHP ZipArchive',
                                                         $zipEnable ? DUPX_Param_item_form_option::OPT_ENABLED : DUPX_Param_item_form_option::OPT_DISABLED);

            $options[] = new DUPX_Param_item_form_option(DUP_PRO_Extraction::ENGINE_ZIP_CHUNK,
                                                         'PHP ZipArchive Chunking',
                                                         $zipEnable ? DUPX_Param_item_form_option::OPT_ENABLED : DUPX_Param_item_form_option::OPT_DISABLED);

            $options[] = new DUPX_Param_item_form_option(DUP_PRO_Extraction::ENGINE_ZIP_SHELL,
                'Shell Exec Unzip',
                function () {
                    $archiveConfig = DUPX_ArchiveConfig::getInstance();
                    $pathsMapping  = $archiveConfig->getPathsMapping();
                    if (DUPX_Paramas_Manager::getInstance()->getValue(DUPX_Paramas_Manager::PARAM_ARCHIVE_ENGINE_SKIP_WP_FILES) !== DUP_PRO_Extraction::FILTER_NONE) {
                        return DUPX_Param_item_form_option::OPT_DISABLED;
                    }
                    if (is_array($pathsMapping) && count($pathsMapping) > 1) {
                        return DUPX_Param_item_form_option::OPT_DISABLED;
                    }
                    if ($archiveConfig->isZipArchive() && DUPX_Conf_Utils::archiveExists() && DUPX_Conf_Utils::shellExecUnzipEnable()) {
                        return DUPX_Param_item_form_option::OPT_ENABLED;
                    }
                    return DUPX_Param_item_form_option::OPT_DISABLED;
                }
            );
        } else {
            // DUPARCHIVE
            $options[] = new DUPX_Param_item_form_option(DUP_PRO_Extraction::ENGINE_DUP,
                                                         'DupArchive',
                                                         $dupEnable ? DUPX_Param_item_form_option::OPT_ENABLED : DUPX_Param_item_form_option::OPT_DISABLED);
        }

        if ($zipEnable) {
            $default = DUP_PRO_Extraction::ENGINE_ZIP_CHUNK;
        } else if ($shellZipEnable) {
            $default = DUP_PRO_Extraction::ENGINE_ZIP_SHELL;
        } else if ($dupEnable) {
            $default = DUP_PRO_Extraction::ENGINE_DUP;
        } else if ($manualEnable) {
            $default = DUP_PRO_Extraction::ENGINE_MANUAL;
        } else {
            $default = null;
        }

        return array(
            'options'      => $options,
            'acceptValues' => $acceptValues,
            'default'      => $default,
            'subNote'      => $subNote
        );
    }
}
