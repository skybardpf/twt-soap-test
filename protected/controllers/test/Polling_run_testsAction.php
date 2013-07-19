<?php
/**
 * Переодический опрос состояний запущеных тестов {@link SoapTest}
 * для определенной функции $func_id {@link SoapFunction}.
 *
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
            $ret = SoapTest::getStatusesTests($ids);
            foreach($ret as $k=>$r){
                $ret[$k]['date_start'] = Yii::app()->dateFormatter->format('dd MMMM yyyy HH:mm:ss', $r['date_start']);
                $ret[$k]['last_return'] = mb_strlen($r['last_return']) > 1000 ? mb_substr($r['last_return'], 0, 1000)."…" : $r['last_return'];
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