<?php
/**
 * @var $this TestsController
 * @var $test SoapTests
 */

$this->breadcrumbs=array(
	'Сервисы'=>array('/servers'),
	'Функции' => $this->createUrl('/functions/list', array('id' => $service_id)),
	'Отчет',
);
//$this->pageTitle = 'Отчет по тесту №'.$test->id.' сервиса «'.$test->service->name.'»';

?>
<h2><?=$this->pageTitle?></h2>

<?php
if ($count_test){
    echo $count_test;
	Yii::app()->clientScript->registerMetaTag('5', null, 'refresh');
?>
<div class="alert alert-info">
    Окно обновляется автоматически раз в 10 секунд, пока тест(ы) не завершится.
</div>
<?php
} else {
    $this->redirect($this->createUrl('functions/list', array('id' => $service_id)));
} ?>