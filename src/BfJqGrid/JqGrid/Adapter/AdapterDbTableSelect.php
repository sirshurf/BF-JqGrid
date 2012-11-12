<?php

namespace BfJqGrid\JqGrid\Adapter;

use Zend\Db\Sql\Where;
use Zend\InputFilter\InputFilter;
use Zend\Db\Metadata\Metadata;
use Zend\Json\Encoder;
use BfJqGrid\JqGrid\JqGrid;
use Zend\Db\RowGateway\RowGateway;
use Zend\Db\Sql\Sql;
use Zend\Http\Request;

/**
 * JqGrid DbTableSelect Adapter
 *
 * @package Ingot_JQuery_JqGrid
 * @copyright Copyright (c) 2005-2009 Warrant Group Ltd.
 *            (http://www.warrant-group.com)
 * @author Andy Roberts
 */
class AdapterDbTableSelect extends AdapterDbSelect {
	public function getSelect() {
		return $this->select;
	}
	protected function filterDatabaseColumns(JqGrid $objGrid) {
		$objMetaData = new Metadata ( $objGrid->getTableGatevay ()->getAdapter () );
		$objMetaTable = $objMetaData->getTable ( $objGrid->getTableGatevay ()->getTable () );
		$arrColumns = $objMetaTable->getColumns ();
		
		$arrFilteredFata = array ();
		$arrUnfirteredData = $objGrid->getRequest ()->getPost ()->toArray ();
		
		foreach ( $arrColumns as $arrColumn ) {
			
			if (isset ( $arrUnfirteredData [$arrColumn->getName ()] )) {
				$arrFilteredFata [$arrColumn->getName ()] = $arrUnfirteredData [$arrColumn->getName ()];
			}
		}
		return $arrFilteredFata;
	}
	public function gridSave(JqGrid $objGrid) {
		$strDbTable = $this->sql->getTable ();
		$objRequest = $objGrid->getRequest ();
		
		$boolError = false;
		
		if ($objRequest->isPost ()) {
			switch ($objRequest->getPost ( "oper" )) {
				case "add" :
					
					$result = $objGrid->getTableGatevay ()->insert ( $this->filterDatabaseColumns ( $objGrid ) );
					
					if (! empty ( $result )) {
						$arrData = array (
								"code" => "ok",
								"msg" => "" 
						);
					} else {
						$arrData = array (
								"code" => "error",
								"msg" => "LBL_UPDATE_FAIL" 
						);
						$boolError = true;
					}
					
					break;
				case "del" :
					
					$idValue = $objRequest->getPost ( 'id' );
					
					if (! empty ( $idValue )) {
						$where = new Where ();
						$where->equalTo ( $objGrid->getIdCol (), $idValue );
						
						$result = $objGrid->getTableGatevay ()->delete ( $where );
						if (! empty ( $result )) {
							$arrData = array (
									"code" => "ok",
									"msg" => "" 
							);
						} else {
							$arrData = array (
									"code" => "error",
									"msg" => "LBL_UPDATE_FAIL" 
							);
							$boolError = true;
						}
					} else {
						$arrData = array (
								"code" => "error",
								"msg" => "LBL_UPDATE_FAIL" 
						);
						$boolError = true;
					}
					break;
				default :
					// Most probably this is edit...
					$idValue = $objRequest->getPost ( 'id' );
					
					if (! empty ( $idValue )) {
						
						$where = new Where ();
						$where->equalTo ( $objGrid->getIdCol (), $idValue );
						
						$result = $objGrid->getTableGatevay ()->update ( $this->filterDatabaseColumns ( $objGrid ), $where );
						
						if (! empty ( $result )) {
							$arrData = array (
									"code" => "ok",
									"msg" => "" 
							);
						} else {
							$arrData = array (
									"code" => "error",
									"msg" => "LBL_UPDATE_FAIL" 
							);
							$boolError = true;
						}
					} else {
						$arrData = array (
								"code" => "error",
								"msg" => "LBL_UPDATE_FAIL" 
						);
						$boolError = true;
					}
			}
		} else {
			// Error No POST
			$arrData = array (
					"code" => "error",
					"msg" => "LBL_UPDATE_FAIL" 
			);
			$boolError = true;
		}
		
		return $arrData;
		
		//
		$intId = ( int ) $objRequest->getPost ( 'id' );
		
		if (! empty ( $intId )) {
			$objRows = $objDbTable->find ( $intId );
			if (! empty ( $objRows )) {
				$objRow = $objDbTable->find ( $intId )->current ();
			} else {
				$objRow = array ();
			}
		} else {
			if ("add" == $objRequest->getPost ( "oper" )) {
				
				$objRow = new RowGateway ( $objGrid->getIdCol (), $strDbTable, $this->sql );
				$rowExistsInDatabase = false;
				// $objRow = $objDbTable->createRow ();
			} else {
				$arrData = array (
						"code" => "error",
						"msg" => $this->view->translate ( "LBL_ERROR_UNAUTHORIZED" ) 
				);
				$boolError = true;
			}
		}
		
		if (empty ( $objRow )) {
			$arrData = array (
					"code" => "error",
					"msg" => $this->view->translate ( "LBL_ERROR_UNAUTHORIZED" ) 
			);
			$boolError = true;
		}
		
		if (! $boolError) {
			if ("del" == $objRequest->getPost ( "oper" )) {
				if ($objRow->delete ()) {
					// Deleted
					$arrData = array (
							"code" => "ok",
							"msg" => "" 
					);
				} else {
					// Delete failed
					$arrData = array (
							"code" => "error",
							"msg" => $this->view->translate ( "LBL_DEL_FAIL" ) 
					);
					$boolError = true;
				}
			} else {
				if ($objRequest->isPost ()) {
					$arrData = $objRequest->getPost ()->toArray ();
					$objRow->populate ( $arrData, $rowExistsInDatabase );
					
					$affectedRows = $objRow->save ();
					
					if (! empty ( $affectedRows )) {
						$arrData = array (
								"code" => "ok",
								"msg" => "" 
						);
					} else {
						$arrData = array (
								"code" => "error",
								"msg" => "LBL_UPDATE_FAIL" 
						);
						$boolError = true;
					}
				} else {
					$arrData = array (
							"code" => "error",
							"msg" => "LBL_UPDATE_FAIL" 
					);
					$boolError = true;
				}
			}
		}
	}
}