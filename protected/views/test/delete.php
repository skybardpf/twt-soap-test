<?php
/**
 * Форма для удаления Unit-теста.
 * @var $this   TestController
 * @var $model  SoapTest
 */

$this->breadcrumbs=array(
	'Сервисы' => $this->createUrl('service/list'),
	'Функции' => $this->createUrl('function/list', array('service_id' => $model->soapFunction->soapService->primaryKey)),
	'Тесты' => $this->createUrl('test/list', array('func_id' => $model->soapFunction->primaryKey)),
	'Удаление теста',
);

echo 'Вы действительно хотите удалить тест «' . CHtml::encode($model->name) .'»?';

$form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
	'id' => 'model-delete-form',
	'type' => 'horizontal',
));

$this->widget('bootstrap.widgets.TbButton', array(
	'buttonType'=>'submit',
	'type'=>'danger',
	'label'=>'Да',
	'htmlOptions' => array('name' => 'result', 'value' => 'yes')
));

$this->widget('bootstrap.widgets.TbButton', array(
	'buttonType'=>'submit',
	'type'=>'success',
	'label'=>'Нет',
	'htmlOptions' => array('name' => 'result', 'value' => 'no')
));

$this->endWidget();