<?php

namespace BfJqGrid\JqGrid\Plugin\Row;

use BfJqGrid\JqGrid;
use BfJqGrid\JqGrid\Plugin;


/**
 * Add On DoubleClick Row redirect... 
 * Using RowID only currently
 *  
 * @package Ingot_JQuery_JqGrid
 * @copyright Copyright (c) 2005-2009 Warrant Group Ltd. (http://www.warrant-group.com)
 * @author Andy Roberts
 */

class DblClkRedirect extends Plugin\PluginAbstract
{
	protected $_additionalParam;
	protected $_additionalStaticParams;
	
	protected $_model;
	protected $_controller;
	protected $_action;

	/**
	 * 
	 * Enter description here ...
	 * @param string $strModel
	 * @param string $strController
	 * @param string $strAction
	 * @param string $strParamName
	 */
	function __construct ($strModel, $strController, $strAction, $strParamName = 'id', $arrAdditionalParams = array())
	{
		$this->_model = $strModel;
		$this->_controller = $strController;
		$this->_action = $strAction;
		$this->_additionalParam = $strParamName;
		$this->_additionalStaticParams = $arrAdditionalParams;
				
	}

	public function preRender ()
	{
		$arrUrlData = array(
			'module' => $this->_model, 'controller' => $this->_controller, 'action' => $this->_action
		) + $this->_additionalStaticParams;
		
		
		
		$sm = $this->getGrid()->getEventManager()->getApplication()->getServiceManager();
		$helper = $sm->get('viewhelpermanager')->get('url');
		$strUrl = $helper->__invoke($this->_model,$arrUrlData);
		
// 		$strUrl = $this->getGrid()
// 			->getView()
// 			->url($arrUrlData, null, true, false);
		
		$this->getGrid()->setOption('ondblClickRow', "function(rowId, iRow, iCol, e){ if(rowId){  document.location.href ='" . $strUrl . "/'+rowId } }");
	
	}

	public function postRender ()
	{ // Not implemented
	}

	public function preResponse ()
	{ // Not implemented
	}

	public function postResponse ()
	{ // Not implemented
	}

	public function getMethods ()
	{
		return array();
	}

	public function getEvents ()
	{
		return array();
	}

}