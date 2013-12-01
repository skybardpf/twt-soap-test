<?php

/**
 * This is the model class for table "function_test".
 *
 * The followings are the available columns in table 'function_test':
 * @property string $id
 * @property string $function_id
 * @property string $service_id
 * @property string $name
 * @property integer $status
 * @property integer $test_result
 * @property string $date_create
 * @property string $date_start
 * @property string $date_end
 * @property string $args
 * @property string $last_return
 * @property string $last_errors
 *
 * The followings are the available model relations:
 * @property Service $service
 * @property Function $function
 */
class FunctionTest extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'function_test';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('function_id, service_id, name, date_create, args, last_return, last_errors', 'required'),
			array('status, test_result', 'numerical', 'integerOnly'=>true),
			array('function_id, service_id', 'length', 'max'=>11),
			array('name', 'length', 'max'=>50),
			array('date_start, date_end', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, function_id, service_id, name, status, test_result, date_create, date_start, date_end, args, last_return, last_errors', 'safe', 'on'=>'search'),
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
			'service' => array(self::BELONGS_TO, 'Service', 'service_id'),
			'function' => array(self::BELONGS_TO, 'Function', 'function_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'function_id' => 'Function',
			'service_id' => 'Service',
			'name' => 'Name',
			'status' => 'Status',
			'test_result' => 'Test Result',
			'date_create' => 'Date Create',
			'date_start' => 'Date Start',
			'date_end' => 'Date End',
			'args' => 'Args',
			'last_return' => 'Last Return',
			'last_errors' => 'Last Errors',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 *
	 * Typical usecase:
	 * - Initialize the model fields with values from filter form.
	 * - Execute this method to get CActiveDataProvider instance which will filter
	 * models according to data in model fields.
	 * - Pass data provider to CGridView, CListView or any similar widget.
	 *
	 * @return CActiveDataProvider the data provider that can return the models
	 * based on the search/filter conditions.
	 */
	public function search()
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id,true);
		$criteria->compare('function_id',$this->function_id,true);
		$criteria->compare('service_id',$this->service_id,true);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('status',$this->status);
		$criteria->compare('test_result',$this->test_result);
		$criteria->compare('date_create',$this->date_create,true);
		$criteria->compare('date_start',$this->date_start,true);
		$criteria->compare('date_end',$this->date_end,true);
		$criteria->compare('args',$this->args,true);
		$criteria->compare('last_return',$this->last_return,true);
		$criteria->compare('last_errors',$this->last_errors,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return FunctionTest the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
