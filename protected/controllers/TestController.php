<?php

class TestController extends Controller
{
	public $defaultAction = 'list';

    public function actions()
    {
        return array(
            'list' => array(
                'class' => 'application.controllers.test.ListAction',
            ),
            'run' => array(
                'class' => 'application.controllers.test.RunAction',
            ),
            'polling_run_tests' => array(
                'class' => 'application.controllers.test.Polling_run_testsAction',
            ),
            'create' => array(
                'class' => 'application.controllers.test.CreateAction',
            ),
            'update' => array(
                'class' => 'application.controllers.test.UpdateAction',
            ),
            'delete' => array(
                'class' => 'application.controllers.test.DeleteAction',
            ),
        );
    }

//    public function actionSelectTestFunction($id)
//    {
//        if(Yii::app()->request->isAjaxRequest){
//            $func = new SoapFunction();
//            $func = $func->model()->findByPk($id);
//            if (!$func) {
//                throw new CHttpException(404, 'Функция не найдена');
//            }
//
//            $tests = new SoapTest();
//            $tests->selectRunningFunctionTests($id);
//
//            echo CJSON::encode(
//                array(
//                    'function_id'   => $id,
//                    'selected'      => true
//                )
//            );
//        }
//    }

//    public function actionSelectTest($id)
//    {
//        if(Yii::app()->request->isAjaxRequest){
//            $test = new SoapTest();
//            $test = $test->model()->findByPk($id);
//            if (!$test) {
//                throw new CHttpException(404, 'Unit-тест не найден');
//            }
//
//            $test->status = SoapTest::STATUS_TEST_RUN;
//            $sel = false;
//            if ($test->save()){
//                $sel = true;
//            }
//
//            echo CJSON::encode(
//                array(
//                    'selected' => $sel
//                )
//            );
//        }
//    }

//    public function actionRunTestFunction($id)
//    {
//        if(Yii::app()->request->isAjaxRequest){
//            $func = new SoapFunction();
//            $func = $func->model()->findByPk($id);
//            if (!$func) {
//                throw new CHttpException(404, 'Функция не найдена');
//            }
//
//            $tests = new SoapTest();
//            $res = $tests->runTestFunction($id);
//
//            echo CJSON::encode(
//                array(
//                    'count'     => SoapTest::getCountRunningTests($func->service->id),
//                    'test_result'=> $res['test_result'],
//                    'test_result_text' => SoapTest::getTestResultByText($res['test_result']),
//                    'runtime'   => $res['runtime'],
//                    'date_start'=> is_null($res['date_start']) ? '' : Yii::app()->dateFormatter->format('dd MMMM yyyy HH:mm:ss', $res['date_start'])
//                )
//            );
//        }
//    }
//
//    public function actionRunTestFunction2($id)
//    {
//        if(Yii::app()->request->isAjaxRequest){
//            $func = new SoapFunction();
//            $func = $func->model()->findByPk($id);
//            if (!$func) {
//                throw new CHttpException(404, 'Функция не найдена');
//            }
//
//            $tests = new SoapTest();
//            $res = $tests->runTestFunction2($id);
//
//            foreach($res as $k=>$v){
//                $res[$k]['test_result_text']= SoapTest::getTestResultByText($v['test_result']);
//                $res[$k]['date_start']      = Yii::app()->dateFormatter->format('dd MMMM yyyy HH:mm:ss', $v['date_start']);
//                $res[$k]['last_return']     = mb_strlen($v['last_return']) > 1000 ? mb_substr($v['last_return'], 0, 1000)."…" : $v['last_return'];
//            }
//            $count = SoapTest::getCountRunningTestsFunc($id);
//
//            echo CJSON::encode(
//                array(
//                    'data'  => $res,
//                    'count' => $count
//                )
//            );
//
////            echo CJSON::encode(
////                array(
////                    'count'     => SoapTest::getCountRunningTestsFunc($id),
////                    'test_result'=> $res['test_result'],
////                    'test_result_text' => SoapTest::getTestResultByText($res['test_result']),
////                    'runtime'   => $res['runtime'],
////                    'date_start'=> is_null($res['date_start']) ? '' : Yii::app()->dateFormatter->format('dd MMMM yyyy HH:mm:ss', $res['date_start'])
////                )
////            );
//        }
//    }
//
//    public function actionRunTest($id)
//    {
//        if(Yii::app()->request->isAjaxRequest){
//            $test = new SoapTest();
//            $test = $test->model()->findByPk($id);
//            if (!$test) {
//                throw new CHttpException(404, 'Unit-тест не найден');
//            }
//            $res = $test->runTest($id);
//
//            echo CJSON::encode(
//                array(
//                    'count'         => SoapTest::getCountRunningTestsFunc($test->function->id),
//                    'last_return'   => mb_strlen($res['last_return']) > 1000 ? mb_substr($res['last_return'], 0, 1000)."…" : $res['last_return'],
//                    'test_result'   => $res['test_result'],
//                    'test_result_text' => SoapTest::getTestResultByText($res['test_result']),
//                    'runtime'       => $res['runtime'],
//                    'date_start'    => is_null($res['date_start']) ? '' : Yii::app()->dateFormatter->format('dd MMMM yyyy HH:mm:ss', $res['date_start'])
//                )
//            );
//        }
//    }
//
//    public function actionRunTestService($id)
//    {
//        if(Yii::app()->request->isAjaxRequest){
//            $service = new SoapService();
//            $service = $service->model()->findByPk($id);
//            if (!$service) {
//                throw new CHttpException(404, 'Сервис не найдена');
//            }
//
//            $tests = new SoapTest();
//            $res = $tests->runTestService($id);
//            foreach($res as $k=>$v){
//                $res[$k]['test_result_text'] = SoapTest::getTestResultByText($v['test_result']);
//                $res[$k]['date_start'] = Yii::app()->dateFormatter->format('dd MMMM yyyy HH:mm:ss', $v['date_start']);
//            }
//            $count = SoapTest::getCountRunningTests($id);
//
//            echo CJSON::encode(
//                array(
//                    'data'  => $res,
//                    'count' => $count
//                )
//            );
//        }
//    }
}