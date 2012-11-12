<?php

/**
 * @see Ingot_JQuery_JqGrid_Plugin_Abstract
 */
require_once 'Ingot/JQuery/JqGrid/Plugin/Abstract.php';

/**
 * Display a pagination interface on grid for navigating through pages,
 * and providing buttons for common row operations.
 *
 * @package Ingot_JQuery_JqGrid
 * @copyright Copyright (c) 2005-2009 Warrant Group Ltd. (http://www.warrant-group.com)
 * @author Andy Roberts
 */

class Ingot_JQuery_JqGrid_Plugin_GroupHeaders extends Ingot_JQuery_JqGrid_Plugin_Abstract {
	
	protected $_pagerName;
	
	public function preRender() {
		// Render Local Settings
		$this->renderOnInitialiseForm ();
		
		$js = sprintf ( '%s("#%s").jqGrid("%s",%s)', ZendX_JQuery_View_Helper_JQuery::getJQueryHandler (), $this->_grid->getId (), 'setGroupHeaders', $this->getConfigString () );
		
		$js .= ';';
		
		
		$this->addOnLoad ( $js );
		
	}
	
	public function getName(){
		return $this->_pagerName;
	}
	
	/**
	 *
	 * Checks that there is additional On Initialise Form actions persent and adds them to theglobal options!
	 * @return Ingot_JQuery_JqGrid_Plugin_Pager
	 */
	private function renderOnInitialiseForm() {
		return $this;
	}
	
	public function getConfigString($strOption = NULL) {

		$arrGridOptions = $this->getOptions();
		
		$strOptions = $this->encodeJsonOptions($arrGridOptions);
		return $strOptions;
	
	}
	
	public function postRender() { // Not implemented
	}
	
	public function preResponse() { // Not implemented
	}
	
	public function postResponse() { // Not implemented
	}
	
	public function getMethods() {
		// Not Implimented
	}
	
	public function getEvents() {
	}
	
	public function addGroupHeaderLine($strColName, $intNumCol, $strTitleText){
		$arrGrpHeaderOptions = $this->getOption('groupHeaders');
		$arrGrpHeaderOptions[] = array("startColumnName"=>$strColName, "numberOfColumns"=> $intNumCol, "titleText"=> $strTitleText);
		$this->setOption('groupHeaders', $arrGrpHeaderOptions);
	}
	
}