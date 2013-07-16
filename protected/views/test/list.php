<?php
/**
 * @var $this                   TestController
 * @var $function               SoapFunction
 * @var $data                   array SoapTest[]
 * @var $runningTests           array
 */
?>

<script>
    var runningTests = <?= (empty($runningTests) ? CJSON::encode(array()) : CJSON::encode($runningTests)); ?>;
</script>

<?php
//Yii::app()->clientScript->registerCoreScript('jquery');
Yii::app()->clientScript->registerScriptFile('/static/js/tests/list.js');

$this->breadcrumbs = array(
    'Сервисы' => $this->createUrl('service/list'),
    'Функции' => $this->createUrl('function/list', array('service_id' => $function->soapService->id)),
    'Тесты'
);

echo CHtml::tag('h2', array(), 'Список тестов для функции «'.$function->name.'» сервиса «'.$function->soapService->name.'»');

//echo CHtml::tag('div', array('class' => 'alert_runtests hidden'),
//    CHtml::tag('div', array('class' => 'alert alert-info'), 'Есть запущенные тесты'));

$data = new CArrayDataProvider($data, array(
    'keyField' => 'id',
    'totalItemCount' => count($data),
    'pagination' => array(
        'pageSize' => 50,
    )
));

$this->widget('ext.bootstrap.widgets.TbGridView', array(
    'id' => 'grid-list-tests',
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
            'class' => implode(' ', array($class, 'row-test-id-'.$data['id'])),
            'data-test-id' => $data['id']
        );
    },
    'htmlOptions' => array(
        'data-function-id' => $function->primaryKey,
    ),
    'columns' => array(
        array(
            'header' => 'ID',
            'name' => 'id',
        ),
        array(
            'header' => 'Описание',
            'name' => 'name',
        ),
        array(
            'header' => 'Аргументы',
            'name' => 'args',
        ),
        array(
            'header'=> 'Время начала',
            'htmlOptions' => array(
                'class' => 'td-date-start'
            ),
            'value' => function($row) {
                if ($row['status'] == SoapTest::STATUS_TEST_STOP AND $row['date_start']){
                    return (Yii::app()->dateFormatter->format('dd MMMM yyyy HH:mm:ss',$row['date_start']));
                }
                return '---';
            }
        ),
        array(
            'header' => 'Время выполнения',
            'htmlOptions' => array(
                'class' => 'td-runtime'
            ),
            'value' => function($row) {
                if ($row['status'] == SoapTest::STATUS_TEST_STOP AND
                    ($row['test_result'] == SoapTest::TEST_RESULT_OK OR $row['test_result'] == SoapTest::TEST_RESULT_ERROR) AND
                    !is_null($row['runtime'])){
                    return $row['runtime'] . ' сек.';
                }
                return '---';
            }
        ),

        array(
            'header' => 'Результат',
            'type' => 'raw',
            'htmlOptions' => array(
                'class' => 'td-last-return'
            ),
            'value' => function($row){
                if ($row['status'] == SoapTest::STATUS_TEST_STOP){
                    return mb_strlen($row['last_return']) > 1000 ? mb_substr($row['last_return'], 0, 1000)."…" : $row['last_return'];
                }
                return '---';
            }
        ),
        array(
            'class' => 'bootstrap.widgets.TbButtonColumn',
            'header' => 'Действие',
            'template' => '{run} {update} {delete}',
            'deleteConfirmation' => false,
            'buttons' => array
            (
                'run' => array
                (
                    'label' => 'Запуск теста',
                    'icon' => 'icon-play-circle',
                    'visible' => function($i, $row){
                        return ($row['status'] == SoapTest::STATUS_TEST_STOP);
                    }
//                    'url' => 'Yii::app()->createUrl("tests/list", array("id"=>$data["id"]))',
                ),
                'update' => array
                (
                    'label' => 'Изменить аргументы',
                    'url' => 'Yii::app()->createUrl("test/update", array("id"=>$data["id"]))',
                ),
                'delete' => array
                (
                    'label' => 'Удалить тест',
                    'url' => 'Yii::app()->createUrl("test/delete", array("id"=>$data["id"]))',
                ),
            ),
        ),
    )
));

$this->widget('bootstrap.widgets.TbButton', array(
    'label' => 'Добавить тест',
    'url' => $this->createUrl('create', array('func_id' => $function->primaryKey)),
    'type' => 'success'
));
?>