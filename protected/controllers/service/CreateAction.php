<?php
/**
 * Добавление нового SOAP сервиса.
 *
 * @author Skibardin A.A. <skybardpf@artektiv.ru>
 *
 * @see SoapService
 */
class CreateAction extends CAction
{
	public function run()
	{
        /**
         * @var $controller ServiceController
         */
        $controller = $this->controller;
        $controller->pageTitle .= 'Добавление SOAP сервиса';
        /**
         * @var $model SoapService
         */
        $model = $controller->createModel();

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