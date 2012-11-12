<?php

return array (
		'jqgrid' => array (),
		'view_manager' => array (
				'template_path_stack' => array (
						'BfJqGrid' => __DIR__ . '/../view' 
				),
				'strategies' => array (
						'ViewJsonStrategy' 
				) 
		),
		'view_helpers' => array (
				'invokables' => array (
						'JqGridHelper' => 'BfJqGrid\View\Helper\JqGridHelper',
						'jqgridhelper' => 'BfJqGrid\View\Helper\JqGridHelper' 
				) 
		)
		 
);
