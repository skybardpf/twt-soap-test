<?php
/**
 * @var $this ServersController
 * @var $model SoapService
 * @var $form TbActiveForm
 */
?>
<?php $form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
	'id'=>'model-form-form',
	'type'=>'horizontal',
	'enableAjaxValidation'=>true,

))?>
<?php echo $form->errorSummary($model); ?>

<?=$form->textFieldRow($model,'name', array('class' => 'input-xxlarge')); ?>
<?=$form->textFieldRow($model,'url', array('class' => 'input-xxlarge')); ?>
<?=$form->textFieldRow($model,'login', array('class' => 'input-xxlarge')); ?>
<?=$form->textFieldRow($model,'password', array('class' => 'input-xxlarge')); ?>

<?php $this->widget('bootstrap.widgets.TbButton', array(
	'buttonType'=>'submit',
	'type'=>'primary',
	'label'=> ($model->isNewRecord ? 'Добавить' : 'Сохранить'),
)); ?>
<?php $this->endWidget(); ?>