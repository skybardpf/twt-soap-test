<?php
/**
 * @var $this FunctionsController
 * @var $data CActiveDataProvider
 * @var $service SoapService
 */

$this->breadcrumbs=array(
	'Сервисы'=>array('/servers'),
	'Функции'
);
$this->pageTitle = 'Функции сервиса «'.$service->name.'»';
?>
<h2><?=$this->pageTitle?></h2>

<div class="alert alert-info">
	Чтобы обновить список функций, <a href="<?=$this->createUrl('servers/update', array('id' => $service->id))?>">отредактируйте сервис «<?=$service->name?>»</a>.
</div>

<?php
$this->widget('ext.bootstrap.widgets.TbGridView', array(
	'type' => 'condensed striped',
	'dataProvider' => $data,
	'template' => "{pager}\n{items}\n{pager}",
	'ajaxUpdate' => false,
	'columns' => array(
		'id',
		'name',
		'testCounts',
		array(
			'template' => '{view}{delete}',
			'class'=>'bootstrap.widgets.TbButtonColumn',
			'viewButtonUrl' => 'Yii::app()->controller->createUrl("arguments/list",array("id"=>$data->primaryKey))',
			'deleteConfirmation' => false,
		),
	)
));
?>