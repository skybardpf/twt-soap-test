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

Yii::app()->clientScript->registerScriptFile($this->getBaseAssets() . '/js/item_delete.js');
Yii::app()->clientScript->registerScriptFile($this->getBaseAssets() . '/js/service/list.js');
?>
    <script>
        window.runningServiceTests = <?= (empty($runningServiceTests) ? CJSON::encode(array()) : CJSON::encode($runningServiceTests)); ?>;
    </script>
<?php

$title = Yii::t('app', 'Список SOAP сервисов');
$this->breadcrumbs = array($title);

echo CHtml::tag('h2', array(), $title);

$data = new CArrayDataProvider($data, array(
    'keyField' => 'id',
    'totalItemCount' => count($data),
    'pagination' => array(
        'pageSize' => 50,
    )
));

/**
 * Кнопка создания нового сервиса
 */
$button_create = $this->widget('bootstrap.widgets.TbButton', array(
        'label' => Yii::t('app', 'Добавить сервис'),
        'url' => $this->createUrl('update'),
        'type' => 'success'
    ),
    true
);
echo '<br/>' . $button_create;

//CHtml::link()
$this->widget('ext.bootstrap.widgets.TbGridView', array(
    'id' => 'grid-list-services',
    'type' => 'condensed striped',
    'dataProvider' => $data,
    'template' => "{pager}\n{items}\n{pager}",
//    'ajaxUpdate' => false,
//    'rowHtmlOptionsExpression' => function ($row, $data) {
//        $class = 'warning';
//        if ($data['status'] == SoapTest::STATUS_TEST_RUN) {
//            $class = 'info';
//        } elseif ($data['test_result'] == SoapTest::TEST_RESULT_OK) {
//            $class = 'success';
//        } else if ($data['test_result'] == SoapTest::TEST_RESULT_ERROR) {
//            $class = 'error';
//        }
//        return array(
//            'class' => implode(' ', array($class, 'row-service-id-' . $data['id'])),
//            'data-service-id' => $data['id']
//        );
//    },
    'columns' => array(
        array(
            'name' => 'id',
            'header' => 'ID',
        ),
        array(
//            'name' => 'name',
            'header' => Yii::t('app', 'Название'),
            "type" => 'raw',
            'value' => 'CHtml::link($data["name"],
                Yii::app()->createUrl(
                    "service/update", array(
                        "id" => $data["id"]
                    )
                )
            )',
        ),
//        array(
//            'name' => 'url',
//            'header' => Yii::t('app', 'URL'),
//        ),
        array(
            'name' => 'count_tests',
            'header' => Yii::t('app', 'Кол-во тестов'),
        ),
//        array(
//            'header' => 'Дата начала',
//            'htmlOptions' => array(
//                'class' => 'td-date-start'
//            ),
//            'value' => function ($row) {
//                if ($row['status'] == SoapTest::STATUS_TEST_STOP && $row['date_start']) {
//                    return (Yii::app()->dateFormatter->format('dd MMMM yyyy HH:mm:ss', $row['date_start']));
//                }
//                return '---';
//            }
//        ),
//        array(
//            'header' => 'Время выполнения',
//            'htmlOptions' => array(
//                'class' => 'td-runtime'
//            ),
//            'value' => function ($row) {
//                if ($row['status'] == SoapTest::STATUS_TEST_STOP && !is_null($row['runtime'])) {
//                    return $row['runtime'] . ' сек.';
//                }
//                return '---';
//            }
//        ),
//        array(
//            'header' => 'Результат',
//            'htmlOptions' => array(
//                'class' => 'td-test-result-text'
//            ),
//            'name' => 'test_result_text'
//        ),
        array(
            'header' => Yii::t('app', 'Функции'),
            'type' => 'raw',
            'value' => 'CHtml::link(
                Yii::t("app", "Функции"),
                Yii::app()->createUrl("function/index", array("sid" => $data["id"]))
            )',
        ),
        array(
            'header' => Yii::t('app', 'Удалить'),
            'type' => 'raw',
            'value' => 'CHtml::link(
                Yii::t("app", "Удалить"),
                "#",
                array(
                    "class" => "delete-item-element",
                    "data-url" => Yii::app()->createUrl("service/delete", array("id" => $data["id"])),
                    "data-redirect_url" => Yii::app()->createUrl("service/index"),
                    "data-question" => Yii::t("app", "Удалить SOAP сервис и все связанные с ним функции и тесты?"),
                    "data-title" => Yii::t("app", "Удалить SOAP сервис?"),
                )
            )',
        ),
    )
));

echo $button_create;