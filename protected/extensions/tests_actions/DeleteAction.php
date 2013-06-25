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
		$test = $this->model->findByPk($id);
		if (!$test) {
            throw new CHttpException(404, 'Не найден Unit-тест.');
        }
		if (Yii::app()->request->isAjaxRequest) {
            $test->delete();
		} else {
			if (isset($_POST['result'])) {
				switch ($_POST['result']) {
					case 'yes':
						if ($test->delete()) {
                            if ($this->controller->id == 'arguments'){
                                $this->controller->redirect($this->controller->createUrl('view', array('id' => $test->function_id)));
                            } elseif ($this->controller->id == 'functions'){
                                $this->controller->redirect($this->controller->createUrl('list', array('id' => $test->service_id)));
                            }
						} else {
							throw new CException('Не удалось удалить сущность');
						}
						break;
					default:
						$this->controller->redirect($this->controller->createUrl('view', array('id' => $id)));
						break;
				}
			}
			$this->controller->render($this->view, array('model' => $test));
		}
	}
}