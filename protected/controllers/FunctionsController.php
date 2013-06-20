<?php

class FunctionsController extends Controller
{
	public $defaultAction = 'list';

	public function actions()
	{
		return array(
			'delete'=>array(
				'class'=>'ext.admin_actions.DeleteAction',
				'model'=> SoapFunction::model(),
			)
		);
	}

	public function actionList($id)
	{
		$service = SoapService::model()->findByPk($id);
		if (!$service) {
			throw new CHttpException(404, 'Сервис не найден');
		}
		$data = new CActiveDataProvider(SoapFunction::model()->service($id), array(
			'criteria' => array('order' => 'name ASC'),
			'pagination' => array(
				'pageSize' => 50,
			)
		));
		$this->render('list', array('data' => $data, 'service' => $service));
	}

	public function actionUpdate($id)
	{
		$model = SoapFunction::model()->findByPk($id);
		if (empty($model)) throw new CHttpException(404);
		$model->setScenario('update');

		if(isset($_POST['ajax']) && $_POST['ajax']==='model-form-form') {
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}

		if (isset($_POST[get_class($model)])) {
			$model->attributes=$_POST[get_class($model)];
			if ($model->save()) {
				$this->redirect($this->createUrl('view', array('id' => $model->id)));
			}
		}
		$this->render('update', array('model' => $model));
	}

	public function actionView($id)
	{
		/** @var $function SoapFunction */
		$function = SoapFunction::model()->findByPk($id);
		$this->redirect($this->createUrl('list', array('id' => $function->service_id)));
	}
}