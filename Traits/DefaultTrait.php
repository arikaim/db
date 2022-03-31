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
 * Default column trait
*/
trait DefaultTrait 
{        
    /**
     * Get default column name
     *
     * @return string
     */
    public function getDefaultColumnName(): string
    {
        return $this->defaultColumnName ?? 'default';
    }

    /**
     * Mutator (get) for default attribute.
     *
     * @return bool
     */
    public function getDefaultAttribute()
    {       
        $column = $this->getDefaultColumnName();

        return ($this->attributes[$column] == 1);
    }

    /**
     * Set model as default
     *
     * @param integer|string|null $id
     * @param integer|null $userId
     * @return bool
     */
    public function setDefault($id = null, ?int $userId = null): bool
    {
        $column = $this->getDefaultColumnName();
        $id = $id ?? $this->id;

        $models = (empty($userId) == false) ? $this->where('user_id','=',$userId) : $this->where('id','<>',$id);
        $models->update([$column => null]);
              
        $model = $this->findById($id);      
        $model->$column = 1;
       
        return (bool)$model->save();               
    }

    /**
     * Get default model
     *
     * @param integer|null $userId
     * @return Model|null
     */
    public function getDefault(?int $userId = null)
    {      
        return $this->defaultQuery($userId)->first();
    }

    /**
     * Default scope
     *
     * @param Builder $query
     * @param integer|null $userId
     * @return Builder
     */
    public function scopeDefaultQuery($query, ?int $userId = null)
    {
        $column = $this->getDefaultColumnName();
        $query = (empty($userId) == false) ? $query->where('user_id','=',$userId) : $query;
        $query = $query->where($column,'=','1');

        return $query;
    }

    /**
     * Return true if default value is set 
     *
     * @param integer|null $userId
     * @return boolean
     */
    public function hasDefault(?int $userId = null): bool
    {
        return \is_object($this->getDefault($userId));
    }
}
