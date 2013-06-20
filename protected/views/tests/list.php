<?php
/**
 * @var $this TestsController
 * @var $data CActiveDataProvider
 * @var $service SoapService
 */

$this->breadcrumbs=array(
	'Сервисы'=>array('/servers'),
	'Тесты'
);
$this->pageTitle = 'Тесты сервиса «'.$service->name.'»';
?>
<h2><?=$this->pageTitle?></h2>

<?php $this->widget('bootstrap.widgets.TbAlert', array(
	'alerts'=>array( // configurations per alert type
		'error', // success, info, warning, error or danger
	),
)); ?>

<?php
$this->widget('ext.bootstrap.widgets.TbGridView', array(
	'type' => 'condensed striped',
	'dataProvider' => $data,
	'template' => "{pager}\n{items}\n{pager}",
	'ajaxUpdate' => false,
	'columns' => array(
		'id',
		'statusTitle',
		array('name' => 'date_create', 'value' => function(SoapTest $data) {return Yii::app()->dateFormatter->format('dd MMMM yyyy HH:mm',$data->date_create);}),
		array('name' => 'date_start', 'value' => function(SoapTest $data) {return Yii::app()->dateFormatter->format('dd MMMM yyyy HH:mm',$data->date_start);}),
		array('name' => 'date_end', 'value' => function(SoapTest $data) {return Yii::app()->dateFormatter->format('dd MMMM yyyy HH:mm',$data->date_end);}),
		'testsCount',
		'successCount',
		'warningCount',
		'errorCount',
		array(
			'type' => 'raw',
			'value' => function(CActiveRecord $data){
				return Yii::app()->controller->widget('bootstrap.widgets.TbButton', array(
					'label' => 'Детально',
					'url' => Yii::app()->controller->createUrl('detail', array('id' => $data->id)),
					'type' => 'info',
					'size' => 'small'
				), true);
			}
		),
	)
));
?>

<?php $this->widget('bootstrap.widgets.TbButton', array(
	'label' => 'Поставить тест в очередь',
	'url' => $this->createUrl('run', array('id' => $service->id)),
	'type' => 'success'
));?>