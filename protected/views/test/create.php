<?php
/**
 * Форма создания Unit-теста для функции.
 * @var $this   TestController
 * @var $model  SoapTest
 */

$this->breadcrumbs = array(
    'Сервисы' => $this->createUrl('service/list'),
    'Функции' => $this->createUrl('function/list', array('id' => $model->soapFunction->soapService->primaryKey)),
    'Тесты' => $this->createUrl('test/list', array('id' => $model->soapFunction->id)),
    'Добавить тест',
);

echo CHtml::tag('h2', array(), 'Добавление теста к функции «'.$model->soapFunction->name.'» сервиса «'.$model->soapFunction->soapService->name.'»');

$this->renderPartial(
    'form',
    array(
        'model' => $model
    )
);