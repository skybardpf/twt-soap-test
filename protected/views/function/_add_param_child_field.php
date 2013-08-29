<?php
/**
 * @var $this           FunctionController
 * @var $model          SoapFunctionParam
 * @var $index          integer
 */
?>
<?php
    $input_param = $model->input_param ? 'input' : 'output';
    $types = SoapFunctionParam::getAllTypesOfData();
?>

<tr class="param-<?= $index; ?>" data-param-index="<?= $index; ?>">
    <td>
    <?php
        echo CHtml::activeHiddenField($model,"[$input_param][$index]input_param");
    ?>
    </td>
    <td>
    <?php
        echo CHtml::activeTextField($model,"[$input_param][$index]name");
    ?>
    </td>
    <td>
    <?php
        echo CHtml::activeHiddenField($model,"[$input_param][$index]type_of_data");
        echo CHtml::TextField('type_of_data', $types[$model->type_of_data], array('disabled' => true));
    ?>
    </td>
    <td>
    <?php
        if (in_array($model->type_of_data, array(SoapFunctionParam::TYPE_DATA_ARRAY_VALUES))){
            echo CHtml::activeDropDownList($model, "[$input_param][$index]array_type_of_data", SoapFunctionParam::getNativeTypesOfData());
        } else {
            echo '---';
        }
    ?>
    </td>
    <td>
    <?php
        echo CHtml::activeCheckBox($model,"[$input_param][$index]required");
    ?>
    </td>
    <td>
    <?php
        echo CHtml::activeTextField($model,"[$input_param][$index]description");
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
    if (in_array($model->type_of_data,
        array(
            SoapFunctionParam::TYPE_DATA_ARRAY_FIELDS,
            SoapFunctionParam::TYPE_DATA_ARRAY_ELEMENTS_STRUCTURE,
            SoapFunctionParam::TYPE_DATA_TABLE
        )
    )){
        $buttons = array();
        if ($model->type_of_data == SoapFunctionParam::TYPE_DATA_TABLE){
            $param_types = array(
                SoapFunctionParam::TYPE_DATA_ELEMENT_TABLE => 'Элемент таблицы'
            );
            $button_label = 'Добавить элемент таблицы';
        } else {
            $param_types = SoapFunctionParam::getTypesOfData();
            $button_label = 'Добавить значение массива';
        }
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
                        'label' => $button_label,
                        'items' => $buttons
                    ),
                ),
                'htmlOptions' => array(
                    'class' => 'add-array-value'
                )
            ));
            ?>
            <table class="table-striped parent-param-<?= $index; ?>">
                <tr>
                    <th></th>
                    <th>Название</th>
                    <th>Тип данных</th>
                    <th>Тип данных массива</th>
                    <th>Обязательное</th>
                    <th>Описание</th>
                    <th>Удалить</th>
                </tr>
                <?php foreach ($model->children as $j=>$child): ?>
                <tr class="tr-child-index-<?= $j; ?>">
                    <td style="width: 30px">
                        <?= CHtml::activeHiddenField($child, "[$input_param][$index][__children__][$j]input_param"); ?>
                    </td>
                    <td>
                        <?= CHtml::activeTextField($child, "[$input_param][$index][__children__][$j]name"); ?>
                    </td>
                    <td>
                        <?php echo CHtml::activeHiddenField($child, "[$input_param][$index][__children__][$j]type_of_data"); ?>
                        <?php echo CHtml::TextField('type_of_data', $types[$child->type_of_data], array('disabled' => true)); ?>
                    </td>
                    <td> --- </td>
                    <td><?php echo CHtml::activeCheckBox($child, "[$input_param][$index][__children__][$j]required"); ?></td>
                    <td><?php echo CHtml::activeTextField($child, "[$input_param][$index][__children__][$j]description"); ?></td>
                    <td>
                        <?php
                        $class = ($child->input_param ? 'del-input-child-param' : 'del-output-child-param');
                        $this->widget('bootstrap.widgets.TbButton', array(
                            'buttonType' => 'button',
                            'type' => 'primary',
                            'label' => 'Удалить',
                            'htmlOptions' => array(
                                'class' => $class,
                                'data-child-index' => $j
                            )
                        ));
                        ?>
                    </td>
                </tr>
                <?php
                    if ($child->type_of_data == SoapFunctionParam::TYPE_DATA_ELEMENT_TABLE){
                        $buttons = array();
                        $param_types = SoapFunctionParam::getNativeTypesOfData();
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
                        <tr class="child-table-element child-index-<?= $j; ?>" data-parent-index="<?= $index; ?>" data-child-index="<?= $j; ?>">
                            <td colspan="5">
                                Содержимое элемента таблицы:
                                <?php
                                $this->widget('bootstrap.widgets.TbButtonGroup', array(
                                    'size' => 'normal',
                                    'type' => 'primary',
                                    'buttons' => array(
                                        array(
                                            'label' => 'Добавить содержимое элемента таблицы',
                                            'items' => $buttons
                                        ),
                                    ),
                                    'htmlOptions' => array(
                                        'class' => 'add-table-element-value'
                                    )
                                ));
                                ?>
                                <table class="table-striped table-element-param-<?= $index.'-'.$j; ?>">
                                    <tr><th></th><th>Название</th><th>Тип данных</th><th>Обязательное</th><th>Описание</th><th>Удалить</th></tr>
                                    <?php
                                        foreach ($child->children as $k=>$element){
                                            $this->renderPartial('_add_element_table', array(
                                                'model' => $element,
                                                'type' => $element->type_of_data,
                                                'index' => $index,
                                                'child_index' => $j,
                                                'element_index' => $k
                                            ));
                                        }
                                    ?>
                                </table>
                            </td>
                        </tr>
                <?php
                    } // $model->type_of_data == SoapFunctionParam::TYPE_DATA_ELEMENT_TABLE
                ?>
                <?php endforeach; ?>
            </table>
        </td>
    </tr>
    <?php
    }
    ?>