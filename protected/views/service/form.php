<?php
    /**
     * @var $this   ServiceController
     * @var $model  SoapService
     * @var $form   TbActiveForm
     */

    $this->breadcrumbs=array(
        'Сервисы' => $this->createUrl('/service/list'),
        ($model->isNewRecord ? 'Добавление' : 'Редактирование'). ' сервиса',
    );

    $form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
        'id' => 'model-form-form',
        'type' => 'horizontal',
        'enableAjaxValidation' => true,

    ));

    echo CHtml::tag('h2', array(), ($model->isNewRecord ? 'Добавление сервиса' : 'Редактирование сервиса «'.$model->name.'»'));

    echo $form->errorSummary($model);

    echo $form->textFieldRow($model,'name', array('class' => 'input-xxlarge'));
    echo $form->textFieldRow($model,'url', array('class' => 'input-xxlarge'));
    echo $form->textFieldRow($model,'login', array('class' => 'input-xxlarge'));
    echo $form->textFieldRow($model,'password', array('class' => 'input-xxlarge'));

    $this->widget('bootstrap.widgets.TbButton', array(
        'buttonType' => 'submit',
        'type' => 'primary',
        'label' => ($model->isNewRecord ? 'Добавить' : 'Сохранить'),
    ));

    $this->endWidget();