<?php

/**
 * This is the model class for table "function_param".
 *
 * The followings are the available columns in table 'function_param':
 * @property string $id
 * @property string $function_id
 * @property string $name
 * @property integer $input_param
 * @property integer $parent_id
 * @property string $type_of_data
 * @property string $array_type_of_data
 * @property integer $required
 * @property string $description
 *
 * The followings are the available model relations:
 * @property Function $function
 */
class FunctionParam extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'function_param';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('function_id, name, parent_id, type_of_data', 'required'),
			array('input_param, parent_id, required', 'numerical', 'integerOnly'=>true),
			array('function_id', 'length', 'max'=>11),
			array('name', 'length', 'max'=>45),
			array('type_of_data, array_type_of_data', 'length', 'max'=>40),
			array('description', 'length', 'max'=>100),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, function_id, name, input_param, parent_id, type_of_data, array_type_of_data, required, description', 'safe', 'on'=>'search'),
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
			'name' => 'Name',
			'input_param' => 'Input Param',
			'parent_id' => 'Parent',
			'type_of_data' => 'Type Of Data',
			'array_type_of_data' => 'Array Type Of Data',
			'required' => 'Required',
			'description' => 'Description',
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
		$criteria->compare('name',$this->name,true);
		$criteria->compare('input_param',$this->input_param);
		$criteria->compare('parent_id',$this->parent_id);
		$criteria->compare('type_of_data',$this->type_of_data,true);
		$criteria->compare('array_type_of_data',$this->array_type_of_data,true);
		$criteria->compare('required',$this->required);
		$criteria->compare('description',$this->description,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return FunctionParam the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
