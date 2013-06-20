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

	<title><?php echo CHtml::encode($this->pageTitle); ?></title>
</head>
<?=$content?>
</html>
