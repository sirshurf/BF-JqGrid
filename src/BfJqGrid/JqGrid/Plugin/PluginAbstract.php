<?php

namespace Bf\JqGrid\JqGrid\Plugin;

use Zend\Json\Expr;

use Zend\View\Helper\ViewModel;
use Zend\Json;
use Bf\JqGrid\JqGrid\JqGrid;

/**
 * Plugin Abstract
 *
 * @package Ingot_JQuery_JqGrid
 * @copyright Copyright (c) 2005-2009 Warrant Group Ltd. (http://www.warrant-group.com)
 * @author Andy Roberts
 */

abstract class PluginAbstract {
	/**
	 * Grid Instance
	 * 
	 * @var Ingot_JQuery_JqGrid
	 */
	protected $_grid;
	
	/**
	 * Grid Data Instance
	 * 
	 * @var object
	 */
	protected $_gridData;
	
	/**
	 * View Instance
	 * 
	 * @var ViewModel
	 */
	protected $_view;
	
	protected $_objEventManger;
	
	/**
	 * Default Plugin Config
	 * 
	 * @var array
	 */
	protected $_defaultConfig = array ();
	
	/**
	 * Plugin Name
	 * 
	 * @var $_defaultPlugin string
	 */
	protected $_defaultPlugin = "";
	
	/**
	 * Plugin options
	 * 
	 * @var array
	 */
	
	protected $_options = array ();
	/**
	 * Set View Instance
	 * 
	 * @param $view
	 */
	public function setView($view) {
		$this->_view = $view;
	}
	
	public function setEventManager($objEventManager) {
		$this->_objEventManger = $objEventManager;
		return $this;
	}
	
	/**
	 * Set Grid Instance 
	 * 
	 * @param $grid Ingot_JQuery_JqGrid
	 * @return void
	 */
	public function setGrid(JqGrid $grid) {
		$this->_grid = $grid;
		return $this;
	}
	
	/**
	 * Get Grid Instance
	 *
	 * @return Ingot_JQuery_JqGrid
	 */
	public function getGrid() {
		return $this->_grid;
	}
	

	public function getEventManager() {
		return $this->_objEventManger;
	}
	
	/**
	 * Set an instance of the grid data structure
	 *
	 * @param object $data
	 * @return void
	 */
	public function setGridData($data) {
		$this->_gridData = $data;
	}
	
	/**
	 * Get an instance of the grid data structure
	 * 
	 * @return object
	 */
	public function getGridData() {
		return $this->_gridData;
	}
	
	/**
	 * Add HTML to plugin
	 *
	 * @param $html HTML string
	 */
	public function addHtml($html) {
		$arrJqGridPluginBroker = $this->_view->jqGridPluginBroker ;
		$arrJqGridPluginBroker ['html'] [] = $html;
		$this->_view->jqGridPluginBroker = $arrJqGridPluginBroker;
	}
	
	/**
	 * Add javascript to plugin for onload
	 *
	 * @param $js javascript string
	 */
	public function addOnLoad($js) {
		$arrJqGridPluginBroker = $this->_view->jqGridPluginBroker ;
		$arrJqGridPluginBroker ['onLoad'] [] = $js;
		$this->_view->jqGridPluginBroker = $arrJqGridPluginBroker;
	}
	
	/**
	 * Add javascript to plugin
	 *
	 * @param $js javascript string
	 */
	public function addJavascript($js, $onload = false) {
		if ($onload == true) {
			return $this->addOnLoad ( $js );
		}
		$arrJqGridPluginBroker = $this->_view->jqGridPluginBroker ;
		$arrJqGridPluginBroker ['js'] [] = $js;
		$this->_view->jqGridPluginBroker = $arrJqGridPluginBroker;
		
	}
	
	/**
	 * Sets options
	 *
	 * @param array $options
	 * @return Ingot_JQuery_JqGrid_Plugin_Abstract
	 */
	public function setOptions(array $options = array()) {
		
		foreach ( $options as $k => $v ) {
			$this->setOption ( $k, $v );
		}
		return $this;
	}
	
	/**
	 * Set a single column option
	 * 
	 * @return Ingot_JQuery_JqGrid_Plugin_Abstract
	 */
	public function setOption($name, $value) {
		
		if (is_array ( $value )) {
			$value = $this->makesaveOptions ( $name, $value );
			$arrOption = $this->getOptions ();
			$this->_options = $this->MergeArrays ( ( array ) $arrOption, array ($name => $value ) );
		
		//			$this->_options = $this->array_merge_recursive_leftsource ( array ($name => $value ), ( array ) $arrOption );
		} else {
			
			$this->_options [$name] = $this->makesaveOptions ( $name, $value );
		
		}
		
		return $this;
	}
	
	function MergeArrays($Arr1, $Arr2) {
		if (is_array($Arr1)) {
			foreach ( $Arr2 as $key => $Value ) {
				if (array_key_exists ( $key, $Arr1 ) && is_array ( $Value ))
					$Arr1 [$key] = $this->MergeArrays ( $Arr1 [$key], $Arr2 [$key] );
				
				else
					$Arr1 [$key] = $Value;
			
			}
		}
		
		return $Arr1;
	
	}
	
	private function makesaveOptions($strOptionName, $mixOptions) {
		if (is_array ( $mixOptions )) {
			foreach ( $mixOptions as $strSubName => $mixSuvOptions ) {
				$mixOptions [$strSubName] = $this->makesaveOptions ( $strSubName, $mixSuvOptions );
			}
		}
		$arrUnEscapeList = array_merge ( ( array ) $this->getMethods (), ( array ) $this->getEvents () );
		if (in_array ( $strOptionName, $arrUnEscapeList, true )) {
			$mixOptions = new Expr ( $mixOptions );
		}
		return $mixOptions;
	}
	
	/**
	 * Get a single option
	 * 
	 * @return mixed
	 */
	public function getOption($name) {
		if (array_key_exists ( $name, $this->_options )) {
			return $this->_options [$name];
		} else {
			return false;
		}
	}
	
	/**
	 * Get a single option
	 * 
	 * @return mixed
	 */
	public function getOptions() {
		return $this->_options;
	
	}
	
	protected function getConfig() {
		
		$arrData = $this->_defaultConfig;
		
		if (! empty ( $this->_defaultPlugin )) {
			$arrConfigData = $this->getOption ( $this->_defaultPlugin );
			if (! empty ( $arrConfigData )) {
				$arrData = array_merge ( $arrData, ( array ) $arrConfigData );
			}
		}
		
		$objGrid = $this->getGrid ();
		return $this->encodeJsonOptions ( $arrData );
	}
	
	public function encodeJsonOptions($arrProperties) {
		
		$strOptions = '';
		
		if ($this->getGrid ()->isUseCustonJson ()) {
			
			$arrUnEscapeList = array_merge ( ( array ) $this->getMethods (), ( array ) $this->getEvents () );
			
			// Iterate over array
			foreach ( ( array ) $arrProperties as $strPropertyKey => $mixProperty ) {
				
				if (! empty ( $strOptions )) {
					$strOptions .= ", ";
				}
				// Check that it's not one of the elements that needs escaiping 	
				if (in_array ( $strPropertyKey, $arrUnEscapeList, true )) {
					// This value does not need escaiping
					$strOptions .= '"' . $strPropertyKey . '":' . $mixProperty;
				} else {
					if (is_array ( $mixProperty )) {
						// Recursive call
						$strOptions .= '"' . $strPropertyKey . '":' . $this->encodeJsonOptions ( $mixProperty );
					} else {
						$strOptions .= '"' . $strPropertyKey . '":' . Json\Encoder::encode($mixProperty );
					}
				
				}
			}
			
			$strOptions = "{" . $strOptions . "}";
		
		} else {
			$strOptions = Json\Encoder::encode($arrProperties);
		}
		return $strOptions;
	}

	
	abstract public function preResponse();
	abstract public function postResponse();
	abstract public function preRender();
	abstract public function postRender();
	
	abstract public function getMethods();
	abstract public function getEvents();
}