<?php
/**
 * Добавление новой функции к SOAP сервису. Функция должна обязательно принадлежать
 * к группе функций {@see GroupFunctions}.
 *
 * @author Skibardin A.A. <skybardpf@artektiv.ru>
 *
 * @see SoapFunction
 * @see SoapService
 * @see GroupFunctions
 */
class CreateAction extends CAction
{
    /**
     * @param int $service_id
     * @throws CHttpException
     */
    public function run($service_id)
	{
        /**
         * @var $controller FunctionController
         */
        $controller = $this->controller;

        /**
         * @var $service SoapService
         */
        $service = $controller->loadService($service_id);
        $controller->pageTitle = 'Создание новой функции для сервиса «'.$service->name.'»';

        /**
         * @var $model SoapFunction
         */
        $model = $controller->createModel($service);

        if(isset($_POST['ajax']) && $_POST['ajax']==='model-form-form') {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }

        $class = get_class($model);
        $input_params = array();
        $output_params = array();

        if (isset($_POST[$class]) && !empty($_POST[$class])) {
            $model->attributes = $_POST[$class];
            if($model->validate()){
                try {
                    $model->save();
                    $controller->redirect($controller->createUrl(
                        'function/list',
                        array('service_id' => $service->primaryKey)
                    ));
                } catch (CException $e){
                    $model->addError('id', $e->getMessage());
                }
            }
        }

        $controller->render(
            'form',
            array(
                'model' => $model,
                'service' => $service,
                'input_params' => $input_params,
                'output_params' => $output_params,
            )
        );
	}
}