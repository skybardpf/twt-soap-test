<?php
/**
 * Модель для группировки функций. Распределение функций по группам к которым они относятся, например,
 * "Мои организации", "Мои контрагенты". Обычно в группу входят функции реализующие следующие действия:
 * - вывод списка List.
 * - вывод конкретной записи Get
 * - удаление Delete
 * - сохранение/добавление Save.
 *
 * Группа привязанна к конкретному сервису. Все функции принадлежат, определенной группе.
 *
 * @author Skibardin A.A. <skybardpf@artektiv.ru>
 *
 * @see SoapFunction
 * @see SoapService
 *
 * @property $id            int
 * @property $name          string
 * @property $service_id    int
 *
 * @property $soapService   SoapService
 */

class GroupFunctions extends CActiveRecord
{
    const GROUP_NAME_DEFAULT = 'default';

    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return GroupFunctions
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
        return 'group_functions';
    }

    /**
     * Связи с другими таблицами.
     * @return array relational rules.
     */
    public function relations()
    {
        return array(
            'soapFunctions' => array(self::HAS_MANY, 'SoapFunction', 'group_id'),
			'soapService' => array(self::BELONGS_TO, 'SoapService', 'service_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'name' => 'Название группы',
        );
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        return array(
            array('name', 'required'),
            array('name', 'unique'),
        );
    }

    /**
     * TODO До удаление группы, перевести все связанные с ней функции на дефолтную группу.
     * TODO Не позволять удаление дефолтной группы.
     */
    protected function beforeDelete()
    {
        return parent::beforeDelete();
    }

}