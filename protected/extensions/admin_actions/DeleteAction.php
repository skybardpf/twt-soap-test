<?php
class DeleteAction extends CAction
{
	/**
	 * @var CActiveRecord
	 */
	public $model = null;

	public $view = 'delete';

	public function run($id)
	{
		$model = $this->model->findByPk($id);
		if (empty($model)) throw new CHttpException(404);
		if (Yii::app()->request->isAjaxRequest) {
			$model->delete();
		} else {
			if (isset($_POST['result'])) {
				switch ($_POST['result']) {
					case 'yes':
						if ($model->delete()) {
							$this->controller->redirect($this->controller->createUrl($this->controller->defaultAction, $_GET));
						} else {
							throw new CException('Не удалось удалить сущность');
						}
						break;
					default:
						$this->controller->redirect($this->controller->createUrl('view', array('id' => $id)));
						break;
				}
			}
			$this->controller->render($this->view, array('model' => $model));
		}
	}
}