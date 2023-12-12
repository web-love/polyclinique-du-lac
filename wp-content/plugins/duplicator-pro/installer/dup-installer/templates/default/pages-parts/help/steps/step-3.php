<?php
defined('ABSPATH') || defined('DUPXABSPATH') || exit;
?>
<!-- ============================================
STEP 3
============================================== -->
<?php
$sectionId   = 'section-step-3';
$expandClass = $sectionId == $open_section ? 'open' : 'close';
?>
<section id="<?php echo $sectionId; ?>" class="expandable <?php echo $expandClass; ?>" >
    <h2 class="header expand-header">Step <span class="step">3</span> of 4: Update Data</h2>
    <div class="content" >
        <a class="help-target" name="help-s3"></a>
        <div id="dup-help-step2" class="help-page">

            <!-- SETTINGS-->
            <h3>Setup</h3>
            These are the new values (URL, Path and Title) you can update for the new location at which your site will be installed at.
            <br/><br/>

            <h3>Replace <sup>pro</sup></h3>
            This section will allow you to add as many custom search and replace items that you would like.  For example you can search for other URLs to replace.  Please use high
            caution when using this feature as it can have unintended consequences as it will search the entire database.   It is recommended to only use highly unique items such as
            full URL or file paths with this option.
            <br/><br/>

            <!-- ADVANCED OPTS -->
            <h3>Options</h3>
            <table class="help-opt">
                <?php
                dupxTplRender('pages-parts/help/widgets/option-heading');

                // new admin account tab
                dupxTplRender('pages-parts/help/options/new-admin-account/heading');
                dupxTplRender('pages-parts/help/options/new-admin-account/username');
                dupxTplRender('pages-parts/help/options/new-admin-account/password');

                // Scan Options tab
                dupxTplRender('pages-parts/help/options/scan-options/heading');
                dupxTplRender('pages-parts/help/options/scan-options/cleanup');
                dupxTplRender('pages-parts/help/options/scan-options/old-url');
                dupxTplRender('pages-parts/help/options/scan-options/old-path');
                dupxTplRender('pages-parts/help/options/scan-options/site-url');
                dupxTplRender('pages-parts/help/options/scan-options/scan-tables');
                dupxTplRender('pages-parts/help/options/scan-options/activate-plugins');
                dupxTplRender('pages-parts/help/options/scan-options/update-email-domains');
                dupxTplRender('pages-parts/help/options/scan-options/full-search');
                dupxTplRender('pages-parts/help/options/scan-options/post-guid');
                dupxTplRender('pages-parts/help/options/scan-options/cross-search');
                dupxTplRender('pages-parts/help/options/scan-options/max-size-check-for-serialize-objects');

                // WP-Config File
                dupxTplRender('pages-parts/help/options/wp-config-file/heading');
                dupxTplRender('pages-parts/help/options/wp-config-file/add-remove-switch');
                dupxTplRender('pages-parts/help/options/wp-config-file/constants');
                dupxTplRender('pages-parts/help/options/wp-config-file/auth-keys');
                dupxTplRender('pages-parts/help/options/wp-config-file/force-ssl-admin');
                ?>
            </table>
        </div>
    </div>
</section>