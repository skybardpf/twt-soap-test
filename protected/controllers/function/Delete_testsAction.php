<?php

class Delete_testsAction extends CAction
{
	public function run($id)
	{
        /**
         * @var $model SoapFunction
         */
        $model = SoapFunction::model()->findByPk($id);
        if (!$model) {
            throw new CHttpException(404, 'Не найдена функция.');
        }
		if (Yii::app()->request->isAjaxRequest) {
			$model->deleteTests();
		} else {
			if (isset($_POST['result'])) {
				switch ($_POST['result']) {
					case 'yes':
						if ($model->deleteTests()) {
							$this->controller->redirect($this->controller->createUrl('list', array('service_id' => $model->soapService->primaryKey)));
						} else {
							throw new CHttpException(500, 'Не удалось удалить тесты для функцию.');
						}
						break;
					default:
						$this->controller->redirect($this->controller->createUrl('list', array('service_id' => $model->soapService->primaryKey)));
                    break;
				}
			}
			$this->controller->render('delete', array('model' => $model));
		}
	}
}