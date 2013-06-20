<?php
/**
 * @var $this ServersController
 * @var $model SoapService
 */

$this->breadcrumbs=array(
	'Сервисы'=>array('/servers'),
	'Добавить',
);
$this->pageTitle = 'Добавление сервиса';
?>
<h2><?=$this->pageTitle?></h2>

<?php $this->renderPartial('form', array('model' => $model)) ?>