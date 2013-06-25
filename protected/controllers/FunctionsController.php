<?php

class FunctionsController extends Controller
{
	public $defaultAction = 'list';

	public function actions()
	{
		return array(
			'delete'=>array(
				'class'=>'ext.tests_actions.DeleteAction',
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

        $sql = 'SELECT
                f.id, f.name, t.count, t.count_worked, t.date_start, t.status, t.runtime, t.test_result
            FROM '.SoapFunction::model()->tableName().' AS f
            LEFT JOIN (
                SELECT
                    `function_id` AS `fid`,
                    COUNT(`id`) AS `count`,
                    SUM(CASE `status`
                        WHEN '.SoapTests::STATUS_TEST_RUN.'
                        THEN 1 ELSE 0 END
                        ) AS `count_worked`,
                    SUM(CASE `test_result`
                        WHEN '.SoapTests::TEST_RESULT_ERROR.' OR '.SoapTests::TEST_RESULT_OK.'
                        THEN (date_end-date_start) ELSE 0 END
                        ) AS `runtime`,
                    MIN(`date_start`) AS `date_start`,
                    MIN(`status`) AS `status`,
                    MAX(`test_result`) AS `test_result`
                FROM '.SoapTests::model()->tableName().'
                GROUP BY `function_id`
            ) t ON f.id=t.fid
            WHERE f.service_id=:service_id';
        $cmd = Yii::app()->db->createCommand($sql);
        $rows = $cmd->queryAll(true, array(
            ":service_id" => $id
        ));

        $data = new CSqlDataProvider($sql, array(
            'params' => array(
                ":service_id" => $id
            ),
            'keyField' => 'id',
            'totalItemCount' => count($rows),
            'pagination' => array(
                'pageSize' => 50,
            )
        ));

        $sql = 'SELECT count(*) AS c
            FROM '.SoapTests::model()->tableName().'
            WHERE service_id=:service_id AND status=:status';
        $cmd = Yii::app()->db->createCommand($sql);
        $row = $cmd->queryRow(true,
            array(
                "service_id"=> $id,
                "status"    => SoapTests::STATUS_TEST_RUN
            )
        );

		$this->render('list', array(
            'data'      => $data,
            'service'   => $service,
            'count_running_tests' => $row['c']
        ));
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