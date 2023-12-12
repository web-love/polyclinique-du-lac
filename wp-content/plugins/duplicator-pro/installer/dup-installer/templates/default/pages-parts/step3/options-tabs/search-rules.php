<?php
/**
 *
 * @package templates/default
 *
 */
defined('ABSPATH') || defined('DUPXABSPATH') || exit;

$paramsManager = DUPX_Paramas_Manager::getInstance();
if ($paramsManager->getValue(DUPX_Paramas_Manager::PARAM_RESTORE_BACKUP_MODE)) {
    ?>
    <div class="hdr-sub3">Search & replace settings</div>
    <?php
    dupxTplRender('parts/restore-backup-mode-notice');
    return;
}
?>
<div class="help-target">
    <?php DUPX_View_Funcs::helpIconLink('step3'); ?>
</div>

<?php if (DUPX_MU::newSiteIsMultisite()) { ?>
    <div class="margin-bottom-2" >
        <div class="hdr-sub3">Network replace</div>  
        <?php
        $paramsManager->getHtmlFormParam(DUPX_Paramas_Manager::PARAM_REPLACE_MODE);
        dupxTplRender('pages-parts/step3/urls-mapping');
        ?>
    </div>
<?php } ?>

<div class="hdr-sub3">Custom Search &amp; Replace</div>
<table class="s3-opts" id="search-replace-table">
    <tr valign="top" id="search-0">
        <td>Search:</td>
        <td><input class="w95" type="text" name="search[]" style="margin-right:5px"></td>
    </tr>
    <tr valign="top" id="replace-0"><td>Replace:</td><td><input class="w95" type="text" name="replace[]"></td></tr>
</table>
<button type="button" onclick="DUPX.addSearchReplace();return false;" style="font-size:12px;display: block; margin: 10px 0 0 0; " class="default-btn">Add More</button>

<div class="hdr-sub3 margin-top-2">Database Scan Options</div>
<div  class="dupx-opts">
    <?php
    $paramsManager->getHtmlFormParam(DUPX_Paramas_Manager::PARAM_EMPTY_SCHEDULE_STORAGE);
    $paramsManager->getHtmlFormParam(DUPX_Paramas_Manager::PARAM_EMAIL_REPLACE);
    $paramsManager->getHtmlFormParam(DUPX_Paramas_Manager::PARAM_FULL_SEARCH);
    $paramsManager->getHtmlFormParam(DUPX_Paramas_Manager::PARAM_MULTISITE_CROSS_SEARCH);
    $paramsManager->getHtmlFormParam(DUPX_Paramas_Manager::PARAM_POSTGUID);
    $paramsManager->getHtmlFormParam(DUPX_Paramas_Manager::PARAM_MAX_SERIALIZE_CHECK);
    ?>
</div>




