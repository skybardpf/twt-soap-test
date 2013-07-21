<?php
/**
 * Управление списком групп функций (@see GroupFunction) для определенного SOAP сервиса
 * (@see SoapService). Сервис определяется параметром $service_id.
 *
 * @author Skibardin A.A. <skybardpf@artektiv.ru>
 *
 * @see SoapService
 * @see SoapFunction
 */
class ListAction extends CAction
{
    /**
     * @param int $service_id
     * @throws CHttpException
     */
    public function run($service_id)
	{
        /**
         * @var $controller Group_functionsController
         */
        $controller = $this->controller;
        $controller->pageTitle .= 'Список групп';
        /**
         * @var $service SoapService
         */
        $service = $controller->loadService($service_id);
        /**
         * @var $data GroupFunctions[]
         */
        $data = $controller->getProviderData($service->primaryKey);

        $controller->render(
            'list',
            array(
                'data' => $data,
                'service' => $service,
            )
        );
	}
}