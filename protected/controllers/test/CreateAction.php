<?php
/**
 * Создаем новый тест для указанной в $func_id функции SoapFunction.
 *
 * @see SoapFunction
 * Class CreateAction
 */
class CreateAction extends CAction
{
	public function run($func_id)
	{
        /**
         * @var $controller TestController
         */
        $controller = $this->controller;

        $function = $controller->loadFunction($func_id);
        $model = $controller->createModel($function);

		if(isset($_POST['ajax']) && $_POST['ajax']==='model-form-form') {
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}

		if (isset($_POST[get_class($model)])) {
			$model->attributes = $_POST[get_class($model)];
            try {
                if ($model->save()) {
                    $controller->redirect($controller->createUrl('list', array('func_id' => $func_id)));
                }
            }catch (Exception $e){
                $model->addError('id', $e->getMessage());
            }
		}
		$controller->render(
            'form',
            array(
                'model' => $model
            )
        );
	}
}