<?php
/**
 * Форма создания/редактирование параметров, возвращаемых значений для функции.
 *
 * @var $this   FunctionController
 * @var $model  SoapFunction
 * @var $params SoapFunctionParam[]
 * @var $form   TbActiveForm
 */
?>

<script>
    window.count_params = <?= count($params); ?>;
</script>

<?php
    $this->pageTitle = 'Параметры функции «'.$model->name.'»';
    echo '<h2>'.$this->pageTitle.'</h2>';

    Yii::app()->clientScript->registerScriptFile('/static/js/function/form.js');

    $this->breadcrumbs = array(
        'Сервисы' => $this->createUrl('service/list'),
        'Функции' => $this->createUrl('function/list', array('service_id' => $model->soapService->primaryKey)),
        'Параметры функции'
    );

    $form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
        'id' => 'model-form-form',
        'type' => 'horizontal',
        'enableAjaxValidation' => true,

    ));

    $this->widget('bootstrap.widgets.TbButton', array(
        'buttonType' => 'submit',
        'type' => 'primary',
        'label' => 'Сохранить'
    ));

    echo '&nbsp;';

    $this->widget('bootstrap.widgets.TbButton', array(
        'buttonType' => 'link',
        'label' => 'Отмена',
        'url' => $this->createUrl(
            'list',
            array(
                'service_id' => $model->soapService->primaryKey,
            )
        )
    ));

    echo $form->errorSummary($model);

    $param_types = SoapFunctionParam::getParamTypes();
    $types = array_merge(array('' => 'Выберите'), SoapFunction::getTypes())
?>

<div class="form">
    <?= $form->dropDownListRow($model, 'type', $types)?>
    <table class="params">
        <tr><th>Название</th><th>Тип</th></tr>
        <?php foreach($params as $i=>$item): ?>
            <tr class="param-<?= $i; ?>">
                <td><?php echo CHtml::activeTextField($item,"[$i]name"); ?></td>
                <td><?php echo CHtml::activeDropDownList($item,"[$i]type", $param_types); ?></td>
                <td><?php
                    $this->widget('bootstrap.widgets.TbButton', array(
                        'buttonType' => 'button',
                        'type' => 'primary',
                        'label' => 'Удалить',
                        'htmlOptions' => array(
                            'class' => 'del-param'
                        )
                    ));
                ?></td>
            </tr>
        <?php endforeach; ?>
    </table>

</div><!-- form -->

<?php


//echo $form->textFieldRow($model, 'name', array('class' => 'input-xxlarge',));
//
//echo $form->textAreaRow($model, 'args', array(
//	'class' => 'input-xxlarge',
//	'hint' => 'Формат JSON, массив аргументов. Например:<br>
//       <code>[{"summa": "1000"}, 3, [1,3,{"test": 4}]]</code> — передать первым аргументов объект со свойством summa равным 1000,
//       вторым аргументом значение 3,
//       третьим массив состоящий из трех элементов 1, 3, и объекта со свойством test и значением 4'
//));

    $this->widget('bootstrap.widgets.TbButton', array(
        'buttonType' => 'button',
        'type' => 'primary',
        'label' => 'Добавить параметр',
        'htmlOptions' => array(
            'class' => 'add-param'
        )
    ));

    $this->endWidget();
?>