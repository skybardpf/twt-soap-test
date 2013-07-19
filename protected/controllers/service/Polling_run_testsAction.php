<?php
/**
 * Переодический опрос состояний запущеных тестов {SoapTest}
 * для всех функции {SoapFunction} определенного сервиса {SoapService}.
 *
 * @see SoapService
 * @see SoapFunction
 * @see SoapTest
 */
class Polling_run_testsAction extends CAction
{
	public function run(array $ids)
	{
        if (!isset($ids[0]) || empty($ids[0])){
            if (Yii::app()->request->isAjaxRequest) {
                echo CJSON::encode(array(
                    'data' => array()
                ));
                Yii::app()->end();
            }
            return false;
        }

        $ids = explode(',', $ids[0]);
        try {
            $ret = SoapService::getStatusesTests($ids);
            foreach($ret as $k=>$r){
                $ret[$k]['date_start'] = Yii::app()->dateFormatter->format('dd MMMM yyyy HH:mm:ss', $r['date_start']);
                $ret[$k]['test_result_text'] = SoapTest::getTestResultByText($r['test_result']);
            }

            if (Yii::app()->request->isAjaxRequest) {
                echo CJSON::encode($ret);
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