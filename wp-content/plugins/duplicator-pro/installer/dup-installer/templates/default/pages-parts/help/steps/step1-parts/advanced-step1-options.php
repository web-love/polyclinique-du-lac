<?php
defined('ABSPATH') || defined('DUPXABSPATH') || exit;
?>
<h3>Options (For Advanced mode)</h3>
The advanced options allow you to change database table prefix, advanced options and other configuration options in the wp-config.php file.

<br/><br/>

<h4>Database</h4>
<table class="help-opt">
    <?php
    dupxTplRender('pages-parts/help/widgets/option-heading');
    dupxTplRender('pages-parts/help/options/advanced/database/table-prefix');
    ?>
</table>
<br/><br/>


<h4>Advanced</h4>
These are the advanced options for advanced users.

<table class="help-opt">
    <?php
    // Engine Settings
    dupxTplRender('pages-parts/help/widgets/option-heading');
    dupxTplRender('pages-parts/help/options/advanced/engine-settings/heading');
    dupxTplRender('pages-parts/help/options/advanced/engine-settings/extraction-mode');
    dupxTplRender('pages-parts/help/options/advanced/engine-settings/wp-core-files');
    
    // Processing
    dupxTplRender('pages-parts/help/options/advanced/processing/heading');
    dupxTplRender('pages-parts/help/options/advanced/processing/safe-mode');
    dupxTplRender('pages-parts/help/options/advanced/processing/file-times');
    dupxTplRender('pages-parts/help/options/advanced/processing/logging');

    // Configuration files
    dupxTplRender('pages-parts/help/options/advanced/configuration-files/heading');
    dupxTplRender('pages-parts/help/options/advanced/configuration-files/wordpress-wp-config');
    dupxTplRender('pages-parts/help/options/advanced/configuration-files/apache-htaccess');
    dupxTplRender('pages-parts/help/options/advanced/configuration-files/other-configurations');
    ?>
</table>
<br/><br/>