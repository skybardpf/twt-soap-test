<?php

/**
 * This is the model class for table "soap_function_args".
 *
 * The followings are the available columns in table 'soap_function_args':
 * @property integer $id
 * @property integer $function_id
 * @property string $name
 * @property string $args
 * @property string $return
 *
 * The followings are the available model relations:
 * @property SoapFunction $function
 * @property SoapTestResult[] $testResults
 */
class SoapFunctionArgs extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return SoapFunctionArgs the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @param $id
	 * @return SoapFunctionArgs
	 */
	public function functionId($id)
	{
		$this->getDbCriteria()->mergeWith(array(
			'condition'=>'function_id = :function_id',
			'params'=>array(':function_id' => $id),
		));
		return $this;
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'soap_function_args';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('function_id, name, args', 'required'),
			array('args', 'trueArgs'),
			array('function_id', 'numerical', 'integerOnly'=>true),
			array('return', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, function_id, name, args, return', 'safe', 'on'=>'search'),
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
			'function' => array(self::BELONGS_TO, 'SoapFunction', 'function_id'),
			'testResults' => array(self::HAS_MANY, 'SoapTestResult', 'function_args_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'function_id' => 'Функция',
			'name' => 'Описание',
			'args' => 'Аргументы',
			'return' => 'Return',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('function_id',$this->function_id);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('args',$this->args,true);
		$criteria->compare('return',$this->return,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	public function trueArgs($attribute, $params = array())
	{
		try {
			ini_set('soap.wsdl_cache_enabled', 0);
			$soapClient = new SoapClient($this->function->service->url, array(
				'login' => $this->function->service->login,
				'password' => $this->function->service->password
			));
			$return = $soapClient->__soapCall($this->function->name, (array) json_decode($this->args, true));
			$this->return = json_encode($return, JSON_UNESCAPED_UNICODE);
		} catch (SoapFault $e) {
			$this->addError($attribute, 'Не правильно заданы параметры ('.$e->getMessage().')');
		}
	}
}