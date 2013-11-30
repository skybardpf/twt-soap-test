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

    public $pageTitle = 'SOAP Unit тесты';

	public function actions()
	{
		return array(
            'index' => 'application.controllers.Function.IndexAction',
            'create' => 'application.controllers.Function.CreateAction',
            'update' => 'application.controllers.Function.UpdateAction',
            'delete' => 'application.controllers.Function.DeleteAction',

            'run_tests' => 'application.controllers.Function.Run_testsAction',
            'polling_run_tests' => 'application.controllers.Function.Polling_run_testsAction',
            'delete_tests' => 'application.controllers.Function.Delete_testsAction',
            'add_param_field' => 'application.controllers.Function.AddParamFieldAction',
            'add_element_table' => 'application.controllers.Function.AddElementTableAction',
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