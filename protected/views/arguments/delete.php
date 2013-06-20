<?php
/**
 * @var $this ArgumentsController
 * @var $model SoapFunctionArgs
 */

$this->breadcrumbs=array(
	'Сервисы'=>array('/servers'),
	'Функции'=>$this->createUrl('/functions/list', array('id' => $model->function->service_id)),
	'Аргументы'=>$this->createUrl('/arguments/list', array('id' => $model->function_id)),
	'Удаление фукнции',
);
?>
Вы действительно хотите удалить фукнцию «<?=CHtml::encode($model->name)?>»?

<?php $form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
	'id'=>'model-delete-form',
	'type'=>'horizontal',
))?>
<?php $this->widget('bootstrap.widgets.TbButton', array(
	'buttonType'=>'submit',
	'type'=>'danger',
	'label'=>'Да',
	'htmlOptions' => array('name' => 'result', 'value' => 'yes')
)); ?>
<?php $this->widget('bootstrap.widgets.TbButton', array(
	'buttonType'=>'submit',
	'type'=>'success',
	'label'=>'Нет',
	'htmlOptions' => array('name' => 'result', 'value' => 'no')
)); ?>
<?php $this->endWidget(); ?>