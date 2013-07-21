<?php
/**
 * Class Group_functionsController
 * @author Skibardin A.A. <skybardpf@artektiv.ru>
 *
 * Управление группами функций для определенного SOAP сервиса.
 */
class Group_functionsController extends Controller
{
    public $pageTitle = 'SOAP Unit тесты | ';

    /**
	 * Declares class-based actions.
	 */
	public function actions()
	{
		return array(
            'create' => 'application.controllers.group_functions.CreateAction',
            'update' => 'application.controllers.group_functions.UpdateAction',
            'delete' => 'application.controllers.group_functions.DeleteAction',
            'list' => 'application.controllers.group_functions.ListAction'
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
     * @param SoapService $service
     * @return GroupFunctions
     */
    public function createModel(SoapService $service)
    {
        $model = new GroupFunctions();
        $model->service_id = $service->primaryKey;
        return $model;
    }

    /**
     * @param integer $id
     * @return GroupFunctions
     * @throws CHttpException
     */
    public function loadModel($id)
    {
        $model = GroupFunctions::model()->findByPk($id);
        if ($model === null) {
            throw new CHttpException(404, 'Не найдена группа.');
        }
        return $model;
    }

    /**
     * @param int $service_id
     * @return GroupFunctions
     */
    public function getProviderData($service_id)
    {
        $data = GroupFunctions::model()->findAll(
            array(
                'condition' => 'service_id=:service_id',
                'params' => array(
                    ':service_id' => $service_id
                )
            )
        );
        return $data;
    }
}