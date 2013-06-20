<?php
/**
 * @var $this TestsController
 * @var $test SoapTest
 */

$this->breadcrumbs=array(
	'Сервисы'=>array('/servers'),
	'Тесты' => $this->createUrl('/tests/list', array('id' => $test->service_id)),
	'Отчет',
);
$this->pageTitle = 'Отчет по тесту №'.$test->id.' сервиса «'.$test->service->name.'»';

?>
<h2><?=$this->pageTitle?></h2>

<?php
if ($test->status != 4):
	Yii::app()->clientScript->registerMetaTag('10', null, 'refresh');
?>
	<div class="alert alert-info">
		Окно обновляется автоматически раз в 10 секунд, пока тест не завершится.
	</div>
<?php endif ?>

<table class="table table-striped table-hover table-condensed">
	<tr>
		<th><?=$test->getAttributeLabel('statusTitle')?></th>
		<td><?=$test->statusTitle?></td>
	</tr>
	<tr>
		<th><?=$test->getAttributeLabel('date_create')?></th>
		<td><?=Yii::app()->dateFormatter->format('dd MMMM yyyy HH:mm',$test->date_create)?></td>
	</tr>
	<tr>
		<th><?=$test->getAttributeLabel('date_start')?></th>
		<td><?=Yii::app()->dateFormatter->format('dd MMMM yyyy HH:mm:ss',$test->date_start)?></td>
	</tr>
	<tr>
		<th><?=$test->getAttributeLabel('date_end')?></th>
		<td><?=Yii::app()->dateFormatter->format('dd MMMM yyyy HH:mm:ss',$test->date_end)?></td>
	</tr>
	<tr>
		<th>Всего тестов</th>
		<td><?=$test->service->testsCount ?: '—'?></td>
	</tr>
	<tr class="info">
		<th><?=$test->getAttributeLabel('testsCount')?></th>
		<td><?=$test->testsCount ?: '—'?></td>
	</tr>
	<tr <?=$test->successCount ? 'class="success"' : ''?>>
		<th><?=$test->getAttributeLabel('successCount')?></th>
		<td><?=$test->successCount ?: '—'?></td>
	</tr>
	<tr <?=$test->warningCount ? 'class="warning"' : ''?>>
		<th><?=$test->getAttributeLabel('warningCount')?></th>
		<td><?=$test->warningCount ?: '—'?></td>
	</tr>
	<tr <?=$test->errorCount ? 'class="error"' : ''?>>
		<th><?=$test->getAttributeLabel('errorCount')?></th>
		<td><?=$test->errorCount ?: '—'?></td>
	</tr>
</table>

<h3>Тесты</h3>

<table class="table table-striped table-condensed table-hover">
	<tr>
		<th>Время</th>
		<th>Функция</th>
		<th>Имя аргументов</th>
		<th>Аргументы</th>
		<th>Результат</th>
	</tr>
	<?php foreach ($test->soapTestResults as $tr): ?>
	<tr class="<?=$tr->result != -1 ? ($tr->result == 1 ? 'success' : 'warning' ) : 'error'?>">
		<td><?=Yii::app()->dateFormatter->format('HH:mm:ss', $tr->date)?></td>
		<td><?=CHtml::encode($tr->functionArgs->function->name)?></td>
		<td><?=CHtml::encode($tr->functionArgs->name)?></td>
		<td><?=CHtml::encode($tr->functionArgs->args)?></td>
		<td><?=CHtml::encode(mb_strlen($tr->functionArgs->return) > 1000 ? mb_substr($tr->functionArgs->return, 0, 1000).'…' : $tr->functionArgs->return)?></td>
	</tr>
	<?php endforeach;?>
</table>