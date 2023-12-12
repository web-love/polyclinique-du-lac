<?php

/**
 * Version Pro Base functionalities
 *
 * Name: Duplicator PRO base
 * Version: 1
 * Author: Snap Creek
 * Author URI: http://snapcreek.com
 *
 * @package Duplicator
 * @copyright (c) 2021, Snapcreek LLC
 *
 */

namespace Duplicator\Addons\ProBase;

defined('ABSPATH') || exit;

class ProBase extends \Duplicator\Core\Addons\AbstractAddonCore
{

    public function init()
    {
        // empty
    }

    public function canEnable()
    {
        return false;
    }

    public static function getAddonFile()
    {
        return __FILE__;
    }
}
