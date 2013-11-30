<?php
/**
 * Редактирование существующего SOAP сервиса.
 *
 * @author Skibardin A.A. <skybardpf@artektiv.ru>
 *
 * @see SoapService
 */
class UpdateAction extends CAction
{
    /**
     * @param integer $id
     * @throws CHttpException
     */
    public function run($id = null)
    {
        /**
         * @var ServiceController $controller
         */
        $controller = $this->controller;

        /**
         * @var SoapService $model
         */
        if ($id === null) {
            $model = new SoapService();
        } else {
            $model = SoapService::model()->findByPk($id);
            if ($model === null) {
                throw new CHttpException(404, 'Не найден SOAP сервис.');
            }
        }

        if (isset($_POST['ajax']) && $_POST['ajax'] === 'model-form-form') {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }

        $controller->pageTitle = Yii::app()->name . ' | ' . Yii::t('app', ($model->isNewRecord ? 'Добавление' : 'Редактирование') . ' SOAP сервиса');

        $data = Yii::app()->request->getPost(get_class($model));
        if ($data) {
            $model->attributes = $data;
            try {
                if ($model->validate()) {
                    $model->save(false);
                    $controller->redirect($controller->createUrl('index'));
                }
            } catch (Exception $e) {
                $model->addError('id', $e->getMessage());
            }
        }

        $controller->render(
            'form',
            array('model' => $model)
        );
    }
}