<?php
defined("ABSPATH") or die("");

/**
 * Used to display notices in the WordPress Admin area
 * This class takes advantage of the 'admin_notice' action.
 *
 * Standard: PSR-2
 * @link http://www.php-fig.org/psr/psr-2
 *
 * @package DUP_PRO
 * @subpackage classes/ui
 * @copyright (c) 2017, Snapcreek LLC
 * @license	https://opensource.org/licenses/GPL-3.0 GNU Public License
 *
 */
class DUP_PRO_UI_Notice
{
    const OPTION_KEY_INSTALLER_HASH_NOTICE               = 'duplicator_pro_inst_hash_notice';
    const OPTION_KEY_ACTIVATE_PLUGINS_AFTER_INSTALL      = 'duplicator_pro_activate_plugins_after_installation';
    const OPTION_KEY_MIGRATION_SUCCESS_NOTICE            = 'duplicator_pro_migration_success';
    const OPTION_KEY_EXPIRED_LICENCE_NOTICE_DISMISS_TIME = 'duplicator_pro_expired_licence_notice_time';
    const EXPIRED_LICENCE_NOTICE_DISMISS_FOR_DAYS        = 14;
    const GEN_INFO_NOTICE                                = 0;
    const GEN_SUCCESS_NOTICE                             = 1;
    const GEN_WARNING_NOTICE                             = 2;
    const GEN_ERROR_NOTICE                               = 3;

    /**
     * init notice actions
     */
    public static function init()
    {
        add_action('admin_init', array(__CLASS__, 'adminInit'));
    }

    public static function adminInit()
    {
        $notices = array();

        if (is_multisite()) {
            $noCapabilitiesNotice = is_super_admin() && !current_user_can('export');
        } else {
            $noCapabilitiesNotice = in_array('administrator', $GLOBALS['current_user']->roles) && !current_user_can('export');
        }

        if ($noCapabilitiesNotice) {
            $notices[] = array(__CLASS__, 'showNoExportCapabilityNotice');
        }

        if (is_multisite()) {
            $displayNotices = is_super_admin() && current_user_can('export');
        } else {
            $displayNotices = current_user_can('export');
        }

        if ($displayNotices) {
            $notices[] = array(__CLASS__, 'newInstallerHashOption');
            $notices[] = array(__CLASS__, 'clearInstallerFilesAction'); // BEFORE MIGRATION SUCCESS NOTICE
            $notices[] = array(__CLASS__, 'migrationSuccessNotice');
            $notices[] = array('DUP_PRO_UI_Alert', 'licenseAlertCheck');
            $notices[] = array('DUP_PRO_UI_Alert', 'activatePluginsAfterInstall');
            $notices[] = array('DUP_PRO_UI_Alert', 'failedScheduleCheck');
        }

        $action = is_multisite() ? 'network_admin_notices' : 'admin_notices';
        foreach ($notices as $notice) {
            add_action($action, $notice);
        }
    }

    public static function newInstallerHashOption()
    {
        if (get_option(self::OPTION_KEY_INSTALLER_HASH_NOTICE) != true) {
            return;
        }

        $screen = get_current_screen();
        if (!in_array($screen->parent_base, array('plugins', 'duplicator-pro'))) {
            return;
        }

        $action        = filter_input(INPUT_POST, 'action', FILTER_SANITIZE_STRING);
        $installerMode = filter_input(INPUT_POST, 'installer_name_mode', FILTER_SANITIZE_STRING);
        if ($screen->id == 'duplicator-pro_page_duplicator-pro-settings' && $action == 'save' && $installerMode == DUP_PRO_Global_Entity::INSTALLER_NAME_MODE_WITH_HASH) {
            delete_option(self::OPTION_KEY_INSTALLER_HASH_NOTICE);
            return;
        }

        if (DUP_PRO_Global_Entity::get_instance()->installer_name_mode == DUP_PRO_Global_Entity::INSTALLER_NAME_MODE_WITH_HASH) {
            delete_option(self::OPTION_KEY_INSTALLER_HASH_NOTICE);
            return;
        }
        ?>
        <div class="dup-notice-success notice notice-success duplicator-pro-admin-notice is-dismissible" data-to-dismiss="<?php echo esc_attr(self::OPTION_KEY_INSTALLER_HASH_NOTICE); ?>" >
            <p>
                <?php DUP_PRO_U::esc_html_e('Duplicator PRO now includes a new option that helps secure the installer.php file.'); ?><br>
                <?php DUP_PRO_U::esc_html_e('After this option is enabled, a security hash will be added to the name of the installer when it\'s downloaded.'); ?>
            </p>
            <p>
                <?php
                echo sprintf(
                    DUP_PRO_U::__('To enable this option or to get more information, open the <a href="%s">Package Settings</a> and visit the Installer section.'),
                                  'admin.php?page=duplicator-pro-settings&tab=package#duplicator-pro-installer-settings');
                ?>
            </p>
        </div>
        <?php
    }

    /**
     * 
     * @return void
     */
    public static function clearInstallerFilesAction()
    {

        if (!DUP_PRO_CTRL_Tools::isDiagnosticPage() || get_option(self::OPTION_KEY_MIGRATION_SUCCESS_NOTICE) == true) {
            return;
        }

        if (DupProSnapLibUtil::filterInputRequest('action', FILTER_DEFAULT) === 'installer') {
            ?>
            <div id="message" class="notice notice-success">
                <?php require DUPLICATOR_PRO_PLUGIN_PATH . '/views/parts/migration-clean-installation-files.php'; ?>
            </div>
            <?php
        }
    }

    /**
     * Shows a display message in the wp-admin if any reserved files are found
     *
     * @return void
     */
    public static function migrationSuccessNotice()
    {
        if (get_option(self::OPTION_KEY_MIGRATION_SUCCESS_NOTICE) != true) {
            return;
        }

        if (DUP_PRO_CTRL_Tools::isDiagnosticPage()) {
            require DUPLICATOR_PRO_PLUGIN_PATH . '/views/parts/migration-message.php';
        } else {
            require DUPLICATOR_PRO_PLUGIN_PATH . '/views/parts/migration-almost-complete.php';
        }
    }

    /**
     * Shows a display message in the wp-admin if the logged in user role has not export capability
     *
     * @return void
     */
    public static function showNoExportCapabilityNotice()
    {
        $errorMessage = DUP_PRO_U::__('<strong>Duplicator Pro</strong><hr> Your logged-in user role does not have export capability so you don\'t have access to Duplicator Pro functionality.') .
            "<br>" .
            sprintf(DUP_PRO_U::__('<strong>RECOMMENDATION:</strong> Add export capability to your role. See FAQ: <a target="_blank" href="%s">%s</a>'), 'https://snapcreek.com/duplicator/docs/faqs-tech/#faq-licensing-040-q', DUP_PRO_U::__('Why is the Duplicator/Packages menu missing from my admin menu?'));
        DUP_PRO_UI_Notice::displayGeneralAdminNotice($errorMessage, DUP_PRO_UI_Notice::GEN_ERROR_NOTICE, true);
    }

    /**
     * display genral admin notice by printing it
     *
     * @param string $htmlMsg html code to be printed
     * @param integer $noticeType constant value of SELF::GEN_
     * @param boolean $isDismissible whether the notice is dismissable or not. Default is true 
     * @param array|string $extraClasses add more classes to the notice div
     * @param array|string $extraAtts assosiate array in which key as attr and value as value of the attr
     * @return void
     */
    public static function displayGeneralAdminNotice($htmlMsg, $noticeType, $isDismissible = true, $extraClasses = array(), $extraAtts = array())
    {
        if (empty($extraClasses)) {
            $classes = array();
        } elseif (is_array($extraClasses)) {
            $classes = $extraClasses;
        } else {
            $classes = array($extraClasses);
        }

        $classes[] = 'notice';

        switch ($noticeType) {
            case self::GEN_INFO_NOTICE:
                $classes[] = 'notice-info';
                break;
            case self::GEN_SUCCESS_NOTICE:
                $classes[] = 'notice-success';
                break;
            case self::GEN_WARNING_NOTICE:
                $classes[] = 'notice-warning';
                break;
            case self::GEN_ERROR_NOTICE:
                $classes[] = 'notice-error';
                break;
            default:
                throw new Exception('Invalid Admin notice type!');
        }

        if ($isDismissible) {
            $classes[] = 'is-dismissible';
        }

        $classesStr = implode(' ', $classes);

        $attsStr = '';
        if (!empty($extraAtts)) {
            $attsStrArr = array();
            foreach ($extraAtts as $att => $attVal) {
                $attsStrArr[] = $att . '="' . $attVal . '"';
            }
            $attsStr = implode(' ', $attsStrArr);
        }
        ?>
        <div class="<?php echo esc_attr($classesStr); ?>" <?php echo $attsStr; ?>>
            <p>
                <?php
                if (self::GEN_ERROR_NOTICE == $noticeType) {
                    ?>
                    <i class='fa fa-exclamation-triangle'></i>
                    <?php
                }
                ?>
                <?php
                echo $htmlMsg;
                ?>
            </p>
        </div>
        <?php
    }
}
