<?php
/**
 * Редактировать параметры функции {SoapFunction}.
 *
 * @see SoapFunction
 */
class UpdateAction extends CAction
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
        /**
         * @var $service SoapService
         */
        $service = $controller->loadService($model->groupFunctions->soapService->primaryKey);
        $controller->pageTitle .= ' | Редактирование функции «'.$model->name.'» для сервиса «'.$service->name.'»';

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
                        $p->function_id = $model->primaryKey;

                        if (isset($params['__children__'])){
                            $parent = $params['__children__'];
                            foreach($parent as $j=>$attr){
                                $child = new SoapFunctionParam();
                                $child->attributes = $attr;
                                $child->function_id = $model->primaryKey;
                                $p->children[$j] = $child;
                                $valid = $child->validate() && $valid;

                                if (isset($attr['--element_table--'])){
                                    $elements = $attr['--element_table--'];
                                    foreach($elements as $k=>$element){
                                        $el = new SoapFunctionParam();
                                        $el->attributes = $element;
                                        $el->function_id = $model->primaryKey;
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
                        $p->function_id = $model->primaryKey;

                        if (isset($params['__children__'])){
                            $parent = $params['__children__'];
                            foreach($parent as $j=>$attr){
                                $child = new SoapFunctionParam();
                                $child->attributes = $attr;
                                $child->function_id = $model->primaryKey;
                                $p->children[$j] = $child;
                                $valid = $child->validate() && $valid;

                                if (isset($attr['--element_table--'])){
                                    $elements = $attr['--element_table--'];
                                    foreach($elements as $k=>$element){
                                        $el = new SoapFunctionParam();
                                        $el->attributes = $element;
                                        $el->function_id = $model->primaryKey;
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
                        foreach ($model->inputParams as $p){
                            $p->delete();
                        }
                        /**
                         * @var $p SoapFunctionParam
                         */
                        foreach ($input_params as $p){
                            $p->save();
                            /**
                             * @var $child SoapFunctionParam
                             */
                            foreach($p->children as $child){
                                $child->parent_id = $p->primaryKey;
                                $child->save();

                                /**
                                 * @var $element SoapFunctionParam
                                 */
                                foreach($child->children as $element){
                                    $element->parent_id = $child->primaryKey;
                                    $element->save();
                                }
                            }
                        }

                        foreach ($model->outputParams as $p){
                            $p->delete();
                        }
                        /**
                         * Сохраняем выходные параметры
                         * @var $p SoapFunctionParam
                         */
                        foreach ($output_params as $p){
                            $p->save();
                            /**
                             * @var $child SoapFunctionParam
                             */
                            foreach($p->children as $child){
                                $child->parent_id = $p->primaryKey;
                                $child->save();

                                /**
                                 * @var $element SoapFunctionParam
                                 */
                                foreach($child->children as $element){
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
        } else {
            foreach($model->inputParams as $p){
                if (empty($p->parent_id)){
                    $p->children = $p->getChildren();

                    foreach($p->children as $k=>$child){
                        if ($child->type_of_data == SoapFunctionParam::TYPE_DATA_ELEMENT_TABLE){
                            $p->children[$k]->children = $child->getChildren();
                        }
                    }

                    $input_params[] = $p;
                }
            }
            foreach($model->outputParams as $p){
                if (empty($p->parent_id)){
                    $p->children = $p->getChildren();

                    foreach($p->children as $k=>$child){
                        if ($child->type_of_data == SoapFunctionParam::TYPE_DATA_ELEMENT_TABLE){
                            $p->children[$k]->children = $child->getChildren();
                        }
                    }

                    $output_params[] = $p;
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