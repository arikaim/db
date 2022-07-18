<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Core\Db\Traits;

/**
 * Find model
*/
trait Find 
{    
    /**
     * Find model by id or uuid
     *
     * @param integer|string $id
     * @return Model|null
     */
    public function findById($id)
    {        
        $column = (\is_numeric($id) == true) ? (string)$this->getKeyName() : 'uuid';
        
        return parent::where($column,'=',$id)->first();
    }
    
    /**
     * Find multiole query
     *
     * @param array $idList
     * @return Builder
     */
    public function findMultiple(array $idList)
    {
        return $this->whereIn('uuid',$idList)->orWhereIn('id',$idList);
    }

    /**
     * Get last row
     *
     * @param string $field
     * @return Model|null
     */
    public function getLastRow(string $field = 'id')
    {
        return $this->latest($field)->first();
    }

    /**
     * Get last id
     *
     * @return integer|null
     */
    public function getLastId(): ?int
    {
        $model = $this->getLastRow();

        return ($model != null) ? $model->id : null;
    }

    /**
     * Find model by column name
     *
     * @param mixed $value
     * @param string|null|array $column
     * @return Model|null
     */
    public function findByColumn($value, $column = null)
    {
        return $this->findQuery($value,$column)->first(); 
    }

    /**
     * Return query builder
     *
     * @param mixed $value
     * @param string|null|array $column
     * @return Builder
     */
    public function findQuery($value, $column = null)
    {      
        if ($column == null) {
            return $this->findByIdQuery($value);
        }

        if (\is_string($column) == true) {
            return parent::where($column,'=',$value);
        }

        if (\is_array($column) == true) {
            $model = $this;
            foreach ($column as $item) {
               $model = $model->orWhere($item,'=',$value);
            }
            return $model;
        }

        return $this->findByIdQuery($value);
    }

    /**
     *  Return query builder
     *
     * @param integer|string $id
     * @return Builder
     */
    public function findByIdQuery($id)
    {       
        return parent::where($this->getIdAttributeName($id),'=',$id);
    }

    /**
     * Return id column name dependiv of id value type for string return uuid
     *
     * @param integer|string $id
     * @return string
     */
    public function getIdAttributeName($id): string
    {
        $uuidAttribute = (\method_exists($this,'getUuidAttributeName') == true) ? $this->getUuidAttributeName() : 'uuid';

        return (\is_numeric($id) == true) ? (string)$this->getKeyName() : $uuidAttribute;
    }

    /**
     * Find collection of models by id or uuid
     *
     * @param array|null $items
     * @return QueryBuilder|false
     */
    public function findItems(?array $items) 
    {
        return (empty($items) == true) ? false : parent::whereIn($this->getIdAttributeName($items[0]),$items);      
    }

    /**
     * Where case insensitive
     *
     * @param string $attribute
     * @param mixed $value
     * @param string $operator
     * @return Builder
     */
    public function whereIgnoreCase(string $attribute, $value, string $operator = '=')
    {
        $value = \strtolower($value);
        
        return $this->whereRaw('LOWER(' . $attribute .') ' . $operator . ' ?',[$value]);
    }
}
