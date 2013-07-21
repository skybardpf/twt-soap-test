<?php
/**
 * @var $this           FunctionController
 * @var $model          SoapFunctionParam
 * @var $index          integer
 * @var $child          boolean
 * @var $child_index    integer
 */
?>
<?php
    $types = SoapFunctionParam::getTypesOfData();

    if ($child){
?>
    <tr class="tr-child-index-<?= $child_index; ?>">
        <td style="width: 50px">
            <?= CHtml::activeHiddenField($model, "[$index][__children__][$child_index]input_param"); ?>
        </td>
        <td>
            <?= CHtml::activeTextField($model, "[$index][__children__][$child_index]name"); ?>
        </td>
        <td>
            <?php echo CHtml::activeHiddenField($model, "[$index][__children__][$child_index]type_of_data"); ?>
            <?php echo CHtml::TextField('type_of_data', $types[$model->type_of_data], array('disabled' => true)); ?>
        </td>
        <td> --- </td>
        <td><?php echo CHtml::activeCheckBox($model, "[$index][__children__][$child_index]required"); ?></td>
        <td><?php echo CHtml::activeTextField($model, "[$index][__children__][$child_index]description"); ?></td>
        <td>
            <?php
            $class = ($model->input_param ? 'del-input-child-param' : 'del-output-child-param');
            $this->widget('bootstrap.widgets.TbButton', array(
                'buttonType' => 'button',
                'type' => 'primary',
                'label' => 'Удалить',
                'htmlOptions' => array(
                    'class' => $class,
                    'data-child-index' => $child_index
                )
            ));
            ?>
        </td>
    </tr>
    <?php
    } else {
    ?>
    <tr class="param-<?= $index; ?>" data-param-index="<?= $index; ?>">
        <td>
        <?php
            echo CHtml::activeHiddenField($model,"[$index]input_param");
        ?>
        </td>
        <td>
        <?php
            echo CHtml::activeTextField($model,"[$index]name");
        ?>
        </td>
        <td>
        <?php
            echo CHtml::activeHiddenField($model,"[$index]type_of_data");
            echo CHtml::TextField('type_of_data', $types[$model->type_of_data], array('disabled' => true));
        ?>
        </td>
        <td>
        <?php
            if (in_array($model->type_of_data, array(SoapFunctionParam::TYPE_DATA_ARRAY_VALUES))){
                echo CHtml::activeDropDownList($model, "[$index]array_type_of_data", SoapFunctionParam::getNativeTypesOfData());
            } else {
                echo '---';
            }
        ?>
        </td>
        <td>
        <?php
            echo CHtml::activeCheckBox($model,"[$index]required");
        ?>
        </td>
        <td>
        <?php
            echo CHtml::activeTextField($model,"[$index]description");
        ?>
        </td>
        <td>
        <?php
            $class = ($model->input_param ? 'del-input-param' : 'del-output-param');
            $this->widget('bootstrap.widgets.TbButton', array(
                'buttonType' => 'button',
                'type' => 'primary',
                'label' => 'Удалить',
                'htmlOptions' => array(
                    'class' => $class
                )
            ));
            ?>
        </td>
    </tr>

    <?php
    }

    if (in_array($model->type_of_data,
        array(
            SoapFunctionParam::TYPE_DATA_ARRAY_FIELDS,
            SoapFunctionParam::TYPE_DATA_ARRAY_ELEMENTS_STRUCTURE,
            SoapFunctionParam::TYPE_DATA_TABLE
        )
    )){
        $buttons = array();
        $param_types = SoapFunctionParam::getTypesOfData();
        foreach($param_types as $k=>$pt){
            $buttons[] = array(
                'label' => $pt,
                'url'=>'#',
                'linkOptions' => array(
                    'data-type-of-data' => $k
                )
            );
        }
    ?>
    <tr class="child-params-<?= $index; ?>" data-param-index="<?= $index; ?>">
        <td colspan="5">
            Элементы структуры: <?= $types[$model->type_of_data]; ?>
            <?php
            $this->widget('bootstrap.widgets.TbButtonGroup', array(
                'size' => 'normal',
                'type' => 'primary',
                'buttons' => array(
                    array(
                        'label' => 'Добавить значение массива',
                        'items' => $buttons
                    ),
                ),
                'htmlOptions' => array(
                    'class' => 'add-array-value'
                )
            ));
            ?>
            <table class="table-striped parent-param-<?= $index; ?>">
                <tr><th></th><th>Название</th><th>Тип данных</th><th>Обязательное</th><th>Описание</th><th>Удалить</th></tr>
            </table>
        </td>
    </tr>
    <?php
    }
    ?>