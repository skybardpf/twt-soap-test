<?php
//namespace controllers\service;

class CreateAction extends CAction
{
	public function run()
	{
        $model = new SoapService();

		if(isset($_POST['ajax']) && $_POST['ajax']==='model-form-form') {
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}


		if (isset($_POST[get_class($model)])) {
			$model->attributes = $_POST[get_class($model)];
            try {
                if ($model->save()) {
                    $this->controller->redirect($this->controller->createUrl('list'));
                }
            }catch (Exception $e){
                var_dump($e->getMessage());
            }
		}
		$this->controller->render('create', array(
            'model' => $model
        ));
	}
}