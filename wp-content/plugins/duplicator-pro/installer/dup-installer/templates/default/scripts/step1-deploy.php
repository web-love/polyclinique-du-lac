<?php
/**
 *
 * @package templates/default
 *
 */
defined('ABSPATH') || defined('DUPXABSPATH') || exit;

$nextStepPrams = array(
    DUPX_Paramas_Manager::PARAM_CTRL_ACTION => 'ctrl-step2',
    DUPX_Security::CTRL_TOKEN               => DUPX_CSRF::generate('ctrl-step2')
);
?>
<script>
    DUPX.deployStep1 = function () {
        DUPX.multipleStepDeploy($('#s1-input-form'), <?php echo DupProSnapJsonU::wp_json_encode($nextStepPrams); ?>);
    };
</script>