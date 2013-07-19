<?php
/**
 * @var $this               FunctionController
 * @var $model              SoapFunction
 * @var $is_function_delete boolean
 * @var $service_id         integer
 */

    $this->breadcrumbs=array(
        'Сервисы'=>array('/service'),
        'Функции'=>$this->createUrl('/functions/list', array('id' => $service_id)),
        'Удаление фукнции',
    );

    echo 'Вы действительно хотите удалить '.($is_function_delete ? 'функцию ' : 'все тесты для фунции ') .'«'.CHtml::encode($model->name).'»?';

    $form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
        'id'=>'model-delete-form',
        'type'=>'horizontal',
    ));

    $this->widget('bootstrap.widgets.TbButton', array(
        'buttonType'=>'submit',
        'type'=>'danger',
        'label'=>'Да',
        'htmlOptions' => array('name' => 'result', 'value' => 'yes')
    ));

    $this->widget('bootstrap.widgets.TbButton', array(
        'buttonType'=>'submit',
        'type'=>'success',
        'label'=>'Нет',
        'htmlOptions' => array('name' => 'result', 'value' => 'no')
    ));
    $this->endWidget();
