<?php
/**
 *
 * @package templates/default
 *
 */
defined('ABSPATH') || defined('DUPXABSPATH') || exit;

$paramsManager = DUPX_Paramas_Manager::getInstance();
?>
<div class="hdr-sub3">Database Settings</div>
<div class="help-target">
    <?php // DUPX_View_Funcs::helpIconLink('step2'); ?>
</div>
<?php

if (DUPX_Custom_Host_Manager::getInstance()->isManaged()) {
    $paramsManager->setFormNote(DUPX_Paramas_Manager::PARAM_DB_TABLE_PREFIX, 'The table prefix must be set according to the managed hosting where you install the site.');
}
$paramsManager->getHtmlFormParam(DUPX_Paramas_Manager::PARAM_DB_TABLE_PREFIX);