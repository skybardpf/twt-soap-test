<?php

class ServersController extends Controller
{
	public $defaultAction = 'list';

	public function actions()
	{
		return array(
			'create'=>array(
				'class'=>'ext.admin_actions.CreateAction',
				'model'=> new SoapService(),
			),
			'update'=>array(
				'class'=>'ext.admin_actions.UpdateAction',
				'model'=> SoapService::model(),
			),
			'delete'=>array(
				'class'=>'ext.admin_actions.DeleteAction',
				'model'=> SoapService::model(),
			)
		);
	}
	public function actionList()
	{
		$data = new CActiveDataProvider(SoapService::model(), array(
			'pagination' => array(
				'pageSize' => 50,
			)
		));
		$this->render('list', $data);
	}

	public function actionView()
	{
		$this->redirect($this->createUrl('list'));
	}
}