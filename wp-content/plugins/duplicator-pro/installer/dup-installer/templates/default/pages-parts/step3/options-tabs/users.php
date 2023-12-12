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
    <?php DUPX_View_Funcs::helpIconLink('step3'); ?>
</div>
<?php
if ($paramsManager->getValue(DUPX_Paramas_Manager::PARAM_RESTORE_BACKUP_MODE)) {
    ?>
    <div class="hdr-sub3">User settings</div>
    <?php
    dupxTplRender('parts/restore-backup-mode-notice');
} else {
    dupxTplRender('pages-parts/step3/usersParts/usersPwdReset');
    dupxTplRender('pages-parts/step3/usersParts/newAdminUser');
} 
