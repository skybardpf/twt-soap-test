<?php
/**
 * @var $this                   ArgumentsController
 * @var $function               SoapFunction
 * @var $data                   CActiveDataProvider
 * @var int                     $count_running_tests
 */

Yii::app()->getClientScript()->registerCoreScript('jquery');

$this->breadcrumbs=array(
	'Сервисы'=>array('/servers'),
	'Функции' => $this->createUrl('/functions/list', array('id' => $function->service_id)),
	'Аргументы'
);
$this->pageTitle = 'Функции «'.$function->name.'» сервиса «'.$function->service->name.'»';
?>

<h2><?=$this->pageTitle?></h2>

<?php
    echo CHtml::tag('div', array('class' => 'alert_runtests hidden'),
    CHtml::tag('div', array('class' => 'alert alert-info'), 'Есть запущенные тесты'));
?>
<?php
$this->widget('ext.bootstrap.widgets.TbGridView', array(
	'type' => 'condensed striped',
	'dataProvider' => $data,
	'template' => "{pager}\n{items}\n{pager}",
	'ajaxUpdate' => false,
	'columns' => array(
		'id',
        array(
            'header'=> 'Описание',
            'name'  => 'name',
            'value' => function($row){
                return $row['name'];
            }
        ),
        array(
            'header'=> 'Дата начала теста',
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
            'header'=> 'Время выполнения теста',
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
            'header'=> 'Аргументы',
            'name'  => 'args',
            'value' => function($row){
                return $row['args'];
            }
        ),
		array(
			'header'=> 'Ответ',
            'type'  => 'raw',
			'name'  => 'last_return',
			'value' => function($row){
                $div = '---';
                if ($row['status'] == SoapTests::STATUS_TEST_STOP){
                    $div = mb_strlen($row['last_return']) > 1000 ? mb_substr($row['last_return'], 0, 1000)."…" : $row['last_return'];
                }
                return CHtml::tag('div', array('class' => 'last_return'), $div);
            }
		),
        array(
            'header'=> '',
            'type'  => 'raw',
            'value' => function($row){
//                return CHtml::link(
//                    CHtml::tag('i', array('class' => 'icon-th-list')),
//                    Yii::app()->controller->createUrl('tests/runtestfunction', array('id' => $row['id'])),
//                    array('title' => 'Запустить тест')
//                );
//                var_dump($row);die;
                $class = 'runtestfunction hidden';
                if ($row['status'] == SoapTests::STATUS_TEST_STOP){
                    $class = 'runtestfunction';
                }
                return CHtml::tag('div', array('class' => $class),
                    CHtml::tag('div', array('class' => 'test_result hidden'), $row['test_result']) .
                    CHtml::tag('div', array('class' => 'status hidden'), $row['status']) .
                    CHtml::tag('div', array('class' => 'test_id hidden'), $row['id']) .
                    CHtml::tag('i', array('class' => 'icon-th-list'))
                );
            }
        ),
        array(
            'header'=> '',
            'type'  => 'raw',
            'value' => function($row){
                return CHtml::link(
                    CHtml::tag('i', array('class' => 'icon-pencil')),
                    Yii::app()->controller->createUrl('arguments/update', array('id' => $row['id'])),
                    array('title' => 'Редактировать тест')
                );
            }
        ),
        array(
            'header'=> '',
            'type'  => 'raw',
            'value' => function($row){
                return CHtml::link(
                    CHtml::tag('i', array('class' => 'icon-trash')),
                    Yii::app()->controller->createUrl('arguments/delete', array('id' => $row['id'])),
                    array('title' => 'Удалить тест')
                );
            }
        ),
//		array(
//			'template' => '{update}{delete}',
//			'class'=>'bootstrap.widgets.TbButtonColumn',
////			'viewButtonUrl' => 'Yii::app()->controller->createUrl("arguments/list",array("id"=>$data->primaryKey))',
//			'viewButtonUrl' => 'function($row) {return $row["id"]; }',
//			'deleteConfirmation' => false,
//		),
	)
));
?>


<?php $this->widget('bootstrap.widgets.TbButton', array(
	'label' => 'Добавить аргументы',
	'url' => $this->createUrl('create', array('function_id' => Yii::app()->request->getParam('id'))),
	'type' => 'success'
));?>

<script>
    var count_running_tests = <?= $count_running_tests; ?>;
    var function_id = <?= $function->id; ?>;

    function runtest(id, target_tr){
        $.ajax({
            type: 'POST',
            dataType: "json",
            url: '/tests/runtest/id/'+id
        })
            .done(function(data, success) {
                if (success == 'success'){
                    target_tr.find('.test_result').text(data.test_result_text);
                    target_tr.find('.runtime').text(data.runtime + ' сек.');
                    target_tr.find('.date_start').text(data.date_start);
                    target_tr.find('.last_return').text(data.last_return);

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
                target_tr.find('.runtestfunction').removeClass('hidden');
            })
            .always(function() {
//            target_tr.find('.runtestfunction').removeClass('hidden');
            })
        ;
    }

    function runtestsfunction(id){
        $.ajax({
            type: 'POST',
            dataType: "json",
            url: '/tests/runtestfunction2/id/'+id
        })
            .done(function(data, success) {
                if (success == 'success'){
                    for(var i= 0, l = data.data.length; i<l; i++){
                        $('body .runtestfunction').each(function(){
                            $(this).removeClass('hidden');
                            var test_id = $(this).find('.test_id').text();
                            if (test_id == data.data[i].test_id){
                                var tr = $(this).parent('td').parent('tr');
                                tr.find('.test_result').text(data.data[i].test_result_text);
                                tr.find('.runtime').text(data.data[i].runtime + ' сек.');
                                tr.find('.date_start').text(data.data[i].date_start);
                                tr.find('.last_return').text(data.data[i].last_return);

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

    function selecttest(ev){
        $.ajax({
            type: 'POST',
            dataType: "json",
            url: '/tests/selecttest/id/'+ev.data.id
        })
            .done(function(data, success) {
                if (success == 'success' && data.selected){
//                    running_tests[ev.data.id] = true;

                    var tr = $(ev.currentTarget).parent('td').parent('tr');
                    tr.find('.test_result').text('Запущен');
                    tr.find('.runtime').text('---');
                    tr.find('.date_start').text('---');
                    tr.find('.last_return').text('---');

                    $('body .alert_runtests').removeClass('hidden');
                    $(ev.currentTarget).addClass('hidden');
                    tr.removeClass();
                    tr.addClass('info');

                    runtest(ev.data.id, tr);
                }
            })
            .fail(function() {
                console.log("error");
                $(ev.currentTarget).removeClass('hidden');
            })
//        .always(function() { alert("complete"); })
        ;
    }

    $('body .runtestfunction').each(function(){
        var test_id = $(this).find('.test_id').text();
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

        $(this).on("click", { id: test_id }, selecttest);
    });

    if (count_running_tests > 0){
        $('body .alert_runtests').removeClass('hidden');

        runtestsfunction(function_id);
    } else {
        $('body .alert_runtests').addClass('hidden');
    }
</script>