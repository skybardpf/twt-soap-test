<?php
/**
 * Удалить тест SoapTest, указанный в $id.
 *
 * @see SoapTest
 */
class DeleteAction extends CAction
{
	public function run($id)
	{
        /**
         * @var $model SoapTest
         */
        $model = SoapTest::model()->findByPk($id);
        if (!$model) {
            throw new CHttpException(404, 'Unit-тест не найден.');
        }

		if (Yii::app()->request->isAjaxRequest) {
			$model->delete();
		} else {
			if (isset($_POST['result'])) {
				switch ($_POST['result']) {
					case 'yes':
						if ($model->delete()) {
							$this->controller->redirect($this->controller->createUrl('list', array('func_id' => $model->soapFunction->primaryKey)));
						} else {
							throw new CHttpException(500, 'Не удалось удалить Unit-тест.');
						}
						break;
					default:
						$this->controller->redirect($this->controller->createUrl('list', array('func_id' => $model->soapFunction->primaryKey)));
                    break;
				}
			}
			$this->controller->render('delete', array('model' => $model));
		}
	}
}