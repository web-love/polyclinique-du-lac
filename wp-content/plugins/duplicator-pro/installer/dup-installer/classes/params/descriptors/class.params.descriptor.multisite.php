<?php
/**
 * Installer params manager
 *
 * Standard: PSR-2
 * @link http://www.php-fig.org/psr/psr-2 Full Documentation
 *
 * @final class DUPX_Paramas_Descriptor_Multisite package SC\DUPX\U
 *
 */
defined('ABSPATH') || defined('DUPXABSPATH') || exit;

/**
 * class where all parameters are initialized. Used by the param manager
 */
final class DUPX_Paramas_Descriptor_multisite implements DUPX_Interface_Paramas_Descriptor
{

    /**
     *
     * @param DUPX_Param_item[] $params
     */
    public static function init(&$params)
    {
        $archive_config = DUPX_ArchiveConfig::getInstance();

        $multisiteInstSubnote = self::getMultisiteInstallerSubNote();
        $standaloneLabel      = 'Convert subsite to standalone'.(empty($multisiteInstSubnote) ? '' : ' *');

        $params[DUPX_Paramas_Manager::PARAM_SUBSITE_ID] = new DUPX_Param_item_form(
            DUPX_Paramas_Manager::PARAM_SUBSITE_ID,
            DUPX_Param_item_form::TYPE_INT,
            DUPX_Param_item_form::FORM_TYPE_SELECT,
            array(
            'default'      => -1,
            'acceptValues' => array(__CLASS__, 'getSubSiteIdsAcceptValues')
            ),
            array(
            'status' => function($paramObj) {
                if (DUPX_Paramas_Manager::getInstance()->getValue(DUPX_Paramas_Manager::PARAM_MULTISITE_INST_TYPE) != 1) {
                    return DUPX_Param_item_form::STATUS_DISABLED;
                } else {
                    return DUPX_Param_item_form::STATUS_ENABLED;
                }
            },
            'label'          => 'Subsite:',
            'wrapperClasses' => array('revalidate-on-change'),
            'options'        => array(__CLASS__, 'getSubSiteIdsOptions'),
            )
        );

        $params[DUPX_Paramas_Manager::PARAM_MULTISITE_INST_TYPE] = new DUPX_Param_item_form(
            DUPX_Paramas_Manager::PARAM_MULTISITE_INST_TYPE,
            DUPX_Param_item_form::TYPE_INT,
            DUPX_Param_item_form::FORM_TYPE_RADIO,
            array(
            'default'        => -1,
            'acceptValues'   => array(__CLASS__, 'getMultisiteActionAcceptValues'),
            'invalidMessage' => 'Multisite install type invalid value'
            ),
            array(
            'status'         => DUPX_Param_item_form::STATUS_ENABLED,
            'label'          => 'Install Type:',
            'wrapperClasses' => array('group-block', 'revalidate-on-change'),
            'options'        => array(
                new DUPX_Param_item_form_option(0, 'Restore multisite network',
                    function () {
                        if (DUPX_ArchiveConfig::getInstance()->isPartialNetwork() || DUPX_Custom_Host_Manager::getInstance()->isManaged()) {
                            return DUPX_Param_item_form_option::OPT_DISABLED;
                        } else {
                            return DUPX_Param_item_form_option::OPT_ENABLED;
                        }
                    },
                    array(
                    'onchange' => "jQuery('#".$params[DUPX_Paramas_Manager::PARAM_SUBSITE_ID]->getFormItemId()."').prop('disabled', true);"
                    ."jQuery('#".$params[DUPX_Paramas_Manager::PARAM_SUBSITE_ID]->getFormWrapperId()."').removeClass('param-wrapper-enabled').addClass('param-wrapper-disabled');"
                    )),
                new DUPX_Param_item_form_option(1, $standaloneLabel, function () {
                        if (DUPX_Paramas_Manager::getInstance()->getValue(DUPX_Paramas_Manager::PARAM_RESTORE_BACKUP_MODE)) {
                            return DUPX_Param_item_form_option::OPT_DISABLED;
                        } else if (DUPX_Conf_Utils::multisitePlusEnabled()) {
                            return DUPX_Param_item_form_option::OPT_ENABLED;
                        } else {
                            return DUPX_Param_item_form_option::OPT_DISABLED;
                        }
                    },
                    array(
                    'onchange' => "jQuery('#".$params[DUPX_Paramas_Manager::PARAM_SUBSITE_ID]->getFormItemId()."').prop('disabled', false);"
                    ."jQuery('#".$params[DUPX_Paramas_Manager::PARAM_SUBSITE_ID]->getFormWrapperId()."').removeClass('param-wrapper-disabled').addClass('param-wrapper-enabled');"
                    ))
            ),
            'subNote' => $multisiteInstSubnote)
        );

        $params[DUPX_Paramas_Manager::PARAM_REPLACE_MODE] = new DUPX_Param_item_form(
            DUPX_Paramas_Manager::PARAM_REPLACE_MODE,
            DUPX_Param_item_form::TYPE_STRING,
            DUPX_Param_item_form::FORM_TYPE_RADIO,
            array(
            'default'      => 'legacy',
            'acceptValues' => array(
                'legacy',
                'mapping'
            )),
            array(
            'label'   => 'Replace Mode:',
            'options' => array(
                new DUPX_Param_item_form_option('legacy', 'Standard', DUPX_Param_item_form_option::OPT_ENABLED, array('title' => 'Set the files current date time to now')),
                new DUPX_Param_item_form_option('mapping', 'Mapping', DUPX_Param_item_form_option::OPT_ENABLED, array('title' => 'Keep the files date time the same'))
            ))
        );

        $params[DUPX_Paramas_Manager::PARAM_MU_REPLACE] = new DUPX_Param_item_form_urlmapping(
            DUPX_Paramas_Manager::PARAM_MU_REPLACE,
            DUPX_Param_item_form_urlmapping::TYPE_ARRAY_STRING,
            DUPX_Param_item_form_urlmapping::FORM_TYPE_URL_MAPPING,
            array(
            'default' => $archive_config->getNewUrlsArrayIdVal()),
            array()
        );

        $params[DUPX_Paramas_Manager::PARAM_MULTISITE_CROSS_SEARCH] = new DUPX_Param_item_form(
            DUPX_Paramas_Manager::PARAM_MULTISITE_CROSS_SEARCH,
            DUPX_Param_item_form::TYPE_BOOL,
            DUPX_Param_item_form::FORM_TYPE_CHECKBOX,
            array(
            'default' => (count($archive_config->subsites) <= MAX_SITES_TO_DEFAULT_ENABLE_CORSS_SEARCH)
            ),
            array(
            'status' => function($paramObj) {
                if (DUPX_MU::newSiteIsMultisite()) {
                    return DUPX_Param_item_form::STATUS_ENABLED;
                } else {
                    return DUPX_Param_item_form::STATUS_SKIP;
                }
            },
            'label'         => 'Database search:',
            'checkboxLabel' => 'Cross-search between the sites of the network.'
            )
        );
    }

    /**
     * 
     * @param DUPX_Param_item[] $params
     */
    public static function updateParamsAfterOverwrite(&$params)
    {
        if ($params[DUPX_Paramas_Manager::PARAM_MULTISITE_INST_TYPE]->getValue() < 0) {
            $acceptValues = $params[DUPX_Paramas_Manager::PARAM_MULTISITE_INST_TYPE]->getAcceptValues();
            if (count($acceptValues) > 0) {
                $params[DUPX_Paramas_Manager::PARAM_MULTISITE_INST_TYPE]->setValue($acceptValues[0]);
            }
        }
    }

    public static function getSubSiteIdsOptions()
    {
        $archive_config = DUPX_ArchiveConfig::getInstance();
        $options        = array();
        foreach ($archive_config->subsites as $subsite) {
            $optStatus      = !DUPX_InstallerState::isImportFromBackendMode() || (count($subsite->filtered_tables) === 0 && count($subsite->filtered_paths) === 0)
                ? DUPX_Param_item_form_option::OPT_ENABLED : DUPX_Param_item_form_option::OPT_DISABLED;
            $label          = $subsite->blogname.' ['.$subsite->domain.$subsite->path.']';
            $options[]      = new DUPX_Param_item_form_option($subsite->id, $label, $optStatus);

        }
        return $options;
    }

    public static function getSubSiteIdsAcceptValues()
    {
        $archive_config = DUPX_ArchiveConfig::getInstance();
        $acceptValues   = array(-1);
        foreach ($archive_config->subsites as $subsite) {
            if (!DUPX_InstallerState::isImportFromBackendMode() || (count($subsite->filtered_tables) === 0 && count($subsite->filtered_paths) === 0)) {
                $acceptValues[] = $subsite->id;
            }
        }
        return $acceptValues;
    }

    /**
     * 
     * @param DUPX_Param_item $param
     * @return int[]
     */
    public static function getMultisiteActionAcceptValues($param)
    {
        $acceptValues = array();
        if (!DUPX_Custom_Host_Manager::getInstance()->isManaged() && !DUPX_ArchiveConfig::getInstance()->mu_is_filtered) {
            $acceptValues[] = 0;
        }
        if (DUPX_Conf_Utils::multisitePlusEnabled()) {
            $acceptValues[] = 1;
        }
        return $acceptValues;
    }

    private static function getMultisiteInstallerSubNote()
    {
        $archive_config = DUPX_ArchiveConfig::getInstance();

        if (($license = $archive_config->getLicenseType()) !== DUPX_LicenseType::BusinessGold) {
            $subNote = '* Requires Business or Gold license. This installer was created with ';
            switch ($archive_config->getLicenseType()) {
                case DUPX_LicenseType::Unlicensed:
                    $subNote .= "an Unlicensed Duplicator Pro.";
                    break;
                case DUPX_LicenseType::Personal:
                    $subNote .= "a Personal license.";
                    break;
                case DUPX_LicenseType::Freelancer:
                    $subNote .= "a Freelancer license.";
                    break;
                default:
                    $subNote .= 'an unknown license type';
            }
        } else {
            $subNote = '';
        }
        return $subNote;
    }
}