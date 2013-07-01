<?php
class SoaptestsCommand extends CConsoleCommand
{
	public function run($service_id)
	{

        file_put_contents('/tmp/tester.txt', 'testXXX');

//        var_dump($service_id);
//        return 'adsljvnavn';
//        die;
        $service = SoapService::model()->findByPk($service_id);
        if ($service) {
            $criteria = new CDbCriteria();
            $criteria->addInCondition('status', array(SoapTests::STATUS_TEST_RUN));
            $criteria->addInCondition('service_id', $service_id);
            $tests = SoapTests::model()->findAll($criteria);
            foreach($tests as $t){
                $t->run($t->id);
            }
        }
	}
}
