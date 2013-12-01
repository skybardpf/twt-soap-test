<?php

/**
 * Модель для таблицы soap_service.
 * Список SOAP сервисов, с указанием параметров доступа.
 *
 * @property integer $id
 * @property string $name
 * @property string $url
 * @property string $login
 * @property string $password
 *
 * The followings are the available model relations:
 * @property SoapFunction[] $functions
 * @property FunctionTest[] $functionTests
 * @property GroupFunctions[] $groupFunctions
 */
class SoapService extends CActiveRecord
{
    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return SoapService the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'service';
    }

    /**
     * @return GroupFunctions Возвращает модель группы по умолчанию.
     * @throws CHttpException
     */
    public function getDefaultGroup()
    {
        /**
         * @var $group GroupFunctions
         */
        $group = GroupFunctions::model()->find(array(
            'select' => 'id',
            'condition' => 'service_id=:service_id AND name=:name',
            'params' => array(
                ':service_id' => $this->primaryKey,
                ':name' => GroupFunctions::GROUP_NAME_DEFAULT,
            ),
        ));
        if ($group == null) {
            throw new CHttpException(500, 'Не существует дефолтной группы для сервиса.');
        }
        return $group;
    }

    /**
     * @return GroupFunctions[] Возвращает список групп. Формат [id => name]
     * @throws CHttpException
     */
    public function getGroups()
    {
        /**
         * @var $data GroupFunctions[]
         */
        $data = GroupFunctions::model()->findAll(array(
            'select' => 'id, name',
            'condition' => 'service_id=:service_id',
            'params' => array(
                ':service_id' => $this->primaryKey,
            ),
        ));
        $groups = array();
        foreach ($data as $v) {
            $groups[$v->id] = $v->name;
        }
        return $groups;
    }

    /**
     * Возвращает список SOAP сервисов.
     * @return array
     */
    public static function getList()
    {
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
            FROM ' . SoapService::model()->tableName() . ' s
            LEFT JOIN (
                SELECT gf.service_id,
                    COUNT(*) AS count,
                    SUM( CASE status
                        WHEN ' . SoapTest::STATUS_TEST_RUN . ' THEN 1
                        WHEN ' . SoapTest::STATUS_TEST_IN_QUEUE . ' THEN 1
                        WHEN ' . SoapTest::STATUS_TEST_STOP . ' THEN 0
                    END ) AS count_running,
                    SUM(
                        IF(status=' . SoapTest::STATUS_TEST_STOP . ',
                        TIME_TO_SEC(TIMEDIFF(date_end, date_start)), 0)
                    ) AS runtime,
                    MIN(date_start) AS date_start,
                    MAX(status) AS status,
                    MAX(test_result) AS test_result
                FROM ' . SoapTest::model()->tableName() . ' t
                JOIN ' . SoapFunction::model()->tableName() . ' f ON t.function_id = f.id
                JOIN ' . GroupFunctions::model()->tableName() . ' gf ON f.group_id = gf.id
                GROUP BY gf.service_id
            ) t ON t.service_id = s.id'
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
                if ($t->status != SoapTest::STATUS_TEST_RUN) {
                    $t->status = SoapTest::STATUS_TEST_IN_QUEUE;
                    $t->save();
                    $list[] = $t;
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
            FROM ' . SoapTest::model()->tableName() . ' t
            JOIN ' . SoapFunction::model()->tableName() . ' f ON f.id=t.function_id
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
        if (empty($listIds)) {
            return array();
        }
        $cmd = Yii::app()->db->createCommand('
            SELECT
                s.id,
                SUM(
                    CASE t.status WHEN ' . SoapTest::STATUS_TEST_STOP . '
                    THEN(t.date_end - t.date_start)
                    ELSE 0 END
                ) AS `runtime`,
                MIN(t.date_start) AS `date_start`,
                MAX(t.test_result) AS `test_result`
            FROM ' . SoapService::model()->tableName() . ' s
            JOIN ' . GroupFunctions::model()->tableName() . ' gf ON gf.service_id = s.id
            JOIN ' . SoapFunction::model()->tableName() . ' f ON gf.id = f.group_id
            JOIN ' . SoapTest::model()->tableName() . ' t ON f.id = t.function_id
            WHERE s.id IN (' . implode(',', $listIds) . ')
            GROUP BY f.id
            HAVING MAX(t.status)=' . SoapTest::STATUS_TEST_STOP
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
            array('name', 'length', 'max' => 100),
            array('login, password', 'length', 'max' => 30),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        return array(
            'functions' => array(self::HAS_MANY, 'SoapFunction', 'service_id'),
            'functionTests' => array(self::HAS_MANY, 'FunctionTest', 'service_id'),
            'groupFunctions' => array(self::HAS_MANY, 'GroupFunctions', 'service_id'),
        );
    }

    /**
     * Возвращает список меток для каждого атрибута таблицы.
     * @return array
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'name' => 'Имя',
            'url' => 'URL',
            'login' => 'Логин',
            'password' => 'Пароль',
            'countTests' => 'Количество тестов'
        );
    }

    /**
     * После добавления нового сервиса,
     * автоматически создаем у него новую группу функций (GROUP_NAME_DEFAULT).
     */
    protected function afterSave()
    {
        parent::afterSave();

        if ($this->isNewRecord) {
            $group = new GroupFunctions();
            $group->service_id = $this->primaryKey;
            $group->name = GroupFunctions::GROUP_NAME_DEFAULT;
            $group->insert();
        }
    }

    /**
     *  До удаление сервиса, удаляем группы, функции и тесты по этим функциям.
     */
    protected function beforeDelete()
    {
        if (parent::beforeDelete()) {
            foreach ($this->groupFunctions as $gf) {
                $gf->delete();
            }
            return true;
        }
        return false;
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
        if (!$this->getSoapClient()) {
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
            $url = $urlParr['scheme'] . '://' .
                $this->login .
                ($this->password ? ':' . $this->password : '') .
                '@' . $urlParr['host'] .
                (isset($urlParr['port']) ? $urlParr['port'] : '') .
                (isset($urlParr['path']) ? $urlParr['path'] : '') .
                (isset($urlParr['query']) ? '?' . $urlParr['query'] : '');
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
                'login' => $this->login,
                'password' => $this->password
            ));
        } catch (SoapFault $e) {
            return null;
        }
    }
}