<?php
/**
 * Добавление новой функции к SOAP сервису. Функция должна обязательно принадлежать
 * к группе функций {@see GroupFunctions}.
 *
 * @author Skibardin A.A. <skybardpf@artektiv.ru>
 *
 * @see SoapFunction
 * @see SoapService
 * @see GroupFunctions
 */
class CreateAction extends CAction
{
    /**
     * @param int $service_id
     * @throws CHttpException
     */
    public function run($service_id)
	{
        /**
         * @var $controller FunctionController
         */
        $controller = $this->controller;

        /**
         * @var $service SoapService
         */
        $service = $controller->loadService($service_id);
        $controller->pageTitle = 'Создание новой функции для сервиса «'.$service->name.'»';

        /**
         * @var $model SoapFunction
         */
        $model = $controller->createModel($service);

        if(isset($_POST['ajax']) && $_POST['ajax']==='model-form-form') {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }

        $class = get_class(SoapFunction::model());
        $class_func_param = get_class(SoapFunctionParam::model());

        $input_params = array();
        $output_params = array();

        if (isset($_POST[$class]) && !empty($_POST[$class])) {
            $valid = true;
            $model->attributes = $_POST[$class];
            if (isset($_POST[$class_func_param]) && !empty($_POST[$class_func_param])){
                foreach($_POST[$class_func_param] as $i=>$params){
                    $p = new SoapFunctionParam();
                    $p->attributes = $_POST[$class_func_param][$i];
//

                    if ($p->input_param){
                        $input_params[$i] = $p;
                    } else {
                        $output_params[$i] = $p;
                    }

                    $valid = $p->validate() && $valid;
                }
            }

            if ($valid && $model->validate()){
                try {
                    if ($model->save()){
                        /**
                         * @var $p SoapFunctionParam
                         */
                        foreach ($input_params as $p){
                            $p->function_id = $model->primaryKey;
                            $p->save();
                        }
                        /**
                         * @var $p SoapFunctionParam
                         */
                        foreach ($output_params as $p){
                            $p->function_id = $model->primaryKey;
                            $p->save();
                        }

                        $controller->redirect(
                            $controller->createUrl(
                                'list',
                                array('service_id' => $service->primaryKey)
                            )
                        );
                    }
                }catch (Exception $e){
                    $model->addError('id', $e->getMessage());
                }
            }
        }

        $controller->render(
            'form',
            array(
                'model' => $model,
                'service' => $service,
                'input_params' => $input_params,
                'output_params' => $output_params,
            )
        );
	}
}