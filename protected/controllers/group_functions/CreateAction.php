<?php
/**
 * Добавление новой группу функции к SOAP сервису.
 *
 * @author Skibardin A.A. <skybardpf@artektiv.ru>
 *
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
         * @var $controller Group_functionsController
         */
        $controller = $this->controller;
        /**
         * @var $service SoapService
         */
        $service = $controller->loadService($service_id);
        /**
         * @var $model GroupFunctions
         */
        $model = $controller->createModel($service->primaryKey);

        if(isset($_POST['ajax']) && $_POST['ajax']==='model-form-form') {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }

        $class = get_class($model);
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
                'service' => $service
            )
        );
	}
}