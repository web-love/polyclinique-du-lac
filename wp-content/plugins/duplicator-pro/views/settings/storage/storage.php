<?php
defined("ABSPATH") or die("");

DUP_PRO_CTRL_Storage_Setting::doMessages();
require('subtabs.php');
require('inc.'.DUP_PRO_CTRL_Storage_Setting::getCurrentSubTab().'.php');
