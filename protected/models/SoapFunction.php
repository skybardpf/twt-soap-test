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
     * @param boolean $required
     * @return boolean
     */
    private function _checkType($val, $type, $required = false)
    {
        if ($required){
            if ($type == SoapFunctionParam::TYPE_DATA_BOOLEAN){
                if (!is_bool($val) && empty($val)){
                    return false;
                }
            } elseif (empty($val)){
                return false;
            }
        }

        if ($type == SoapFunctionParam::DEFAULT_TYPE_OF_DATA){
            return is_string($val);
        } elseif ($type == SoapFunctionParam::TYPE_DATA_INTEGER){
            return is_int($val);
        } elseif ($type == SoapFunctionParam::TYPE_DATA_BOOLEAN){
            return is_bool($val);
        } elseif ($type == SoapFunctionParam::TYPE_DATA_ARRAY){
            return is_array($val);
        }elseif ($type == SoapFunctionParam::TYPE_DATA_DATE){
            return (FALSE !== strtotime($val));
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
                    } elseif (!$this->_checkType($ret[$key], $output_params[$key]->type_of_data, $output_params[$key]->required)) {
                        $wrong_data_type[] = array(
                            'key' => $key,
                            'type_of_data' => $output_params[$key]->type_of_data
                        );
                    }
                }
            }

//            var_dump($ret);die;
        }

//        if (in_array($this->soapFunction->type, array('get', 'list'))){
//            $ret = CJSON::decode($return);
//            $errors = array();
//            if ($this->soapFunction->type == 'list'){
//                if (empty($ret)){
//                    throw new CSoapTestException('Получен неизвестный результат функции.');
//                }
////                        foreach($ret as $p){
////                            var_dump($p);die;
//                $errors = $this->soapFunction->checkParams($ret[0]);
////                        }
//            } elseif ($this->soapFunction->type == 'get'){
//                if (!empty($ret)){
//                    if (!isset($ret[0])){
//                        throw new CSoapTestException('Получен неизвестный результат функции.');
//                    }
//                    $errors = $this->soapFunction->checkParams($ret[0]);
//                }
//            }
//            if (!empty($errors)){
//                throw new CSoapTestException('Ошибки:<br/>' . implode('<br/>', $errors).'<br/>Результат:<br/>'.$return);
//            }
//        } elseif ($this->soapFunction->type == 'save'){
////                    if (!ctype_digit($return)){
////                        throw new CSoapTestException('Ошибки при сохранении данных.<br/>Результат:<br/>'.$return);
////                    }
////                    $args = CJSON::decode($this->args);
////                    $ret = $this->soapFunction->checkParams($args);
////                    if (!empty($ret)){
////                        throw new CSoapTestException('Ошибки в передаваемых параметрах:<br/>' . implode('<br/>', $ret));
////                    }
//
//
//        } elseif($this->soapFunction->type == 'delete'){
//            if (!is_bool($return)){
//                throw new CSoapTestException('Ответ не boolean:<br/>Результат:<br/>'.$return);
//            }
//        }

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

        if ($this->type != self::FUNCTION_TYPE_DELETE){
            $output_params = $this->getParamsByType(SoapFunctionParam::TYPE_OUTPUT);
            if (empty($output_params)){
                throw new CSoapTestException('Для функции не заданы выходные параметры.');
            }
        }

        $not_found = array();
        $required = array();
        $wrong_data_type = array();
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
                } elseif (!$this->_checkType($value, $input_params[$key]->type_of_data)) {
                    $wrong_data_type[] = array(
                        'key' => $key,
                        'type_of_data' => $input_params[$key]->type_of_data
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