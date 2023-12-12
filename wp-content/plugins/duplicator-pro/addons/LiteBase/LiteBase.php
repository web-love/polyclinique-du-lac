<?php

/**
 * Version Lite Base functionalities
 *
 * Name: Duplicator LITE base
 * Version: 1
 * Author: Snap Creek
 * Author URI: http://snapcreek.com
 *
 * @package Duplicator
 * @copyright (c) 2021, Snapcreek LLC
 *
 */

namespace Duplicator\Addons\LiteBase;

defined('ABSPATH') || exit;

class LiteBase extends \Duplicator\Core\Addons\AbstractAddonCore
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
