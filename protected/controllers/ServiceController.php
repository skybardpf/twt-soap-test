<?php
/**
 * Class ServiceController.
 * Управление SOAP сервисами.
 */
class ServiceController extends Controller
{
    public $defaultAction = 'list';

	public function actions()
	{
		return array(
			'create'=>array(
				'class' => 'application.controllers.service.CreateAction',
			),
			'update' => array(
				'class' => 'application.controllers.service.UpdateAction',
			),
			'delete' => array(
				'class' => 'application.controllers.service.DeleteAction',
			),
            'list' => array(
                'class' => 'application.controllers.service.ListAction',
            ),
            'run_tests' => array(
                'class' => 'application.controllers.service.Run_testsAction',
            ),
            'polling_run_tests' => array(
                'class' => 'application.controllers.service.Polling_run_testsAction',
            ),
		);
	}
}