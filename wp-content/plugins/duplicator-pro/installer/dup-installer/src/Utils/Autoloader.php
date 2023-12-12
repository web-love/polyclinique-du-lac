<?php

/**
 * Auloader calsses
 *
 * Standard: PSR-2
 * @link http://www.php-fig.org/psr/psr-2
 *
 * @package Duplicator
 * @copyright (c) 2021, Snapcreek LLC
 *
 */

namespace Duplicator\Utils;

defined('ABSPATH') || exit;

final class Autoloader
{
    const ROOT_NAMESPACE = 'Duplicator\\';

    protected static $nameSpacesMapping = null;

    /**
     *
     */
    public static function register()
    {
        spl_autoload_register(array(__CLASS__, 'load'));
    }

    /**
     * 
     * @param string $className
     * @return boolean
     */
    public static function load($className)
    {
        if (strpos($className, self::ROOT_NAMESPACE) !== 0) {
            return;
        }

        foreach (self::getNamespacesMapping() as $namespace => $mappedPath) {
            if (strpos($className, $namespace) !== 0) {
                continue;
            }

            $filepath = $mappedPath . str_replace('\\', '/', substr($className, $namespace)) . '.php';

            if (file_exists($filepath)) {
                include_once($filepath);
                return true;
            }
        }

        return false;
    }

    /**
     * 
     * @staticvar [string] $mapping
     * @return [string]
     */
    protected static function getNamespacesMapping()
    {
        // the order is important, it is necessary to insert the longest namespaces first
        static $mapping = array(
            'Duplicator\\Addons\\' => DUPLICATOR____PATH . '/addons/',
            'Duplicator\\'         => DUPLICATOR____PATH . '/src/'
        );
        return $mapping;
    }
}