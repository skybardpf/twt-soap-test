<?php
/**
 * @var $this ServiceController
 * @var $model SoapService
 */

$this->breadcrumbs=array(
	'Сервисы'=>array('/service'),
	'Добавить',
);
$this->pageTitle = 'Добавление сервиса';
?>
<h2><?=$this->pageTitle?></h2>

<?php $this->renderPartial('form', array('model' => $model)) ?>