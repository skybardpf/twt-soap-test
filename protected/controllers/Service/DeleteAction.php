<?php
/**
 * Удаление SOAP сервиса.
 *
 * @author Skibardin A.A. <skybardpf@artektiv.ru>
 */
class DeleteAction extends CAction
{
    /**
     * @param integer $id
     * @throws CHttpException
     */
    public function run($id)
	{
        if (Yii::app()->request->isAjaxRequest){
            try {
                /**
                 * @var SoapService $service
                 */
                $service = SoapService::model()->findByPk($id);
                if ($service === null) {
                    throw new CException('Не найден SOAP сервис.');
                }
                if (!$service->delete()) {
                    throw new CException('Не удалось удалить сервис.');
                }

                echo CJSON::encode(array(
                    'success' => true,
                ));

            } catch(CException $e){
                echo CJSON::encode(array(
                    'success' => false,
                    'error' => $e->getMessage(),
                ));
            }
        }
	}
}