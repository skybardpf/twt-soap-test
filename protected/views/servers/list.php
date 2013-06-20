<?php
/**
 * @var $this ServersController
 * @var $data CActiveDataProvider
 */

$this->breadcrumbs=array(
	'Сервисы'=>array('/servers'),
	'Список',
);
$this->pageTitle = 'Список сервисов';
?>
<h2><?=$this->pageTitle?></h2>

<?php
$this->widget('ext.bootstrap.widgets.TbGridView', array(
	'type' => 'condensed striped',
	'dataProvider' => $data,
	'template' => "{pager}\n{items}\n{pager}",
	'ajaxUpdate' => false,
	'columns' => array(
		'id',
		'name',
		array('name' => 'url', 'type' => 'url'),
		'testsCount',
		array(
			'type' => 'raw',
			'value' => function(CActiveRecord $data){
				return CHtml::link(
					CHtml::tag('i', array('class' => 'icon-th-list')),
					Yii::app()->controller->createUrl('tests/list', array('id' => $data->primaryKey)),
					array('title' => 'Тесты')
				);
			}
		),
		array(
			'class'=>'bootstrap.widgets.TbButtonColumn',
			'viewButtonUrl' => 'Yii::app()->controller->createUrl("functions/list",array("id"=>$data->primaryKey))',
			'deleteConfirmation' => false,
		),
	)
));
?>

<?php $this->widget('bootstrap.widgets.TbButton', array(
	'label' => 'Добавить сервис',
	'url' => $this->createUrl('create'),
	'type' => 'success'
));?>