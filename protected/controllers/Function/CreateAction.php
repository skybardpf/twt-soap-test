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
        $controller->pageTitle .= ' | Создание новой функции для сервиса «'.$service->name.'»';
        /**
         * @var $model SoapFunction
         */
        $model = $controller->createModel($service);

        if(isset($_POST['ajax']) && $_POST['ajax']==='model-form-form') {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }

        $class_func = get_class(SoapFunction::model());
        $class_func_param = get_class(SoapFunctionParam::model());

        $input_params = array();
        $output_params = array();

        if (isset($_POST[$class_func]) && !empty($_POST[$class_func])) {
            $valid = true;
            $model->attributes = $_POST[$class_func];
            if (isset($_POST[$class_func_param]) && !empty($_POST[$class_func_param])){
                /**
                 * Входящие параметры
                 */
                if (isset($_POST[$class_func_param]['input'])){
                    foreach($_POST[$class_func_param]['input'] as $i=>$params){
                        $p = new SoapFunctionParam();
                        $p->attributes = $params;
//                        $p->function_id = $model->primaryKey;

                        if (isset($params['__children__'])){
                            $parent = $params['__children__'];
                            foreach($parent as $j=>$attr){
                                $child = new SoapFunctionParam();
                                $child->attributes = $attr;
//                                $child->function_id = $model->primaryKey;
                                $p->children[$j] = $child;
                                $valid = $child->validate() && $valid;

                                if (isset($attr['--element_table--'])){
                                    $elements = $attr['--element_table--'];
                                    foreach($elements as $k=>$element){
                                        $el = new SoapFunctionParam();
                                        $el->attributes = $element;
//                                        $el->function_id = $model->primaryKey;
                                        $child->children[$k] = $el;
                                        $valid = $el->validate() && $valid;
                                    }
                                }
                            }
                        }
                        $input_params[$i] = $p;
                        $valid = $p->validate() && $valid;
                    }
                }

                /**
                 * Выходные параметры
                 */
                if (isset($_POST[$class_func_param]['output'])){
                    foreach($_POST[$class_func_param]['output'] as $i=>$params){
                        $p = new SoapFunctionParam();
                        $p->attributes = $params;
//                        $p->function_id = $model->primaryKey;

                        if (isset($params['__children__'])){
                            $parent = $params['__children__'];
                            foreach($parent as $j=>$attr){
                                $child = new SoapFunctionParam();
                                $child->attributes = $attr;
//                                $child->function_id = $model->primaryKey;
                                $p->children[$j] = $child;
                                $valid = $child->validate() && $valid;

                                if (isset($attr['--element_table--'])){
                                    $elements = $attr['--element_table--'];
                                    foreach($elements as $k=>$element){
                                        $el = new SoapFunctionParam();
                                        $el->attributes = $element;
//                                        $el->function_id = $model->primaryKey;
                                        $child->children[$k] = $el;
                                        $valid = $el->validate() && $valid;
                                    }
                                }
                            }
                        }
                        $output_params[$i] = $p;
                        $valid = $p->validate() && $valid;
                    }
                }
            }

            if ($valid && $model->validate()){
                try {
                    if ($model->save()){
//

                        /**
                         * @var $p SoapFunctionParam
                         */
                        foreach ($input_params as $p){
                            $p->function_id = $model->primaryKey;
                            $p->save();
                            /**
                             * @var $child SoapFunctionParam
                             */
                            foreach($p->children as $child){
                                $child->parent_id = $p->primaryKey;
                                $child->function_id = $model->primaryKey;
                                $child->save();

                                /**
                                 * @var $element SoapFunctionParam
                                 */
                                foreach($child->children as $element){
                                    $element->parent_id = $child->primaryKey;
                                    $element->function_id = $model->primaryKey;
                                    $element->save();
                                }
                            }
                        }

                        /**
                         * Сохраняем выходные параметры
                         * @var $p SoapFunctionParam
                         */
                        foreach ($output_params as $p){
                            $p->function_id = $model->primaryKey;
                            $p->save();
                            /**
                             * @var $child SoapFunctionParam
                             */
                            foreach($p->children as $child){
                                $child->function_id = $model->primaryKey;
                                $child->parent_id = $p->primaryKey;
                                $child->save();

                                /**
                                 * @var $element SoapFunctionParam
                                 */
                                foreach($child->children as $element){
                                    $element->function_id = $model->primaryKey;
                                    $element->parent_id = $child->primaryKey;
                                    $element->save();
                                }
                            }
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