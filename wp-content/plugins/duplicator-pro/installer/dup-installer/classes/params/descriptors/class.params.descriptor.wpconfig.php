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
final class DUPX_Params_Descriptor_wpconfig implements DUPX_Interface_Paramas_Descriptor
{
    const NOTICE_ID_WP_CONF_PARAM_PATHS_EMPTY      = 'wp_conf_param_paths_empty_to_validate';
    const NOTICE_ID_WP_CONF_PARAM_DOMAINS_MODIFIED = 'wp_conf_param_domains_empty_to_validate';

    /**
     *
     * @param DUPX_Param_item[] $params
     */
    public static function init(&$params)
    {
        $archiveConfig = DUPX_ArchiveConfig::getInstance();

        $params[DUPX_Paramas_Manager::PARAM_WP_CONF_DISALLOW_FILE_EDIT] = new DUPX_Param_item_form_wpconfig(
            DUPX_Paramas_Manager::PARAM_WP_CONF_DISALLOW_FILE_EDIT,
            DUPX_Param_item_form::TYPE_BOOL,
            DUPX_Param_item_form::FORM_TYPE_CHECKBOX,
            array(
            'default' => $archiveConfig->getDefineArrayValue('DISALLOW_FILE_EDIT')
            ), array(
            'label'         => 'DISALLOW_FILE_EDIT:',
            'checkboxLabel' => 'Disable the Plugin/Theme Editor'
            )
        );

        $params[DUPX_Paramas_Manager::PARAM_WP_CONF_DISALLOW_FILE_MODS] = new DUPX_Param_item_form_wpconfig(
            DUPX_Paramas_Manager::PARAM_WP_CONF_DISALLOW_FILE_MODS,
            DUPX_Param_item_form::TYPE_BOOL,
            DUPX_Param_item_form::FORM_TYPE_CHECKBOX,
            array(
            'default' => $archiveConfig->getDefineArrayValue('DISALLOW_FILE_MODS', array(
                'value'      => false,
                'inWpConfig' => false
                )
            )
            ), array(
            'label'         => 'DISALLOW_FILE_MODS:',
            'checkboxLabel' => 'This will block users being able to use the plugin and theme installation/update functionality from the WordPress admin area'
            )
        );

        $params[DUPX_Paramas_Manager::PARAM_WP_CONF_AUTOSAVE_INTERVAL] = new DUPX_Param_item_form_wpconfig(
            DUPX_Paramas_Manager::PARAM_WP_CONF_AUTOSAVE_INTERVAL,
            DUPX_Param_item_form::TYPE_INT,
            DUPX_Param_item_form::FORM_TYPE_NUMBER,
            array(// ITEM ATTRIBUTES
            'default' => $archiveConfig->getDefineArrayValue('AUTOSAVE_INTERVAL', array(
                'value'      => 60,
                'inWpConfig' => false
                )
            )
            ), array(// FORM ATTRIBUTES
            'label'            => 'AUTOSAVE_INTERVAL:',
            'subNote'          => 'Auto-save interval in seconds (default:60)',
            'min'              => 5,
            'step'             => 1,
            'wrapperClasses'   => array('small'),
            'postfixElement'   => 'label',
            'postfixElemLabel' => 'Sec.',
            )
        );

        $params[DUPX_Paramas_Manager::PARAM_WP_CONF_WP_POST_REVISIONS] = new DUPX_Param_item_form_wpconfig(
            DUPX_Paramas_Manager::PARAM_WP_CONF_WP_POST_REVISIONS,
            DUPX_Param_item_form::TYPE_INT,
            DUPX_Param_item_form::FORM_TYPE_NUMBER,
            array(// ITEM ATTRIBUTES
            'default'          => $archiveConfig->getDefineArrayValue('WP_POST_REVISIONS', array(
                'value'      => true,
                'inWpConfig' => false
                )
            ),
            'sanitizeCallback' => function ($value) {
                //convert bool on int
                if ($value === true) {
                    $value = PHP_INT_MAX;
                }
                if ($value === false) {
                    $value = 0;
                }
                return $value;
            },
            ), array(// FORM ATTRIBUTES
            'label'          => 'WP_POST_REVISIONS:',
            'subNote'        => 'Number of article revisions. Select 0 to disable revisions. Disable the field to enable revisions.',
            'min'            => 0,
            'step'           => 1,
            'wrapperClasses' => array('small')
            )
        );

        $params[DUPX_Paramas_Manager::PARAM_WP_CONF_FORCE_SSL_ADMIN] = new DUPX_Param_item_form_wpconfig(
            DUPX_Paramas_Manager::PARAM_WP_CONF_FORCE_SSL_ADMIN,
            DUPX_Param_item_form::TYPE_BOOL,
            DUPX_Param_item_form::FORM_TYPE_CHECKBOX,
            array(
            'default' => $archiveConfig->getDefineArrayValue('FORCE_SSL_ADMIN')
            ), array(
            'label'         => 'FORCE_SSL_ADMIN:',
            'checkboxLabel' => 'Enforce Admin SSL'
            )
        );

        $params[DUPX_Paramas_Manager::PARAM_GEN_WP_AUTH_KEY] = new DUPX_Param_item_form(
            DUPX_Paramas_Manager::PARAM_GEN_WP_AUTH_KEY,
            DUPX_Param_item_form::TYPE_BOOL,
            DUPX_Param_item_form::FORM_TYPE_CHECKBOX,
            array(
            'default' => false
            ), array(
            'label'         => 'Auth Keys:',
            'checkboxLabel' => 'Generate New Unique Authentication Keys and Salts',
            'status'        => $archiveConfig->getLicenseType() >= DUPX_LicenseType::Freelancer ? DUPX_Param_item_form::STATUS_ENABLED : DUPX_Param_item_form::STATUS_DISABLED,
            'subNote'       => $archiveConfig->getLicenseType() >= DUPX_LicenseType::Freelancer ? '' : 'Available only in Freelancer and above'
            )
        );

        $params[DUPX_Paramas_Manager::PARAM_WP_CONF_AUTOMATIC_UPDATER_DISABLED] = new DUPX_Param_item_form_wpconfig(
            DUPX_Paramas_Manager::PARAM_WP_CONF_AUTOMATIC_UPDATER_DISABLED,
            DUPX_Param_item_form::TYPE_BOOL,
            DUPX_Param_item_form::FORM_TYPE_CHECKBOX,
            array(
            'default' => $archiveConfig->getDefineArrayValue('AUTOMATIC_UPDATER_DISABLED', array(
                'value'      => false,
                'inWpConfig' => false
                )
            )
            ), array(
            'label'         => 'AUTOMATIC_UPDATER_DISABLED:',
            'checkboxLabel' => 'Disable automatic updater'
            )
        );

        $autoUpdateValue = $archiveConfig->getWpConfigDefineValue('WP_AUTO_UPDATE_CORE');
        if (is_bool($autoUpdateValue)) {
            $autoUpdateValue = ($autoUpdateValue ? 'true' : 'false');
        }
        $params[DUPX_Paramas_Manager::PARAM_WP_CONF_WP_AUTO_UPDATE_CORE] = new DUPX_Param_item_form_wpconfig(
            DUPX_Paramas_Manager::PARAM_WP_CONF_WP_AUTO_UPDATE_CORE,
            DUPX_Param_item_form::TYPE_STRING,
            DUPX_Param_item_form::FORM_TYPE_SELECT,
            array(
            'default'      => array(
                'value'      => $autoUpdateValue,
                'inWpConfig' => $archiveConfig->inWpConfigDefine('WP_AUTO_UPDATE_CORE')
            ),
            'acceptValues' => array('', 'false', 'true', 'minor')),
                                                                 array(
            'label'   => 'WP_AUTO_UPDATE_CORE:',
            'options' => array(
                new DUPX_Param_item_form_option('minor', 'Enable only core minor updates - Default'),
                new DUPX_Param_item_form_option('false', 'Disable all core updates'),
                new DUPX_Param_item_form_option('true', 'Enable all core updates')
            )
            )
        );

        $params[DUPX_Paramas_Manager::PARAM_WP_CONF_IMAGE_EDIT_OVERWRITE] = new DUPX_Param_item_form_wpconfig(
            DUPX_Paramas_Manager::PARAM_WP_CONF_IMAGE_EDIT_OVERWRITE,
            DUPX_Param_item_form::TYPE_BOOL,
            DUPX_Param_item_form::FORM_TYPE_CHECKBOX,
            array(
            'default' => $archiveConfig->getDefineArrayValue('IMAGE_EDIT_OVERWRITE', array(
                'value'      => true,
                'inWpConfig' => false
                )
            )
            ), array(
            'label'         => 'IMAGE_EDIT_OVERWRITE:',
            'checkboxLabel' => 'Create only one set of image edits'
            )
        );

        $params[DUPX_Paramas_Manager::PARAM_WP_CONF_WP_CACHE] = new DUPX_Param_item_form_wpconfig(
            DUPX_Paramas_Manager::PARAM_WP_CONF_WP_CACHE,
            DUPX_Param_item_form::TYPE_BOOL,
            DUPX_Param_item_form::FORM_TYPE_CHECKBOX,
            array(
            'default' => $archiveConfig->getDefineArrayValue('WP_CACHE')
            ),
                                                             array(
            'label'         => 'WP_CACHE:',
            'checkboxLabel' => 'Keep Enabled'
            )
        );

        $params[DUPX_Paramas_Manager::PARAM_WP_CONF_WPCACHEHOME] = new DUPX_Param_item_form_wpconfig(
            DUPX_Paramas_Manager::PARAM_WP_CONF_WPCACHEHOME,
            DUPX_Param_item_form::TYPE_STRING,
            DUPX_Param_item_form::FORM_TYPE_TEXT,
            array(// ITEM ATTRIBUTES
            'default'          => $archiveConfig->getDefineArrayValue("WPCACHEHOME"),
            'sanitizeCallback' => function ($value) {
                $result = DupProSnapLibUtil::sanitize_non_stamp_chars_newline_and_trim($value);
                // WPCACHEHOME want final slash
                return DupProSnapLibIou::safePathTrailingslashit($result);
            }
            ), array(// FORM ATTRIBUTES
            'label'   => 'WPCACHEHOME:',
            'subNote' => 'This define is not part of the WordPress core but is a define used by WP Super Cache.'
            )
        );

        $params[DUPX_Paramas_Manager::PARAM_WP_CONF_WP_TEMP_DIR] = new DUPX_Param_item_form_wpconfig(
            DUPX_Paramas_Manager::PARAM_WP_CONF_WP_TEMP_DIR,
            DUPX_Param_item_form::TYPE_STRING,
            DUPX_Param_item_form::FORM_TYPE_TEXT,
            array(// ITEM ATTRIBUTES
            'default'          => $archiveConfig->getDefineArrayValue("WP_TEMP_DIR"),
            'sanitizeCallback' => array('DUPX_Paramas_Descriptors', 'sanitizePath')
            ), array(// FORM ATTRIBUTES
            'label' => 'WP_TEMP_DIR:',
            //'wrapperClasses' => array('small'),
            //'subNote'        => 'Wordpress admin maximum memory limit (default:256M)'
            )
        );

        $params[DUPX_Paramas_Manager::PARAM_WP_CONF_WP_DEBUG] = new DUPX_Param_item_form_wpconfig(
            DUPX_Paramas_Manager::PARAM_WP_CONF_WP_DEBUG,
            DUPX_Param_item_form::TYPE_BOOL,
            DUPX_Param_item_form::FORM_TYPE_CHECKBOX,
            array(
            'default' => $archiveConfig->getDefineArrayValue('WP_DEBUG')
            ), array(
            'label'         => 'WP_DEBUG:',
            'checkboxLabel' => 'Display errors and warnings'
            )
        );

        $debugLogValue = $archiveConfig->getWpConfigDefineValue('WP_DEBUG_LOG');
        if (is_string($debugLogValue)) {
            $debugLogValue = empty($debugLogValue) ? false : true;
        }
        $params[DUPX_Paramas_Manager::PARAM_WP_CONF_WP_DEBUG_LOG] = new DUPX_Param_item_form_wpconfig(
            DUPX_Paramas_Manager::PARAM_WP_CONF_WP_DEBUG_LOG,
            DUPX_Param_item_form::TYPE_BOOL,
            DUPX_Param_item_form::FORM_TYPE_CHECKBOX,
            array(
            'default' => array(
                'value'      => $debugLogValue,
                'inWpConfig' => $archiveConfig->inWpConfigDefine('WP_DEBUG_LOG')
            )
            ), array(
            'label'         => 'WP_DEBUG_LOG:',
            'checkboxLabel' => 'Log errors and warnings',
            )
        );

        $params[DUPX_Paramas_Manager::PARAM_WP_CONF_WP_DISABLE_FATAL_ERROR_HANDLER] = new DUPX_Param_item_form_wpconfig(
            DUPX_Paramas_Manager::PARAM_WP_CONF_WP_DISABLE_FATAL_ERROR_HANDLER,
            DUPX_Param_item_form::TYPE_BOOL,
            DUPX_Param_item_form::FORM_TYPE_CHECKBOX,
            array(
            'default' => $archiveConfig->getDefineArrayValue('WP_DISABLE_FATAL_ERROR_HANDLER')
            ), array(
            'label'         => 'WP_DISABLE_FATAL_ERROR_HANDLER:',
            'checkboxLabel' => 'Disable fatal error handler',
            'status'        => version_compare($archiveConfig->version_wp, '5.2.0', '<') ? DUPX_Param_item_form::STATUS_SKIP : DUPX_Param_item_form::STATUS_ENABLED
            )
        );

        $params[DUPX_Paramas_Manager::PARAM_WP_CONF_WP_DEBUG_DISPLAY] = new DUPX_Param_item_form_wpconfig(
            DUPX_Paramas_Manager::PARAM_WP_CONF_WP_DEBUG_DISPLAY,
            DUPX_Param_item_form::TYPE_BOOL,
            DUPX_Param_item_form::FORM_TYPE_CHECKBOX,
            array(
            'default' => $archiveConfig->getDefineArrayValue('WP_DEBUG_DISPLAY')
            ), array(
            'label'         => 'WP_DEBUG_DISPLAY:',
            'checkboxLabel' => 'Display errors and warnings'
            )
        );

        $params[DUPX_Paramas_Manager::PARAM_WP_CONF_SCRIPT_DEBUG] = new DUPX_Param_item_form_wpconfig(
            DUPX_Paramas_Manager::PARAM_WP_CONF_SCRIPT_DEBUG,
            DUPX_Param_item_form::TYPE_BOOL,
            DUPX_Param_item_form::FORM_TYPE_CHECKBOX,
            array(
            'default' => $archiveConfig->getDefineArrayValue('SCRIPT_DEBUG')
            ), array(
            'label'         => 'SCRIPT_DEBUG:',
            'checkboxLabel' => 'JavaScript or CSS errors'
            )
        );

        $params[DUPX_Paramas_Manager::PARAM_WP_CONF_CONCATENATE_SCRIPTS] = new DUPX_Param_item_form_wpconfig(
            DUPX_Paramas_Manager::PARAM_WP_CONF_CONCATENATE_SCRIPTS,
            DUPX_Param_item_form::TYPE_BOOL,
            DUPX_Param_item_form::FORM_TYPE_CHECKBOX,
            array(
            'default' => $archiveConfig->getDefineArrayValue('CONCATENATE_SCRIPTS', array(
                'value'      => false,
                'inWpConfig' => false
                )
            )
            ), array(
            'label'         => 'CONCATENATE_SCRIPTS:',
            'checkboxLabel' => 'Concatenate all JavaScript files into one URL'
            )
        );

        $params[DUPX_Paramas_Manager::PARAM_WP_CONF_SAVEQUERIES] = new DUPX_Param_item_form_wpconfig(
            DUPX_Paramas_Manager::PARAM_WP_CONF_SAVEQUERIES,
            DUPX_Param_item_form::TYPE_BOOL,
            DUPX_Param_item_form::FORM_TYPE_CHECKBOX,
            array(
            'default' => $archiveConfig->getDefineArrayValue('SAVEQUERIES')
            ), array(
            'label'         => 'SAVEQUERIES:',
            'checkboxLabel' => 'Save database queries in an array ($wpdb->queries)'
            )
        );

        $params[DUPX_Paramas_Manager::PARAM_WP_CONF_ALTERNATE_WP_CRON] = new DUPX_Param_item_form_wpconfig(
            DUPX_Paramas_Manager::PARAM_WP_CONF_ALTERNATE_WP_CRON,
            DUPX_Param_item_form::TYPE_BOOL,
            DUPX_Param_item_form::FORM_TYPE_CHECKBOX,
            array(
            'default' => $archiveConfig->getDefineArrayValue('ALTERNATE_WP_CRON', array(
                'value'      => false,
                'inWpConfig' => false
                )
            )
            ), array(
            'label'         => 'ALTERNATE_WP_CRON:',
            'checkboxLabel' => 'Use an alternative Cron with WP'
            )
        );

        $params[DUPX_Paramas_Manager::PARAM_WP_CONF_DISABLE_WP_CRON] = new DUPX_Param_item_form_wpconfig(
            DUPX_Paramas_Manager::PARAM_WP_CONF_DISABLE_WP_CRON,
            DUPX_Param_item_form::TYPE_BOOL,
            DUPX_Param_item_form::FORM_TYPE_CHECKBOX,
            array(
            'default' => $archiveConfig->getDefineArrayValue('DISABLE_WP_CRON', array(
                'value'      => false,
                'inWpConfig' => false
                )
            )
            ), array(
            'label'         => 'DISABLE_WP_CRON:',
            'checkboxLabel' => 'Disable cron entirely'
            )
        );

        $params[DUPX_Paramas_Manager::PARAM_WP_CONF_WP_CRON_LOCK_TIMEOUT] = new DUPX_Param_item_form_wpconfig(
            DUPX_Paramas_Manager::PARAM_WP_CONF_WP_CRON_LOCK_TIMEOUT,
            DUPX_Param_item_form::TYPE_INT,
            DUPX_Param_item_form::FORM_TYPE_NUMBER,
            array(
            'default'   => $archiveConfig->getDefineArrayValue('WP_CRON_LOCK_TIMEOUT', array(
                'value'      => 60,
                'inWpConfig' => false
                )
            ),
            'min_range' => 1
            ), array(
            'min'            => 1,
            'step'           => 1,
            'label'          => 'WP_CRON_LOCK_TIMEOUT:',
            'wrapperClasses' => array('small'),
            'subNote'        => 'Cron process cannot run more than once every WP_CRON_LOCK_TIMEOUT seconds',
            )
        );

        $params[DUPX_Paramas_Manager::PARAM_WP_CONF_EMPTY_TRASH_DAYS] = new DUPX_Param_item_form_wpconfig(
            DUPX_Paramas_Manager::PARAM_WP_CONF_EMPTY_TRASH_DAYS,
            DUPX_Param_item_form::TYPE_INT,
            DUPX_Param_item_form::FORM_TYPE_NUMBER,
            array(
            'default'   => $archiveConfig->getDefineArrayValue('EMPTY_TRASH_DAYS', array(
                'value'      => 30,
                'inWpConfig' => false
                )
            ),
            'min_range' => 0
            ), array(
            'min'            => 0,
            'step'           => 1,
            'label'          => 'EMPTY_TRASH_DAYS:',
            'wrapperClasses' => array('small'),
            'subNote'        => 'How many days deleted post should be kept in trash before being deleted permanently',
            )
        );

        $params[DUPX_Paramas_Manager::PARAM_WP_CONF_COOKIE_DOMAIN] = new DUPX_Param_item_form_wpconfig(
            DUPX_Paramas_Manager::PARAM_WP_CONF_COOKIE_DOMAIN,
            DUPX_Param_item_form::TYPE_STRING,
            DUPX_Param_item_form::FORM_TYPE_TEXT,
            array(// ITEM ATTRIBUTES
            'default'          => $archiveConfig->getDefineArrayValue("COOKIE_DOMAIN"),
            'sanitizeCallback' => array('DupProSnapLibUtil', 'sanitize_non_stamp_chars_newline_and_trim')
            ), array(// FORM ATTRIBUTES
            'label'   => 'COOKIE_DOMAIN:',
            'subNote' => 'Set <a href="http://www.askapache.com/htaccess/apache-speed-subdomains.html" target="_blank">different domain</a> for cookies.subdomain.example.com'
            )
        );

        $params[DUPX_Paramas_Manager::PARAM_WP_CONF_WP_MEMORY_LIMIT] = new DUPX_Param_item_form_wpconfig(
            DUPX_Paramas_Manager::PARAM_WP_CONF_WP_MEMORY_LIMIT,
            DUPX_Param_item_form::TYPE_STRING,
            DUPX_Param_item_form::FORM_TYPE_TEXT,
            array(// ITEM ATTRIBUTES
            'default'          => $archiveConfig->getDefineArrayValue('WP_MEMORY_LIMIT'),
            'sanitizeCallback' => array('DupProSnapLibUtil', 'sanitize_non_stamp_chars_newline_and_trim'),
            'validateRegex'    => DUPX_Param_item::VALIDATE_REGEX_AZ_NUMBER
            ), array(// FORM ATTRIBUTES
            'label'          => 'WP_MEMORY_LIMIT:',
            'wrapperClasses' => array('small'),
            'subNote'        => 'PHP memory limit (default:30M; Multisite default:64M)'
            )
        );

        $params[DUPX_Paramas_Manager::PARAM_WP_CONF_WP_MAX_MEMORY_LIMIT] = new DUPX_Param_item_form_wpconfig(
            DUPX_Paramas_Manager::PARAM_WP_CONF_WP_MAX_MEMORY_LIMIT,
            DUPX_Param_item_form::TYPE_STRING,
            DUPX_Param_item_form::FORM_TYPE_TEXT,
            array(// ITEM ATTRIBUTES
            'default'          => $archiveConfig->getDefineArrayValue('WP_MAX_MEMORY_LIMIT'),
            'sanitizeCallback' => array('DupProSnapLibUtil', 'sanitize_non_stamp_chars_newline_and_trim'),
            'validateRegex'    => DUPX_Param_item::VALIDATE_REGEX_AZ_NUMBER
            ), array(// FORM ATTRIBUTES
            'label'          => 'WP_MAX_MEMORY_LIMIT:',
            'wrapperClasses' => array('small'),
            'subNote'        => 'Wordpress admin maximum memory limit (default:256M)'
            )
        );
    }

    /**
     *
     * @param DUPX_Param_item[] $params
     * @throws Exception
     */
    public static function updateParamsAfterOverwrite(&$params)
    {
        //UPDATE PATHS AUTOMATICALLY
        self::setDefaultWpConfigPathValue($params, DUPX_Paramas_Manager::PARAM_WP_CONF_WP_TEMP_DIR, 'WP_TEMP_DIR');
        self::setDefaultWpConfigPathValue($params, DUPX_Paramas_Manager::PARAM_WP_CONF_WPCACHEHOME, 'WPCACHEHOME');
        self::wpConfigPathsNotices();

        //UPDATE DOMAINS AUTOMATICALLY
        self::setDefaultWpConfigDomainValue($params, DUPX_Paramas_Manager::PARAM_WP_CONF_COOKIE_DOMAIN, "COOKIE_DOMAIN");
        self::wpConfigDomainNotices();
    }

    /**
     * Tries to replace the old path with the new path for the given wp config define.
     * If that's not possible returns a notice to the user.
     *
     * @param DUPX_Param_item[] $params
     * @param string $paramKey
     * @param string $wpConfigKey
     * @return void
     * @throws Exception
     */
    protected static function setDefaultWpConfigPathValue(&$params, $paramKey, $wpConfigKey)
    {
        if (!self::wpConfigNeedsUpdate($params, $paramKey, $wpConfigKey)) {
            return;
        }

        $oldMainPath = $params[DUPX_Paramas_Manager::PARAM_PATH_OLD]->getValue();
        $newMainPath = $params[DUPX_Paramas_Manager::PARAM_PATH_NEW]->getValue();
        $wpConfigVal = DUPX_ArchiveConfig::getInstance()->getDefineArrayValue($wpConfigKey);

        // TRY TO CHANGE THE VALUE OR RESET
        if (($wpConfigVal['value'] = DUPX_ArchiveConfig::getNewSubString($oldMainPath, $newMainPath, $wpConfigVal['value'])) === false) {
            $wpConfigVal['inWpConfig'] = false;
            $wpConfigVal['value']      = '';

            DUPX_NOTICE_MANAGER::getInstance()->addNextStepNotice(array(
                'shortMsg'    => 'WP CONFIG custom paths disabled.',
                'level'       => DUPX_NOTICE_ITEM::NOTICE,
                'longMsg'     => "The " . $params[$paramKey]->getLabel() . " path could not be set programmatically and has been disabled<br>\n",
                'longMsgMode' => DUPX_NOTICE_ITEM::MSG_MODE_HTML
                ), DUPX_NOTICE_MANAGER::ADD_UNIQUE_APPEND, self::NOTICE_ID_WP_CONF_PARAM_PATHS_EMPTY);
        }

        $params[$paramKey]->setValue($wpConfigVal);
    }

    /**
     * Tries to replace the old domain with the new domain for the given wp config define.
     * If that's not possible returns a notice to the user.
     *
     * @param DUPX_Param_item[] $params
     * @param string $paramKey
     * @param string $wpConfigKey
     * @throws Exception
     */
    protected static function setDefaultWpConfigDomainValue(&$params, $paramKey, $wpConfigKey)
    {
        if (!self::wpConfigNeedsUpdate($params, $paramKey, $wpConfigKey)) {
            return;
        }

        $wpConfigVal  = DUPX_ArchiveConfig::getInstance()->getDefineArrayValue($wpConfigKey);
        $parsedUrlNew = parse_url(DUPX_Paramas_Manager::getInstance()->getValue(DUPX_Paramas_Manager::PARAM_URL_NEW));
        $parsedUrlOld = parse_url(DUPX_Paramas_Manager::getInstance()->getValue(DUPX_Paramas_Manager::PARAM_URL_OLD));

        if ($wpConfigVal['value'] == $parsedUrlOld['host']) {
            $wpConfigVal['value'] = $parsedUrlNew['host'];
        } else {
            $wpConfigVal['inWpConfig'] = false;
            $wpConfigVal['value']      = '';

            DUPX_NOTICE_MANAGER::getInstance()->addNextStepNotice(array(
                'shortMsg'    => 'WP CONFIG domains disabled.',
                'level'       => DUPX_NOTICE_ITEM::NOTICE,
                'longMsg'     => "The " . $params[$paramKey]->getLabel() . " domain could not be set programmatically and has been disabled<br>\n",
                'longMsgMode' => DUPX_NOTICE_ITEM::MSG_MODE_HTML
                ), DUPX_NOTICE_MANAGER::ADD_UNIQUE_APPEND, self::NOTICE_ID_WP_CONF_PARAM_DOMAINS_MODIFIED);
        }

        $params[$paramKey]->setValue($wpConfigVal);
    }

    protected static function wpConfigNeedsUpdate(&$params, $paramKey, $wpConfigKey)
    {
        if ($params[DUPX_Paramas_Manager::PARAM_RESTORE_BACKUP_MODE]->getValue()) {
            return false;
        }

        // SKIP IF PARAM IS OVERWRITTEN
        if ($params[$paramKey]->getStatus() === DUPX_Param_item::STATUS_OVERWRITE) {
            return false;
        }

        // SKIP IF EMPTY
        $wpConfigVal = DUPX_ArchiveConfig::getInstance()->getDefineArrayValue($wpConfigKey);
        if (strlen($wpConfigVal['value']) === 0) {
            return false;
        }

        // EMPTY IF DISABLED
        if ($wpConfigVal['inWpConfig'] == false) {
            $wpConfigVal['value'] = '';
            $params[$paramKey]->setValue($wpConfigVal);
            return false;
        }

        return true;
    }

    protected static function wpConfigPathsNotices()
    {
        $noticeManager = DUPX_NOTICE_MANAGER::getInstance();

        /** PREPEND IF EXISTS */
        $noticeManager->addNextStepNotice(array(
            'shortMsg'    => '',
            'level'       => DUPX_NOTICE_ITEM::NOTICE,
            'longMsg'     => "It was found that the following config paths were outside of the source site's home path (" .
            DUPX_ArchiveConfig::getInstance()->getRealValue("originalPaths")->home . "):<br><br>\n",
            'longMsgMode' => DUPX_NOTICE_ITEM::MSG_MODE_HTML
            ), DUPX_NOTICE_MANAGER::ADD_UNIQUE_PREPEND_IF_EXISTS, self::NOTICE_ID_WP_CONF_PARAM_PATHS_EMPTY);

        /** APPEND IF EXISTS */
        $msg = '<br>Keeping config paths that are outside of the home path may cause malfunctions, so these settings have been disabled by default,';
        $msg .= ' but you can set them manually if necessary by switching the install mode to "Advanced" and at Step 3 navigating to "Options" &gt; "WP-Config File"';

        $noticeManager->addNextStepNotice(array(
            'shortMsg'    => '',
            'level'       => DUPX_NOTICE_ITEM::NOTICE,
            'longMsg'     => $msg,
            'longMsgMode' => DUPX_NOTICE_ITEM::MSG_MODE_HTML
            ), DUPX_NOTICE_MANAGER::ADD_UNIQUE_APPEND_IF_EXISTS, self::NOTICE_ID_WP_CONF_PARAM_PATHS_EMPTY);

        $noticeManager->saveNotices();
    }

    protected static function wpConfigDomainNotices()
    {
        $noticeManager = DUPX_NOTICE_MANAGER::getInstance();

        /** PREPEND IF EXISTS */
        $noticeManager->addNextStepNotice(array(
            'shortMsg'    => '',
            'level'       => DUPX_NOTICE_ITEM::NOTICE,
            'longMsg'     => "The following config domains were disabled:<br><br>\n",
            'longMsgMode' => DUPX_NOTICE_ITEM::MSG_MODE_HTML
            ), DUPX_NOTICE_MANAGER::ADD_UNIQUE_PREPEND_IF_EXISTS, self::NOTICE_ID_WP_CONF_PARAM_DOMAINS_MODIFIED);

        /** APPEND IF EXISTS */
        $msg = '<br>The plugin was unable to automatically replace the domain, so the setting has been disabled by default.';
        $msg .= ' Please review them by switching the install mode to "Advanced" and at Step 3 navigating to "Options" &gt; "WP-Config File"';

        $noticeManager->addNextStepNotice(array(
            'shortMsg'    => '',
            'level'       => DUPX_NOTICE_ITEM::NOTICE,
            'longMsg'     => $msg,
            'longMsgMode' => DUPX_NOTICE_ITEM::MSG_MODE_HTML
            ), DUPX_NOTICE_MANAGER::ADD_UNIQUE_APPEND_IF_EXISTS, self::NOTICE_ID_WP_CONF_PARAM_DOMAINS_MODIFIED);

        $noticeManager->saveNotices();
    }
}
