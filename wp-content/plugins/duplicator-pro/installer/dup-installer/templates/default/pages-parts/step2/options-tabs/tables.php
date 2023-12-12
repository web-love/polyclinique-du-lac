<?php
/**
 *
 * @package templates/default
 *
 */
defined('ABSPATH') || defined('DUPXABSPATH') || exit;

$paramsManager = DUPX_Paramas_Manager::getInstance();
?>
<div class="help-target">
    <?php DUPX_View_Funcs::helpIconLink('step2'); ?>
</div>
<div  class="dupx-opts">
    <?php
    if ($paramsManager->getValue(DUPX_Paramas_Manager::PARAM_RESTORE_BACKUP_MODE)) {
        dupxTplRender('parts/restore-backup-mode-notice');
    }
    ?>
    <div class="hdr-sub3">Extract and replace option tables</div> 
    <?php $paramsManager->getHtmlFormParam(DUPX_Paramas_Manager::PARAM_DB_TABLES); ?>
</div>