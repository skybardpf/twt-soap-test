<?php

class UpdateAction extends CAction
{
	public function run($id)
	{
        $model = SoapService::model()->findByPk($id);
		if (!$model) {
            throw new CHttpException(404, 'Не найден сервис.');
        }
		if(isset($_POST['ajax']) && $_POST['ajax']==='model-form-form') {
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}

		if (isset($_POST[get_class($model)])) {
			$model->attributes=$_POST[get_class($model)];
			if ($model->save()) {
				$this->controller->redirect($this->controller->createUrl('list'));
			}
		}
		$this->controller->render('update', array('model' => $model));
	}
}