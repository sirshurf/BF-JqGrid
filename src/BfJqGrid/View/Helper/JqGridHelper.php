<?php


namespace BfJqGrid\View\Helper;

use Zend\View\Helper\ViewModel;

use Zend\View\Helper\AbstractHelper;
use BfJqGrid\JqGrid;

/**
 * JqGrid View Helper
 * 
 * @package Ingot_JQuery_JqGrid
 * @copyright Copyright (c) 2005-2009 Warrant Group Ltd. (http://www.warrant-group.com)
 * @author Andy Roberts
 */

class JqGridHelper extends AbstractHelper {

	private $_objViewModel;
	private $_arrUnEscapeList = array ();
	
	protected function getOptionsString(JqGrid\JqGrid $grid) {
		
		$arrGridOptions = $grid->getOptions ();
		
		$strOptions = $grid->encodeJsonOptions($arrGridOptions);
		return $strOptions;
	}
	
	/**
	 * Render jqGrid
	 * */	
	 /**
	 * __invoke
	 *
	 * @access public
	 * @param Ingot_JQuery_JqGrid $grid
	 */
	public function __invoke($grid){
		
		$html = array ();
		$js = array ();
		$onload = array ();
		
		$onload [] = sprintf ( '%s("#%s").jqGrid(%s);', 'jQuery', $grid->getId (), $this->getOptionsString ( $grid ) );
		
		$html [] = '<table id="' . $grid->getId () . '"><tr><td /></tr></table>';
		
		$arrJqGridPluginBroker = $grid->getView()->jqGridPluginBroker;

		// Load the jqGrid plugin view variables
		$html = array_merge ( $html, $arrJqGridPluginBroker['html'] );
		$js = array_merge ( $js, $arrJqGridPluginBroker ['js'] );
		$onload = array_merge ( $onload, $arrJqGridPluginBroker['onLoad'] );
		
		$this->view->headScript ()->appendScript ( 'jQuery(function() {'.implode ( "\n", $onload ).'});' );
		$this->view->headScript ()->appendScript ( implode ( "\n", $js ) );
		
		return implode ( "\n", $html );
	}
	
}