<?php

/**
 * This is the model class for table "soap_service".
 *
 * The followings are the available columns in table 'soap_service':
 * @property integer $id
 * @property string $name
 * @property string $url
 * @property string $login
 * @property string $password
 * @property integer $testsCount
 *
 * The followings are the available model relations:
 * @property SoapFunction[] $soapFunctions
 * @property SoapTest[] $tests
 */
class SoapService extends CActiveRecord
{
	public function getTestsCount()
	{
		return $this->getDbConnection()->createCommand(
			'SELECT COUNT(*)
			FROM soap_function AS sf
			JOIN soap_function_args AS sfa ON sfa.function_id = sf.id
			WHERE sf.service_id =:service_id'
		)->queryScalar(array(':service_id' => $this->id));
	}

	/** @var SoapClient */
	private $_soapClient = null;

	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return SoapService the static model class
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
		return 'soap_service';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('name, url', 'required'),
			array('url', 'url'),
			array('login, password', 'safe'),
			array('url', 'isSoapServiceUrl', 'skipOnError' => true),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, name, url', 'safe', 'on'=>'search'),
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
			'soapFunctions' => array(self::HAS_MANY, 'SoapFunction', 'service_id'),
			'tests' => array(self::HAS_MANY, 'SoapTest', 'service_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'name' => 'Имя',
			'url' => 'URL',
			'login' => 'Логин',
			'password' => 'Пароль',
			'testsCount' => 'Количество тестов'
		);
	}

	protected function afterSave()
	{
		parent::afterSave();
		$soap_functions = $this->_soapClient->__getFunctions();
		$in_db_functions = array();
		if (!$this->isNewRecord) {
			$in_db_functions = self::getDbConnection()->createCommand()
			->select('name')
			->from(SoapFunction::model()->tableName())
			->where('service_id = :service_id')
			->queryColumn(array(':service_id' => $this->id));
		}
		$functions = array();
		foreach ($soap_functions as $f) {
			$matches = null;
			preg_match('/(\w+)\s+(\w+)\((.*?)\)/i', ($f), $matches);
			if (!empty($matches)) {
				$function_name = $matches[2];
				if (!in_array($function_name, $functions)) {
					$functions[] = $function_name;
					if (!in_array($function_name, $in_db_functions)) {
						$function = new SoapFunction();
						$function->service_id = $this->id;
						$function->name = $function_name;
						$function->save(false);
					}
				}
			}
		}
	}

	protected function beforeDelete()
	{
		foreach ($this->soapFunctions as $f) {
			$f->delete();
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
		$criteria->compare('name',$this->name,true);
		$criteria->compare('url',$this->url,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	public function isSoapServiceUrl($attribute, $params = array())
	{
		if (!$this->isAvailableService()) {
			$this->addError($attribute, 'Не удается связаться с сервером');
			return;
		}
		if (!$this->getSoapClient()){
			$this->addError($attribute, 'Не является WSDL сервисом');
		}
	}

    public function isAvailableService(){
        //проверка на доступность сервиса
        $url = $this->url;
        if ($this->login) {
            $urlParr = parse_url($url);
            $url = $urlParr['scheme'].'://'.
                $this->login.
                ($this->password ? ':'.$this->password : '').
                '@'.$urlParr['host'].
                (isset($urlParr['port']) ? $urlParr['port'] : '').
                (isset($urlParr['path']) ? $urlParr['path'] : '').
                (isset($urlParr['query']) ? '?'.$urlParr['query'] : '');
        }
        $result = @file_get_contents($url);
        return (bool)$result;
    }

    public function getSoapClient(){
        $client = NULL;
        try {
            ini_set('soap.wsdl_cache_enabled', 0);
            $client = $this->_soapClient = new SoapClient($this->url, array(
                'login'     => $this->login,
                'password'  => $this->password
            ));
        } catch (SoapFault $e) {
//            $client = false;
        }
        return $client;
    }
}