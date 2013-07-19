<?php
/**
 * Показать список функций (SoapFunction) для определенного SOAP сервиса (SoapService).
 * Сервис определяется параметром $service_id.
 *
 * @see SoapService
 * @see SoapFunction
 */
class ListAction extends CAction
{
	public function run($service_id)
	{
        $service = SoapService::model()->findByPk($service_id);
        if (!$service) {
            throw new CHttpException(404, 'Сервис не найден.');
        }

        $data = SoapFunction::getList($service->primaryKey);
        $runningFuncTests = array();
        foreach($data as $k=>$v){
            $data[$k]['test_result_text'] = SoapTest::getTestResultByText($v['test_result'], $v['status']);
            if ($v['has_running_tests']){
                $runningFuncTests[] = $v['id'];
            }
        }
        $this->controller->render('list', array(
            'data' => $data,
            'service' => $service,
            'runningFuncTests' => $runningFuncTests
        ));
	}
}