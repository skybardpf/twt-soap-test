<?php
/**
 * @var $this ArgumentsController
 * @var $model SoapFunctionArgs
 * @var $form TbActiveForm
 */
?>
<?php $form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
	'id'=>'model-form-form',
	'type'=>'horizontal',
	'enableAjaxValidation'=>true,

))?>
<?php echo $form->errorSummary($model); ?>

<?=$form->textFieldRow($model, 'name', array('class' => 'input-xxlarge',));?>

<?=$form->textAreaRow($model, 'args', array(
	'class' => 'input-xxlarge',
	'hint' => 'Формат JSON, массив аргументов. Например:<br>
       <code>[{"summa": "1000"}, 3, [1,3,{"test": 4}]]</code> — передать первым аргументов объект со свойством summa равным 1000,
       вторым аргументом значение 3,
       третьим массив состоящий из трех элементов 1, 3, и объекта со свойством test и значением 4'
)); ?>

<?php $this->widget('bootstrap.widgets.TbButton', array(
	'buttonType'=>'submit',
	'type'=>'primary',
	'label'=> ($model->isNewRecord ? 'Добавить' : 'Сохранить'),
)); ?>
<?php $this->endWidget(); ?>