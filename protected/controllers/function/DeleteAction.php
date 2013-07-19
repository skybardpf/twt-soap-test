<?php
/**
 * Удаление функции. Будут удалены все связанные с ней тесты и параметры.
 *
 * @author Skibardin A.A. <skybardpf@artektiv.ru>
 *
 * @see SoapFunction
 * @see SoapTest
 * @see SoapFunctionParam
 */
class DeleteAction extends CAction
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
			$model->delete();
		} else {
			if (isset($_POST['result'])) {
				switch ($_POST['result']) {
					case 'yes':
						if ($model->delete()) {
							$controller->redirect($controller->createUrl('list', array('service_id' => $group->soapService->primaryKey)));
						} else {
							throw new CHttpException(500, 'Не удалось удалить функцию.');
						}
						break;
					default:
                        $controller->redirect($controller->createUrl('list', array('service_id' => $group->soapService->primaryKey)));
                    break;
				}
			}
            $controller->render(
                'delete',
                array(
                    'model' => $model,
                    'is_function_delete' => true,
                    'service_id' => $group->soapService->primaryKey,
                )
            );
		}
	}
}