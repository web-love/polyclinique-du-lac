<?php

/**
 * Class that collects the functions of initial checks on the requirements to run the plugin
 *
 * Standard: PSR-2
 * @link http://www.php-fig.org/psr/psr-2
 *
 * @package Duplicator
 * @copyright (c) 2021, Snapcreek LLC
 *
 */

namespace Duplicator\Pro;

defined('ABSPATH') || exit;

class Requirements implements \Duplicator\Core\RequirementsInterface
{
    const DUP_LITE_PLUGIN_KEY = 'duplicator/duplicator.php';

    /**
     *
     * @var string // curent plugin file full path
     */
    protected static $pluginFile = '';

    /**
     *
     * @var string // message on deactivation
     */
    protected static $deactivationMessage = '';

    /**
     * This function checks the requirements to run Duplicator.
     * At this point wordpress is not yet completely initialized so functionality is limited.
     * It need to hook into "admin_init" to get the full functionality of wordpress.
     *
     * @param string $pluginFile    // main plugin file path
     * @return boolean              // true if plugin can be exectued
     */
    public static function canRun($pluginFile)
    {
        $result           = true;
        self::$pluginFile = $pluginFile;

        if (self::isPluginActive(self::DUP_LITE_PLUGIN_KEY)) {
            /* Deactivation of the plugin disabled in favor of a notification for the next version
             * Uncomment this to enable the logic.
             *
              add_action('admin_init', array(__CLASS__, 'addLiteEnableNotice'));
              self::$deactivationMessage = 'Can\'t enable Duplicator PRO if the LITE version is enabled';

              $result = false;
             */
        }

        if ($result === false) {
            register_activation_hook($pluginFile, array(__CLASS__, 'deactivateOnActivation'));
        }

        return $result;
    }

    public static function afterLoadedChecks()
    {
        // empty
    }

    /**
     *
     * @param string $plugin
     * @return boolean // return strue if plugin key is active and plugin file exists
     */
    protected static function isPluginActive($plugin)
    {
        $isActive = false;
        if (in_array($plugin, (array) get_option('active_plugins', array()))) {
            $isActive = true;
        }

        if (is_multisite()) {
            $plugins = get_site_option('active_sitewide_plugins');
            if (isset($plugins[$plugin])) {
                $isActive = true;
            }
        }

        return ($isActive && file_exists(WP_PLUGIN_DIR . '/' . $plugin));
    }

    /**
     * display admin notice only if user can manage plugins.
     */
    public static function addLiteEnableNotice()
    {
        if (current_user_can('activate_plugins')) {
            add_action('admin_notices', array(__CLASS__, 'liteEnabledNotice'));
        }
    }

    /**
     * deactivate current plugin on activation
     */
    public static function deactivateOnActivation()
    {
        deactivate_plugins(plugin_basename(self::$pluginFile));
        wp_die(self::$deactivationMessage);
    }

    /**
     * diplay admin notice if duplicator pro is enabled
     */
    public static function liteEnabledNotice()
    {
        ?>
        <div class="error notice">
            <p>
                <?php echo 'DUPLICATOR PRO: ' . __('Duplicator PRO cannot be work if Duplicator LITE is active.', 'duplicator-pro'); ?>
            </p>
            <p>
                <?php echo __('If you want to use Duplicator PRO you must first deactivate the LITE version', 'duplicator-pro'); ?><br>
                <b>
                    <?php echo __('Please disable the LITE version if it is not needed.', 'duplicator-pro'); ?>
                </b>
            </p>
        </div>
        <?php
    }
}
