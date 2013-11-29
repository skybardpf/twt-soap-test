<?php
/**
 * Controller is the customized base controller class.
 * All controller classes for this application should extend from this base class.
 */
class Controller extends CController
{
    /**
     * @var string the default layout for the controller view. Defaults to '//layouts/column1',
     * meaning using a single column layout. See 'protected/views/layouts/column1.php'.
     */
    public $layout = '//layouts/main';
    /**
     * @var array context menu items. This property will be assigned to {@link CMenu::items}.
     */
    public $menu = array();
    /**
     * @var array the breadcrumbs of the current page. The value of this property will
     * be assigned to {@link CBreadcrumbs::links}. Please refer to {@link CBreadcrumbs::links}
     * for more details on how to specify this property.
     */
    public $breadcrumbs = array();

    /**
     * @var string | null Путь к опубликованной папку assets.
     */
    private static $_baseAssets = null;

    /**
     * @return string Возвращает путь к опубликованной папке assets.
     */
    public function getBaseAssets(){
        if (self::$_baseAssets === null) {
            self::$_baseAssets = Yii::app()->assetManager->publish(
                Yii::app()->getBasePath() . '/static',
                false,
                -1,
                YII_DEBUG
            );
        }
        return self::$_baseAssets;
    }
}