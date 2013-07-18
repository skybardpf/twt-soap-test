<?php
/**
 * Запуск теста на выполнение {@link SoapTest}.
 *
 * @see SoapTest
 */
class RunAction extends CAction
{
    /**
     * Запускаем тест на выполнение.
     *
     * @param int $id
     * @return bool
     * @throws CHttpException | CException
     */
	public function run($id)
	{
        try {
            /**
             * @var $test SoapTest
             */
            $test = SoapTest::model()->findByPk($id);
            if (!$test) {
                throw new CException('Тест не найден.');
            }
            if (!$test->run()){
                throw new CException('Тест уже запущен.');
            }

            $format = new CFormatter();
            if (Yii::app()->request->isAjaxRequest) {
                echo CJSON::encode(array(
                    'success' => true,
                    'data' => array(
                        'last_return' => $format->formatHtml(mb_strlen($test->last_return) > 1000 ? mb_substr($test->last_return, 0, 1000)."…" : $test->last_return),
                        'test_result' => $test->test_result,
                        'date_start' => Yii::app()->dateFormatter->format('dd MMMM yyyy HH:mm:ss', $test->date_start),
                        'runtime' => ($test->test_result == SoapTest::TEST_RESULT_OK)
                            ? ($test->date_end - $test->date_start)
                            : 0,
                    )
                ));
                Yii::app()->end();
            }
            return true;

        } catch(CException $e){
            if (Yii::app()->request->isAjaxRequest) {
                echo CJSON::encode(array(
                    'success' => false,
                    'message' => $e->getMessage()
                ));
                Yii::app()->end();
            } else {
                throw new CHttpException(500, $e->getMessage());
            }
        }
        return false;
	}
}