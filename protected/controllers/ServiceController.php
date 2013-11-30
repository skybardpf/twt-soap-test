<?php
/**
 * Class ServiceController.
 *
 * Основной контроллер. Все вызовы, кроме ошибок, идут сюда.
 * Управление SOAP сервисами.
 *
 * @author Skibardin A.A. <skybardpf@artektiv.ru>
 */
class ServiceController extends Controller
{
    /**
     * @return array
     */
    public function actions()
    {
        return array(
            'update' => 'application.controllers.Service.UpdateAction',
            'delete' => 'application.controllers.Service.DeleteAction',
            'index' => 'application.controllers.Service.IndexAction',
        );
    }
}