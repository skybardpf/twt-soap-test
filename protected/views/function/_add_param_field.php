<?php
/**
 * @var $this           FunctionController
 * @var $model          SoapFunctionParam
 * @var $index          integer
 */
?>
<?php
    $types = SoapFunctionParam::getTypesOfData();
//var_dump($model->input_param);die;
?>

<tr class="param-<?= $index; ?>">
    <td><?php echo CHtml::activeHiddenField($model,"[$index]input_param"); ?></td>
    <td><?php echo CHtml::activeTextField($model,"[$index]name"); ?></td>
    <td>
        <?php echo CHtml::activeHiddenField($model,"[$index]type_of_data"); ?>
        <?php echo CHtml::TextField('type_of_data', $types[$model->type_of_data], array('disabled' => true)); ?>
    </td>
    <td><?php echo CHtml::activeCheckBox($model,"[$index]required"); ?></td>
    <td><?php echo CHtml::activeTextField($model,"[$index]description"); ?></td>
    <td>
    <?php
        $this->widget('bootstrap.widgets.TbButton', array(
            'buttonType' => 'button',
            'type' => 'primary',
            'label' => 'Удалить',
            'htmlOptions' => array(
                'class' => ($model->input_param ? 'del-input-param' : 'del-output-param')
            )
        ));
    ?>
    </td>
</tr>