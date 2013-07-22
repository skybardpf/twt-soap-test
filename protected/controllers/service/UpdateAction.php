<?php
/**
 * Редактирование существующего SOAP сервиса.
 *
 * @author Skibardin A.A. <skybardpf@artektiv.ru>
 *
 * @see SoapService
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
         * @var $controller ServiceController
         */
        $controller = $this->controller;
        $controller->pageTitle .= 'Редактирование SOAP сервиса';
        /**
         * @var $model SoapService
         */
        $model = $controller->loadModel($id);

		if(isset($_POST['ajax']) && $_POST['ajax']==='model-form-form') {
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}

		if (isset($_POST[get_class($model)])) {
			$model->attributes = $_POST[get_class($model)];
            try {
                if ($model->save()) {
                    $controller->redirect($controller->createUrl('list'));
                }
            }catch (Exception $e){
                $model->addError('id', $e->getMessage());
            }
		}
		$controller->render(
            'form',
            array('model' => $model)
        );
	}
}