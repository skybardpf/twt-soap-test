<?php
/**
 * Создаем новый тест для указанной в $func_id функции SoapFunction.
 *
 * @see SoapFunction
 * Class CreateAction
 */
class CreateAction extends CAction
{
	public function run($func_id)
	{
        $function = SoapFunction::model()->findByPk($func_id);
        if (!$function) {
            throw new CHttpException(404, 'Функция не найдена.');
        }

        $model = new SoapTest();
        $model->soapFunction = $function;
        $model->function_id = $function->primaryKey;
        $model->date_create = time();

		if(isset($_POST['ajax']) && $_POST['ajax']==='model-form-form') {
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}

		if (isset($_POST[get_class($model)])) {
			$model->attributes = $_POST[get_class($model)];
            try {
                if ($model->save()) {
                    $this->controller->redirect($this->controller->createUrl('list', array('func_id' => $func_id)));
                }
            }catch (Exception $e){
                $model->addError('id', $e->getMessage());
            }
		}
		$this->controller->render(
            'create',
            array(
                'model' => $model
            )
        );
	}
}