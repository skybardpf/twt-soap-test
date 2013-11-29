<?php
/**
 * Список SOAP сервисов.
 *
 * @author Skibardin A.A. <skybardpf@artektiv.ru>
 *
 * @var ServiceController $this
 * @var SoapService[] $data
 * @var array $runningServiceTests
 */
?>

<script>
    window.runningServiceTests = <?= (empty($runningServiceTests) ? CJSON::encode(array()) : CJSON::encode($runningServiceTests)); ?>;
</script>

<?php
    Yii::app()->clientScript->registerScriptFile($this->getBaseAssets() . '/js/service/list.js');

    $this->breadcrumbs=array(
        'Сервисы' => array('/service'),
        'Список',
    );

    echo CHtml::tag('h2', array(), 'Список SOAP сервисов');

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
        'rowHtmlOptionsExpression' => function($row, $data){
            $class = 'warning';
            if ($data['status'] == SoapTest::STATUS_TEST_RUN){
                $class = 'info';
            } elseif ($data['test_result'] == SoapTest::TEST_RESULT_OK){
                $class = 'success';
            } else if ($data['test_result'] == SoapTest::TEST_RESULT_ERROR){
                $class  = 'error';
            }
            return array(
                'class' => implode(' ', array($class, 'row-service-id-'.$data['id'])),
                'data-service-id' => $data['id']
            );
        },
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
                'name' => 'url',
                'header' => 'URL',
                'type' => 'url'
            ),
            array(
                'name' => 'count_tests',
                'header' => 'Всего тестов'
            ),
            array(
                'header'=> 'Дата начала',
                'htmlOptions' => array(
                    'class' => 'td-date-start'
                ),
                'value' => function($row) {
                    if ($row['status'] == SoapTest::STATUS_TEST_STOP && $row['date_start']){
                        return (Yii::app()->dateFormatter->format('dd MMMM yyyy HH:mm:ss',$row['date_start']));
                    }
                    return '---';
                }
            ),
            array(
                'header'=> 'Время выполнения',
                'htmlOptions' => array(
                    'class' => 'td-runtime'
                ),
                'value' => function($row) {
                    if ($row['status'] == SoapTest::STATUS_TEST_STOP && !is_null($row['runtime'])){
                        return $row['runtime'] . ' сек.';
                    }
                    return '---';
                }
            ),
            array(
                'header'=> 'Результат',
                'htmlOptions' => array(
                    'class' => 'td-test-result-text'
                ),
                'name' => 'test_result_text'
            ),
            array(
                'header' => 'Действие',
                'class' => 'bootstrap.widgets.TbButtonColumn',
                'template' => '{run_tests} {view} {update} {delete}',
                'deleteConfirmation' => false,
                'buttons' => array
                (
                    'run_tests' => array
                    (
                        'visible' => function($ind, $row){
                            return ($row['count_tests'] > 0 && !$row['has_running_tests']);
                        },
                        'label' => 'Выполнить тесты',
                        'icon' => 'icon-play-circle',
    //                    'url' => 'Yii::app()->createUrl("function/run_tests", array("id"=>$data["id"]))',
                    ),
                    'view' => array
                    (
                        'label' => 'Просмотр функций',
                        'url' => 'Yii::app()->createUrl("function/list", array("service_id"=>$data["id"]))',
                    ),
                    'update' => array
                    (
                        'label' => 'Редактировать сервис',
                        'url' => 'Yii::app()->createUrl("service/update", array("id"=>$data["id"]))',
                    ),
                    'delete' => array
                    (
                        'label' => 'Удалить сервис',
                        'url' => 'Yii::app()->createUrl("service/delete", array("id"=>$data["id"]))',
                    ),
                ),
            ),
        )
    ));

    $this->widget('bootstrap.widgets.TbButton', array(
        'label' => 'Добавить сервис',
        'url' => $this->createUrl('create'),
        'type' => 'success'
    ));
?>