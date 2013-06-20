<?php

/**
 * This is the model class for table "soap_test_result".
 *
 * The followings are the available columns in table 'soap_test_result':
 * @property integer $id
 * @property integer $function_args_id
 * @property integer $test_id
 * @property integer $date
 * @property integer $result
 *
 * The followings are the available model relations:
 * @property SoapTest $test
 * @property SoapFunctionArgs $functionArgs
 */
class SoapTestResult extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return SoapTestResult the static model class
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
		return 'soap_test_result';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('function_args_id, test_id, date, result', 'required'),
			array('function_args_id, test_id, date, result', 'numerical', 'integerOnly'=>true),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, function_args_id, test_id, date, result', 'safe', 'on'=>'search'),
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
			'test' => array(self::BELONGS_TO, 'SoapTest', 'test_id'),
			'functionArgs' => array(self::BELONGS_TO, 'SoapFunctionArgs', 'function_args_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'function_args_id' => 'Function Args',
			'test_id' => 'Test',
			'date' => 'Date',
			'result' => 'Result',
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
		$criteria->compare('function_args_id',$this->function_args_id);
		$criteria->compare('test_id',$this->test_id);
		$criteria->compare('date',$this->date);
		$criteria->compare('result',$this->result);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}