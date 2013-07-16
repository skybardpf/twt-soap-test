<?php

/**
 * Модель для таблицы soap_function.
 * Список функций, которые поддерживает определенный SOAP сервис.
 *
 * @property integer    $id
 * @property integer    $service_id
 * @property string     $name
 *
 * The followings are the available model relations:
 * @property SoapService $soapService
 * @property SoapTest[] $soapTests
 */
class SoapFunctionParam extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return SoapFunction the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

    public function primaryKey() {
//        $pk = parent::getPrimaryKey();
        return array('function_id', 'name');
    }

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'soap_function_param';
    }

    /**
     * @static
     * @return array
     */
    public static function getParamTypes()
    {
        return array(
            'string' => 'string',
            'integer' => 'integer',
            'bool' => 'bool',
            'array' => 'array',
            'date' => 'date',
        );
    }


//    /**
//     * Возвращает список функция для определенного сервиса.
//     *
//     * @static
//     * @param integer $service_id
//     * @return SoapFunction[]
//     */
//    public static function getList($service_id)
//    {
//        $cmd = Yii::app()->db->createCommand('
//            SELECT
//                f.id, f.name,
//                (CASE
//                    WHEN t.count_tests IS NULL THEN 0
//                    ELSE t.count_tests END
//                ) as `count_tests`,
//                (CASE
//                    WHEN t.count_running IS NULL THEN 0
//                    WHEN t.count_running > 0 THEN 1
//                    WHEN t.count_running = 0 THEN 0
//                    END
//                ) as `has_running_tests`,
//                t.date_start,
//                t.status,
//                t.runtime,
//                t.test_result
//            FROM '.SoapFunction::model()->tableName().' AS f
//            LEFT JOIN (
//                SELECT
//                    `function_id` AS `fid`,
//                    COUNT(`id`) AS `count_tests`,
//                    SUM(CASE `status`
//                        WHEN '.SoapTest::STATUS_TEST_RUN.' THEN 1
//                        WHEN '.SoapTest::STATUS_TEST_IN_QUEUE.' THEN 1
//                        WHEN '.SoapTest::STATUS_TEST_STOP.' THEN 0
//                        END
//                    ) AS `count_running`,
//                    SUM(CASE `test_result`
//                        WHEN '.SoapTest::TEST_RESULT_ERROR.' OR '.SoapTest::TEST_RESULT_OK.'
//                        THEN (date_end-date_start) ELSE 0 END
//                    ) AS `runtime`,
//                    MIN(`date_start`) AS `date_start`,
//                    MAX(`status`) AS `status`,
//                    MAX(`test_result`) AS `test_result`
//                FROM '.SoapTest::model()->tableName().'
//                GROUP BY `function_id`
//            ) t ON f.id=t.fid
//            WHERE f.service_id=:service_id'
//        );
//        return $cmd->queryAll(true, array(
//            ":service_id" => $service_id
//        ));
//    }

//    /**
//     * Возвращает список ID запущенных тестов для определенного сервиса.
//     *
//     * @static
//     * @param integer $service_id
//     * @return int
//     */
//    public static function getRunningTests($service_id)
//    {
//        $cmd = Yii::app()->db->createCommand('
//            SELECT f.id
//            FROM '.SoapFunction::model()->tableName().' f
//            JOIN '.SoapTest::model()->tableName().' t ON t.function_id = f.id
//            WHERE f.service_id=:service_id AND t.status!=:status
//            GROUP BY f.id'
//        );
//        $data = $cmd->queryAll(true,
//            array(
//                ":service_id" => $service_id,
//                ":status" => SoapTest::STATUS_TEST_STOP
//            )
//        );
//        $ret = array();
//        foreach ($data as $v){
//            $ret[] = $v['id'];
//        }
//        return $ret;
//    }

//    /**
//     * Возвращает текущие статусы тестов. Список тестов передается в виде
//     * массива ID.
//     *
//     * @static
//     * @param array $listIds
//     * @return array
//     */
//    public static function getStatusesTests(array $listIds)
//    {
//        if (empty($listIds)){
//            return array();
//        }
//        $cmd = Yii::app()->db->createCommand('
//            SELECT
//                f.id,
//                SUM(
//                    CASE status WHEN '.SoapTest::STATUS_TEST_STOP.'
//                    THEN(date_end - date_start)
//                    ELSE 0 END
//                ) AS `runtime`,
//                MIN(date_start) AS `date_start`,
//                MAX(test_result) AS `test_result`
//            FROM '.SoapFunction::model()->tableName().' f
//            JOIN '.SoapTest::model()->tableName().' t
//                ON t.function_id = f.id
//            WHERE f.id IN ('.implode(',', $listIds).')
//            GROUP BY f.id
//            HAVING MAX(status)='.SoapTest::STATUS_TEST_STOP
//        );
//        return $cmd->queryAll();
//    }

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('name, type', 'required'),
//			array('name', 'unique'),
//			array('service_id', 'numerical', 'integerOnly'=>true),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
//			array('id, service_id, name', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'soapFunction' => array(self::BELONGS_TO, 'soapFunction', 'function_id'),
//			'soapTests' => array(self::HAS_ONE, 'SoapTest', 'function_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
            'name' => 'Название параметра',
			'type' => 'Тип',
		);
	}

//    /**
//     *  До удаление функции, удаляем все связанные с ней тесты.
//     */
//	protected function beforeDelete()
//	{
//        $this->deleteTests();
//		return parent::beforeDelete();
//	}
//
//    /**
//     *  Удалениям все тесты, связанные с данной функцией.
//     */
//    public function deleteTests()
//    {
//        foreach ($this->soapTests as $t) {
//            $t->delete();
//        }
//    }

//    /**
//     * Поставить все тесты данной функции в очередь на выполнение.
//     * Если тест уже запущен его статус не изменяется.
//     * @return array SoapTest[]
//     */
//    public function putTestsInQueue()
//    {
//        $list = array();
//        foreach ($this->soapTests as $t) {
//            if ($t->status != SoapTest::STATUS_TEST_RUN){
//                $t->status = SoapTest::STATUS_TEST_IN_QUEUE;
//                $t->save();
//                $list[]= $t;
//            }
//        }
//        return $list;
//    }

//    /**
//     * Возвращает результат по всем отработаным тестам (@see SoapTest::STATUS_TEST_STOP)
//     * данной функции.
//     *
//     * @return array
//     */
//    public function getLastResult()
//    {
//        $cmd = Yii::app()->db->createCommand(
//            'SELECT
//                SUM(CASE `status` WHEN :status THEN (date_end-date_start) ELSE 0 END) AS `runtime`,
//                MIN(`date_start`) AS `date_start`,
//                MAX(`test_result`) AS `test_result`
//            FROM '.SoapTest::model()->tableName().'
//            WHERE function_id=:function_id AND status=:status'
//        );
//        return $cmd->queryRow(true, array(
//            ":status" => SoapTest::STATUS_TEST_STOP,
//            ':function_id' => $this->primaryKey
//        ));
//    }
//
//	/**
//	 * Retrieves a list of models based on the current search/filter conditions.
//	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
//	 */
//	public function search()
//	{
//		// Warning: Please modify the following code to remove attributes that
//		// should not be searched.
//
//		$criteria=new CDbCriteria;
//
//		$criteria->compare('id',$this->id);
//		$criteria->compare('service_id',$this->service_id);
//		$criteria->compare('name',$this->name,true);
//
//		return new CActiveDataProvider($this, array(
//			'criteria'=>$criteria,
//		));
//	}
}