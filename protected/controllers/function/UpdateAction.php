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
//        var_dump($model);die;
        $service = $controller->loadService($model->groupFunctions->soapService->primaryKey);
        $controller->pageTitle = 'Редактирование функции «'.$model->name.'» для сервиса «'.$service->name.'»';

        if(isset($_POST['ajax']) && $_POST['ajax']==='model-form-form') {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }

        $class = get_class(SoapFunctionParam::model());

        $names = array();
        if (isset($_POST[$class]) && !empty($_POST[$class])) {
//            $old_args = $model->args;
//            $model->attributes = $_POST[get_class($model)];
//            var_dump($_POST[$class]);die;

            foreach ($model->soapFunctionParams as $p){
                $p->delete();
            }

            $valid = true;
            $new_params = array();
            foreach($_POST[$class] as $i=>$item){
//                $name = $_POST[$class][$i]['name'];

//                var_dump($i);
//                var_dump($item);
//                die;

                $p = new SoapFunctionParam();
                $p->function_id = $model->primaryKey;
                $p->attributes = $_POST[$class][$i];
                if ($p->validate()){
                    $p->save();
                    $names[$p['name']] = $p;
                } else {
//                    var_dump($p->getErrors());
                }

//                if(isset($names[$name])){
//                    /**
//                     * @var $p SoapFunctionParam
//                     */
//                    $p = $names[$name];
//                    $p->attributes = $_POST[$class][$i];
//                    if ($p->validate()){
//                        $p->save();
//                    }
//
////                    $valid = $names[$name]->validate() && $valid;
//                } else {
//                    $p = new SoapFunctionParam();
//                    $p->function_id = $model->primaryKey;
//                    $p->attributes = $_POST[$class][$i];
//                    if ($p->validate()){
//                        $p->save();
//                    }
//                }

            }
            $this->controller->redirect($this->controller->createUrl('list', array('service_id' => $model->service_id)));



//            foreach($model->soapFunctionParams as $i=>$item){
//                if(isset($_POST[$class][$i])){
//                    $item->attributes = $_POST[$class][$i];
//                }
//                $valid = $item->validate() && $valid;
//            }
//            if($valid){
                try {
//                    foreach($model->soapFunctionParams as $item){
//                        $item->save();
//                    }
                }catch (Exception $e){
//                    $model->addError('id', $e->getMessage());
                }
//            }
        } else {
            foreach ($model->soapFunctionParams as $p){
                $names[$p['name']] = $p;
            }
        }

//        var_dump($model->soapFunctionParams);die;

        $controller->render(
            'form',
            array(
                'model' => $model,
                'service' => $service,
                'function_params' => $model->soapFunctionParams
            )
        );
	}
}