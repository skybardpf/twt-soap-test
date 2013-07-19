<?php
/**
 * Рекдировать существующий тест SoapTest.
 *
 * @see SoapTest
 * Class UpdateAction
 */
class UpdateAction extends CAction
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

        if(isset($_POST['ajax']) && $_POST['ajax']==='model-form-form') {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }

        if (isset($_POST[get_class($model)])) {
            $old_args = $model->args;
            $model->attributes = $_POST[get_class($model)];
            try {
                if ($model->validate()){
                    if ($old_args != $model->args){
                        $model->test_result = SoapTest::TEST_RESULT_NOT_EXECUTED;
                        $model->date_start = NULL;
                        $model->date_end = NULL;
                        $model->last_return = NULL;
                    }
                    if ($model->save()) {
                        $this->controller->redirect($this->controller->createUrl('list', array('func_id' => $model->soapFunction->primaryKey)));
                    }

                }
            }catch (Exception $e){
                $model->addError('id', $e->getMessage());
            }
        }
        $this->controller->render(
            'form',
            array(
                'model' => $model
            )
        );
	}
}