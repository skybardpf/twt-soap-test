<?php

/**
 * This is the model class for table "soap_test".
 *
 * The followings are the available columns in table 'soap_test':
 * @property integer $id
 * @property integer $service_id
 * @property integer $date_create
 * @property integer $date_start
 * @property integer $date_end
 * @property integer $status
 * @property array $statusTitle
 * @property array $statsTest
 * @property integer $successCount
 * @property integer $errorCount
 * @property integer $warningCount
 *
 * The followings are the available model relations:
 * @property SoapTestResult[] $soapTestResults
 * @property SoapService $service
 */
class SoapTestOld extends CActiveRecord
{
	public $status = 1;

	private $_statsTest = null;

	public $statuses = array(
		'0' => '—',
		'1' => 'В очереди',
		'2' => 'В работе',
		'3' => 'Ошибка, ждет в очереди нового запуска',
		'4' => 'Отработал'
	);

	public function getStatusTitle()
	{
		return $this->statuses[$this->status];
	}

	public function getTestsCount()
	{
		return count($this->soapTestResults);
	}

	/**
	 * @return array
	 */
	public function getStatsTest()
	{
		if ($this->_statsTest == null) {
			$this->_statsTest = $this->getDbConnection()->createCommand(
				'SELECT
					SUM(error) AS error, SUM(warning) AS warning, SUM(success) AS success
				FROM (
					SELECT
						CASE WHEN (result = -1) THEN 1 END AS error,
						CASE WHEN (result = 0) THEN 1 END AS warning,
						CASE WHEN (result = 1) THEN 1 END AS success
					FROM
						soap_test_result
					WHERE
						test_id = :test_id
				)'
			)->queryRow(true, array(':test_id' => $this->id));
		}
		return $this->_statsTest;
	}

	public function getSuccessCount()
	{
        $a = $this->getStatsTest();
		return $a['success'];
	}

	public function getWarningCount()
	{
        $a = $this->getStatsTest();
        return $a['warning'];
//		return $this->getStatsTest()['warning'];
	}

	public function getErrorCount()
	{
        $a = $this->getStatsTest();
        return $a['error'];
//		return $this->getStatsTest()['error'];
	}

	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return SoapTest the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @param $id
	 * @return SoapTest
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
		return 'soap_test';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('service_id, status', 'required'),
			array('service_id', 'numerical', 'integerOnly'=>true),
			array('service_id', 'inQueue'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, service_id', 'safe', 'on'=>'search'),
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
			'soapTestResults' => array(self::HAS_MANY, 'SoapTestResult', 'test_id'),
			'service' => array(self::BELONGS_TO, 'SoapService', 'service_id'),
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
			'date_create' => 'Дата создания',
			'date_start' => 'Дата старта',
			'date_end' => 'Дата окончания',
			'status' => 'Статус',
			'statusTitle' => 'Статус',
			'testsCount' => 'Выполненно тестов',
			'successCount' => 'Пройдено',
			'warningCount' => 'Не точно',
			'errorCount' => 'Ошибка',
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
		$criteria->compare('service_id',$this->service_id);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	protected function beforeSave()
	{
		if ($this->isNewRecord) {
			$this->date_create = time();
		}
		return parent::beforeSave();
	}

	public function inQueue($attribute, $params = array())
	{
		if ($this->isNewRecord && self::model()->countByAttributes(array('service_id' => $this->service_id, 'status' => 1))) {
			$this->addError($attribute, 'Сервис уже в очереди на тестирование. Дождитесь завершения.');
		}
	}

    private function isAvailableService(){
        //проверка на доступность сервиса
        $url = $this->service->url;
        if ($this->service->login) {
            $urlParr = parse_url($url);
            $url = $urlParr['scheme'].'://'.
                $this->service->login.
                ($this->service->password ? ':'.$this->service->password : '').
                '@'.$urlParr['host'].
                (isset($urlParr['port']) ? $urlParr['port'] : '').
                (isset($urlParr['path']) ? $urlParr['path'] : '').
                (isset($urlParr['query']) ? '?'.$urlParr['query'] : '');
        }
        $result = @file_get_contents($url);
        return (bool)$result;
    }

	/**
	 * @throws CHttpException
	 */
	public function runTests()
	{
		try {
			if ($this->status == 1) {
				$this->date_start = time();
			}
			$this->status = 2;
			$this->save();

//			//проверка на доступность сервиса
//			$url = $this->service->url;
//			if ($this->service->login) {
//				$urlParr = parse_url($url);
//				$url = $urlParr['scheme'].'://'.
//					$this->service->login.
//					($this->service->password ? ':'.$this->service->password : '').
//					'@'.$urlParr['host'].
//					(isset($urlParr['port']) ? $urlParr['port'] : '').
//					(isset($urlParr['path']) ? $urlParr['path'] : '').
//					(isset($urlParr['query']) ? '?'.$urlParr['query'] : '');
//			}
//			$result = @file_get_contents($url);
			if (!$this->isAvailableService()) {
				throw new CException('Ошибка соедиения с сервисом ('.$this->service->url.')');
			}

			//соединение с сервисом
			$soapClient = null;
			ini_set('soap.wsdl_cache_enabled', 0);
			try {
				$soapClient = new SoapClient($this->service->url, array(
					'login' => $this->service->login,
					'password' => $this->service->password
				));
				Yii::trace('Соединение с '.$this->service->url);
			} catch (SoapFault $e) {
				throw new CException('Ошибка соедиения с сервисом ('.$e->getMessage().')');
			}

			/** @var $args SoapFunctionArgs[] */
			$args = SoapFunctionArgs::model()->findAllBySql(
				'SELECT sfa.* FROM '.SoapFunctionArgs::model()->tableName().' AS sfa
				JOIN '.SoapFunction::model()->tableName().' AS sf ON sfa.function_id = sf.id
				WHERE sfa.id NOT IN (
					SELECT str.function_args_id
					FROM '.SoapTestResult::model()->tableName().' AS str
					WHERE str.test_id = :test_id
				) AND sf.service_id = :service_id', array(':test_id'=>$this->id, ':service_id' => $this->service_id)
			);
			foreach ($args as $fa) {
				$test_result = new SoapTestResult();
				$test_result->function_args_id = $fa->id;
				$test_result->test_id = $this->id;
				$test_result->date = time();
				try {
					$return = $soapClient->__soapCall($fa->function->name, (array) json_decode($fa->args, true));
					if ($fa->return == json_encode($return, JSON_UNESCAPED_UNICODE)) {
						Yii::trace('Удача');
						$test_result->result = 1;
					} else {
						Yii::trace('Вернули, но с ошибкой');
						$test_result->result = 0;
					}
				} catch (SoapFault $e) {
					Yii::trace('Полный провал');
					$test_result->result = -1;
				}
				$test_result->save();
			}

			$this->refresh();
			$this->date_end = time();
			$this->status = 4;
			$this->save();
		} catch (Exception $e) {
			$this->status = 3;
			$this->save();
		}
	}
}