<?php
/**
 * Форма создания/редактирование карточки функции.
 * Можно указать входные/выходные параметры, тип функции (get, list, save, delete).
 * Задать описание.
 *
 * @var $this       FunctionController
 * @var $service    SoapService
 * @var $model      SoapFunction
 * @var $function_params     SoapFunctionParam[]
 * @var $form       TbActiveForm
 */
?>

<script>
    window.count_params = <?= count($function_params); ?>;
</script>

<?php
    echo '<h2>'.$this->pageTitle.'</h2>';

    Yii::app()->clientScript->registerScriptFile('/static/js/function/form.js');

    $this->breadcrumbs = array(
        'Сервисы' => $this->createUrl('service/list'),
        'Функции' => $this->createUrl('function/list', array('service_id' => $service->primaryKey)),
    );
    if (!$model->isNewRecord){
        $this->breadcrumbs['Тесты функции'] = $this->createUrl('test/list', array('func_id' => $model->primaryKey));
    }
    $this->breadcrumbs[] = 'Карточка функции';

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
        'url' => $this->createUrl('function/list', array('service_id' => $service->primaryKey)),
    ));
    echo '<br/><br/>';

    if ($model->hasErrors()){
        echo '<br/><br/>'.$form->errorSummary($model);
    }

    $param_types = SoapFunctionParam::getParamTypes();
    $groups = $service->getGroups();
    $types = array_merge(array('' => 'Выберите'), SoapFunction::getTypes())
?>

<fieldset>
    <?= $form->dropDownListRow($model, 'group_id', $groups); ?>
    <?= $form->textFieldRow($model, 'name'); ?>
    <?= $form->dropDownListRow($model, 'type', $types); ?>
    <?= $form->textAreaRow($model, 'description'); ?>

    Входные параметры:<br/>
    <table class="table input-params">
        <tr><th>Название</th><th>Тип</th><th>Описание</th><th>Удалить</th></tr>
        <?php foreach($function_params as $i=>$item): ?>
            <tr class="param-<?= $i; ?>">
                <td><?php echo CHtml::activeTextField($item,"[$i]name"); ?></td>
                <td><?php echo CHtml::activeDropDownList($item,"[$i]type", $param_types); ?></td>
                <td><?php echo CHtml::activeCheckBox($item,"[$i]required"); ?></td>
                <td><?php echo CHtml::activeTextField($item,"[$i]description"); ?></td>
                <td><?php
                    $this->widget('bootstrap.widgets.TbButton', array(
                        'buttonType' => 'button',
                        'type' => 'primary',
                        'label' => 'Удалить',
                        'htmlOptions' => array(
                            'class' => 'del-input-param'
                        )
                    ));
                    ?></td>
            </tr>
        <?php endforeach; ?>
    </table>

<?php
    $this->widget('bootstrap.widgets.TbButton', array(
        'buttonType' => 'button',
        'type' => 'primary',
        'label' => 'Добавить входной параметр',
        'htmlOptions' => array(
            'class' => 'add-input-param'
        )
    ));
?>
    <br/><br/>
    Выходные параметры:<br/>
    <table class="table output-params">
        <tr><th>Название</th><th>Тип</th><th>Описание</th><th>Удалить</th></tr>
        <?php foreach($function_params as $i=>$item): ?>
            <tr class="param-<?= $i; ?>">
                <td><?php echo CHtml::activeTextField($item,"[$i]name"); ?></td>
                <td><?php echo CHtml::activeDropDownList($item,"[$i]type", $param_types); ?></td>
                <td><?php echo CHtml::activeTextField($item,"[$i]description"); ?></td>
                <td><?php
                    $this->widget('bootstrap.widgets.TbButton', array(
                        'buttonType' => 'button',
                        'type' => 'primary',
                        'label' => 'Удалить',
                        'htmlOptions' => array(
                            'class' => 'del-output-param'
                        )
                    ));
                ?></td>
            </tr>
        <?php endforeach; ?>
    </table>

<?php
    $this->widget('bootstrap.widgets.TbButton', array(
        'buttonType' => 'button',
        'type' => 'primary',
        'label' => 'Добавить выходной параметр',
        'htmlOptions' => array(
            'class' => 'add-output-param'
        )
    ));
?>
</fieldset>

<?php
    $this->endWidget();
?>