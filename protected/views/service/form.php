<?php
/**
 * @var ServiceController $this
 * @var SoapService $model
 *
 */

$title = Yii::t('app', ($model->isNewRecord ? 'Добавление' : 'Редактирование') . ' сервиса');
$this->breadcrumbs = array($title);

/**
 * @var TbActiveForm $form
 */
$form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
    'id' => 'model-form-form',
    'type' => 'horizontal',
    'enableAjaxValidation' => true,
));

echo CHtml::tag('h2', array(), $title . ($model->isNewRecord ?  '' : ' «'.CHtml::encode($model->name).'»'));

if ($model->hasErrors()){
    echo $form->errorSummary($model);
}

echo $form->textFieldRow($model, 'name', array('class' => 'input-xxlarge'));
echo $form->textFieldRow($model, 'url', array('class' => 'input-xxlarge'));
echo $form->textFieldRow($model, 'login', array('class' => 'input-xxlarge'));
echo $form->textFieldRow($model, 'password', array('class' => 'input-xxlarge'));

$this->widget('bootstrap.widgets.TbButton', array(
    'buttonType' => 'submit',
    'type' => 'primary',
    'label' => Yii::t('app', ($model->isNewRecord ? 'Добавить' : 'Сохранить')),
));
echo '&nbsp;&nbsp;';
$this->widget('bootstrap.widgets.TbButton', array(
    'url' => $this->createUrl('index'),
    'label' => Yii::t('app', 'Отмена'),
));

$this->endWidget();