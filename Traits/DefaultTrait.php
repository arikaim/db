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
    public function getDefaultColumnName()
    {
        return (isset($this->defaultColumnName) == true) ? $this->defaultColumnName : 'default';
    }

    /**
     * Mutator (get) for default attribute.
     *
     * @return array
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
     * @param integer $userId
     * @return bool
     */
    public function setDefault($id = null, $userId = null)
    {
        $column = $this->getDefaultColumnName();
        $id = (empty($id) == true) ? $this->id : $id;

        $models = (empty($userId) == false) ? $this->where('user_id','=',$userId) : $this->where('id','<>',$id);
        $models->update([$column => null]);
              
        $model = $this->findById($id);      
        $model->$column = 1;
       
        return $model->save();               
    }

    /**
     * Get default model
     *
     * @param integer $userId
     * @return Model|null
     */
    public function getDefault($userId = null)
    {
        $column = $this->getDefaultColumnName();

        $model = (empty($userId) == false) ? $this->where('user_id','=',$userId) : $this;
        $model = $model->where($column,'=','1')->first();

        return (\is_object($model) == true) ? $model : null; 
    }

    /**
     * Return true if default value is set 
     *
     * @param integer $userId
     * @return boolean
     */
    public function hasDefault($userId = null)
    {
        return \is_object($this->getDefault($userId));
    }
}
