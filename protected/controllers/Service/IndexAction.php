<?php
/**
 * Список действующих SOAP сервисов.
 *
 * @author Skibardin A.A. <skybardpf@artektiv.ru>
 *
 * @see SoapService
 */
class IndexAction extends CAction
{
    public function run()
	{
        /**
         * @var ServiceController $controller
         */
        $controller = $this->controller;
        $controller->pageTitle = Yii::app()->name . ' | '. Yii::t('app', 'Список SOAP сервисов');

//        $data = SoapService::getList();
        $data = SoapService::model()->findAll();

        $this->controller->render(
            'index',
            array(
                'data' => $data,
            )
        );
	}
}