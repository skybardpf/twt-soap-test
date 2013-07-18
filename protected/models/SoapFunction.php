<?php

/**
 * Модель для таблицы soap_function.
 * Список функций, которые поддерживает определенный SOAP сервис.
 *
 * @property integer    $id
 * @property integer    $group_id
 * @property string     $name
 * @property string     $type           Тип функции CRUD
 * @property string     $description    Описание функции
 *
 * @property SoapTest[]             $soapTests
 * @property SoapFunctionParam[]    $soapFunctionParams
 * @property GroupFunctions         $groupFunctions
 */
class SoapFunction extends CActiveRecord
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

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'soap_function';
    }

    /**
     * @param array $params
     * @return array
     */
    public function checkParams(array $params)
    {
        $errors = array();
        foreach ($this->soapFunctionParams as $p){
            if ($this->type == 'save' && $p['name'] == 'id' && isset($params['id'])){
                if (!$this->_checkType($params['id'], $p['type'])) {
                    $errors[] = ' - Неверный тип поля ('.$p['type'].'): '.$p['name'];
                }
                unset($params['id']);
            } elseif (!isset($params[$p['name']])){
                $errors[] = ' - Пропущенно поле: '.$p['name'];
            } else {
                if (!$this->_checkType($params[$p['name']], $p['type'])) {
                    $errors[] = ' - Неверный тип поля ('.$p['type'].'): '.$p['name'];
                }
                unset($params[$p['name']]);
            }
        }
//        $p = array();
//        if (!empty($params)){
//            foreach ($params as $k=>$v){
//                $p[] = $k;
//            }
////            var_dump($p);die;
//            $errors = array_merge($errors, array(' - Переданы лишние поля: '.implode(', ', $p).'<br/>'));
//        }
        return $errors;
    }

    /**
     * @static
     * @return array
     */
    public static function getTypes()
    {
        return array(
            'get' => 'Get',
            'list' => 'List',
            'save' => 'Save',
            'delete' => 'Delete',
        );
    }

    /**
     * @param mixed $val
     * @param string $type
     * @param bool $is_empty
     * @return bool
     */
    private function _checkType($val, $type, $is_empty=true)
    {
        if ($is_empty && empty($val)){
            return true;
        }
        if ($type == 'string'){
            return is_string($val);
        } elseif ($type == 'integer'){
            return is_int($val);
        } elseif ($type == 'bool'){
            return is_bool($val);
        } elseif ($type == 'array'){
            return is_array($val);
        }elseif ($type == 'date'){
            return (FALSE !== strtotime($val));
        }
        return false;
    }

    /**
     * Возвращает список функция для определенного сервиса.
     *
     * @static
     * @param integer $service_id
     * @return SoapFunction[]
     */
    public static function getList($service_id)
    {
        $cmd = Yii::app()->db->createCommand('
            SELECT
                f.id,
                gf.name as `group_name`,
                gf.id as `group_id`,
                f.name,
                (CASE
                    WHEN t.count_tests IS NULL THEN 0
                    ELSE t.count_tests
                END) as `count_tests`,
                (CASE
                    WHEN t.count_running IS NULL THEN 0
                    WHEN t.count_running > 0 THEN 1
                    WHEN t.count_running = 0 THEN 0
                END) as `has_running_tests`,
                t.date_start,
                t.status,
                t.runtime,
                t.test_result
            FROM '.SoapFunction::model()->tableName().' AS f
            JOIN '.GroupFunctions::model()->tableName().' as gf ON gf.id=f.group_id
            LEFT JOIN
                (SELECT
                    `function_id` AS `fid`,
                        COUNT(`id`) AS `count_tests`,
                        SUM(CASE `status`
                            WHEN '.SoapTest::STATUS_TEST_RUN.' THEN 1
                            WHEN '.SoapTest::STATUS_TEST_IN_QUEUE.' THEN 1
                            WHEN '.SoapTest::STATUS_TEST_STOP.' THEN 0
                        END) AS `count_running`,
                        SUM(IF(`test_result`='.SoapTest::TEST_RESULT_ERROR.' OR
                             `test_result`='.SoapTest::TEST_RESULT_OK.',
                            (date_end - date_start),  0
                        )) AS `runtime`,
                        MIN(`date_start`) AS `date_start`,
                        MAX(`status`) AS `status`,
                        MAX(`test_result`) AS `test_result`
                FROM '.SoapTest::model()->tableName().'
                GROUP BY `function_id`) t ON f.id = t.fid
            WHERE gf.service_id = :service_id'
        );
        return $cmd->queryAll(true, array(
            ":service_id" => $service_id
        ));
    }

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
                f.id,
                SUM(
                    CASE status WHEN '.SoapTest::STATUS_TEST_STOP.'
                    THEN(date_end - date_start)
                    ELSE 0 END
                ) AS `runtime`,
                MIN(date_start) AS `date_start`,
                MAX(test_result) AS `test_result`
            FROM '.SoapFunction::model()->tableName().' f
            JOIN '.SoapTest::model()->tableName().' t
                ON t.function_id = f.id
            WHERE f.id IN ('.implode(',', $listIds).')
            GROUP BY f.id
            HAVING MAX(status)='.SoapTest::STATUS_TEST_STOP
        );
        return $cmd->queryAll();
    }

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('name', 'required'),
			array('name', 'unique'),
			array('name', 'length', 'max' => 45),

            array('type', 'required'),
			array('type', 'in', 'range' => array_keys(SoapFunction::getTypes())),

            array('group_id', 'required'),
//            array('group_id', 'in', 'range' => array_keys(SoapFunction::getTypes())),

            array('description', 'length', 'max' => 45),

//            array('service_id', 'numerical', 'integerOnly'=>true),
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
//			'soapService' => array(self::BELONGS_TO, 'SoapService', 'service_id'),
			'soapTests' => array(self::HAS_MANY, 'SoapTest', 'function_id'),
			'soapFunctionParams' => array(self::HAS_MANY, 'SoapFunctionParam', 'function_id'),
			'groupFunctions' => array(self::BELONGS_TO, 'GroupFunctions', 'group_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
            'group_id' => 'Группа',
            'name' => 'Имя',
            'type' => 'Тип функции',
            'description' => 'Описание',
		);
	}

    /**
     *  До удаление функции, удаляем все связанные с ней тесты.
     */
	protected function beforeDelete()
	{
        $this->deleteTests();
		return parent::beforeDelete();
	}

    /**
     *  Удалениям все тесты, связанные с данной функцией.
     */
    public function deleteTests()
    {
        foreach ($this->soapTests as $t) {
            $t->delete();
        }
    }

    /**
     * Поставить все тесты данной функции в очередь на выполнение.
     * Если тест уже запущен его статус не изменяется.
     * @return array SoapTest[]
     */
    public function putTestsInQueue()
    {
        $list = array();
        foreach ($this->soapTests as $t) {
            if ($t->status != SoapTest::STATUS_TEST_RUN){
                $t->status = SoapTest::STATUS_TEST_IN_QUEUE;
                $t->save();
                $list[]= $t;
            }
        }
        return $list;
    }

    /**
     * Возвращает результат по всем отработаным тестам (@see SoapTest::STATUS_TEST_STOP)
     * данной функции.
     *
     * @return array
     */
    public function getLastResult()
    {
        $cmd = Yii::app()->db->createCommand(
            'SELECT
                SUM(CASE `status` WHEN :status THEN (date_end-date_start) ELSE 0 END) AS `runtime`,
                MIN(`date_start`) AS `date_start`,
                MAX(`test_result`) AS `test_result`
            FROM '.SoapTest::model()->tableName().'
            WHERE function_id=:function_id AND status=:status'
        );
        return $cmd->queryRow(true, array(
            ":status" => SoapTest::STATUS_TEST_STOP,
            ':function_id' => $this->primaryKey
        ));
    }
}