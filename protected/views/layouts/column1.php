<?php
/**
 * @var $this Controller
 */
?>
<?php $this->beginContent('//layouts/main'); ?>
<body>
<header class="container">
	<?php if(isset($this->breadcrumbs)) {
		$this->widget('bootstrap.widgets.TbBreadcrumbs', array(
			'links'=>$this->breadcrumbs,
		));
	}?>
</header>
<section class="container">
	<?php echo $content; ?>
</section>
<footer class="container">
	<hr>
	© <?=Yii::app()->name?>, <?=date('Y')?>
</footer>
</body>
<?php $this->endContent(); ?>
