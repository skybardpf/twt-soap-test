<?php
/**
 * Список SOAP сервисов.
 *
 * @author Skibardin A.A. <skybardpf@artektiv.ru>
 *
 * @var $this       Group_functionsController
 * @var $data       GroupFunctions[]
 * @var $service    SoapService
 */
//    Yii::app()->clientScript->registerScriptFile($this->getStaticAssets() . '/js/service/list.js');

    $this->breadcrumbs=array(
        'Сервисы' => $this->createUrl('/service/list'),
        'Список функций' => $this->createUrl('/function/list', array('service_id' => $service->primaryKey)),
        'Группы функций'
    );

    echo CHtml::tag('h2', array(), 'Список групп функций сервиса «'.$service->name.'»');

    $data = new CArrayDataProvider($data, array(
        'keyField' => 'id',
        'totalItemCount' => count($data),
        'pagination' => array(
            'pageSize' => 50,
        )
    ));

    $this->widget('ext.bootstrap.widgets.TbGridView', array(
        'id' => 'grid-list-services',
        'type' => 'condensed striped',
        'dataProvider' => $data,
        'template' => "{pager}\n{items}\n{pager}",
        'ajaxUpdate' => false,
        'columns' => array(
            array(
                'name' => 'id',
                'header' => 'ID',
            ),
            array(
                'name' => 'name',
                'header' => 'Название'
            ),
            array(
                'header' => 'Действие',
                'class' => 'bootstrap.widgets.TbButtonColumn',
                'template' => '{update} {delete}',
                'deleteConfirmation' => false,
                'buttons' => array
                (
                    'update' => array
                    (
                        'visible' => function($index, $data){
                            return ($data['name'] != GroupFunctions::GROUP_NAME_DEFAULT);
                        },
                        'label' => 'Редактировать группу',
                        'url' => 'Yii::app()->createUrl("group_functions/update", array("id"=>$data["id"]))',
                    ),
                    'delete' => array
                    (
                        'visible' => function($index, $data){
                            return ($data['name'] != GroupFunctions::GROUP_NAME_DEFAULT);
                        },
                        'label' => 'Удалить группу',
                        'url' => 'Yii::app()->createUrl("group_functions/delete", array("id"=>$data["id"]))',
                    ),
                ),
            ),
        )
    ));

    $this->widget('bootstrap.widgets.TbButton', array(
        'label' => 'Добавить группу',
        'url' => $this->createUrl('create', array('service_id' => $service->primaryKey)),
        'type' => 'success'
    ));
?>