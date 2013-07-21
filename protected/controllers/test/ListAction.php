<?php
/**
 * Показать список тестов SoapTest для определенной функции SoapFunction.
 * Функция определяется параметром $func_id.
 *
 * @see SoapTest
 * @see SoapFunction
 */
class ListAction extends CAction
{
    /**
     * @param int $func_id
     * @throws CHttpException
     */
	public function run($func_id)
	{
        /**
         * @var $controller TestController
         */
        $controller = $this->controller;
        $function = $controller->loadFunction($func_id);

        $data = SoapTest::getList($function->primaryKey);
        $runningTests = SoapTest::getRunningTests($function->primaryKey);

        $this->controller->render('list',
            array(
                'runningTests' => $runningTests,
                'function' => $function,
                'data' => $data,
            )
        );
	}
}