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

use Closure;

/**
 * Manage models with parent - child relations.
 *  Change parrent id column name in model:
 *       protected $parentColumn = 'column name';
*/
trait Tree 
{       
    /**
     * Get parent id attribute name default: parent_id
     *
     * @return string
     */
    public function getParentColumn(): string
    {
        return $this->parentColumn ?? 'parent_id';
    }
    
    /**
     * Parent model relation
     *
     * @return mixed
     */
    public function parent()
    {
        return $this->belongsTo(Self::class,$this->getParentColumn());
    }

    /**
     * Get root model
     * @param object|null $model
     * @return object
     */
    public function getRoot(?object $model = null): object  
    {
        $model = $model ?? $this;

        while ($model->parent != null) {
            $model = $model->parent;
        }

        return $model;
    }

    /**
     * Get model tree
     *
     * @param object|null $model
     * @return array
     */
    public function getModelPath(?object $model = null): array
    {
        $model = $model ?? $this;
        $result = [];
        \array_unshift($result,$model->toArray());
      
        while ($model != null) {
            $parentId = $model->attributes[$this->getParentColumn()];
            $model = parent::where('id','=',$parentId)->first();
            if ($model !== null) {
                \array_unshift($result,$model->toArray());
            }
        }

        return $result;
    }

    /**
     * Query for root items
     *
     * @param object $query
     * @return object
     */
    public function scopeRootQuery($query): object
    {      
        return $query->whereNull($this->getParentColumn());
    }

    /**
     * Gte model tree for current model
     *
     * @return array
     */
    public function getTreePath(): array
    {      
        return $this->getModelPath($this);
    }

    /**
     * Return true if model item have child items
     *
     * @param integer|string|null $id
     * @return boolean
     */
    public function hasChild($id = null): bool
    {
        $model = $this->findByColumn($id ?? $this->id,$this->getParentColumn());

        return ($model !== null) ? ($model->count() > 0) : false;          
    }

    /**
     * Childs list query
     *
     * @param object $query
     * @param integer $id
     * @return object
     */
    public function scopeChildListQuery($query, int $id): object
    {      
        return $query->where($this->getParentColumn(),'=',$id);
    }

    /**
     * Delete recursive
     *
     * @param string|integer $id
     * @param Closure|null $callback
     * @return bool
     */
    public function deleteChilds($id, ?Closure $callback = null): bool
    {
        if ($this->hasChild($id) == false) {
            $model = $this->findById($id);
            if ($model !== null) {
                if (is_callable($callback) == true) {
                    $callback($model->id);
                }
                $model->delete();
            }
            return true;
        }

        $model = $this->childListQuery($id)->get();
        foreach($model as $item) {
            $this->deleteChilds($item->id);              
        }

        return true;
    }
}
