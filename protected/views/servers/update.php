<?php
/**
 * @var $this ServersController
 * @var $model SoapService
 */

$this->breadcrumbs=array(
	'Сервисы'=>array('/servers'),
	'Редактировать сервис',
);
$this->pageTitle = 'Редактирование сервиса «'.$model->name.'»';
?>
<h2><?=$this->pageTitle?></h2>

<?php $this->renderPartial('form', array('model' => $model)) ?>