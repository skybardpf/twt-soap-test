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
        /**
         * @var $controller ServiceController
         */
        $controller = $this->controller;
        $controller->pageTitle .= 'Удаление SOAP сервиса';

        /**
         * @var $model SoapService
         */
        $model = $controller->loadModel($id);

		if (Yii::app()->request->isAjaxRequest) {
			$model->delete();
		} else {
			if (isset($_POST['result'])) {
				switch ($_POST['result']) {
					case 'yes':
						if ($model->delete()) {
							$controller->redirect($controller->createUrl('list'));
						} else {
							throw new CHttpException(500, 'Не удалось удалить сервис.');
						}
						break;
					default:
						$controller->redirect($controller->createUrl('list'));
                    break;
				}
			}
			$controller->render('delete', array('model' => $model));
		}
	}
}