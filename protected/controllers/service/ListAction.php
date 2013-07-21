<?php
/**
 * Выводим список всех действующих SOAP сервисов.
 *
 * @author Skibardin A.A. <skybardpf@artektiv.ru>
 *
 * @see SoapService
 */
class ListAction extends CAction
{
    /**
     * Список SOAP сервисов.
     */
    public function run()
	{
        /**
         * @var $controller ServiceController
         */
        $controller = $this->controller;
        $controller->pageTitle .= 'Список SOAP сервисов';

        $data = SoapService::getList();
        $runningServiceTests = array();
        foreach($data as $k=>$v){
            $data[$k]['test_result_text'] = SoapTest::getTestResultByText($v['test_result'], $v['status']);
            if ($v['has_running_tests']){
                $runningServiceTests[] = $v['id'];
            }
        }

        $this->controller->render(
            'list',
            array(
                'data' => $data,
                'runningServiceTests' => $runningServiceTests
            )
        );
	}
}