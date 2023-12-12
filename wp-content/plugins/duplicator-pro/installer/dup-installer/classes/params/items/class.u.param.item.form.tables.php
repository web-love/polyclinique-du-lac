<?php
/**
 * param descriptor
 *
 * Standard: PSR-2
 * @link http://www.php-fig.org/psr/psr-2 Full Documentation
 *
 * @package SC\DUPX\U
 *
 */
defined('ABSPATH') || defined('DUPXABSPATH') || exit;

/**
 * this class handles the entire block selection block.
 */
class DUPX_Param_item_form_tables extends DUPX_Param_item_form
{

    const TYPE_ARRAY_TABLES          = 'arraytbl';
    const FORM_TYPE_TABLES_SELECT    = 'tablessel';
    const TABLE_ITEM_POSTFIX         = '_item';
    const TABLE_NAME_POSTFIX_EXTRACT = '_extract';
    const TABLE_NAME_POSTFIX_REPLACE = '_replace';

    public function __construct($name, $type, $formType, $attr = null, $formAttr = array())
    {
        if ($type != self::TYPE_ARRAY_TABLES) {
            throw new Exception('the type must be '.self::TYPE_ARRAY_TABLES);
        }

        if ($formType != self::FORM_TYPE_TABLES_SELECT) {
            throw new Exception('the form type must be '.self::FORM_TYPE_TABLES_SELECT);
        }
        parent::__construct($name, $type, $formType, $attr, $formAttr);
    }

    protected function htmlItem()
    {
        if ($this->formType == self::FORM_TYPE_TABLES_SELECT) {
            $this->tablesSelectHtml();
        } else {
            parent::htmlItem();
        }
    }

    protected function tablesSelectHtml()
    {
        $tables = DUPX_DB_Tables::getInstance();


        $value = $this->getInputValue();
        ?>
        <table id="plugins_list_table_selector" class="list_table_selector" >
            <thead>
                <tr>
                    <th class="name" >Original Table</th>
                    <th class="info" >Install name</th>
                    <th class="action" >Extract</th>
                    <th class="action" >Replace</th>
                </tr>
            </thead>
            <tfoot>
                <tr>
                    <th class="name" >Original Table</th>
                    <th class="info" >Install name</th>
                    <th class="action" >Extract</th>
                    <th class="action" >Replace</th>
                </tr>
            </tfoot>
            <tbody>
                <?php
                $index = 0;
                foreach ($value as $name => $tableVals) {
                    $this->tableHtmlItem($tableVals, $tables->getTableObjByName($name), $index);
                    $index++;
                }
                ?>
            </tbody>
        </table>
        <?php
    }

    /**
     * 
     * @param array $vals
     * @param DUPX_DB_Table_item $tableOjb
     */
    protected function tableHtmlItem($vals, DUPX_DB_Table_item $tableOjb, $index)
    {
        $itemClasses          = array(
            'table-item',
            $this->getFormItemId().self::TABLE_ITEM_POSTFIX
        );
        $extractCheckboxAttrs = array(
            'id'    => $this->getFormItemId().self::TABLE_NAME_POSTFIX_EXTRACT.'_'.$index,
            'name'  => $this->getName().self::TABLE_NAME_POSTFIX_EXTRACT.'_'.$index,
            'class' => $this->getFormItemId().self::TABLE_NAME_POSTFIX_EXTRACT,
            'value' => 1
        );
        $replaceCheckboxAttrs = array(
            'id'    => $this->getFormItemId().self::TABLE_NAME_POSTFIX_REPLACE.'_'.$index,
            'name'  => $this->getName().self::TABLE_NAME_POSTFIX_REPLACE.'_'.$index,
            'class' => $this->getFormItemId().self::TABLE_NAME_POSTFIX_REPLACE,
            'value' => 1
        );

        if ($tableOjb->canBeExctracted()) {
            if ($vals['extract']) {
                $extractCheckboxAttrs['checked'] = '';
            }

            if ($vals['replace']) {
                $replaceCheckboxAttrs['checked'] = '';
            }
        } else {
            $itemClasses[]                    = 'no-display';
            $extractCheckboxAttrs['disabled'] = '';
            $replaceCheckboxAttrs['disabled'] = '';
        }

        if ($this->isDisabled() || $this->isReadonly()) {
            $extractCheckboxAttrs['disabled'] = '';
            $replaceCheckboxAttrs['disabled'] = '';

            $skipSendValue = true;
        } else {
            $skipSendValue = false;
        }
        ?>
        <tr class="<?php echo implode(' ', $itemClasses); ?>" >
            <td class="name" >
                <span class="table-name" ><?php echo DUPX_U::esc_html($tableOjb->getOriginalName()); ?></span><br>
                Rows: <b><?php echo $tableOjb->getRows(); ?></b> Size: <b><?php echo $tableOjb->getSize(true); ?></b>
            </td>
            <td class="info" >
                <span class="table-name" ><b><?php echo DUPX_U::esc_html($tableOjb->getNewName()); ?></b></span><br>
                &nbsp;
            </td>
            <td class="action extract" >
                <?php
                if (!$skipSendValue) {
                    // if is disabled or readonly don't senta tables nme so params isn't updated
                    ?>
                    <input type="hidden" name="<?php echo $this->getName().'[]'; ?>" value="<?php echo DUPX_U::esc_attr($tableOjb->getOriginalName()); ?>" >
                    <?php
                }
                DUPX_U_Html::checkboxSwitch(
                    $extractCheckboxAttrs,
                    array(
                        'title' => 'Extract in database'
                    )
                );
                ?>            
            </td>
            <td class="action replace" >
                <?php
                DUPX_U_Html::checkboxSwitch(
                    $replaceCheckboxAttrs,
                    array(
                        'title' => 'Apply replace engine at URLs and paths in database'
                    )
                );
                ?> 
            </td>
        </tr>
        <?php
    }

    /**
     * 
     * @param mixed $value
     * @param mixed $validateValue // variable passed by reference. Updated to validated value in the case, the value is a valid value.
     * @return bool     // true if is a valid value for this object
     */
    public function isValid($value, &$validateValue = null)
    {
        $validateValue = (array) $value;

        $avaiableTables = DUPX_DB_Tables::getInstance()->getTablesNames();
        $validateTables = array_keys($validateValue);

        // all tables in list have to exist in  avaiable tables
        foreach ($validateValue as $table => $tableValues) {
            if (!in_array($table, $avaiableTables)) {
                DUPX_Log::info('INVALID '.$table.' ISN\'T IN AVAIBLE LIST: '.DUPX_Log::varToString($avaiableTables));
                return false;
            }
        }

        // all tables abaliable have to exists in list
        foreach ($avaiableTables as $avaibleTable) {
            if (!in_array($avaibleTable, $validateTables)) {
                DUPX_Log::info('AVAIABLE '.$avaibleTable.' ISN\'T IN PARAM LIST TABLE');
                return false;
            }
        }

        return true;
    }

    /**
     * 
     * @param array $superObject
     * @return array
     */
    public function getValueFilter($superObject)
    {
        $nameExtract = $this->name.self::TABLE_NAME_POSTFIX_EXTRACT;
        $nameReplace = $this->name.self::TABLE_NAME_POSTFIX_REPLACE;

        $result = array();

        if (isset($superObject[$this->name]) && is_array($superObject[$this->name])) {
            foreach ($superObject[$this->name] as $index => $tableName) {
                $result[$tableName] = array(
                    'name'    => $tableName,
                    'extract' => isset($superObject[$nameExtract.'_'.$index]) ? filter_var($superObject[$nameExtract.'_'.$index], FILTER_VALIDATE_BOOLEAN) : false,
                    'replace' => isset($superObject[$nameReplace.'_'.$index]) ? filter_var($superObject[$nameReplace.'_'.$index], FILTER_VALIDATE_BOOLEAN) : false
                );
            }
        }

        return $result;
    }

    /**
     * return sanitized value
     * 
     * @param mixed $value
     * @return array
     * @throws Exception
     */
    public function getSanitizeValue($value)
    {
        $newValues      = (array) $value;
        $sanitizeValues = array();

        foreach ($newValues as $key => $newValue) {
            $sanitizedKey = DupProSnapLibUtil::sanitize_non_stamp_chars_newline_and_trim($key);
            $newValue     = (array) $newValue;

            $sanitizedNewValue            = self::getParamItemValueFromData();
            $sanitizedNewValue['name']    = isset($newValue['name']) ? DupProSnapLibUtil::sanitize_non_stamp_chars_newline_and_trim($newValue['name']) : '';
            $sanitizedNewValue['extract'] = isset($newValue['extract']) ? filter_var($newValue['extract'], FILTER_VALIDATE_BOOLEAN) : false;
            $sanitizedNewValue['replace'] = isset($newValue['replace']) ? filter_var($newValue['replace'], FILTER_VALIDATE_BOOLEAN) : false;

            $sanitizeValues[$sanitizedKey] = $sanitizedNewValue;
        }
        return $sanitizeValues;
    }

    /**
     * 
     * @param string $type
     */
    protected static function getDefaultAttrForType($type)
    {
        $attrs = parent::getDefaultAttrForType($type);
        if ($type == self::TYPE_ARRAY_TABLES) {
            $attrs['default'] = array();
        }

        return $attrs;
    }

    /**
     * 
     * @param string $formType
     * @return array
     */
    protected static function getDefaultAttrForFormType($formType)
    {
        $attrs = parent::getDefaultAttrForFormType($formType);
        if ($formType == self::FORM_TYPE_TABLES_SELECT) {
            $attrs['wrapperContainerTag'] = 'div';
            $attrs['inputContainerTag']   = 'div';
        }
        return $attrs;
    }

    /**
     * return param item from data
     * 
     * @param string $name
     * @param bool $extract
     * @param boolt $replace
     * @return array
     * @throws Exception
     */
    public static function getParamItemValueFromData($name = '', $extract = false, $replace = false)
    {
        return array(
            'name'    => $name,
            'extract' => $extract,
            'replace' => $replace
        );
    }
}