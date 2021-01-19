<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Core\Db;

/**
 * Search condition
*/
class SearchCondition 
{
    const AND    = 'and';
    const OR     = 'or';
    const NOT    = 'not';
    const IN     = 'in';
    const NOT_IN = 'not in';

    /**
     * Create condition array
     *
     * @param string $field
     * @param mixed $value
     * @param string $operator
     * @param string $queryOperator
     * @return array
     */
    public static function create($field, $searchFieldName, $operator = null, $queryOperator = null)
    {
        $operator = $operator ?? '=';
        $tokens = \explode(':',$operator);
        $operatorParams = null;   
        if (isset($tokens[1]) == true) {
            $operatorParams = $tokens[1];
            $operator = $tokens[0];
        } 

        return [
            'field'           => $field,
            'search_field'    => $searchFieldName,
            'operator'        => $operator,
            'operator_params' => $operatorParams,
            'query_operator'  => $queryOperator ?? 'and'
        ];
    } 

    /**
     * Parse search condition.
     *
     * @param array $condition
     * @param array $search
     * @return array
     */
    public static function parse($condition, $search)
    {
        $searchField = $condition['search_field'];
        $searchValue = $search[$searchField] ?? '';

        if (empty($condition['operator_params']) == false && $condition['operator'] == 'like') {
            $searchValue = \str_replace('{value}',$searchValue,$condition['operator_params']);
        }

        $condition['search_value'] = $searchValue;

        return $condition;
    }
}
