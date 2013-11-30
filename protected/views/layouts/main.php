<?php
/**
 * @var Controller $this
 * @var string $content
 */
Yii::app()->bootstrap->registerAllCss();
Yii::app()->bootstrap->registerCoreScripts();

$clientScript = Yii::app()->clientScript;
$clientScript->registerCoreScript('jquery.ui');
$clientScript->registerCssFile(
    $clientScript->getCoreScriptUrl().'/jui/css/base/jquery-ui.css'
);
$this->widget('ext.widgets.loading.LoadingWidget');

?>
<!DOCTYPE html>
<html lang="<?= Yii::app()->language ?>">
<head>
    <meta name="language" content="<?= Yii::app()->language; ?>"/>
    <meta charset="<?= Yii::app()->charset; ?>"/>
    <title><?= CHtml::encode($this->pageTitle); ?></title>
</head>
<body>

<header class="container">
    <?php
    if (isset($this->breadcrumbs)) {
        $this->widget('bootstrap.widgets.TbBreadcrumbs', array(
            'homeLink' => CHtml::link(
                Yii::t('app', 'Сервисы'),
                Yii::app()->createAbsoluteUrl('/')
            ),
            'links' => $this->breadcrumbs,
        ));
    }
    ?>
</header>

<section class="container">
    <?= $content; ?>
</section>

<footer class="container">
    <hr>
    <?= '© ' . Yii::app()->name . ', ' . date('Y'); ?>
</footer>

</body>
</html>
