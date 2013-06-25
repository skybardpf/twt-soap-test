<?php

class ArgumentsController extends Controller
{
    public $defaultAction = 'view';

	public function actions()
	{
		return array(
			'create'=>array(
				'class'=>'ext.tests_actions.CreateAction',
				'model'=> function () {
					$test = new SoapTests();
                    $test->function_id = Yii::app()->request->getParam('function_id');
                    $test->status      = SoapTests::STATUS_TEST_STOP;
                    $test->test_result = SoapTests::TEST_RESULT_NOT_EXECUTED;
					return $test;
				},
			),
			'update'=>array(
				'class'=>'ext.tests_actions.UpdateAction',
				'model'=> SoapTests::model(),
			),
			'delete'=>array(
				'class'=>'ext.tests_actions.DeleteAction',
				'model'=> SoapTests::model(),
			)
		);
	}

	public function actionList($id)
	{
		$function = SoapFunction::model()->findByPk($id);
		if (!$function) {
			throw new CHttpException(404, 'Сервис не найден');
		}

        $sql = '
            SELECT id,
                name,
                date_start,
                status,
                ( CASE test_result
                    WHEN '.SoapTests::TEST_RESULT_ERROR.' OR '.SoapTests::TEST_RESULT_OK.'
                    THEN( date_end - date_start )
                    ELSE 0
                    END
                ) AS `runtime`,
                test_result,
                last_return,
                args
            FROM '.SoapTests::model()->tableName().'
            WHERE function_id = :function_id';
        $cmd = Yii::app()->db->createCommand($sql);
        $rows = $cmd->queryAll(true, array(
            ":function_id" => $id
        ));

        $data = new CSqlDataProvider($sql, array(
            'params' => array(
                ":function_id" => $id
            ),
            'keyField' => 'id',
            'totalItemCount' => count($rows),
            'pagination' => array(
                'pageSize' => 50,
            )
        ));


        $sql = 'SELECT count(*) AS c
            FROM '.SoapTests::model()->tableName().'
            WHERE function_id=:function_id AND status=:status';
        $cmd = Yii::app()->db->createCommand($sql);
        $row = $cmd->queryRow(true,
            array(
                ":function_id"  => $id,
                ":status"       => SoapTests::STATUS_TEST_RUN
            )
        );

		$this->render('list',
            array(
                'count_running_tests' => $row['c'],
                'function'  => $function,
                'data'      => $data,
                'service_id'=> $function->service->id
            )
        );
	}

	public function actionView($id)
	{
        $func = SoapFunction::model()->findByPk($id);
        if (!$func) {
            throw new CHttpException(404, 'Функция не найдена.');
        }
		$this->redirect($this->createUrl('list', array('id' => $id)));
	}
}