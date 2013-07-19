<?php
/**
 * Форма создания/редактирование карточки функции.
 * Можно указать входные/выходные параметры, тип функции (get, list, save, delete).
 * Задать описание.
 *
 * @var $this           FunctionController
 * @var $service        SoapService
 * @var $model          SoapFunction
 * @var $input_params   SoapFunctionParam[]
 * @var $output_params  SoapFunctionParam[]
 * @var $form           TbActiveForm
 */
?>

<script>
    window.count_params = <?= count($output_params+$input_params); ?>;
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
        'enableAjaxValidation' => true,'clientOptions'=>array(
            'validateOnSubmit' => true,
            'validateOnType' => true,
        ),
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
        echo $form->errorSummary($model);
    }

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
        <tr><th></th><th>Название</th><th>Тип данных</th><th>Обязательное</th><th>Описание</th><th>Удалить</th></tr>
        <?php
            foreach($input_params as $i=>$item){
                $this->renderPartial('_add_param_field', array(
                    'model' => $item,
                    'index' => $i,
                ));
            }
        ?>
    </table>

<?php
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

    $this->widget('bootstrap.widgets.TbButtonGroup', array(
        'size' => 'normal',
        'type' => 'primary',
        'buttons' => array(
            array(
                'label' => 'Добавить входной параметр',
                'items' => $buttons,
            ),
        ),
        'htmlOptions' => array(
            'class' => 'add-input-param'
        )
    ));
?>
    <br/><br/>
    Выходные параметры:<br/>
    <table class="table output-params">
        <tr><th></th><th>Название</th><th>Тип данных</th><th>Обязательное</th><th>Описание</th><th>Удалить</th></tr>
        <?php
            foreach($output_params as $i=>$item){
                $this->renderPartial('_add_param_field', array(
                    'model' => $item,
                    'index' => $i,
                ));
            }
        ?>
    </table>

<?php
    $this->widget('bootstrap.widgets.TbButtonGroup', array(
        'size' => 'normal',
        'type' => 'primary',
        'buttons' => array(
            array(
                'label' => 'Добавить выходной параметр',
                'items' => $buttons
            ),
        ),
        'htmlOptions' => array(
            'class' => 'add-output-param'
        )
    ));
?>

</fieldset>

<?php
    $this->endWidget();
?>