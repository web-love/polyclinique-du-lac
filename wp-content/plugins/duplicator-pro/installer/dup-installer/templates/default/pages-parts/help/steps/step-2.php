<?php
defined('ABSPATH') || defined('DUPXABSPATH') || exit;
?>
<!-- ============================================
STEP 2
============================================== -->
<?php
$sectionId   = 'section-step-2';
$expandClass = $sectionId == $open_section ? 'open' : 'close';
?>
<section id="<?php echo $sectionId; ?>" class="expandable <?php echo $expandClass; ?>" >
    <h2 class="header expand-header">Step <span class="step">2</span> of 4: Install Database</h2>
    <div class="content" >
        <div id="dup-help-step1" class="help-page">            
            <!-- OPTIONS-->
            <h3>Options</h3>
            <table class="help-opt">
                <?php
                dupxTplRender('pages-parts/help/widgets/option-heading');
                dupxTplRender('pages-parts/help/options/database/spacing');
                dupxTplRender('pages-parts/help/options/database/mysql-mode');
                dupxTplRender('pages-parts/help/options/database/charset');
                dupxTplRender('pages-parts/help/options/database/collation');
                ?>
            </table>            
        </div>
    </div>
</section>
