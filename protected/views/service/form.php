<?php
/**
 * @var $this   ServiceController
 * @var $model  SoapService
 * @var $form   TbActiveForm
 */
?>
<?php
$form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
	'id'=>'model-form-form',
	'type'=>'horizontal',
	'enableAjaxValidation'=>true,

));

echo $form->errorSummary($model);

echo $form->textFieldRow($model,'name', array('class' => 'input-xxlarge'));
echo $form->textFieldRow($model,'url', array('class' => 'input-xxlarge'));
echo $form->textFieldRow($model,'login', array('class' => 'input-xxlarge'));
echo $form->textFieldRow($model,'password', array('class' => 'input-xxlarge'));

$this->widget('bootstrap.widgets.TbButton', array(
    'buttonType'=>'submit',
    'type'=>'primary',
    'label'=> ($model->isNewRecord ? 'Добавить' : 'Сохранить'),
));

$this->endWidget();
?>