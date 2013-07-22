<?php
/**
 * Удалить, указанный в $id тест SoapTest.
 *
 * @see SoapTest
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
         * @var $controller TestController
         */
        $controller = $this->controller;

        /**
         * @var $model SoapTest
         */
        $model = $controller->loadModel($id);
        $controller->pageTitle .= ' | Удаление теста для функции "'.$model->soapFunction->name.'"';

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