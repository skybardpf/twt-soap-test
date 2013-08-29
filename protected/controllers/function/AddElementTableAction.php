<?php
/**
 * Только Ajax. Функция генерирует Html код, для элемента таблицы.
 *
 * @author Skibardin A.A. <skybardpf@artektiv.ru>
 *
 * @see SoapFunction
 */
class AddElementTableAction extends CAction
{
    /**
     * @param string $type           Тип данных, генериреум соответствующий код.
     * @param integer $index
     * @param integer $child_index
     * @param integer $element_index
     * @param string $input_param    Входящий или исходящий параметр.
     *
     * @throws CHttpException
     */
    public function run($type, $index, $child_index, $element_index, $input_param='false')
	{
        if (Yii::app()->request->isAjaxRequest){
            /**
             * @var $controller FunctionController
             */
            $controller = $this->controller;

            $model = new SoapFunctionParam();
            $model->input_param = ($input_param == 'true') ? 1 : 0;

            $controller->renderPartial('_add_element_table', array(
                'model' => $model,
                'type' => $type,
                'index' => $index,
                'child_index' => $child_index,
                'element_index' => $element_index,
            ));
        }
	}
}