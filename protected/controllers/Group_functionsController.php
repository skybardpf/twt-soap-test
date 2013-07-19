<?php
/**
 * Class Group_functionsController
 * @author Skibardin A.A. <skybardpf@artektiv.ru>
 *
 * Управление группами функций для определенного SOAP сервиса.
 */
class Group_functionsController extends Controller
{
	/**
	 * Declares class-based actions.
	 */
	public function actions()
	{
		return array(
            'create' => 'application.controllers.group_functions.CreateAction'
        );
	}

    /**
     * @param int $service_id
     * @return SoapService
     * @throws CHttpException
     */
    public function loadService($service_id)
    {
        $service = SoapService::model()->findByPk($service_id);
        if ($service === null) {
            throw new CHttpException(404, 'Не найден SOAP сервис.');
        }
        return $service;
    }

    /**
     * @param int $service_id
     * @return GroupFunctions
     */
    public function createModel($service_id)
    {
        $model = new GroupFunctions();
        $model->service_id = $service_id;
        return $model;
    }
}