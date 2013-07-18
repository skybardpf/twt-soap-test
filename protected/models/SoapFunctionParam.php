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
	 * @return SoapFunctionParam the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

    public function primaryKey()
    {
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

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('name, type', 'required'),
			array('description', 'safe'),
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
			'description' => 'Описание',
		);
	}
}