<?php

/**
 * Модель для таблицы soap_function_param.
 * Параметр функции. Используются для валидации входящих и исходящих параметров при тестировании.
 *
 * @property integer    $id
 * @property integer    $function_id
 * @property string     $name
 * @property boolean    $input_param           input (true) | output (false)
 * @property string     $type_of_data
 * @property boolean    $required
 * @property string     $description
 *
 * @property SoapFunction $soapFunction
 */
class SoapFunctionParam extends CActiveRecord
{
    const DEFAULT_TYPE_OF_DATA = 'string';
    const TYPE_DATA_BOOLEAN = 'boolean';
    const TYPE_DATA_INTEGER = 'integer';
    const TYPE_DATA_ARRAY = 'array';
    const TYPE_DATA_DATE = 'date';
    const TYPE_DATA_TABLE = 'table';

    const TYPE_INPUT = 1;
    const TYPE_OUTPUT = 0;

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
			array('name', 'match', 'pattern' => '/^[A-Za-z0-9_]+$/'),
			array('input_param, type_of_data', 'required'),

			array('type_of_data', 'in', 'range' => array_keys(self::getTypesOfData())),

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
        return array(
            self::DEFAULT_TYPE_OF_DATA => 'Строка (String)',
            self::TYPE_DATA_INTEGER => 'Число (Integer)',
            self::TYPE_DATA_BOOLEAN => 'Булево (Boolean)',
            self::TYPE_DATA_DATE => 'Дата (Date)',
            self::TYPE_DATA_ARRAY => 'Массив (Array)',
            self::TYPE_DATA_TABLE => 'Таблица (Table)',
       );
    }
}