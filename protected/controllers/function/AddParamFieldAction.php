<?php
/**
 * Только Ajax. Функция генерирует Html код, для различных типов данных параметров
 * функции и отдает его.
 *
 * @author Skibardin A.A. <skybardpf@artektiv.ru>
 *
 * @see SoapFunction
 */
class AddParamFieldAction extends CAction
{
    /**
     * @param string $type           Тип данных, генериреум соответствующий код.
     * @param integer $index         Индекс в массиве уже существующих параметров.
     * @param string $input_param    Входящий или исходящий параметр.
     * @param string $child
     * @param integer $child_index
     * @throws CHttpException
     */
    public function run($type, $index, $input_param='false', $child='false', $child_index=-1)
	{
        if (Yii::app()->request->isAjaxRequest){
            /**
             * @var $controller FunctionController
             */
            $controller = $this->controller;

            $model = new SoapFunctionParam();
            $types = SoapFunctionParam::getTypesOfData();
            $model->input_param = ($input_param == 'true') ? 1 : 0;
            $child = ($child == 'true') ? true : false;
            $model->type_of_data = (!isset($types[$type])) ? SoapFunctionParam::DEFAULT_TYPE_OF_DATA : $type;

            $controller->renderPartial('_add_param_field', array(
                'model' => $model,
                'index' => $index,
                'child' => $child,
                'child_index' => $child_index
            ));
        }
	}
}