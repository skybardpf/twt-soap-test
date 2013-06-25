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

//    public function actionRunAll($id)
//    {
//        echo 'actionRunAll'.$id;die;
//    }

//    public function actionRunTest($id)
//    {
//        $test = new SoapTests();
//        $test->run($id);
//    }

    public function actionSelectTestFunction($id)
    {
        if(Yii::app()->request->isAjaxRequest){
            $func = new SoapFunction();
            $func = $func->model()->findByPk($id);
            if (!$func) {
                throw new CHttpException(404, 'Функция не найдена');
            }

            $tests = new SoapTests();
            $tests->selectRunningFunctionTests($id);

            echo CJSON::encode(
                array(
                    'function_id'   => $id,
                    'selected'      => true
                )
            );
        }
    }

    public function actionSelectTest($id)
    {
        if(Yii::app()->request->isAjaxRequest){
            $test = new SoapTests();
            $test = $test->model()->findByPk($id);
            if (!$test) {
                throw new CHttpException(404, 'Unit-тест не найден');
            }

            $test->status = SoapTests::STATUS_TEST_RUN;
            $sel = false;
            if ($test->save()){
                $sel = true;
            }

            echo CJSON::encode(
                array(
                    'selected' => $sel
                )
            );
        }
    }

    public function actionRunTestFunction($id)
    {
        if(Yii::app()->request->isAjaxRequest){
            $func = new SoapFunction();
            $func = $func->model()->findByPk($id);
            if (!$func) {
                throw new CHttpException(404, 'Функция не найдена');
            }

            $tests = new SoapTests();
            $res = $tests->runTestFunction($id);

            echo CJSON::encode(
                array(
                    'count'     => SoapTests::getCountRunningTests($func->service->id),
                    'test_result'=> $res['test_result'],
                    'test_result_text' => SoapTests::getTestResultByText($res['test_result']),
                    'runtime'   => $res['runtime'],
                    'date_start'=> is_null($res['date_start']) ? '' : Yii::app()->dateFormatter->format('dd MMMM yyyy HH:mm:ss', $res['date_start'])
                )
            );
        }
    }

    public function actionRunTestFunction2($id)
    {
        if(Yii::app()->request->isAjaxRequest){
            $func = new SoapFunction();
            $func = $func->model()->findByPk($id);
            if (!$func) {
                throw new CHttpException(404, 'Функция не найдена');
            }

            $tests = new SoapTests();
            $res = $tests->runTestFunction2($id);

            foreach($res as $k=>$v){
                $res[$k]['test_result_text']= SoapTests::getTestResultByText($v['test_result']);
                $res[$k]['date_start']      = Yii::app()->dateFormatter->format('dd MMMM yyyy HH:mm:ss', $v['date_start']);
                $res[$k]['last_return']     = mb_strlen($v['last_return']) > 1000 ? mb_substr($v['last_return'], 0, 1000)."…" : $v['last_return'];
            }
            $count = SoapTests::getCountRunningTestsFunc($id);

            echo CJSON::encode(
                array(
                    'data'  => $res,
                    'count' => $count
                )
            );

//            echo CJSON::encode(
//                array(
//                    'count'     => SoapTests::getCountRunningTestsFunc($id),
//                    'test_result'=> $res['test_result'],
//                    'test_result_text' => SoapTests::getTestResultByText($res['test_result']),
//                    'runtime'   => $res['runtime'],
//                    'date_start'=> is_null($res['date_start']) ? '' : Yii::app()->dateFormatter->format('dd MMMM yyyy HH:mm:ss', $res['date_start'])
//                )
//            );
        }
    }

    public function actionRunTest($id)
    {
        if(Yii::app()->request->isAjaxRequest){
            $test = new SoapTests();
            $test = $test->model()->findByPk($id);
            if (!$test) {
                throw new CHttpException(404, 'Unit-тест не найден');
            }
            $res = $test->runTest($id);

            echo CJSON::encode(
                array(
                    'count'         => SoapTests::getCountRunningTestsFunc($test->function->id),
                    'last_return'   => mb_strlen($res['last_return']) > 1000 ? mb_substr($res['last_return'], 0, 1000)."…" : $res['last_return'],
                    'test_result'   => $res['test_result'],
                    'test_result_text' => SoapTests::getTestResultByText($res['test_result']),
                    'runtime'       => $res['runtime'],
                    'date_start'    => is_null($res['date_start']) ? '' : Yii::app()->dateFormatter->format('dd MMMM yyyy HH:mm:ss', $res['date_start'])
                )
            );
        }
    }

    public function actionRunTestService($id)
    {
        if(Yii::app()->request->isAjaxRequest){
            $service = new SoapService();
            $service = $service->model()->findByPk($id);
            if (!$service) {
                throw new CHttpException(404, 'Сервис не найдена');
            }

            $tests = new SoapTests();
            $res = $tests->runTestService($id);
            foreach($res as $k=>$v){
                $res[$k]['test_result_text'] = SoapTests::getTestResultByText($v['test_result']);
                $res[$k]['date_start'] = Yii::app()->dateFormatter->format('dd MMMM yyyy HH:mm:ss', $v['date_start']);
            }
            $count = SoapTests::getCountRunningTests($id);

            echo CJSON::encode(
                array(
                    'data'  => $res,
                    'count' => $count
                )
            );
        }
    }

    private function runConsoleTest($service_id) {
//        var_dump(Yii::app()->basePath);die;
//        $cmd=PHP_BINDIR."/php ".Yii::app()->basePath.'/../'.' '.$cmd;
//        if((PHP_OS == 'WINNT' || PHP_OS == 'WIN32'))
//            pclose(popen('start /b '.$cmd, 'r'));
//        else
//            pclose(popen($cmd.' /dev/null &', 'r'));
//        return true;



//        var_dump(realpath('./console.php'));die;
//        $runner = new TConsoleRunner('./console.php');
////        var_dump($runner);die;
//        var_dump($runner->run('yiic soaptests --id=4'));

//        $commandPath = Yii::app()->getBasePath() . DIRECTORY_SEPARATOR . 'commands';
//        $runner = new TConsoleRunner('console.php');
//        $runner->addCommands($commandPath);
//        $commandPath = Yii::getFrameworkPath() . DIRECTORY_SEPARATOR . 'cli' . DIRECTORY_SEPARATOR . 'commands';
////        var_dump($commandPath);die;
//        $runner->addCommands($commandPath);

//        $args = array('yiic', 'soaptests', $service_id);
//        ob_start();
//        $runner->run($args);
//        echo htmlentities(ob_get_clean(), null, Yii::app()->charset);
    }
}