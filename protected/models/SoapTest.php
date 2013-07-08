<?php
class CSoapTestException extends CException {}

/**
 *  Модель для проведения тестов. Тест принадлежит определенной функции SOAP сервиса.
 *
 *  @author Skibardin A.A. <skybardpf@artektiv.ru>
 *
 *  @see SoapFunction
 *  @see SoapService
 *
 *  @property $id           int
 *  @property $name         string
 *  @property $function_id  int
 *  @property $date_create  int timestamp
 *  @property $date_start   int timestamp
 *  @property $date_end     int timestamp
 *  @property $status       int
 *  @property $test_result  int
 *  @property $args         string
 *  @property $last_return  string
 *  @property $soapFunction SoapFunction
 */
class SoapTest extends CActiveRecord {
    const STATUS_TEST_STOP          = 1;
    const STATUS_TEST_IN_QUEUE      = 2;
    const STATUS_TEST_RUN           = 3;

    const TEST_RESULT_OK            = 1;
    const TEST_RESULT_NOT_EXECUTED  = 2;
    const TEST_RESULT_ERROR         = 3;

    /**
     * Возвращает список тестов для определенной функции.
     *
     * @static
     * @param int $func_id
     * @return SoapTest[]
     */
    public static function getList($func_id)
    {
        $cmd = Yii::app()->db->createCommand('
            SELECT
                id,
                name,
                function_id,
                date_start,
                status,
                ( CASE test_result
                    WHEN '.self::TEST_RESULT_ERROR.' OR '.self::TEST_RESULT_OK.'
                    THEN( date_end - date_start )
                    ELSE 0
                    END
                ) AS `runtime`,
                test_result,
                last_return,
                args
            FROM '.self::model()->tableName().'
            WHERE function_id = :function_id'
        );
        return $cmd->queryAll(true, array(
            ":function_id" => $func_id
        ));
    }

    /**
     * Возвращает текущие статусы тестов. Список тестов передается в виде
     * массива ID.
     *
     * @static
     * @param array $listIds
     * @return array
     */
    public static function getStatusesTests(array $listIds)
    {
        if (empty($listIds)){
            return array();
        }
        $cmd = Yii::app()->db->createCommand('
            SELECT
                id,
                date_start,
                status,
                ( CASE test_result
                    WHEN '.self::TEST_RESULT_ERROR.' OR '.self::TEST_RESULT_OK.'
                    THEN( date_end - date_start )
                    ELSE 0
                    END
                ) AS `runtime`,
                test_result,
                last_return
            FROM '.self::model()->tableName().'
            WHERE id IN ('.implode(',', $listIds).') AND status=:status'

        );
        return $cmd->queryAll(true,
            array(
                ":status" => SoapTest::STATUS_TEST_STOP
            )
        );
    }

    /**
     * Возвращает список ID запущенных тестов для определенной функции.
     *
     * @static
     * @param int $func_id
     * @return array
     */
    public static function getRunningTests($func_id)
    {
        $cmd = Yii::app()->db->createCommand('
            SELECT id
            FROM '.self::model()->tableName().'
            WHERE function_id=:function_id AND status=:status');
        $data = $cmd->queryAll(true,
            array(
                ":function_id"  => $func_id,
                ":status"       => SoapTest::STATUS_TEST_RUN
            )
        );

        $ret = array();
        foreach ($data as $v){
            $ret[] = $v['id'];
        }
        return $ret;
    }

    /**
     * Запускаем Unit-тест на выполнение.
     * @param bool $force Если TRUE, тест запускается даже, если он уже запущен.
     * @return bool Если false, значит тест уже выполняется.
     */
    public function run($force = false)
    {
        if (!$force && $this->status == self::STATUS_TEST_RUN){
            return false;
        }

        $test_result = self::TEST_RESULT_NOT_EXECUTED;
        $this->date_start = time();
        $this->date_end = NULL;
        $this->status = self::STATUS_TEST_RUN;
        $this->test_result = $test_result;
        $this->save();

        try {
            if (!$this->soapFunction->soapService->isAvailableService()){
                throw new CSoapTestException('SOAP сервис не доступен.');
            }
            $soapClient = $this->soapFunction->soapService->getSoapClient();
            if (!$soapClient){
                throw new CSoapTestException('Не является WSDL сервисом.');
            }

            try {
                $return = $soapClient->__soapCall($this->soapFunction->name, (array)json_decode($this->args, true));
                if (empty($return) || !isset($return->return) || empty($return->return) || !is_string($return->return)){
                    throw new CSoapTestException('Не получен результат функции.');
                }
                if (stripos($return->return, 'error') !== false){
                    throw new CSoapTestException($return->return);
                }
                $return = $return->return;

            } catch (SoapFault $e) {
                throw new CSoapTestException($e->getMessage());
            }
        } catch (CSoapTestException $e){
            $return = $e->getMessage();
            $test_result = self::TEST_RESULT_ERROR;
        }

        $this->last_return = $return;
        $this->date_end = time();
        $this->status = self::STATUS_TEST_STOP;
        $this->test_result = ($test_result != self::TEST_RESULT_ERROR) ?  self::TEST_RESULT_OK : $test_result;
        $this->save();

        return true;
    }

    /**
     * @param  int $errorId
     * @param  int $status
     * @return string
     */
    public static function getTestResultByText($errorId, $status = SoapTest::STATUS_TEST_STOP){
        if ($status == SoapTest::STATUS_TEST_RUN){
            $text = 'Выполняется';
        } elseif ($errorId == SoapTest::TEST_RESULT_OK){
            $text = 'Без ошибок';
        } elseif ($errorId == SoapTest::TEST_RESULT_ERROR){
            $text = 'Ошибка';
        } else {
            $text = 'Не выполнялось';
        }
        return $text;
    }

//    public function selectRunningFunctionTests($function_id)
//    {
//        $cmd = Yii::app()->db->createCommand(
//            'UPDATE '.SoapTest::model()->tableName().'
//            SET status=:status
//            WHERE function_id=:function_id'
//        );
//        $cmd->execute(array(
//            ":status"       => SoapTest::STATUS_TEST_RUN,
//            ":function_id"  => $function_id
//        ));
//    }

//    public function runTestFunction($function_id)
//    {
//        $cmd = Yii::app()->db->createCommand(
//            'SELECT id
//            FROM '.SoapTest::model()->tableName().'
//            WHERE function_id=:function_id AND status=:status'
//        );
//        $tests = $cmd->queryAll(true, array(
//            ":status"     => SoapTest::STATUS_TEST_RUN,
//            ':function_id'=> $function_id
//        ));
//        foreach($tests as $t){
//            $this->run($t['id']);
//        }
//
//        $cmd = Yii::app()->db->createCommand(
//            'SELECT
//                SUM(CASE `status` WHEN :status THEN (date_end-date_start) ELSE 0 END) AS `runtime`,
//                MIN(`date_start`) AS `date_start`,
//                MAX(`test_result`) AS `test_result`
//            FROM '.SoapTest::model()->tableName().'
//            WHERE function_id=:function_id'
//        );
//        $res = $cmd->queryRow(true, array(
//            ":status"     => SoapTest::STATUS_TEST_STOP,
//            ':function_id'=> $function_id
//        ));
//        return $res;
//    }

//    public function runTestFunction2($function_id)
//    {
//        $cmd = Yii::app()->db->createCommand(
//            'SELECT id
//            FROM '.SoapTest::model()->tableName().'
//            WHERE function_id=:function_id AND status=:status'
//        );
//        $tests = $cmd->queryAll(true, array(
//            ":status"     => SoapTest::STATUS_TEST_RUN,
//            ':function_id'=> $function_id
//        ));
//        foreach($tests as $t){
//            $this->run($t['id']);
//        }
//
//        $cmd = Yii::app()->db->createCommand(
//            'SELECT
//                id as `test_id`,
//                `last_return`,
//                `status`,
//                (CASE `status` WHEN :status THEN (date_end-date_start) ELSE 0 END) AS `runtime`,
//                `date_start`,
//                `test_result`
//            FROM '.SoapTest::model()->tableName().'
//            WHERE function_id=:function_id'
//        );
//        $res = $cmd->queryAll(true, array(
//            ":status"       => SoapTest::STATUS_TEST_STOP,
//            ':function_id'  => $function_id
//        ));
//        return $res;
//    }
//
//    public function runTest($test_id)
//    {
//        $this->run($test_id);
//
//        $cmd = Yii::app()->db->createCommand(
//            'SELECT
//                `last_return`,
//                (CASE `status` WHEN :status THEN (date_end-date_start) ELSE 0 END) AS `runtime`,
//                `date_start`,
//                `test_result`
//            FROM '.SoapTest::model()->tableName().'
//            WHERE id=:test_id'
//        );
//        $res = $cmd->queryRow(true, array(
//            ":status"   => SoapTest::STATUS_TEST_STOP,
//            ':test_id'  => $test_id
//        ));
//        return $res;
//    }
//
//    public function runTestService($service_id)
//    {
//        $cmd = Yii::app()->db->createCommand(
//            'SELECT id, function_id
//            FROM '.SoapTest::model()->tableName().'
//            WHERE service_id=:service_id AND status=:status'
//        );
//        $tests = $cmd->queryAll(true, array(
//            ":status"     => SoapTest::STATUS_TEST_RUN,
//            ':service_id' => $service_id
//        ));
//        $func = array();
//        foreach($tests as $t){
//            $func[$t['function_id']] = $t['function_id'];
//            $this->run($t['id']);
//        }
//
//        $cmd = Yii::app()->db->createCommand(
//            'SELECT
//                function_id,
//                SUM(CASE `status`
//                    WHEN :status THEN (date_end-date_start) ELSE 0 END
//                ) AS `runtime`,
//                MIN(`date_start`) AS `date_start`,
//                MAX(`test_result`) AS `test_result`
//            FROM '.SoapTest::model()->tableName().'
//            WHERE service_id=:service_id AND function_id IN ('.implode(',', $func).')
//            GROUP BY function_id'
//        );
//        $res = $cmd->queryAll(true, array(
//            ":status"       => SoapTest::STATUS_TEST_STOP,
//            ':service_id'   => $service_id
//        ));
//        return $res;
//    }
//


//    public static function getCountRunningTests($service_id){
//        $cmd = Yii::app()->db->createCommand(
//            'SELECT COUNT(*) AS `c`
//            FROM '.SoapTest::model()->tableName().'
//            WHERE service_id=:service_id AND status=:status'
//        );
//        $row = $cmd->queryRow(true, array(
//            ":status"     => SoapTest::STATUS_TEST_RUN,
//            ':service_id' => $service_id
//        ));
//        return $row['c'];
//    }

//    public static function getCountRunningTestsFunc($function_id){
//        $cmd = Yii::app()->db->createCommand(
//            'SELECT COUNT(*) AS `c`
//            FROM '.SoapTest::model()->tableName().'
//            WHERE function_id=:function_id AND status=:status'
//        );
//        $row = $cmd->queryRow(true, array(
//            ":status"     => SoapTest::STATUS_TEST_RUN,
//            ':function_id' => $function_id
//        ));
//        return $row['c'];
//    }

//    /**
//     * @param   int $id
//     * @return  SoapTest
//     */
//    public function functionId($id)
//    {
//        $this->getDbCriteria()->mergeWith(array(
//            'condition' => 'function_id = :function_id',
//            'params'    => array(':function_id' => $id),
//        ));
////        var_dump($this);die;
//        return $this;
//    }

    public function trueArgs($attribute)
    {
        if (!empty($this->$attribute) && is_null(CJSON::decode($this->$attribute))){
            $this->addError(
                $attribute,
                'Не правильный формат JSON для аргументов.'
            );
        }
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
//            'id' => 'ID',
            'name' => 'Название',
            'args' => 'Аргументы',
//            'testCounts' => 'Количество тестов'
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        return array(
            'soapFunction' => array(self::BELONGS_TO, 'SoapFunction', 'function_id'),
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