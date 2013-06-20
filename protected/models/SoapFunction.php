<?php

/**
 * This is the model class for table "soap_function".
 *
 * The followings are the available columns in table 'soap_function':
 * @property integer $id
 * @property integer $service_id
 * @property string $name
 *
 * The followings are the available model relations:
 * @property SoapService $service
 * @property SoapFunctionArgs[] $functionArgs
 */
class SoapFunction extends CActiveRecord
{
	public function getTestCounts()
	{
		return count($this->functionArgs);
	}

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
	 * @param integer $id
	 * @return SoapFunction
	 */
	public function service($id)
	{
		$this->getDbCriteria()->mergeWith(array(
			'condition'=>'service_id = :service_id',
			'params'=>array(':service_id' => $id),
		));
		return $this;
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'soap_function';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('service_id, name', 'required'),
			array('service_id', 'numerical', 'integerOnly'=>true),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, service_id, name', 'safe', 'on'=>'search'),
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
			'service' => array(self::BELONGS_TO, 'SoapService', 'service_id'),
			'functionArgs' => array(self::HAS_MANY, 'SoapFunctionArgs', 'function_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'service_id' => 'Сервис',
			'name' => 'Имя',
			'testCounts' => 'Количество тестов'
		);
	}

	protected function beforeDelete()
	{
		foreach ($this->functionArgs as $arg) {
			$arg->delete();
		}

		return parent::beforeDelete();
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
		$criteria->compare('service_id',$this->service_id);
		$criteria->compare('name',$this->name,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}