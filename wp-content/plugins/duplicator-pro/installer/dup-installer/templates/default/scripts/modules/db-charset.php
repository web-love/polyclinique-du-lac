<?php
/**
 *
 * @package templates/default
 *
 */
defined('ABSPATH') || defined('DUPXABSPATH') || exit;

$paramsManager = DUPX_Paramas_Manager::getInstance();
?>
<script>
    const dbCharsetDefaultID = <?php echo DupProSnapJsonU::wp_json_encode($paramsManager->getFormItemId(DUPX_Paramas_Manager::PARAM_DB_CHARSET)); ?>;
    const dbCollateDefaultID = <?php echo DupProSnapJsonU::wp_json_encode($paramsManager->getFormItemId(DUPX_Paramas_Manager::PARAM_DB_COLLATE)); ?>;

    $(document).ready(function ()
    {
        $('#' + dbCharsetDefaultID).on('change', function () {
            let collateDefault = $(this).find(':selected').data('collation-default');
            let collations = $(this).find(':selected').data('collations');
            let collateObj = $('#' + dbCollateDefaultID);

            collateObj.empty();
            $("<option></option>")
                    .appendTo(collateObj)
                    .attr('value', '')
                    .text(<?php echo json_encode(DUPX_Paramas_Descriptor_database::EMPTY_COLLATION_LABEL); ?> + ' [' + collateDefault + ']')
                    .prop('selected', true);

            for (let i = 0; i < collations.length; i++) {
                let label = collations[i] + (collations[i] === collateDefault ? <?php echo json_encode(DUPX_Paramas_Descriptor_database::DEFAULT_COLLATE_POSTFIX); ?> : '');
                $("<option></option>")
                        .appendTo(collateObj)
                        .attr('value', collations[i])
                        .text(label);
            }
        });
    });
</script>