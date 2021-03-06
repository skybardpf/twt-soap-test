<?php
/**
 * Запускаем на выволнения тесты, помеченые как выбранные на запуск.
 * Для выбора тестов используете Mark_run_testsAction.
 *
 * @see SoapTest
 * @see SoapFunction
 */
class Run_testsAction extends CAction
{
	public function run($id)
	{
        try {
            /**
             * @var $function SoapFunction
             */
            $function = SoapFunction::model()->findByPk($id);
            if (!$function) {
                throw new CHttpException(404, 'Функция не найдена.');
            }

            $list = $function->putTestsInQueue();
            /**
             * @var $t SoapTest
             */
            foreach($list as $t){
                $t->run();
            }
            $ret = $function->getLastResult();

            if (Yii::app()->request->isAjaxRequest) {
                echo CJSON::encode(array(
                    'success' => true,
                    'data' => array(
                        'test_result_text' => SoapTest::getTestResultByText($ret['test_result']),
                        'test_result' => $ret['test_result'],
                        'date_start' => Yii::app()->dateFormatter->format('dd MMMM yyyy HH:mm:ss', $ret['date_start']),
                        'runtime' => $ret['runtime'],
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