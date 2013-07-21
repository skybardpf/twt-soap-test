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
    const FUNCTION_TYPE_GET = 'get';
    const FUNCTION_TYPE_LIST = 'list';
    const FUNCTION_TYPE_SAVE = 'save';
    const FUNCTION_TYPE_DELETE = 'delete';

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
     * @param integer $input_param
     * @return SoapFunctionParam[] Возвращает список входных параметров. Формат [name => model].
     * @throws CHttpException
     */
    public function getParamsByType($input_param = SoapFunctionParam::TYPE_INPUT)
    {
        /**
         * @var $params SoapFunctionParam[]
         */
        $params = array();
        foreach($this->soapFunctionParams as $p){
            if ($p->input_param == $input_param){
                $params[$p->name] = $p;
            }
        }
        return $params;
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
     * @param string $type_of_data
     * @param mixed $value
     * @param boolean $required
     * @return boolean
     */
    private function _checkNativeType($type_of_data, $value, $required = false)
    {
        if ($required){
            if ($type_of_data == SoapFunctionParam::TYPE_DATA_BOOLEAN){
                if (!is_bool($value) && empty($value)){
                    return false;
                }
            } elseif (empty($value)){
                return false;
            }
        }

        if ($type_of_data == SoapFunctionParam::DEFAULT_TYPE_OF_DATA){
            return is_string($value);
        } elseif ($type_of_data == SoapFunctionParam::TYPE_DATA_INTEGER){
            return is_integer((int)$value);
        } elseif ($type_of_data == SoapFunctionParam::TYPE_DATA_BOOLEAN){
            return is_bool($value);
        } elseif ($type_of_data == SoapFunctionParam::TYPE_DATA_ARRAY){
            return is_array($value);
        }elseif ($type_of_data == SoapFunctionParam::TYPE_DATA_DATE){
            return (FALSE !== strtotime($value));
        }
        return false;
    }

    /**
     * @param SoapFunctionParam $param
     * @param mixed $value
     * @return boolean
     */
    private function _checkAllType(SoapFunctionParam $param, $value)
    {
        if (SoapFunctionParam::isNativeType($param->type_of_data)){
            return $this->_checkNativeType($param->type_of_data, $value, $param->required);
        }

        if ($param->required && empty($value)){
            return false;
        }

        // Example: [{"000000002", "000000003"}]
        if ($param->type_of_data == SoapFunctionParam::TYPE_DATA_ARRAY_VALUES){
            if (!is_array($value) || empty($param->array_type_of_data)){
                return false;
            }
            foreach($value as $v){
                if (!$this->_checkNativeType($param->array_type_of_data, $v)){
                    return false;
                }
            }
            return true;
        // Example: [{id_yur0":"000000002", "type_yur0":"Контрагенты"}]
        } elseif ($param->type_of_data == SoapFunctionParam::TYPE_DATA_ARRAY_ID_INDEX_TYPE_INDEX){
            if (!is_array($value) || !isset($value[0])){
                return false;
            }
            $value = $value[0];
            $count = count($value);
            if ($count % 2){
                return false;
            }
            for($i = 0, $len = $count / 2; $i<$len; $i++){
                $key_id = 'id_yur'.$i;
                $key_type = 'type_yur'.$i;
                if (!isset($value[$key_id]) || !isset($value[$key_type]) || !is_string($value[$key_id]) || !is_string($value[$key_type])){
                    return false;
                }
            }
            return true;
        }
        return false;
    }

    /**
     * @param string $return JSON строка с ответом от SOAP сервиса.
     * @return array
     * @throws CSoapTestException
     */
    public function checkAfterRequest($return)
    {
        $not_found = array();
        $required = array();
        $wrong_data_type = array();

        $types = SoapFunctionParam::getTypesOfData();
        if($this->type == self::FUNCTION_TYPE_DELETE){
            if (!is_bool($return)){
                $wrong_data_type[] = array(
                    'key' => 'return',
                    'type_of_data' => SoapFunctionParam::TYPE_DATA_BOOLEAN
                );
            }
        } elseif($this->type == self::FUNCTION_TYPE_GET){
            $ret = CJSON::decode($return);

            if (!empty($ret)){
                if (!isset($ret[0])){
                    throw new CSoapTestException('Получен неизвестный результат функции.');
                }
                $ret = $ret[0];
                $output_params = $this->getParamsByType(SoapFunctionParam::TYPE_OUTPUT);

                foreach ($output_params as $key=>$value){
                    if (!isset($ret[$key])){
                        $not_found[] = $key;
                    } elseif (!$this->_checkAllType($output_params[$key], $ret[$key])) {
                        if ($output_params[$key]->type_of_data == SoapFunctionParam::TYPE_DATA_ARRAY_VALUES) {
                            $type = $types[$output_params[$key]->type_of_data] . ' : ' . $types[$output_params[$key]->array_type_of_data];
                        } elseif ($output_params[$key]->type_of_data == SoapFunctionParam::TYPE_DATA_ARRAY_ID_INDEX_TYPE_INDEX) {
                            $type = 'массив вида: [{id_yur0":"000000002", "type_yur0":"Контрагенты"}]';
                        } else {
                            $type = $types[$output_params[$key]->type_of_data];
                        }
                        $wrong_data_type[] = array(
                            'key' => $key,
                            'type_of_data' => $type
                        );
                    }
                }
            }
        }

        if (empty($not_found) && empty($required) && empty($wrong_data_type)){
            return array();
        }
        return array(
            'not_found' => $not_found,
            'required' => $required,
            'wrong_data_type' => $wrong_data_type,
        );
    }

    /**
     * @param SoapTest $test
     * @return array
     * @throws CSoapTestException
     */
    public function checkBeforeRequest(SoapTest $test)
    {
        if (empty($test->args) && !is_string($test->args)){
            throw new CSoapTestException('Не заданы аргументы теста.');
        }
        $args = CJSON::decode($test->args);
        if (empty($args) || !is_array($args) || !isset($args[0])){
            throw new CSoapTestException('Неправильный формат аргументов теста.');
        }
        $args = $args[0];

        $input_params = $this->getParamsByType(SoapFunctionParam::TYPE_INPUT);
        if (empty($input_params)){
            throw new CSoapTestException('Для функции не заданы входящие параметры.');
        }

        $output_params = array();
        if ($this->type != self::FUNCTION_TYPE_DELETE){
            $output_params = $this->getParamsByType(SoapFunctionParam::TYPE_OUTPUT);
            if (empty($output_params)){
                throw new CSoapTestException('Для функции не заданы выходные параметры.');
            }
        }

        $not_found = array();
        $required = array();
        $wrong_data_type = array();

        $types = SoapFunctionParam::getTypesOfData();

        if($this->type == self::FUNCTION_TYPE_SAVE){
            // TODO для save отдельный обработчик
        } else {
            foreach ($args as $key=>$value){
                if (!isset($input_params[$key])){
                    $not_found[] = $key;
                } elseif (empty($key)) {
                    if ($input_params[$key]->required){
                        $required[] = $key;
                    }
                } elseif (!$this->_checkAllType($input_params[$key], $value)) {
                    if ($output_params[$key]->type_of_data == SoapFunctionParam::TYPE_DATA_ARRAY_VALUES) {
                        $type = $types[$output_params[$key]->type_of_data] . ' : ' . $types[$output_params[$key]->array_type_of_data];
                    } else {
                        $type = $types[$output_params[$key]->type_of_data];
                    }

                    $wrong_data_type[] = array(
                        'key' => $key,
                        'type_of_data' => $type
                    );
                }
            }
        }
        if (empty($not_found) && empty($required) && empty($wrong_data_type)){
            return array();
        }
        return array(
            'not_found' => $not_found,
            'required' => $required,
            'wrong_data_type' => $wrong_data_type,
        );
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
            WHERE gf.service_id = :service_id
            ORDER BY f.group_id ASC, f.name ASC'
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

            array('SoapFunctionParam', 'validateParams'),

            array('description', 'length', 'max' => 45),

//            array('service_id', 'numerical', 'integerOnly'=>true),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
//			array('id, service_id, name', 'safe', 'on'=>'search'),
		);
	}

    /**
     * Валидация заданных параметров функции.
     * @param string $attribute
     */
    public function validateParams($attribute){
        if (isset($_POST[$attribute]) && !empty($_POST[$attribute])){
            $input_names = array();
            $output_names = array();
            foreach ($_POST[$attribute] as $i=>$param){
                $p = new SoapFunctionParam();
                $p->attributes = $_POST[$attribute][$i];
//                var_dump($p->attributes);die;
                $valid = true;
                foreach($p->attributes as $atr){
                    if (!$p->validate($atr)){
                        $valid = false;
                        $this->addError($attribute.'['.$i.']['.$atr.']', $p->getError($atr));
                    }
                }
                if ($p->input_param){
                    if (!isset($input_names[$p->name])){
                        $input_names[$p->name] = $p;
                    } else {
                        $this->addError($attribute.'['.$i.'][name]', 'Название входного параметра {'.$p->name.'} уже существует.');
                    }
                } else {
                    if (!isset($output_names[$p->name])){
                        $output_names[$p->name] = $p;
                    } else {
                        $this->addError($attribute.'['.$i.'][name]', 'Название выходного параметра {'.$p->name.'} уже существует.');
                    }
                }
            }
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
     * До удаление функции, удаляем все связанные с ней тесты {@see SoapTest}
     * и параметры {@see SoapFunctionParam}.
     */
	protected function beforeDelete()
	{
        if (parent::beforeDelete()){
            $this->deleteParams();
            $this->deleteTests();
            return true;
        }
		return false;
	}

    /**
     * Удалениям все тесты, связанные с данной функцией.
     * @return boolean $valid
     */
    public function deleteTests()
    {
        $valid = true;
        foreach ($this->soapTests as $t) {
            if (!$t->delete()){
                $valid = false;
            }
        }
        return $valid;
    }

    /**
     * Удалениям все параметры, связанные с данной функцией.
     * @return boolean $valid
     */
    public function deleteParams()
    {
        $valid = true;
        foreach ($this->soapFunctionParams as $t) {
            if (!$t->delete()){
                $valid = false;
            }
        }
        return $valid;
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