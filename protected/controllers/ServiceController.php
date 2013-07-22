<?php
/**
 * Class ServiceController.
 * Управление SOAP сервисами.
 *
 * @author Skibardin A.A. <skybardpf@artektiv.ru>
 */
class ServiceController extends Controller
{
    public $defaultAction = 'list';

    private $_static_assets = null;

    public $pageTitle = 'SOAP Unit тесты';

	public function actions()
	{
		return array(
			'create'=>array(
				'class' => 'application.controllers.service.CreateAction',
			),
			'update' => array(
				'class' => 'application.controllers.service.UpdateAction',
			),
			'delete' => array(
				'class' => 'application.controllers.service.DeleteAction',
			),
            'list' => array(
                'class' => 'application.controllers.service.ListAction',
            ),
            'run_tests' => array(
                'class' => 'application.controllers.service.Run_testsAction',
            ),
            'polling_run_tests' => array(
                'class' => 'application.controllers.service.Polling_run_testsAction',
            ),
		);
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

    /**
     * @param integer $service_id
     * @return SoapService
     * @throws CHttpException
     */
    public function loadModel($service_id)
    {
        $service = SoapService::model()->findByPk($service_id);
        if ($service === null) {
            throw new CHttpException(404, 'Не найден SOAP сервис.');
        }
        return $service;
    }

    /**
     * @return SoapService
     */
    public function createModel()
    {
        return new SoapService();
    }
}