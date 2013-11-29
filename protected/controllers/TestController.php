<?php
/**
 * Class TestController
 */
class TestController extends Controller
{
	public $defaultAction = 'list';

    public $pageTitle = 'SOAP Unit тесты';

    public function actions()
    {
        return array(
            'list' => array(
                'class' => 'application.controllers.test.ListAction',
            ),
            'run' => array(
                'class' => 'application.controllers.test.RunAction',
            ),
            'polling_run_tests' => array(
                'class' => 'application.controllers.test.Polling_run_testsAction',
            ),
            'create' => array(
                'class' => 'application.controllers.test.CreateAction',
            ),
            'update' => array(
                'class' => 'application.controllers.test.UpdateAction',
            ),
            'delete' => array(
                'class' => 'application.controllers.test.DeleteAction',
            ),
        );
    }

    /**
     * @param int $id
     * @return SoapTest
     * @throws CHttpException
     */
    public function loadModel($id)
    {
        $model = SoapTest::model()->findByPk($id);
        if ($model === null) {
            throw new CHttpException(404, 'Тест не найден.');
        }
        return $model;
    }

    /**
     * @param SoapFunction $function
     * @return SoapTest
     * @throws CHttpException
     */
    public function createModel(SoapFunction $function)
    {
        $model = new SoapTest();
        $model->soapFunction = $function;
        $model->function_id = $function->primaryKey;
        $model->date_create = time();
        return $model;
    }

    /**
     * @param int $func_id
     * @return SoapFunction
     * @throws CHttpException
     */
    public function loadFunction($func_id)
    {
        $function = SoapFunction::model()->findByPk($func_id);
        if ($function === null) {
            throw new CHttpException(404, 'Функция не найдена.');
        }
        return $function;
    }
}