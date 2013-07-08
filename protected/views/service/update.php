<?php
/**
 * @var $this ServiceController
 * @var $model SoapService
 */

$this->breadcrumbs=array(
	'Сервисы'=>array('/service'),
	'Редактировать сервис',
);
$this->pageTitle = 'Редактирование сервиса «'.$model->name.'»';
?>
<h2><?=$this->pageTitle?></h2>

<?php $this->renderPartial('form', array('model' => $model)) ?>