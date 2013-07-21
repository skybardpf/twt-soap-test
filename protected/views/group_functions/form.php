<?php
    /**
     * @var $this       Group_functionsController
     * @var $model      GroupFunctions
     * @var $form       TbActiveForm
     */

    $this->breadcrumbs = array(
        'Сервисы' => $this->createUrl('service/list'),
        'Список функций' => $this->createUrl('/function/list', array('service_id' => $model->soapService->primaryKey)),
        'Список групп' => $this->createUrl('/group_functions/list', array('service_id' => $model->soapService->primaryKey)),
        'Добавить группу'
    );

    echo CHtml::tag('h2', array(), ($model->isNewRecord ? 'Добавление' : 'Редактирование').' группы для сервиса «'.$model->soapService->name.'»');

    $form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
        'id' => 'model-form-form',
        'type' => 'horizontal',
        'enableAjaxValidation' => true,
    ));

    echo $form->errorSummary($model);

    echo $form->textFieldRow($model,'name', array('class' => 'input-xxlarge'));

    $this->widget('bootstrap.widgets.TbButton', array(
        'buttonType' => 'submit',
        'type' => 'primary',
        'label' => ($model->isNewRecord ? 'Добавить' : 'Сохранить'),
    ));

    $this->endWidget();