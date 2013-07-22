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
        $count_children = 0;

        if (isset($_POST[$class_func]) && !empty($_POST[$class_func])) {
            $valid = true;
            $model->attributes = $_POST[$class_func];
            if (isset($_POST[$class_func_param]) && !empty($_POST[$class_func_param])){
//                var_dump($_POST[$class_func_param]);die;
                foreach($_POST[$class_func_param] as $i=>$params){
                    $p = new SoapFunctionParam();
                    $p->attributes = $_POST[$class_func_param][$i];
                    $p->function_id = $model->primaryKey;

                    if (isset($_POST[$class_func_param][$i]['__children__'])){
                        $parent = $_POST[$class_func_param][$i]['__children__'];
                        foreach($parent as $j=>$attr){
                            $child = new SoapFunctionParam();
                            $child->attributes = $attr;
                            $child->function_id = $model->primaryKey;
                            $p->children[$j] = $child;

                            $valid = $child->validate() && $valid;
                        }
                    }

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
                        foreach ($model->soapFunctionParams as $p){
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
                                $child->parent_name = $p->name;
                                $child->save();
                            }
                        }
                        /**
                         * @var $p SoapFunctionParam
                         */
                        foreach ($output_params as $p){
                            $p->save();
                            /**
                             * @var $child SoapFunctionParam
                             */
                            foreach($p->children as $child){
                                $child->parent_name = $p->name;
                                $child->save();
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
            $i = 0;
            foreach ($model->soapFunctionParams as $p){
                if (empty($p->parent_name)){
                    $p->children = $p->getChildren();
                    $count_children += count($p->children);

                    if ($p->input_param){
                        $input_params[$i] = $p;
                    } else {
                        $output_params[$i] = $p;
                    }
                    $i++;
                }
            }
        }
//        var_dump($count_children);

        $controller->render(
            'form',
            array(
                'model' => $model,
                'service' => $service,
                'input_params' => $input_params,
                'output_params' => $output_params,
                'count_children' => $count_children
            )
        );
	}
}