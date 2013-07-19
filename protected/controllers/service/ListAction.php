<?php

class ListAction extends CAction
{
	public function run()
	{
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