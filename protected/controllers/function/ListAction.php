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
        /**
         * @var $controller FunctionController
         */
        $controller = $this->controller;
        /**
         * @var $service SoapService
         */
        $service = $controller->loadService($service_id);

        $controller->pageTitle .= ' | Список функций сервиса "'.$service->name.'"';

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