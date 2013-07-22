<?php
/**
 * Only Ajax. Запуск теста на выполнение {@link SoapTest}.
 *
 * @author Skibardin A.A. <skybardpf@artektiv.ru>
 *
 * @see SoapTest
 */
class RunAction extends CAction
{
    /**
     * Запускаем тест на выполнение.
     *
     * @param integer $id
     */
	public function run($id)
	{
        if (Yii::app()->request->isAjaxRequest){
            try {
                /**
                 * @var $controller TestController
                 */
                $controller = $this->controller;

                /**
                 * @var $test SoapTest
                 */
                $test = $controller->loadModel($id);
                if ($test->is_running()){
                    echo CJSON::encode(array(
                        'success' => false,
                        'message' => 'Тест уже запущен.'
                    ));
                    Yii::app()->end();
                }
                $test->run();

                echo CJSON::encode(array(
                    'success' => true,
                    'data' => array(
                        'last_errors' => $test->last_errors,
                        'last_return' => mb_strlen($test->last_return) > 1000 ? mb_substr($test->last_return, 0, 1000)."…" : $test->last_return,
                        'test_result' => $test->test_result,
                        'date_start' => Yii::app()->dateFormatter->format('dd MMMM yyyy HH:mm:ss', $test->date_start),
                        'runtime' => ($test->test_result == SoapTest::TEST_RESULT_OK || $test->test_result == SoapTest::TEST_RESULT_ERROR)
                            ? abs(strtotime($test->date_end) - strtotime($test->date_start))
                            : 0,
                    )
                ));
                Yii::app()->end();
            } catch(CException $e){
                echo CJSON::encode(array(
                    'success' => false,
                    'message' => $e->getMessage()
                ));
                Yii::app()->end();
            }
        }
	}
}