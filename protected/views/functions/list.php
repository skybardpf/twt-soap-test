<?php
/**
 * @var $this FunctionsController
 * @var $data CActiveDataProvider
 * @var $service SoapService
 * @var int $count_running_tests
 */

$this->breadcrumbs=array(
	'Сервисы'=>array('/servers'),
	'Функции'
);
$this->pageTitle = 'Функции сервиса «'.$service->name.'»';
?>
<h2><?=$this->pageTitle?></h2>

<div class="alert alert-info">
	Чтобы обновить список функций, <a href="<?=$this->createUrl('servers/update', array('id' => $service->id))?>">отредактируйте сервис «<?=$service->name?>»</a>.
</div>

<?= CHtml::tag('div', array('class' => 'alert_runtests hidden'),
        CHtml::tag('div', array('class' => 'alert alert-info'), 'Есть запущенные тесты')
    );
?>

<?php
$this->widget('ext.bootstrap.widgets.TbGridView', array(
	'type' => 'condensed striped',
	'dataProvider' => $data,
	'template' => "{pager}\n{items}\n{pager}",
	'ajaxUpdate' => false,
	'columns' => array(
        'id',
        array('name' => 'name', 'header'=> 'Название функции'),
        array(
            'header'=> 'Всего тестов',
            'value' => function($row) {
                return (is_null($row['count']) ? 0 : $row['count']);
            }
        ),
//        array(
//            'header'=> 'Запущено тестов',
//            'value' => function($row) {
//                return (is_null($row['count_worked']) ? 0 : $row['count_worked']);
//            }
//        ),
        array(
            'header'=> 'Дата начала тестов',
            'type'  => 'raw',
            'value' => function($row) {
                $div = '---';
                if ($row['status'] == SoapTests::STATUS_TEST_STOP AND $row['date_start']){
                    $div = (Yii::app()->dateFormatter->format('dd MMMM yyyy HH:mm:ss',$row['date_start']));
                }
                return CHtml::tag('div', array('class' => 'date_start'), $div);
            }
        ),
        array(
            'header'=> 'Время выполнения тестов',
            'type'  => 'raw',
            'value' => function($row) {
                $div = '---';
                if ($row['status'] == SoapTests::STATUS_TEST_STOP AND
                    ($row['test_result'] == SoapTests::TEST_RESULT_OK OR $row['test_result'] == SoapTests::TEST_RESULT_ERROR) AND
                    !is_null($row['runtime'])){
                    $div = $row['runtime'] . ' сек.';
                }
                return CHtml::tag('div', array('class' => 'runtime'),$div);
            }
        ),
        array(
            'header'=> 'Результат тестов',
            'type'  => 'raw',
            'value' => function($row){
                if ($row['status'] == SoapTests::STATUS_TEST_RUN){
                    $div = 'Запущено';
//                    $running_tests[$row['id']] = $row['id'];
                } elseif ($row['test_result'] == SoapTests::TEST_RESULT_OK){
                    $div = 'Без ошибок';
                } elseif ($row['test_result'] == SoapTests::TEST_RESULT_ERROR){
                    $div = 'Ошибка';
                } else {
                    $div = 'Не выполнялось';
                }
                return CHtml::tag('div', array('class' => 'test_result'), $div);
            }
        ),

        array(
            'header'=> '',
            'type'  => 'raw',
            'value' => function($row){
                $class = 'runtestfunction hidden';
//                $icon_class = 'grid-view-loading';
                $icon_class = 'icon-th-list';
                if ($row['count'] AND $row['status'] == SoapTests::STATUS_TEST_STOP){
                    $class = 'runtestfunction';
                }
                return CHtml::tag('div', array('class' => $class),
                    CHtml::tag('div', array('class' => 'test_result hidden'), $row['test_result']) .
                    CHtml::tag('div', array('class' => 'status hidden'), $row['status']) .
                    CHtml::tag('div', array('class' => 'function_id hidden'), $row['id']) .
                    CHtml::tag('i', array('class'   => $icon_class))
                );
            }
        ),
		array(
            'header'=> '',
            'type'  => 'raw',
            'value' => function($row){
                return CHtml::link(
                    CHtml::tag('i', array('class' => 'icon-pencil')),
                    Yii::app()->controller->createUrl('arguments/list', array('id' => $row['id'])),
                    array('title' => 'Просмотреть тесты')
                );
            }
		),
//        array(
//            'header'=> '',
//            'type'  => 'raw',
//            'value' => function($row){
//                return CHtml::link(
//                    CHtml::tag('i', array('class' => 'icon-trash')),
//                    Yii::app()->controller->createUrl('functions/delete', array('id' => $row['id'])),
//                    array('title' => 'Удалить тесты')
//                );
//            }
//        ),
//        array(
//            'header'=> '',
//            'type'  => 'raw',
//            'value' => function(/*$row*/){
//                return CHtml::tag('i', array('class' => 'icon-trash'));
//            }
////			'template' => '{view}{delete}',
////			'class'=>'bootstrap.widgets.TbButtonColumn',
////			'viewButtonUrl' => function($row){
////                return $row['id'];
////            },
//////            'Yii::app()->controller->createUrl("arguments/list",array("id"=>function(){return $row["id"];}))',
////			'deleteConfirmation' => false,
//        ),

	)
));
?>
<script >
var is_running_tests = <?= $count_running_tests; ?>;
var running_tests = [];
var service_id = <?= $service->id; ?>;

function runtestsfunction(id, target_tr){
    $.ajax({
        type: 'POST',
        dataType: "json",
        url: '/tests/runtestfunction/id/'+id
    })
        .done(function(data, success) {
            if (success == 'success'){
                target_tr.find('.test_result').text(data.test_result_text);
                target_tr.find('.runtime').text(data.runtime + ' сек.');
                target_tr.find('.date_start').text(data.date_start);

                var cl = 'warning';
                if (data.test_result == 1){
                    cl = 'success';
                } else if (data.test_result == 3){
                    cl = 'error';
                }
                target_tr.removeClass();
                target_tr.addClass(cl);

                target_tr.find('.runtestfunction').removeClass('hidden');

                if (data.count > 0){
                    $('body .alert_runtests').removeClass('hidden');
                } else {
                    $('body .alert_runtests').addClass('hidden');
                }

            }
        })
        .fail(function() {
            console.log("error");
        })
        .always(function() {
//            target_tr.find('.runtestfunction').removeClass('hidden');
        })
    ;
}

function runtestservice(id){
    $.ajax({
        type: 'POST',
        dataType: "json",
        url: '/tests/runtestservice/id/'+id
    })
        .done(function(data, success) {
            if (success == 'success'){
                for(var i= 0, l = data.data.length; i<l; i++){
                    $('body .runtestfunction').each(function(ind){
                        $(this).removeClass('hidden');
                        var func_id = $(this).find('.function_id').text();
                        if (func_id == data.data[i].function_id){
                            var tr = $(this).parent('td').parent('tr');
                            tr.find('.test_result').text(data.data[i].test_result_text);
                            tr.find('.runtime').text(data.data[i].runtime + ' сек.');
                            tr.find('.date_start').text(data.data[i].date_start);

                            var cl = 'warning';
                            if (data.data[i].test_result == 1){
                                cl = 'success';
                            } else if (data.data[i].test_result == 3){
                                cl = 'error';
                            }
                            tr.removeClass();
                            tr.addClass(cl);
                        }
                    });
                }
                if (data.count > 0){
                    $('body .alert_runtests').removeClass('hidden');
                } else {
                    $('body .alert_runtests').addClass('hidden');
                }
            }
        })
        .fail(function() {
            console.log("error");
        })
        .always(function() {
//            target_tr.find('.runtestfunction').removeClass('hidden');
        })
    ;
}

function selecttestsfunction(ev){
    $.ajax({
        type: 'POST',
        dataType: "json",
        url: '/tests/selecttestfunction/id/'+ev.data.id
    })
        .done(function(data, success) {
            if (success == 'success' && data.selected){
                running_tests[ev.data.id] = true;

                var tr = $(ev.currentTarget).parent('td').parent('tr');
                tr.find('.test_result').text('Запущен');
                tr.find('.runtime').text('---');
                tr.find('.date_start').text('---');

                $('body .alert_runtests').removeClass('hidden');
                $(ev.currentTarget).addClass('hidden');
                tr.removeClass();
                tr.addClass('info');

                runtestsfunction(ev.data.id, tr);
            }
        })
        .fail(function() {
            $(ev.currentTarget).removeClass('hidden');
            console.log("error");
        })
//        .always(function() { alert("complete"); })
    ;
}

$('body .runtestfunction').each(function(){
    var func_id = $(this).find('.function_id').text();
    var tr = $(this).parent('td').parent('tr');
    var test_result = $(this).find('.test_result').text();
    var status = $(this).find('.status').text();

    var cl = 'warning';
    if (status == 1){
        cl = 'info';
    } else if (test_result == 1){
        cl = 'success';
    } else if (test_result == 3){
        cl = 'error';
    }
    tr.removeClass();
    tr.addClass(cl);

    $(this).on("click", { id: func_id }, selecttestsfunction);
});

if (is_running_tests){
    $('body .alert_runtests').removeClass('hidden');

    runtestservice(service_id);
} else {
    $('body .alert_runtests').addClass('hidden');
}
</script>