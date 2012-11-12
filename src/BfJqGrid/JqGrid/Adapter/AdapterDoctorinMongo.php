<?php

namespace BfJqGrid\JqGrid\Adapter;

use BfJqGrid\JqGrid;
use ZendPaginatorAdapter\DoctrineMongoDBAdapter;
use DoctrineModule\Paginator\Adapter as DoctrineCollectionAdapter;

/**
 * JqGrid Array Adapter
 *
 * @package Ingot_JQuery_JqGrid
 * @copyright Copyright (c) 2005-2009 Warrant Group Ltd.
 *            (http://www.warrant-group.com)
 * @author Andy Roberts
 */
class AdapterDoctorinMongo extends DoctrineMongoDBAdapter implements AdapterInterface {
	
	/**
	 * Sort Array
	 *
	 * @param Zend_Db_Expr $field        	
	 * @param string $direction        	
	 */
	public function sort($field, $direction) {
		$this->queryBuilder->sort ( $field, $direction );
	}
	
	/**
	 * Filter Array
	 *
	 * @param string $field        	
	 * @param string $value        	
	 * @param string $operator        	
	 */
	public function filter($field, $value, $expression, $options = array()) {
		if (isset ( $options ['multiple'] )) {
			return $this->_multiFilter ( array (
					
					'field' => $field,
					'value' => $value,
					'expression' => $expression 
			), $options );
		}
		
		return $this->_singleFilter ( $field, $value, $expression );
	}
	
	/**
	 * Multiple Field Filter
	 *
	 * @param
	 *        	$rules
	 * @param
	 *        	$options
	 */
	private function _multiFilter($rules, $options = array()) {
		foreach ( $rules ['field'] as $key => $rule ) {
			// Check that the field is not EMPTY and is NOT NUMERIc (becouse
			// then 0 is a valid data)
			if (empty ( $rules ['value'] [$key] ) && ! is_numeric ( $rules ['value'] [$key] )) {
				continue;
			}
			
			switch ($rules ['expression'] [$key]) {
				
				case 'EQUAL' :
					$this->queryBuilder->field ( $rules ['field'] [$key] )->equals ( $rules ['value'] [$key] );
					break;
				case 'NOT_EQUAL' :
					$this->queryBuilder->field ( $rules ['field'] [$key] )->notEqual ( $rules ['value'] [$key] );
					break;
				case 'LESS_THAN' :
					$this->queryBuilder->field ( $rules ['field'] [$key] )->lt ( $rules ['value'] [$key] );
					break;
				case 'LESS_THAN_OR_EQUAL' :
					$this->queryBuilder->field ( $rules ['field'] [$key] )->lte ( $rules ['value'] [$key] );
					break;
				case 'GREATER_THAN' :
					$this->queryBuilder->field ( $rules ['field'] [$key] )->gt ( $rules ['value'] [$key] );
					break;
				case 'GREATER_THAN_OR_EQUAL' :
					$this->queryBuilder->field ( $rules ['field'] [$key] )->gte ( $rules ['value'] [$key] );
					break;
				case 'IN' :
					$this->queryBuilder->field ( $rules ['field'] [$key] )->in ( $rules ['value'] [$key] );
					break;
				case 'NOT_IN' :
					$this->queryBuilder->field ( $rules ['field'] [$key] )->notIn ( $rules ['value'] [$key] );
					break;				
				case 'BEGIN_WITH' :
					$objMongoRegex = new \MongoRegex ( '/' . $rules ['value'] [$key] . ".*/iu" );
					$this->queryBuilder->field ( $rules ['field'] [$key] )->equals ( $objMongoRegex );
					break;				
				case 'NOT_BEGIN_WITH' :
					$objMongoRegex = new \MongoRegex ( '/' . $rules ['value'] [$key] . ".*/iu" );
					$this->queryBuilder->field ( $rules ['field'] [$key] )->notEqual ( $objMongoRegex );
					break;				
				case 'END_WITH' :
					$objMongoRegex = new \MongoRegex ( '/.*' . $rules ['value'] [$key] . "/iu" );
					$this->queryBuilder->field ( $rules ['field'] [$key] )->equals ( $objMongoRegex );
					break;				
				case 'NOT_END_WITH' :
					$objMongoRegex = new \MongoRegex ( '/.*' . $rules ['value'] [$key] . "/iu" );
					$this->queryBuilder->field ( $rules ['field'] [$key] )->notEqual ( $objMongoRegex );
					break;				
				case 'NOT_CONTAIN' :
					$objMongoRegex = new \MongoRegex ( '/.*' . $rules ['value'] [$key] . ".*/iu" );
					$this->queryBuilder->field ( $rules ['field'] [$key] )->notEqual ( $objMongoRegex );
					break;				
				default :
				case 'CONTAIN' :
					$objMongoRegex = new \MongoRegex ( '/.*' . $rules ['value'] [$key] . ".*/iu" );
					$this->queryBuilder->field ( $rules ['field'] [$key] )->equals ( $objMongoRegex );
			}
		}
	}
	
	/**
	 * Single Field Filter
	 *
	 * @param
	 *        	$field
	 * @param
	 *        	$value
	 * @param
	 *        	$operator
	 */
	private function _singleFilter($field, $value, $expression) {
		foreach ( $this->_array as $row => $item ) {
			if ($this->_compare ( strtolower ( $item [$field] ), strtolower ( $value ), $expression ) != true) {
				unset ( $this->_array [$row] );
			}
		}
		
		$this->_count = count ( $this->_array );
	}
	
	/**
	 * Compare two values
	 *
	 * @param mixed $a        	
	 * @param mixed $b        	
	 * @param string $expression        	
	 * @return boolean
	 */
	private function _compare($a, $b, $expression) {
		switch ($expression) {
			case "BEGIN_WITH" :
			case "NOT_BEGIN_WITH" :
				$bool = strpos ( $a, $b ) === 0;
				return ($expression == 'BEGIN_WITH') ? $bool : ! $bool;
				break;
			
			case "IN" :
			case "NOT_IN" :
				$bool = in_array ( $a, ( array ) $b );
				return ($expression == 'IN') ? $bool : ! $bool;
			
			case "END_WITH" :
			case "NOT_END_WITH" :
				$bool = substr_compare ( $a, $b, strlen ( $a ) - strlen ( $b ), strlen ( $b ) ) == 0;
				return ($expression == 'END_WITH') ? $bool : ! $bool;
				break;
			
			case "CONTAIN" :
			case "NOT_CONTAIN" :
				$bool = strpos ( $a, $b ) > - 1;
				return ($expression == 'CONTAIN') ? $bool : ! $bool;
				break;
			
			case "EQUAL" :
				return $a === $b;
				break;
			
			case "NOT_EQUAL" :
				return $a !== $b;
				break;
			
			case "LESS_THAN" :
				return $a < $b;
				break;
			
			case "LESS_THAN_OR_EQUAL" :
				return $a <= $b;
				break;
			
			case "GREATHER_THAN" :
				return $a > $b;
				break;
			
			case "GREATER_THAN_OR_EQUAL" :
				return $a >= $b;
				break;
			
			default :
				return true;
				break;
		}
	}
}