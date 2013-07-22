<?php
class UpdateAction extends CAction
{
	/**
	 * @var CActiveRecord
	 */
	public $model = null;

	public $view = 'update';

	public $scenario = 'update';

	public function run($id)
	{

		$model = $this->model->findByPk($id);

//        var_dump($model);die;

		if (empty($model)) throw new CHttpException(404);
		$model->setScenario($this->scenario);

		if(isset($_POST['ajax']) && $_POST['ajax']==='model-form-form') {
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}

//
//        var_dump(get_class($model));die;
//
		if (isset($_POST[get_class($model)])) {
			$model->attributes=$_POST[get_class($model)];
			if ($model->save()) {
				$this->controller->redirect($this->controller->createUrl('view', array('id' => $model->function_id)));
			}
		}
		$this->controller->render($this->view, array('model' => $model));
	}
}