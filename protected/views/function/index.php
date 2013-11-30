<?php
/**
 * @var FunctionController $this
 * @var SoapFunction[] $data
 * @var SoapService $service
 * @var array $runningFuncTests
 */
?>

<script>
    window.runningFuncTests = <?= (empty($runningFuncTests) ? CJSON::encode(array()) : CJSON::encode($runningFuncTests)); ?>;
</script>

<?php
    Yii::app()->clientScript->registerScriptFile($this->getBaseAssets() . '/js/function/list.js');

    $this->breadcrumbs=array(
        'Группы функций' => $this->createUrl('/group_functions/list', array('service_id' => $service->primaryKey)),
        'Функции'
    );

    echo '<h2>Функции сервиса «'.$service->name.'»</h2>';

    $this->widget('bootstrap.widgets.TbButton', array(
        'buttonType' => 'link',
        'type' => 'success',
        'label' => 'Добавить функцию',
        'url' => $this->createUrl("create", array('service_id' => $service->primaryKey)),
    ));
    echo '&nbsp;';
    $this->widget('bootstrap.widgets.TbButton', array(
        'buttonType' => 'link',
        'type' => 'success',
        'label' => 'Управление группами функций',
        'url' => $this->createUrl("group_functions/list", array('service_id' => $service->primaryKey)),
    ));

    $data = new CArrayDataProvider($data, array(
        'keyField' => 'id',
        'totalItemCount' => count($data),
        'pagination' => array(
            'pageSize' => 50,
        )
    ));

    $this->widget('ext.groupgridview.BootGroupGridView', array(
        'id' => 'grid-list-functions',
        'itemsCssClass' => 'table table-bordered table-condensed',
        'dataProvider' => $data,
        'extraRowColumns' => array('group_name'),
//        'mergeColumns' => array('login_date'),
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
                'class' => implode(' ', array($class, 'row-function-id-'.$data['id'])),
                'data-function-id' => $data['id']
            );
        },
        'htmlOptions' => array(
            'data-service-id' => $service->primaryKey,
        ),
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
                'name' => 'count_tests',
                'header' => 'Всего тестов',
            ),
            array(
                'header'=> 'Дата начала тестов',
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
                'header'=> 'Время выполнения тестов',
                'htmlOptions' => array(
                    'class' => 'td-runtime'
                ),
                'value' => function($row) {
                    if ($row['status'] == SoapTest::STATUS_TEST_STOP &&
                        ($row['test_result'] == SoapTest::TEST_RESULT_OK || $row['test_result'] == SoapTest::TEST_RESULT_ERROR) && !is_null($row['runtime'])){
                        return $row['runtime'] . ' сек.';
                    }
                    return '---';
                }
            ),
            array(
                'header'=> 'Результат тестов',
                'htmlOptions' => array(
                    'class' => 'td-test-result-text'
                ),
                'name' => 'test_result_text'
            ),
            array(
                'class'=> 'bootstrap.widgets.TbButtonColumn',
                'header' => 'Функция',
                'template'=>'{update} {delete}',
                'deleteConfirmation' => false,
                'buttons' => array(
                    'update' => array
                    (
                        'label' => 'Редактирование параметров функции',
                        'url' => 'Yii::app()->createUrl("function/update", array("id"=>$data["id"]))',
                    ),
//                    'view' => array
//                    (
//                        'label' => 'Просмотр функции',
//                        'url' => 'Yii::app()->createUrl("function/view", array("id"=>$data["id"]))',
//                    ),
                    'delete' => array
                    (
                        'label' => 'Удалить функцию',
                        'url' => 'Yii::app()->createUrl("function/delete", array("id"=>$data["id"]))',
                    ),
                ),
            ),
            array(
                'class'=> 'bootstrap.widgets.TbButtonColumn',
                'header' => 'Тесты',
                'template'=>'{run_tests} {view}',
                'deleteConfirmation' => false,
                'buttons' => array(
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
                        'label' => 'Просмотр тестов',
                        'url' => 'Yii::app()->createUrl("test/list", array("func_id"=>$data["id"]))',
                    ),
                ),
            ),
        ),
    ));
?>