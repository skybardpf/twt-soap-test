<?php

class ArgumentsController extends Controller
{
	public function actions()
	{
		return array(
			'create'=>array(
				'class'=>'ext.admin_actions.CreateAction',
				'model'=> function () {
					$args = new SoapFunctionArgs();
					$args->function_id = Yii::app()->request->getParam('function_id');
					return $args;
				},
			),
			'update'=>array(
				'class'=>'ext.admin_actions.UpdateAction',
				'model'=> SoapFunctionArgs::model(),
			),
			'delete'=>array(
				'class'=>'ext.admin_actions.DeleteAction',
				'model'=> SoapFunctionArgs::model(),
			)
		);
	}

	public function actionList($id)
	{
		$function = SoapFunction::model()->findByPk($id);
		if (!$function) {
			throw new CHttpException(404, 'Сервис не найден');
		}
		$data = new CActiveDataProvider(SoapFunctionArgs::model()->functionId($id), array(
			'pagination' => array(
				'pageSize' => 50,
			)
		));
		$this->render('list', array('function' => $function, 'data' => $data));
	}

	public function actionView($id)
	{
		/** @var $arg SoapFunctionArgs */
		$arg = SoapFunctionArgs::model()->findByPk($id);
		$this->redirect($this->createUrl('list', array('id' => $arg->function_id)));
	}
}