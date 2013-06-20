<?php
/**
 * @var $this ArgumentsController
 * @var $model SoapFunctionArgs
 */

$this->breadcrumbs=array(
	'Сервисы'=>array('/servers'),
	'Функции'=>$this->createUrl('/functions/list', array('id' => $model->function->service->id)),
	'Аргументы' => $this->createUrl('/arguments/list', array('id' => $model->function->id)),
	'Добавить аргументы функции сервиса',
);
$this->pageTitle = 'Добавление аргумента функции «'.$model->function->name.'» сервиса «'.$model->function->service->name.'»';
?>
<h2><?=$this->pageTitle?></h2>

<?php $this->renderPartial('form', array('model' => $model)) ?>