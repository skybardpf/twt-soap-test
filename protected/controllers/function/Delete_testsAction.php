<?php
/**
 * Удаление тестов для указанной функции.
 *
 * @author Skibardin A.A. <skybardpf@artektiv.ru>
 *
 * @see SoapFunction
 * @see SoapTest
 */
class Delete_testsAction extends CAction
{
	public function run($id)
	{
        /**
         * @var $controller FunctionController
         */
        $controller = $this->controller;
        /**
         * @var $model SoapFunction
         */
        $model = $controller->loadModel($id);
        $group = $model->groupFunctions;

        if (Yii::app()->request->isAjaxRequest) {
			$model->deleteTests();
		} else {
			if (isset($_POST['result'])) {
				switch ($_POST['result']) {
                    case 'yes':
                        if ($model->deleteTests()) {
                            $controller->redirect($controller->createUrl('list', array('service_id' => $group->soapService->primaryKey)));
                        } else {
                            throw new CHttpException(500, 'Не удалось удалить тесты.');
                        }
                    break;
                    default:
                        $controller->redirect($controller->createUrl('list', array('service_id' => $group->soapService->primaryKey)));
                    break;
				}
			}
			$this->controller->render(
                'delete',
                array(
                    'model' => $model,
                    'is_function_delete' => false,
                    'service_id' => $group->soapService->primaryKey,
                )
            );
		}
	}
}