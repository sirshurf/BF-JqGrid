<?php
/**
 * @see Ingot_JQuery_JqGrid_Column_Decorator_Abstract
 */
require_once 'Ingot/JQuery/JqGrid/Column/Decorator/Abstract.php';

/**
 * Decorate a column which contains a checkbox with Icons
 * 
 * @package Ingot_JQuery_JqGrid
 * @copyright Copyright (c) 2005-2009 Warrant Group Ltd. (http://www.warrant-group.com)
 * @author Andy Roberts
 */

class Ingot_JQuery_JqGrid_Column_Decorator_CheckboxIcon extends Ingot_JQuery_JqGrid_Column_Decorator_Abstract
{
    protected $_options = array();
    
    CONST ICON_CHECKED = 'ui-icon ui-icon-check';
    CONST ICON_UNCHECKED = ' ';

    /**
     * Constructor
     * 
     * @return void
     */
    public function __construct($column, $options = array())
    {
        $this->_column = $column;
        $this->_options = $options;
        
    }

    /**
	 * Decorate column to display URL links
	 * 
	 * @return void
	 */
    public function decorate()
    {
    		
    	$strIconChecked = $this->getOption('icon_checked');		
		if (empty($strIconChecked)) {
			$strIconChecked = self::ICON_CHECKED;
		}
		
		$strIconUnChecked = $this->getOption('icon_unchecked');		
		if (empty($strIconUnChecked)) {
			$strIconUnChecked = self::ICON_UNCHECKED;
		}
    	
        $this->_column->setOption('formatter', new Zend_Json_Expr('checkboxIcon'));  
        $this->_column->setOption('fixed', TRUE);  
        $this->_column->setOption('width', 60);  
        $this->_column->setOption('align', 'center');      
		$this->_objGrid->getView ()->headScript()->appendScript($this->createJsFormatter());
		
		if ($this->_column->getOption('editable')){
        	$this->_column->setOption('edittype', 'custom');
        	
//        	$this->_column->setOption('editoptions',array("value" => '<span class="grid-icon '.$strIconChecked.'></span>:<span class="grid-icon '.$strIconUnChecked.'"></span>'));
        	
        	$this->_column->setOption('editoptions',array("custom_element" => new Zend_Json_Expr('checkboxIconElement'),"custom_value" => new Zend_Json_Expr('checkboxIconValue')));
        	
		}
    }
    
    protected function getCheckedString(){
    		
    	$strIconChecked = $this->getOption('icon_checked');		
		if (empty($strIconChecked)) {
			$strIconChecked = self::ICON_CHECKED;
		}
		return '<span class="grid-icon '.$strIconChecked.'"></span>';
    }
    
    protected function createJsFormatter()
    {
    

		
		$strIconUnChecked = $this->getOption('icon_unchecked');		
		if (empty($strIconUnChecked)) {
			$strIconUnChecked = self::ICON_UNCHECKED;
		}
    	
    	ob_start();
    	?>    
		function checkboxIcon ( cellvalue, options, rowObject ) {
			var returnValue = null;
			
			if (cellvalue == '1') {
				//Enabled
				returnValue = '<?php echo $this->getCheckedString();?>';
				
			} else {
				//Disabled
				returnValue = '<span class="grid-icon  <?php echo $strIconUnChecked;?>"></span>';
			}
		
			return returnValue;
		}
		function checkboxIconElement (vl, options) {
	
			elem = document.createElement("input");
				elem.type = "checkbox";
				
				if( !options.value ) {
					var vl1 = vl.toLowerCase();
					if(vl1.search(/(false|0|no|off|undefined)/i)<0 && vl1!=="") {
						if(vl == '<?php echo $this->getCheckedString();?>'){
							elem.checked=true;
							elem.defaultChecked=true;
							elem.value = '1';
						} else {
							elem.value = '0';
						} 
						 vl;
					} else {
						elem.value = "on";
					}
					$(elem).attr("offval","off");
				} else {
					var cbval = options.value.split(":");
					if(vl === cbval[0]) {
						elem.checked=true;
						elem.defaultChecked=true;
					}
					elem.value = cbval[0];
					$(elem).attr("offval",cbval[1]);
				}
				checkboxIconSetAttributes(elem, options, ['value']);
				options = checkboxIconBindEv(elem,options);
				$(elem).attr("role","checkbox");

			return elem;
		}
		function checkboxIconValue (elem, operation, value) {
			
		    if(operation === 'get') {
		    	if ($(elem).is(':checked')){
		    		return '1';
		    	} else {
       				return '0';
       			}
    		} else if(operation === 'set') {
    			
    			if(value==checkboxIcon()){
    				$(elem).attr("checked",false);
    				$(elem).attr("value",0);
    			}else{
    				$(elem).attr("checked",true);
    				$(elem).attr("value",1);
    			}
    		}
	
		}
		
		function checkboxIconBindEv (el, opt) {
			if($.isFunction(opt.dataInit)) {
				opt.dataInit(el);
			}
			if(opt.dataEvents) {
				$.each(opt.dataEvents, function() {
					if (this.data !== undefined) {
						$(el).bind(this.type, this.data, this.fn);
					} else {
						$(el).bind(this.type, this.fn);
					}
				});
			}
			return opt;
		}
		function checkboxIconSetAttributes(elm, atr, exl ) {
		
			var exclude = ['dataInit','dataEvents','dataUrl', 'buildSelect','sopt', 'searchhidden', 'defaultValue', 'attr','custom_element'];
			if(typeof(exl) != "undefined" && $.isArray(exl)) {
				exclude = $.extend(exclude, exl);
			}
			$.each(atr, function(key, value){
				if($.inArray(key, exclude) === -1) {
					$(elm).attr(key,value);
				}
			});
			if(!atr.hasOwnProperty('id')) {
				$(elm).attr('id', $.jgrid.randId());
			}
		}
		
		<?php 
		$strHtml = ob_get_clean();
		return $strHtml;    	
    }
}