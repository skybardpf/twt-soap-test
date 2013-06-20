<?php

class TestsController extends Controller
{
	public $defaultAction = 'list';

	public function actionDetail($id)
	{
		$test = SoapTest::model()->findByPk($id);
		$this->render('detail', array('test' => $test));
	}

	public function actionList($id)
	{
		$service = SoapService::model()->findByPk($id);
		if (!$service) {
			throw new CHttpException(404, 'Сервис не найден');
		}
		$data = new CActiveDataProvider(SoapTest::model()->service($id), array(
			'pagination' => array(
				'pageSize' => 50,
			),
			'criteria' => array(
				'order' => 'id DESC'
			)
		));
		$this->render('list', array('data' => $data, 'service' => $service));
	}

	public function actionRun($id)
	{
		$test = new SoapTest();
		$test->service_id = $id;
		if ($test->save()) {
			$this->redirect($this->createUrl('detail', array('id' => $test->id)));
		} else {
			Yii::app()->user->setFlash('error', $test->getError('service_id'));
			$this->redirect($this->createUrl('list', array('id' => $id)));
		}
	}
}