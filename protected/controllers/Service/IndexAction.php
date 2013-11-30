<?php
/**
 * Список действующих SOAP сервисов.
 *
 * @author Skibardin A.A. <skybardpf@artektiv.ru>
 *
 * @see SoapService
 */
class IndexAction extends CAction
{
    public function run()
	{
        /**
         * @var ServiceController $controller
         */
        $controller = $this->controller;
        $controller->pageTitle = Yii::app()->name . ' | SOAP сервисы';

        $data = SoapService::getList();
        $runningServiceTests = array();
        foreach($data as $k=>$v){
            $data[$k]['test_result_text'] = SoapTest::getTestResultByText($v['test_result'], $v['status']);
            if ($v['has_running_tests']){
                $runningServiceTests[] = $v['id'];
            }
        }

        $this->controller->render(
            'index',
            array(
                'data' => $data,
                'runningServiceTests' => $runningServiceTests
            )
        );
	}
}