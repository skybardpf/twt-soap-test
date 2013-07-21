<?php
/**
 * Редактирование группы функции к SOAP сервису.
 *
 * @author Skibardin A.A. <skybardpf@artektiv.ru>
 *
 * @see SoapService
 * @see GroupFunctions
 */
class UpdateAction extends CAction
{
    /**
     * @param integer $id
     * @throws CHttpException
     */
    public function run($id)
	{
        /**
         * @var $controller Group_functionsController
         */
        $controller = $this->controller;
        /**
         * @var $model GroupFunctions
         */
        $model = $controller->loadModel($id);

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
                        'list',
                        array(
                            'service_id' => $model->soapService->primaryKey
                        )
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
            )
        );
	}
}