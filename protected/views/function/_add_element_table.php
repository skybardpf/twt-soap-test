<?php
/**
 * @var $this           FunctionController
 * @var $model          SoapFunctionParam
 * @var $type           string
 * @var $index          integer
 * @var $child_index    integer
 * @var $element_index  integer
 */
?>
<?php
    $types = SoapFunctionParam::getNativeTypesOfData();
    $model->type_of_data = (!isset($types[$type])) ? SoapFunctionParam::DEFAULT_TYPE_OF_DATA : $type;

    $input_param = $model->input_param ? 'input' : 'output';
    $structure = "[$input_param][$index][__children__][$child_index][--element_table--][$element_index]";
?>
    <tr class="tr-element-table-index-<?= $element_index; ?>">
        <td style="width: 50px">
            <?= CHtml::activeHiddenField($model, $structure."input_param"); ?>
        </td>
        <td>
            <?= CHtml::activeTextField($model, $structure."name"); ?>
        </td>
        <td>
            <?php echo CHtml::activeHiddenField($model, $structure."type_of_data"); ?>
            <?php echo CHtml::TextField('type_of_data', $types[$model->type_of_data], array('disabled' => true)); ?>
        </td>
        <td> --- </td>
        <td><?php echo CHtml::activeCheckBox($model, $structure."required"); ?></td>
        <td><?php echo CHtml::activeTextField($model, $structure."description"); ?></td>
        <td>
            <?php
            $class = ($model->input_param ? 'del-input-element-table' : 'del-output-element-table');
            $this->widget('bootstrap.widgets.TbButton', array(
                'buttonType' => 'button',
                'type' => 'primary',
                'label' => 'Удалить',
                'htmlOptions' => array(
                    'class' => $class,
                    'data-element-table' => $element_index
                )
            ));
            ?>
        </td>
    </tr>