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

    private $_static_assets = null;

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
            'delete' => array(
                'class' => 'application.controllers.function.DeleteAction',
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

            'add_param_field' => array(
                'class' => 'application.controllers.function.AddParamFieldAction',
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

    /**
     * Делаем предварительную настройку.
     * @param CAction $action
     * @return boolean
     */
    protected function beforeAction($action)
    {
        if ($this->_static_assets === null){
            $this->_static_assets = Yii::app()->assetManager->publish(
                Yii::app()->getBasePath().'/static',
                false,
                -1,
                YII_DEBUG
            );
        }
        return parent::beforeAction($action);
    }

    /**
     * @return string Путь к опубликованным данным.
     */
    public function getStaticAssets()
    {
        return $this->_static_assets;
    }
}