<?php

class DeleteAction extends CAction
{
//	/**
//	 * @var CActiveRecord
//	 */
//	public $model = null;
//
//	public $view = 'delete';
//
	public function run($id)
	{
        $model = SoapService::model()->findByPk($id);
        if (!$model) {
            throw new CHttpException(404, 'Не найден сервис.');
        }
		if (Yii::app()->request->isAjaxRequest) {
			$model->delete();
		} else {
			if (isset($_POST['result'])) {
				switch ($_POST['result']) {
					case 'yes':
						if ($model->delete()) {
							$this->controller->redirect($this->controller->createUrl('list'));
						} else {
							throw new CHttpException(500, 'Не удалось удалить сервис.');
						}
						break;
					default:
						$this->controller->redirect($this->controller->createUrl('list'));
                    break;
				}
			}
			$this->controller->render('delete', array('model' => $model));
		}
	}
}