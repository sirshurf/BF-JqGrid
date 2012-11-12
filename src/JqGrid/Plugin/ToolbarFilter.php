<?php

namespace Bf\JqGrid\JqGrid\Plugin;

use Bf\JqGrid\JqGrid;

/**
 * Display a search filter on each column
 *
 * @package Ingot_JQuery_JqGrid
 * @copyright Copyright (c) 2005-2009 Warrant Group Ltd. (http://www.warrant-group.com)
 * @author Andy Roberts
 */

class ToolbarFilter extends PluginAbstract
{
    protected $_options;
	
    public function __construct($options = array())
    {
		$this->setOptions($options);
    }

    public function preRender()
    {
        
        if (! isset($this->_options['stringResult'])) {
            $this->_options['stringResult'] = true;
        }
        
        $js = sprintf('%s("#%s").filterToolbar(%s);', 
                'jQuery', 
                $this->getGrid()->getId(), 
                $this->encodeJsonOptions($this->_options));
        
        $this->addOnLoad($js);
        
        $columns = $this->getGrid()->getColumns();
        
        foreach ($columns as $column) {
            $column->setOption('search', true);
        }
    }

    public function postRender()
    {    // Not implemented
    }

    public function preResponse()
    {    // Not implemented
    }

    public function postResponse()
    {    // Not implemented
    }
		
	public function getMethods(){
		return array();
	}
	
	public function getEvents(){
		return array("beforeSearch", "afterSearch",	"beforeClear",	"afterClear");
	}
	
}