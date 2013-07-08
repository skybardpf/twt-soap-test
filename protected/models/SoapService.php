<?php

/**
 * Модель для таблицы soap_service.
 * Список SOAP сервисов, с указанием параметров доступа.
 *
 * @property integer    $id
 * @property string     $name
 * @property string     $url
 * @property string     $login
 * @property string     $password
 *
 * Связи с другими таблицами.
 * @property SoapFunction[] $soapFunctions
 */
class SoapService extends CActiveRecord
{
    /** @var SoapClient */
//    private $_soapClient = null;

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
     * Возвращает список SOAP сервисов.
     *
     * @return array
     */
    public static function getList()
	{
        /*$cmd = Yii::app()->db->createCommand(
            'SELECT
                s.id,
                s.name,
                s.url,
                CASE
                    WHEN t.count IS NULL THEN 0
                    ELSE t.count
                END AS `count_tests`,
                CASE
                    WHEN f.count IS NULL THEN 0
                    ELSE f.count
                END AS `count_functions`
            FROM '.SoapService::model()->tableName().' s
            LEFT JOIN
                (SELECT
                    f.service_id, COUNT(*) AS `count`
                FROM '.SoapTest::model()->tableName().' t
                JOIN '.SoapFunction::model()->tableName().' f ON t.function_id = f.id
                GROUP BY f.service_id
            ) t ON t.service_id = s.id
            LEFT JOIN (
                SELECT
                    service_id, COUNT(*) AS `count`
                FROM '.SoapFunction::model()->tableName().'
                GROUP BY service_id
            ) f ON f.service_id=s.id'
        );*/

        $cmd = Yii::app()->db->createCommand(
            'SELECT
                s.id,
                s.name,
                s.url,
                CASE
                    WHEN t.count IS NULL THEN 0
                    ELSE t.count
                END AS count_tests,
                ( CASE
                        WHEN t.count_running IS NULL THEN 0
                        WHEN t.count_running > 0 THEN 1
                        WHEN t.count_running = 0 THEN 0
                END ) AS has_running_tests,
                t.status,
                t.date_start,
                t.test_result,
                t.runtime
            FROM '.SoapService::model()->tableName().' s
            LEFT JOIN (
                SELECT f.service_id,
                   COUNT( * ) AS count,
                   SUM( CASE status
                        WHEN '.SoapTest::STATUS_TEST_RUN.' THEN 1
                        WHEN '.SoapTest::STATUS_TEST_IN_QUEUE.' THEN 1
                        WHEN '.SoapTest::STATUS_TEST_STOP.' THEN 0
                   END ) AS count_running,
                   SUM( CASE status
                        WHEN '.SoapTest::STATUS_TEST_STOP.'
                        THEN( date_end - date_start )
                        ELSE 0
                   END ) AS runtime,
                   MIN( date_start ) AS date_start,
                   MAX( status ) AS status,
                   MAX( test_result ) AS test_result
                FROM '.SoapTest::model()->tableName().' t
                JOIN '.SoapFunction::model()->tableName().' f ON t.function_id = f.id
                GROUP BY f.service_id
            ) t ON t.service_id = s.id
            '
        );
		return $cmd->queryAll();
	}

    /**
     * Поставить все тесты данного сервиса в очередь на выполнение.
     * Если тест уже запущен его статус не изменяется.
     * @return array SoapTest[]
     */
    public function putTestsInQueue()
    {
        $list = array();
        foreach ($this->soapFunctions as $f) {
            foreach ($f->soapTests as $t) {
                if ($t->status != SoapTest::STATUS_TEST_RUN){
                    $t->status = SoapTest::STATUS_TEST_IN_QUEUE;
                    $t->save();
                    $list[]= $t;
                }
            }
        }
        return $list;
    }

    /**
     * Возвращает результат по всем отработаным тестам (@see SoapTest::STATUS_TEST_STOP)
     * данного сервиса.
     *
     * @return array
     */
    public function getLastResult()
    {
        $cmd = Yii::app()->db->createCommand(
            'SELECT
                SUM(CASE `status` WHEN :status THEN (date_end-date_start) ELSE 0 END) AS `runtime`,
                MIN(`date_start`) AS `date_start`,
                MAX(`test_result`) AS `test_result`
            FROM '.SoapTest::model()->tableName().' t
            JOIN '.SoapFunction::model()->tableName().' f ON f.id=t.function_id
            WHERE service_id=:service_id AND status=:status'
        );
        return $cmd->queryRow(true, array(
            ":status" => SoapTest::STATUS_TEST_STOP,
            ':service_id' => $this->primaryKey
        ));
    }

    /**
     * Возвращает текущие статусы тестов. Список тестов передается в виде
     * массива ID.
     *
     * @static
     * @param array $listIds
     * @return array
     */
    public static function getStatusesTests(array $listIds)
    {
        if (empty($listIds)){
            return array();
        }
        $cmd = Yii::app()->db->createCommand('
            SELECT
                s.id,
                SUM(
                    CASE status WHEN '.SoapTest::STATUS_TEST_STOP.'
                    THEN(date_end - date_start)
                    ELSE 0 END
                ) AS `runtime`,
                MIN(date_start) AS `date_start`,
                MAX(test_result) AS `test_result`
            FROM '.SoapFunction::model()->tableName().' f
            JOIN '.SoapTest::model()->tableName().' t ON t.function_id = f.id
            JOIN '.SoapService::model()->tableName().' s ON s.id = f.service_id
            WHERE s.id IN ('.implode(',', $listIds).')
            GROUP BY f.id
            HAVING MAX(status)='.SoapTest::STATUS_TEST_STOP
        );
        return $cmd->queryAll();
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
//			array('id, name, url', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		return array(
			'soapFunctions' => array(self::HAS_MANY, 'SoapFunction', 'service_id'),
		);
	}

	/**
     * Возвращает список меток для каждого атрибута таблицы.
	 * @return array
	 */
	public function attributeLabels()
	{
		return array(
			'id'        => 'ID',
			'name'      => 'Имя',
			'url'       => 'URL',
			'login'     => 'Логин',
			'password'  => 'Пароль',
			'countTests'=> 'Количество тестов'
		);
	}

    /**
     *  После сохранения сервиса, получаем список его функций и добавляем в БД.
     */
    protected function afterSave()
	{
		parent::afterSave();

        $client = $this->getSoapClient();
		$soap_functions = $client->__getFunctions();
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

    /**
     *  До удаление сервиса, удаляем все его функции и тесты по этим функциям.
     */
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
//		// Warning: Please modify the following code to remove attributes that
//		// should not be searched.
//
//		$criteria=new CDbCriteria;
//
//		$criteria->compare('id',$this->id);
//		$criteria->compare('name',$this->name,true);
//		$criteria->compare('url',$this->url,true);
//
//		return new CActiveDataProvider($this, array(
//			'criteria'=>$criteria,
//		));
	}

    /**
     * Валидатор проверки URL SOAP сервиса.
     */
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

    /**
     * Проверка на доступности SOAP сервиса.
     *
     * @return bool
     */
    public function isAvailableService()
    {
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

    /**
     * Создаем соединение с SOAP сервисом и возвращаем созданный объект.
     * Возвращаем NULL в случае неудачи.
     *
     * @return null|SoapClient
     */
    public function getSoapClient()
    {
        try {
//            if ($this->_soapClient !== null) {
//                return $this->_soapClient;
//            }
            ini_set('soap.wsdl_cache_enabled', 0);
            return new SoapClient($this->url, array(
                'login'     => $this->login,
                'password'  => $this->password
            ));
        } catch (SoapFault $e) {
            return null;
        }
    }
}