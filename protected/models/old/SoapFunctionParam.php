<?php

/**
 * Модель для таблицы soap_function_param.
 * Параметр функции. Используются для валидации входящих и исходящих параметров при тестировании.
 *
 * @author Skibardin A.A. <skybardpf@artektiv.ru>
 *
 * @property integer    $id
 * @property integer    $function_id
 * @property string     $name
 * @property string     $parent_name
 * @property integer    $parent_id
 * @property boolean    $input_param           input (true) | output (false)
 * @property string     $type_of_data
 * @property string     $array_type_of_data     Для массивов: Тип данных значений массивов.
 * @property boolean    $required
 * @property string     $description
 *
 * @property SoapFunction           $soapFunction
 * @property SoapFunctionParam      $parent_test
 * @property SoapFunctionParam[]    $children_test
 */
class SoapFunctionParam extends CActiveRecord
{
    public $children = array();

    const CHILDREN_DELIMITER = '--children--';

    const DEFAULT_TYPE_OF_DATA = 'string';
    const TYPE_DATA_BOOLEAN = 'boolean';
    const TYPE_DATA_INTEGER = 'integer';
    const TYPE_DATA_ARRAY = 'array';
    const TYPE_DATA_DATE = 'date';

    const TYPE_DATA_ELEMENT_TABLE = 'element_table';
    const TYPE_DATA_TABLE = 'table';

    const TYPE_DATA_ARRAY_VALUES= 'array_values';
    const TYPE_DATA_FIELD_VALUE = 'field_value';    // TODO not used
    const TYPE_DATA_ARRAY_FIELDS = 'array_fields';
    const TYPE_DATA_ARRAY_ID_INDEX_TYPE_INDEX = 'array_id_index_type_index';
    const TYPE_DATA_ARRAY_ELEMENTS_STRUCTURE = 'array_elements_structure';

    const TYPE_INPUT = 1;
    const TYPE_OUTPUT = 0;

    /**
     * @var array $children Список параметров, которые принадлежат текущему параметру.
     * Различные виды массивов.
     */
//    public $children = array();

	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return SoapFunctionParam the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

    /**
     * @return array Возвращаем составной ключ для таблицы.
     */
    public function primaryKey()
    {
        return array('function_id', 'name', 'input_param');
    }

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'soap_function_param';
    }

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
			array('name', 'required', 'message' => 'Укажите название параметра.'),
			array('name', 'match', 'pattern' => '/^[A-Za-zАБВГДЕЁЖЗИЙКЛМНОПРСТУФХЦЧШЩЬЫЪЭЮЯабвгдеёжзийклмнопрстуфхцчшщьыъэюя0-9_]+$/'),
			array('input_param, type_of_data', 'required'),

			array('type_of_data', 'in', 'range' => array_keys(self::getAllTypesOfData())),
			array('array_type_of_data', 'in', 'range' => array_keys(self::getNativeTypesOfData())),

			array('required, input_param', 'boolean'),
			array('input_param', 'default', 'value' => false),
			array('required', 'default', 'value' => false),

            array('description', 'safe'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		return array(
			'soapFunction' => array(self::BELONGS_TO, 'soapFunction', 'function_id'),

//            'parent' => array(self::BELONGS_TO, 'SoapFunctionParam', 'parent_id'),
//            'children' => array(self::HAS_MANY, 'SoapFunctionParam', 'parent_id'),
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
			'input_param' => 'Входной/Выходной параметр',
			'type_of_data' => 'Тип данных',
			'array_type_of_data' => 'Тип данных массива',
			'required' => 'Рекомендуемое поле',
			'description' => 'Описание',
		);
	}

    /**
     * @static
     * @return array Возвращает массив поддерживаемых типов данных (key => label).
     */
    public static function getTypesOfData()
    {
        return array_merge(
            self::getNativeTypesOfData(),
            array(
//                self::TYPE_DATA_ARRAY => 'Массив (Array)',
//                self::TYPE_DATA_FIELD_VALUE => 'Поле:Значение',
                self::TYPE_DATA_ARRAY_VALUES => 'Массив значений',
                self::TYPE_DATA_ARRAY_ID_INDEX_TYPE_INDEX => 'Массив ID(Индекс)-TYPE(Индекс)',
                self::TYPE_DATA_ARRAY_FIELDS => 'Массив Поле:Значение',
                self::TYPE_DATA_ARRAY_ELEMENTS_STRUCTURE => 'Массив ElementsStructure',
                self::TYPE_DATA_TABLE => 'Таблица (Table)',
            )
       );
    }

    /**
     * @static
     * @return array Возвращает все поддерживаемые типы данных (key => label).
     */
    public static function getAllTypesOfData()
    {
        return array_merge(
            self::getNativeTypesOfData(),
            self::getTypesOfData(),
            array(SoapFunctionParam::TYPE_DATA_ELEMENT_TABLE => 'Элемент таблицы')
        );
    }

    /**
     * @static
     * @return array Возвращает массив простых типов данных (key => label).
     */
    public static function getNativeTypesOfData()
    {
        return array(
            self::DEFAULT_TYPE_OF_DATA => 'Строка (String)',
            self::TYPE_DATA_INTEGER => 'Число (Integer)',
            self::TYPE_DATA_BOOLEAN => 'Булево (Boolean)',
            self::TYPE_DATA_DATE => 'Дата (Date)',
            self::TYPE_DATA_ARRAY => 'Массив (Array)',
        );
    }

    /**
     * @static
     * @param string $type
     * @return boolean
     */
    public static function isNativeType($type)
    {
        return in_array($type, array_keys(self::getNativeTypesOfData()));
    }

    /**
     * @return SoapFunctionParam[]
     */
    public function getChildren()
    {
        $data = $this->findAll(
            'parent_id=:parent_id',
            array(
                ':parent_id' => $this->primaryKey,
            )
        );
        return $data;
    }
}