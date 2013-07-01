<?php
/**
 * @var $this Controller
 * @var $content string
 */
Yii::app()->bootstrap->registerAllCss();
Yii::app()->bootstrap->registerCoreScripts();
?>
<!DOCTYPE html>
<html lang="<?=Yii::app()->language?>">
<head>
	<meta name="language" content="<?=Yii::app()->language?>" />
	<meta charset="<?=Yii::app()->charset?>"/>

<!--    <script type='text/javascript' src='http://ajax.googleapis.com/ajax/libs/jquery/2.0.2/jquery.min.js'></script>-->

	<title><?php echo CHtml::encode($this->pageTitle); ?></title>
</head>
<?=$content?>
</html>
