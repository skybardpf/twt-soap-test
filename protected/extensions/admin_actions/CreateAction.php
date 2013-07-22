<?php
class CreateAction extends CAction
{
	/**
	 * @var CActiveRecord|callable
	 */
	public $model = null;

	public $view = 'create';

	public function run()
	{
		if (is_callable($this->model)) {
			$model = call_user_func($this->model);
		} else {
			$model = $this->model;
		}

		if(isset($_POST['ajax']) && $_POST['ajax']==='model-form-form') {
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}

		if (isset($_POST[get_class($model)])) {
			$model->attributes=$_POST[get_class($model)];
			if ($model->save()) {
				$this->controller->redirect($this->controller->createUrl('view', array('id' => $model->id)));
			}
		}
		$this->controller->render($this->view, array('model' => $model));
	}
}