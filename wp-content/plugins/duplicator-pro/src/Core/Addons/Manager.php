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

namespace Duplicator\Core\Addons;

defined('ABSPATH') || exit;

final class Manager
{
    /**
     *
     * @var self
     */
    private static $instance = null;

    /**
     *
     * @var [AbstractAddonCore]
     */
    private $addons = array();

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

    /**
     * inizialize addons
     */
    private function __construct()
    {
        $this->addons = self::getAddonListFromFolder();
    }

    public function inizializeAddons()
    {
        foreach ($this->addons as $addon) {
            if ($addon->canEnable() && $addon->hasDependencies()) {
                $addon->init();
            }
        }

        do_action('duplicator_addons_loaded');
    }

    public function getAvaiableAddons()
    {
        $result = array();
        foreach ($this->addons as $addon) {
            $result[] = $addon->getSlug();
        }

        return $result;
    }

    /**
     * return addons folder
     *
     * @return string
     */
    public static function getAddonsPath()
    {
        return DUPLICATOR____PATH . '/addons';
    }

    /**
     *
     * @return [AbstractAddonCore]
     */
    private static function getAddonListFromFolder()
    {
        $addonList = array();

        $checkDir = trailingslashit(self::getAddonsPath());

        if (!is_dir($checkDir)) {
            return;
        }

        if (($dh = opendir($checkDir)) == false) {
            return;
        }

        while (($elem = readdir($dh)) !== false) {
            if ($elem === '.' || $elem === '..') {
                continue;
            }

            $fullPath = $checkDir . $elem;

            if (!is_dir($fullPath)) {
                continue;
            }

            $addonMainFile = $checkDir . $elem . '/' . $elem . '.php';
            if (!file_exists($addonMainFile)) {
                continue;
            }

            try {
                if (!is_subclass_of('\\Duplicator\\Addons\\Test\\' . $elem, 'Duplicator\\Core\\Addons\\AbstractAddonCore')) {
                    \DUP_PRO_Log::trace('Addon main file ' . $addonMainFile . ' don\'t contain a main class that extend AbstractAddonCore');
                    continue;
                }
            } catch (\Exception $e) {
                \DUP_PRO_Log::trace('Addon file ' . $addonMainFile . ' exists but not countain addon main core class');
                continue;
            } catch (\Error $e) {
                \DUP_PRO_Log::trace('Addon file ' . $addonMainFile . ' exists but generate an error');
                continue;
            }

            $addonClass                      = '\\Duplicator\\Addons\\Test\\' . $elem;
            $addonObj                        = $addonClass::getInstance();
            $addonList[$addonObj->getSlug()] = $addonObj;
        }
        closedir($dh);

        return $addonList;
    }
}
