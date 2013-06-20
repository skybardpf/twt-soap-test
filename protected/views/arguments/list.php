<?php
/**
 * @var $this ArgumentsController
 * @var $function SoapFunction
 * @var $data CActiveDataProvider
 */

$this->breadcrumbs=array(
	'Сервисы'=>array('/servers'),
	'Функции' => $this->createUrl('/functions/list', array('id' => $function->service_id)),
	'Аргументы'
);
$this->pageTitle = 'Функции «'.$function->name.'» сервиса «'.$function->service->name.'»';
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
		'args',
		array(
			'name' => 'return',
			'value' => 'mb_strlen($data->return) > 1000 ? mb_substr($data->return, 0, 1000)."…" : $data->return'
		),
		array(
			'template' => '{update}{delete}',
			'class'=>'bootstrap.widgets.TbButtonColumn',
//			'viewButtonUrl' => 'Yii::app()->controller->createUrl("arguments/list",array("id"=>$data->primaryKey))',
			'deleteConfirmation' => false,
		),
	)
));
?>


<?php $this->widget('bootstrap.widgets.TbButton', array(
	'label' => 'Добавить аргументы',
	'url' => $this->createUrl('create', array('function_id' => Yii::app()->request->getParam('id'))),
	'type' => 'success'
));?>