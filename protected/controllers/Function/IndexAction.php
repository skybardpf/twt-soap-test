<?php
/**
 * Показать список функций (SoapFunction) для определенного SOAP сервиса (SoapService).
 * Сервис определяется параметром $sid.
 *
 * @see SoapService
 * @see SoapFunction
 */
class IndexAction extends CAction
{
	public function run($sid)
	{
        /**
         * @var $controller FunctionController
         */
        $controller = $this->controller;
        /**
         * @var $service SoapService
         */
        $service = $controller->loadService($sid);

        $controller->pageTitle .= ' | Список функций сервиса "'.$service->name.'"';

        $data = SoapFunction::getList($service->primaryKey);
        $runningFuncTests = array();
        foreach($data as $k=>$v){
            $data[$k]['test_result_text'] = SoapTest::getTestResultByText($v['test_result'], $v['status']);
            if ($v['has_running_tests']){
                $runningFuncTests[] = $v['id'];
            }
        }
        $this->controller->render('index', array(
            'data' => $data,
            'service' => $service,
            'runningFuncTests' => $runningFuncTests
        ));
	}
}