<?php

class Ingot_JQuery_JqGrid_Column_DoubleColumn
{

	public static function createSelectColumn (Ingot_JQuery_JqGrid $objGrid, $mixValueData, $options = array(), $boolRequiered = TRUE)
	{
		
		$objAdapter = $objGrid->getAdapter();
		
		if ($objAdapter instanceof Ingot_JQuery_JqGrid_Adapter_DbTableSelect && is_string($mixValueData)) {
			$objTable = $objGrid->getAdapter()
				->getSelect()
				->getTable();
			$strTableClassName = get_class($objTable);
			$arrReferenceMap = $objTable->getReferenceByName($mixValueData);
			
			$strTableClass = $arrReferenceMap[$strTableClassName::REF_TABLE_CLASS];
			$arrColumns = array_merge($arrReferenceMap[$strTableClassName::REF_COLUMNS], array(
				$arrReferenceMap['displayColumn']
			));
			$index = $arrReferenceMap[$strTableClassName::COLUMNS][0];
			
			$objReferenceTable = new $strTableClass();
			$objReferenceTableSelect = $objReferenceTable->select(TRUE);
			
			if (!isset($options) || !isset($options['addIsDeleted']) || empty($options['addIsDeleted'])){
				$objReferenceTableSelect->where("is_deleted = ?", false);
			}
			
			if (!empty($options['sql_rder'])){
				$objReferenceTableSelect->order($options['sql_rder']);
				unset($options['sql_rder']);
			}
			
			if (!empty($options['sql_where'])){
				foreach ((array)$options['sql_where'] as $strWhereCond=>$mixParam){
					$objReferenceTableSelect->where($strWhereCond,$mixParam);
				}
				unset($options['sql_where']);
			}
			
			$objReferenceTableSelect->reset(Zend_Db_Select::COLUMNS);
			$objReferenceTableSelect->columns($arrColumns);
			
			$arrValues = $objReferenceTable->getAdapter()->fetchPairs($objReferenceTableSelect);
		
		//if (empty($arrValues)){
		//  $arrValues = array();
		//}
		

		} else {
			$arrValues = $mixValueData;
			
			if (! isset($options['index'])) {
				throw new Exception('When using Values an Index mast be set');
			}
			$index = $options['index'];
		
		}
		
		$name = $objGrid->getId() . '_' . $index;
		
		$arrMergedoptions = array_merge($options, array(
			'index' => $index
		));
		
		if (!empty($arrMergedoptions['editable'])){
			$arrMergedoptions['editable'] = FALSE;
		}
		
		$objParentColumn = new Ingot_JQuery_JqGrid_Column($name,$arrMergedoptions );
		$objParentDecorator = new Ingot_JQuery_JqGrid_Column_Decorator_Search_DoubleSelect($objParentColumn, array(
			'value' => $arrValues
		));
		$objGrid->addColumn($objParentDecorator);
		
		if (! isset($options['editable']) || ($options['editable'] === true)) {
			
			if (empty($options['label'])) {
				$options['label'] = $objParentColumn->label;
			}
			
			$options['hidden'] = true;
			
			$objEditParentColumn = new Ingot_JQuery_JqGrid_Column($index, $options);
			
			$arrEditOptions = array();
			if (!empty($options['editoptions'])){
				$arrEditOptions = $options['editoptions'];
			}
			$arrEditOptions['value'] = $arrValues;
			
			$objParentEditDecorator = new Ingot_JQuery_JqGrid_Column_Decorator_Edit_Select($objEditParentColumn, $arrEditOptions, array(
				'required' => $boolRequiered, 'edithidden' => true
			));
			$objGrid->addColumn($objParentEditDecorator);
		}
	
	}

}