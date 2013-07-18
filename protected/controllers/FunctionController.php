<?php
/**
 * Class FunctionController
 * @author Skibardin A.A. <skybardpf@artektiv.ru>
 *
 * Управление функциями.
 */
class FunctionController extends Controller
{
	public $defaultAction = 'list';

	public function actions()
	{
		return array(
            'list' => array(
                'class' => 'application.controllers.function.ListAction',
            ),
            'create' => array(
                'class' => 'application.controllers.function.CreateAction',
            ),
            'update' => array(
                'class' => 'application.controllers.function.UpdateAction',
            ),
            'run_tests' => array(
                'class' => 'application.controllers.function.Run_testsAction',
            ),
            'polling_run_tests' => array(
                'class' => 'application.controllers.function.Polling_run_testsAction',
            ),
            'delete_tests' => array(
                'class' => 'application.controllers.function.Delete_testsAction',
            ),
		);
	}

    /**
     * @param SoapService $service
     * @return SoapFunction
     */
    public function createModel(SoapService $service)
    {
        $model = new SoapFunction();
        $group = $service->getDefaultGroup();
        $model->group_id = $group->primaryKey;
        return $model;
    }

    /**
     * @param int $id
     * @return SoapFunction
     * @throws CHttpException
     */
    public function loadModel($id)
    {
        $model = SoapFunction::model()->findByPk($id);
        if ($model === null) {
            throw new CHttpException(404, 'Функция не найдена.');
        }
        return $model;
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
}