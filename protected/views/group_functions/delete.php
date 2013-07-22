<?php
    /**
     * @var $this       Group_functionsController
     * @var $model      GroupFunctions
     * @var $service    SoapService
     */

    $this->breadcrumbs = array(
        'Сервисы' => $this->createUrl('service/list'),
        'Список функций' => $this->createUrl('/function/list', array('service_id' => $model->soapService->primaryKey)),
        'Список групп' => $this->createUrl('/group_functions/list', array('service_id' => $model->soapService->primaryKey)),
        'Удалить группу'
    );

    echo 'Вы действительно хотите удалить сервис «'.CHtml::encode($model->name).'»?';

    $form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
        'id' => 'model-delete-form',
        'type' => 'horizontal',
    ));

    $this->widget('bootstrap.widgets.TbButton', array(
        'buttonType' => 'submit',
        'type' => 'danger',
        'label' => 'Да',
        'htmlOptions' => array('name' => 'result', 'value' => 'yes')
    ));

    $this->widget('bootstrap.widgets.TbButton', array(
        'buttonType' => 'submit',
        'type' => 'success',
        'label' => 'Нет',
        'htmlOptions' => array('name' => 'result', 'value' => 'no')
    ));

    $this->endWidget();