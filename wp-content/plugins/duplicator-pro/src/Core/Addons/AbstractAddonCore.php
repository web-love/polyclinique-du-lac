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

abstract class AbstractAddonCore
{
    const ADDON_DATA_CONTEXT = 'duplicator_addon';

    /**
     *
     * @var [self]
     */
    private static $instances = array();

    /**
     *
     * @var array
     */
    protected $addonData = array();

    /**
     *
     * @return self
     */
    public static function getInstance()
    {
        $class = get_called_class();
        if (!isset(self::$instances[$class])) {
            self::$instances[$class] = new static();
        }

        return self::$instances[$class];
    }

    protected function __construct()
    {
        $reflect         = new \ReflectionClass(get_called_class());
        $this->addonData = self::getInitAddonData($reflect->getShortName());
    }

    abstract public function init();

    /**
     *
     * @throws Exception
     */
    public static function getAddonFile()
    {
        // To prevent the warning about static abstract functions that appears in PHP 5.4/5.6 I use this trick.
        throw new Exception('this function have to overwritte on child class');
    }

    /**
     *
     * @return string
     */
    public function getSlug()
    {
        return $this->addonData['slug'];
    }

    /**
     *
     * @return boolean
     */
    public function canEnable()
    {
        if (version_compare(PHP_VERSION, $this->addonData['requiresPHP'], '<')) {
            return false;
        }

        global $wp_version;
        if (version_compare($wp_version, $this->addonData['requiresWP'], '<')) {
            return false;
        }

        if (version_compare(DUPLICATOR_PRO_VERSION, $this->addonData['requiresDuplcator'], '<')) {
            return false;
        }

        return true;
    }

    public function hasDependencies()
    {
        $avaliableAddons = Manager::getInstance()->getAvaiableAddons();
        return !array_diff($this->addonData['requiresAddons'], $avaliableAddons);
    }

    public static function getAddonData()
    {
        return $this->addonData;
    }

    protected static function getInitAddonData($class)
    {
        $data          = get_file_data(static::getAddonFile(), self::getDefaltHeaders(), self::ADDON_DATA_CONTEXT);
        $getDefaultVal = self::getDefaultHeadersValues();

        foreach ($data as $key => $val) {
            if (strlen($val) === 0) {
                $data[$key] = $getDefaultVal[$key];
            }
        }

        if (!is_array($data['requiresAddons'])) {
            $data['requiresAddons'] = explode(',', $data['requiresAddons']);
        }
        $data['requiresAddons'] = array_map('trim', $data['requiresAddons']);

        $data['slug'] = $class;
        if (strlen($data['name']) === 0) {
            $data['name'] = $data['slug'];
        }
        return $data;
    }

    protected static function getDefaultHeadersValues()
    {
        static $defaultHeaders = null;
        if (is_null($defaultHeaders)) {
            $defaultHeaders = array(
                'name'              => '',
                'addonURI'          => '',
                'version'           => '0',
                'description'       => '',
                'author'            => '',
                'authorURI'         => '',
                'requiresWP'        => '4.0',
                'requiresPHP'       => '5.3',
                'requiresDuplcator' => '4.0.2',
                'requiresAddons'    => array()
            );
        }
        return $defaultHeaders;
    }

    protected static function getDefaltHeaders()
    {
        return array(
            'name'              => 'Name',
            'addonURI'          => 'Addon URI',
            'version'           => 'Version',
            'description'       => 'Description',
            'author'            => 'Author',
            'authorURI'         => 'Author URI',
            'requiresWP'        => 'Requires WP min version',
            'requiresPHP'       => 'Requires PHP',
            'requiresDuplcator' => 'Requires Duplicator min version',
            'requiresAddons'    => 'Requires addons'
        );
    }
}
