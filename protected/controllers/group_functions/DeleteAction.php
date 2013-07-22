<?php
/**
 * Удаление группы функций сервиса.
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
        /**
         * @var $controller Group_functionsController
         */
        $controller = $this->controller;
        $controller->pageTitle .= 'Удаление группы';
        /**
         * @var $model GroupFunctions
         */
        $model = $controller->loadModel($id);

		if (Yii::app()->request->isAjaxRequest) {
            if ($model->name == GroupFunctions::GROUP_NAME_DEFAULT){
                echo CJSON::encode(
                    array(
                        'error' => 'Нельзя удалить группу по умолчанию.'
                    )
                );
                Yii::app()->end();
            }
			$model->delete();
		} else {
			if (isset($_POST['result'])) {
				switch ($_POST['result']) {
					case 'yes':
                        if ($model->name == GroupFunctions::GROUP_NAME_DEFAULT){
                            throw new CHttpException(500, 'Нельзя удалить группу по умолчанию');
                        }
						if (!$model->delete()) {
                            throw new CHttpException(500, 'Не удалось удалить группу.');
						}
                        $controller->redirect($controller->createUrl('list', array('service_id' => $model->soapService->primaryKey)));
                    break;
					default:
						$controller->redirect($controller->createUrl('list', array('service_id' => $model->soapService->primaryKey)));
                    break;
				}
			}
			$controller->render('delete', array('model' => $model));
		}
	}
}