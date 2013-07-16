<?php

class FunctionController extends Controller
{
	public $defaultAction = 'list';

	public function actions()
	{
		return array(
            'list' => array(
                'class' => 'application.controllers.function.ListAction',
            ),
            'update' => array(
                'class' => 'application.controllers.function.UpdateAction',
            ),
            'run_tests' => array(
                'class' => 'application.controllers.function.Run_testsAction',
            ),
            'polling_run_tests' => array(
                'class' => 'application.controllers.function.Polling_run_testsAction',
            ),
            'delete_tests' => array(
                'class' => 'application.controllers.function.Delete_testsAction',
            ),
		);
	}

//	public function actionUpdate($id)
//	{
//		$model = SoapFunction::model()->findByPk($id);
//		if (empty($model)) throw new CHttpException(404);
//		$model->setScenario('update');
//
//		if(isset($_POST['ajax']) && $_POST['ajax']==='model-form-form') {
//			echo CActiveForm::validate($model);
//			Yii::app()->end();
//		}
//
//		if (isset($_POST[get_class($model)])) {
//			$model->attributes=$_POST[get_class($model)];
//			if ($model->save()) {
//				$this->redirect($this->createUrl('view', array('id' => $model->id)));
//			}
//		}
//		$this->render('update', array('model' => $model));
//	}
//
//	public function actionView($id)
//	{
//		/** @var $function SoapFunction */
//		$function = SoapFunction::model()->findByPk($id);
//		$this->redirect($this->createUrl('list', array('id' => $function->service_id)));
//	}
}