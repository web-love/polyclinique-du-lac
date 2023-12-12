<?php

/**
 * Interface that collects the functions of initial checks on the requirements to run the plugin
 *
 * Standard: PSR-2
 * @link http://www.php-fig.org/psr/psr-2
 *
 * @package Duplicator
 * @copyright (c) 2021, Snapcreek LLC
 *
 */

namespace Duplicator\Core;

interface RequirementsInterface
{

    public static function canRun($pluginFile);

    public static function afterLoadedChecks();
}
