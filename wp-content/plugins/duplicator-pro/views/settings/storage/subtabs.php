<?php
defined("ABSPATH") or die("");
?>
<style>
    .dpro-sub-tabs a, .dpro-sub-tabs b {
        display: inline-block;
        box-shadow: none;
    }
    .dpro-sub-tabs a:not(:last-child):after, 
    .dpro-sub-tabs b:not(:last-child):after {
        content: '|';  
        padding: 0 6px; 
        color: #000; 
        font-weight: normal;
    }
</style>
<div class='dpro-sub-tabs'>
    <?php
    foreach (DUP_PRO_CTRL_Storage_Setting::getSubTabs() as $tabKey => $tabLabel) {
        if (DUP_PRO_CTRL_Storage_Setting::getCurrentSubTab() == $tabKey) {
            ?>
            <b><?php echo $tabLabel; ?></b>
        <?php } else { ?>
            <a href="<?php echo DUP_PRO_CTRL_Storage_Setting::getSubTabURL($tabKey); ?>"><?php echo $tabLabel; ?></a>
            <?php
        }
    }
    ?>
</div>