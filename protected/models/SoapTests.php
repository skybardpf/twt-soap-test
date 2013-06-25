<?php
class CSoapTestsException extends CException {}

/**
 * Created by JetBrains PhpStorm.
 * User: leadert
 * Date: 20.06.13
 * Time: 16:02
 * To change this template use File | Settings | File Templates.
 *
 *  The followings are the available model relations:
 *  @property SoapService       $service
 *  @property SoapFunction      $function
 */
class SoapTests extends CActiveRecord {
//    const STATUS_TEST_NEW           = 0;
    const STATUS_TEST_RUN           = 1;
    const STATUS_TEST_STOP          = 2;

//    const TEST_RESULT_WORKED            = 0;
    const TEST_RESULT_OK                = 1;
    const TEST_RESULT_NOT_EXECUTED      = 2;
    const TEST_RESULT_ERROR             = 3;

    const STATUS_SERVICE_NOT_AVAILABLE  = 4;
    const STATUS_NOT_WSDL_SERVICE       = 5;
    const STATUS_NOT_CALL_FUNCTION      = 6;
    const STATUS_NOT_RETURN_RESULT      = 7;

//    private $_test_id = NULL;

    private function run($id)
    {
        $test = self::model()->findByPk($id);
        if (!$test OR $test->status != self::STATUS_TEST_RUN){
            return;
        }

//        $status = self::STATUS_TEST_RUN;
        $test_result = self::TEST_RESULT_OK;
        $return = '';

        $test->date_start   = time();
        $test->date_end     = NULL;
//        $test->status       = $status;
        $test->test_result  = $test_result;
        $test->save();

        try {
            if (!$test->service->isAvailableService()){
                throw new CSoapTestsException('Сервис не доступен.', self::STATUS_SERVICE_NOT_AVAILABLE);
            }
            $soapClient = $test->service->getSoapClient();
            if (!$soapClient){
                throw new CSoapTestsException('Не является WSDL сервисом.', self::STATUS_NOT_WSDL_SERVICE);
            }

            try {
                $return = $soapClient->__soapCall($test->function->name, (array)json_decode($test->args, true));
                if (!$return){
                    throw new CSoapTestsException('Не получен результат функции.', self::STATUS_NOT_RETURN_RESULT);
                }
                $return = (isset($return->return)) ? $return->return : '';

//                if ($fa->return != json_encode($return, JSON_UNESCAPED_UNICODE)) {
//                    Yii::trace('Удача');
//                    $test_result->result = 1;
//                } else {
//                    Yii::trace('Вернули, но с ошибкой');
//                    $test_result->result = 0;
//                }
            } catch (SoapFault $e) {
                throw new CSoapTestsException('Не удалось вызвать функцию.', self::STATUS_NOT_CALL_FUNCTION);
            }


        } catch (CSoapTestsException $e){
//            echo $e->getMessage()."</br>";
//            echo $e->getCode();

//            $test_result = $e->getCode();
            $test_result = self::TEST_RESULT_ERROR;
        }

        $test->last_return = $return;
        $test->date_end = time();
        $test->status = self::STATUS_TEST_STOP;
        $test->test_result = $test_result;
        $test->save();
    }

    public function selectRunningFunctionTests($function_id)
    {
        $cmd = Yii::app()->db->createCommand(
            'UPDATE '.SoapTests::model()->tableName().'
            SET status=:status
            WHERE function_id=:function_id'
        );
        $cmd->execute(array(
            ":status"       => SoapTests::STATUS_TEST_RUN,
            ":function_id"  => $function_id
        ));
    }

    public function runTestFunction($function_id)
    {
        $cmd = Yii::app()->db->createCommand(
            'SELECT id
            FROM '.SoapTests::model()->tableName().'
            WHERE function_id=:function_id AND status=:status'
        );
        $tests = $cmd->queryAll(true, array(
            ":status"     => SoapTests::STATUS_TEST_RUN,
            ':function_id'=> $function_id
        ));
        foreach($tests as $t){
            $this->run($t['id']);
        }

        $cmd = Yii::app()->db->createCommand(
            'SELECT
                SUM(CASE `status` WHEN :status THEN (date_end-date_start) ELSE 0 END) AS `runtime`,
                MIN(`date_start`) AS `date_start`,
                MAX(`test_result`) AS `test_result`
            FROM '.SoapTests::model()->tableName().'
            WHERE function_id=:function_id'
        );
        $res = $cmd->queryRow(true, array(
            ":status"     => SoapTests::STATUS_TEST_STOP,
            ':function_id'=> $function_id
        ));
        return $res;
    }

    public function runTestFunction2($function_id)
    {
        $cmd = Yii::app()->db->createCommand(
            'SELECT id
            FROM '.SoapTests::model()->tableName().'
            WHERE function_id=:function_id AND status=:status'
        );
        $tests = $cmd->queryAll(true, array(
            ":status"     => SoapTests::STATUS_TEST_RUN,
            ':function_id'=> $function_id
        ));
        foreach($tests as $t){
            $this->run($t['id']);
        }

        $cmd = Yii::app()->db->createCommand(
            'SELECT
                id as `test_id`,
                `last_return`,
                `status`,
                (CASE `status` WHEN :status THEN (date_end-date_start) ELSE 0 END) AS `runtime`,
                `date_start`,
                `test_result`
            FROM '.SoapTests::model()->tableName().'
            WHERE function_id=:function_id'
        );
        $res = $cmd->queryAll(true, array(
            ":status"       => SoapTests::STATUS_TEST_STOP,
            ':function_id'  => $function_id
        ));
        return $res;
    }

    public function runTest($test_id)
    {
        $this->run($test_id);

        $cmd = Yii::app()->db->createCommand(
            'SELECT
                `last_return`,
                (CASE `status` WHEN :status THEN (date_end-date_start) ELSE 0 END) AS `runtime`,
                `date_start`,
                `test_result`
            FROM '.SoapTests::model()->tableName().'
            WHERE id=:test_id'
        );
        $res = $cmd->queryRow(true, array(
            ":status"   => SoapTests::STATUS_TEST_STOP,
            ':test_id'  => $test_id
        ));
        return $res;
    }

    public function runTestService($service_id)
    {
        $cmd = Yii::app()->db->createCommand(
            'SELECT id, function_id
            FROM '.SoapTests::model()->tableName().'
            WHERE service_id=:service_id AND status=:status'
        );
        $tests = $cmd->queryAll(true, array(
            ":status"     => SoapTests::STATUS_TEST_RUN,
            ':service_id' => $service_id
        ));
        $func = array();
        foreach($tests as $t){
            $func[$t['function_id']] = $t['function_id'];
            $this->run($t['id']);
        }

        $cmd = Yii::app()->db->createCommand(
            'SELECT
                function_id,
                SUM(CASE `status`
                    WHEN :status THEN (date_end-date_start) ELSE 0 END
                ) AS `runtime`,
                MIN(`date_start`) AS `date_start`,
                MAX(`test_result`) AS `test_result`
            FROM '.SoapTests::model()->tableName().'
            WHERE service_id=:service_id AND function_id IN ('.implode(',', $func).')
            GROUP BY function_id'
        );
        $res = $cmd->queryAll(true, array(
            ":status"       => SoapTests::STATUS_TEST_STOP,
            ':service_id'   => $service_id
        ));
        return $res;
    }

    public static function getTestResultByText($trid){
        if ($trid == SoapTests::TEST_RESULT_OK){
            $text = 'Без ошибок';
        } elseif ($trid == SoapTests::TEST_RESULT_ERROR){
            $text = 'Ошибка';
        } else {
            $text = 'Не выполнялось';
        }
        return $text;
    }

    public static function getCountRunningTests($service_id){
        $cmd = Yii::app()->db->createCommand(
            'SELECT COUNT(*) AS `c`
            FROM '.SoapTests::model()->tableName().'
            WHERE service_id=:service_id AND status=:status'
        );
        $row = $cmd->queryRow(true, array(
            ":status"     => SoapTests::STATUS_TEST_RUN,
            ':service_id' => $service_id
        ));
        return $row['c'];
    }

    public static function getCountRunningTestsFunc($function_id){
        $cmd = Yii::app()->db->createCommand(
            'SELECT COUNT(*) AS `c`
            FROM '.SoapTests::model()->tableName().'
            WHERE function_id=:function_id AND status=:status'
        );
        $row = $cmd->queryRow(true, array(
            ":status"     => SoapTests::STATUS_TEST_RUN,
            ':function_id' => $function_id
        ));
        return $row['c'];
    }

    /**
     * @param   int $id
     * @return  SoapTests
     */
    public function functionId($id)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => 'function_id = :function_id',
            'params'    => array(':function_id' => $id),
        ));
//        var_dump($this);die;
        return $this;
    }

    public function trueArgs($attribute, $params = array())
    {
//        try {
//            ini_set('soap.wsdl_cache_enabled', 0);
//            $soapClient = new SoapClient($this->function->service->url, array(
//                'login' => $this->function->service->login,
//                'password' => $this->function->service->password
//            ));
//            $return = $soapClient->__soapCall($this->function->name, (array) json_decode($this->args, true));
//            $this->return = json_encode($return, JSON_UNESCAPED_UNICODE);
//        } catch (SoapFault $e) {
//
//        }
//


//            var_dump($attribute, $params);
//        die;

        if (is_null(CJSON::decode($this->$attribute))){
            $this->addError($attribute, 'Не правильно заданы аргументы ('.$this->$attribute.')');
        }
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
//            'soapTestResults' => array(self::HAS_MANY, 'SoapTestResult', 'test_id'),
            'service' => array(self::BELONGS_TO, 'SoapService', 'service_id'),
            'function' => array(self::BELONGS_TO, 'SoapFunction', 'function_id'),
//            'function_args' => array(self::BELONGS_TO, 'SoapFunctionArgs', 'function_args_id'),
        );
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('name, args', 'required'),
            array('args', 'trueArgs'),
//            array('function_id', 'numerical', 'integerOnly'=>true),
//            array('return', 'safe'),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
//            array('id, function_id, name, args, return', 'safe', 'on'=>'search'),
        );
    }

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'soap_tests';
    }

    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return SoapTest the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }
}