<?php
class TestCommand extends CConsoleCommand
{
	public function actionRun()
	{
		$criteria = new CDbCriteria();
		$criteria->addInCondition('status', array(1,3));
		/** @var $tests SoapTest[] */
		$tests = SoapTest::model()->findAll($criteria);
		if (!empty($tests)) {
			$tests[0]->runTests();
		}
	}
}
