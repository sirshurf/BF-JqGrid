<?php

namespace Bf\JqGrid\JqGrid\Adapter;

use Bf\JqGrid\JqGrid\Adapter\Sql\Predicate\NotContains;
use Bf\JqGrid\JqGrid\Adapter\Sql\Predicate\Contains;
use Bf\JqGrid\JqGrid\Adapter\Sql\Predicate\NotEndWith;
use Bf\JqGrid\JqGrid\Adapter\Sql\Predicate\NotBeginsWith;
use Bf\JqGrid\JqGrid\Adapter\Sql\Predicate\NotEndWithLike;
use Bf\JqGrid\JqGrid\Adapter\Sql\Predicate\EndsWith;
use Bf\JqGrid\JqGrid\Adapter\Sql\Predicate\BeginsWith;
use Zend\Db\Sql\Where;
use Zend\Paginator\Adapter\DbSelect;
use Bf\JqGrid\JqGrid;
use Bf\JqGrid\JqGrid\Adapter\Sql\Predicate;
use Zend\Paginator\Adapter;
use Zend\Db\Sql\Predicate\PredicateSet;

/**
 * JqGrid DbSelect Adapter
 *
 * @package Ingot_JQuery_JqGrid
 * @copyright Copyright (c) 2005-2009 Warrant Group Ltd.
 *            (http://www.warrant-group.com)
 * @author andy.roberts
 */
class AdapterDbSelect extends DbSelect implements AdapterInterface {
	protected $_operator = array (
			'EQUAL' => '= ?',
			'NOT_EQUAL' => '!= ?',
			'LESS_THAN' => '< ?',
			'LESS_THAN_OR_EQUAL' => '<= ?',
			'GREATER_THAN' => '> ?',
			'GREATER_THAN_OR_EQUAL' => '>= ?',
			'BEGIN_WITH' => 'LIKE ?',
			'NOT_BEGIN_WITH' => 'NOT LIKE ?',
			'END_WITH' => 'LIKE ?',
			'NOT_END_WITH' => 'NOT LIKE ?',
			'CONTAIN' => 'LIKE ?',
			'NOT_CONTAIN' => 'NOT LIKE ?',
			'IN' => 'IN (?)' 
	);
	
	/**
	 * Sort the result set by a specified column.
	 *
	 * @param Zend_Db_Expr $field
	 *        	Column name
	 * @param string $direction
	 *        	Ascending (ASC) or Descending (DESC)
	 * @return void
	 */
	public function sort($field, $direction) {
		if (isset ( $field )) {
			// Bypas becouse of the grouping
			$arrSortList = explode ( ",", $field );
			
			$arrSortList [count ( $arrSortList ) - 1] .= ' ' . $direction;
			
			$this->select->order ( $arrSortList );
		}
	}
	
	/**
	 * Filter the result set based on criteria.
	 *
	 * @param string $field
	 *        	Column name
	 * @param string $value
	 *        	Value to filter result set
	 * @param string $operation
	 *        	Search operator
	 */
	public function filter($field, $value, $expression, $options = array(), $useHaving = array()) {
		// $strKey = $expression[0];
		// $boolTest = array_key_exists(array($strKey,'NOT_CONTAIN'),
		// $this->_operator);
		// if (! array_key_exists($expression, $this->_operator)) {
		// return;
		// }
		if (isset ( $options ['multiple'] )) {
			return $this->_multiFilter ( array (
					
					'field' => $field,
					'value' => $value,
					'expression' => $expression,
					'useHaving' => $useHaving 
			), $options );
		}
		
		return $this->select->where ( $field . ' ' . $this->_operator [$expression], $this->_setWildCardInValue ( $expression, $value ) );
	}
	
	/**
	 * Multiple filtering
	 *
	 * @return
	 *
	 */
	protected function _multiFilter($rules, $options = array()) {
		$boolean = strtoupper ( $options ['boolean'] );
		
		foreach ( $rules ['field'] as $key => $rule ) {
			// Check that the field is not EMPTY and is NOT NUMERIc (becouse
			// then 0 is a valid data)
			if (empty ( $rules ['value'] [$key] ) && ! is_numeric ( $rules ['value'] [$key] )) {
				continue;
			}
			if (! empty ( $rules ['useHaving'] [$key] )) {
				if ($boolean == 'OR') {
					$this->select->orHaving ( $rule . ' ' . $this->_operator [$rules ['expression'] [$key]], $this->_setWildCardInValue ( $rules ['expression'] [$key], $rules ['value'] [$key] ) );
				} else {
					$this->select->having ( $rule . ' ' . $this->_operator [$rules ['expression'] [$key]], $this->_setWildCardInValue ( $rules ['expression'] [$key], $rules ['value'] [$key] ) );
				}
			} else {
				if ($boolean == 'OR') {
					$this->select->where ( $this->where ( $rules ['expression'] [$key],$rule, $rules ['value'] [$key] ), PredicateSet::OP_OR );
				} else {
					$this->select->where ( $this->where ( $rules ['expression'] [$key],$rule, $rules ['value'] [$key] ) );
					
				}
			}
		}
// 		var_dump($this->select->getSqlString());
	}
	protected function where($rule, $key, $val) {
		$where = new Where ();
		
		switch (strtoupper ( $rule )) {
			case 'BEGIN_WITH' :
				$where->addPredicate ( new BeginsWith ( $key, $val ) );
				break;
			case 'NOT_BEGIN_WITH' :
				$where->addPredicate ( new NotBeginsWith ( $key, $val ) );
				break;
			case 'END_WITH' :
				$where->addPredicate ( new EndsWith ( $key, $val ) );
				break;
			case 'NOT_END_WITH' :
				$where->addPredicate ( new NotEndWith ( $key, $val ) );
				break;
			case 'CONTAIN' :
				$where->addPredicate ( new Contains ( $key, $val ) );
				break;
			case 'NOT_CONTAIN' :
				$where->addPredicate ( new NotContains ( $key, $val ) );
				break;
			case 'LESS_THAN' :
				$where->lessThan ( $key, $val );
				break;
			case 'LESS_THAN_OR_EQUAL' :
				$where->lessThanOrEqualTo ( $key, $val );
				break;
			case 'GREATER_THAN' :
				$where->greaterThan ( $key, $val );
				break;
			case 'GREATER_THAN_OR_EQUAL' :
				$where->greaterThanOrEqualTo ( $key, $val );
				break;
			case 'IN' :
				$where->in ( $key, $val );
				break;
			case 'NOT_IN' :
// 				$where->addPredicate ( new NotContains ( $key, $val ) );
				throw new \Exception("Error");
				break;
			default:
				throw new \Exception("Error Default");
		}
		
		return $where;
	}
	
	public function getSelect(){
		return $this->select;
	}
}
