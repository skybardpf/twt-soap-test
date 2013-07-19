<?php
/**
 * Форма создания/редактирование Unit-теста для функции.
 *
 * @var $this   TestController
 * @var $model  SoapTest
 * @var $form   TbActiveForm
 */

$this->breadcrumbs = array(
    'Сервисы' => $this->createUrl('service/list'),
    'Функции' => $this->createUrl('function/list', array('service_id' => $model->soapFunction->groupFunctions->soapService->primaryKey)),
    'Тесты' => $this->createUrl('test/list', array('func_id' => $model->soapFunction->primaryKey)),
    'Добавить тест',
);

echo CHtml::tag('h2', array(), (($model->isNewRecord) ? 'Добавление' : 'Редактирование') . ' теста к функции «'.$model->soapFunction->name.'» сервиса «'.$model->soapFunction->groupFunctions->soapService->name.'»');

$form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
	'id' => 'model-form-form',
	'type' => 'horizontal',
	'enableAjaxValidation' => true,

));

echo $form->errorSummary($model);

echo $form->textFieldRow($model, 'name', array('class' => 'input-xxlarge',));

echo $form->textAreaRow($model, 'args', array(
	'class' => 'input-xxlarge',
	'hint' => 'Формат JSON, массив аргументов. Например:<br>
       <code>[{"summa": "1000"}, 3, [1,3,{"test": 4}]]</code> — передать первым аргументов объект со свойством summa равным 1000,
       вторым аргументом значение 3,
       третьим массив состоящий из трех элементов 1, 3, и объекта со свойством test и значением 4'
));

$this->widget('bootstrap.widgets.TbButton', array(
	'buttonType' => 'submit',
	'type' => 'primary',
	'label' => ($model->isNewRecord ? 'Добавить' : 'Сохранить'),
));

$this->endWidget();