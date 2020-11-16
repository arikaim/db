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

use Arikaim\Core\Utils\DateTime;

/**
 * Soft delete trait
 *      
*/
trait SoftDelete 
{    
    /**
     * Return true if model is deleted
     *
     * @return boolean
     */
    public function isDeleted()
    {
        return (\is_null($this->{$this->getDeletedColumn()}) == false);
    }

    /**
     * Get delete models count.
     *
     * @return integer
     */
    public function getDeletedCount()
    {
        $query = $this->softDeletedQuery();
        
        return $query->count();
    }

    /**
     * Soft delete model
     *
     * @param integer string $id
     * @return boolean
     */
    public function softDelete($id = null)
    {
        $model = (empty($id) == true) ? $this : $this->findById($id);
        $columnName = $model->getDeletedColumn();

        return $model->update([
            $columnName => DateTime::getTimestamp()
        ]);
    }

    /**
     * Restore soft deleted models
     *
     * @param integer|string|null string $id
     * @return boolean
     */
    public function restore($id = null)
    {
        $model = (empty($id) == true) ? $this : $this->findById($id);
        $columnName = $model->getDeletedColumn();
        $model->{$columnName} = null;
        
        return $model->save();
    }

    /**
     * Restore all soft deleted rows
     *
     * @return boolean
     */
    public function restoreAll()
    {
        $columName = $this->getDeletedColumn();
        $query = $this->softDeletedQuery();
        
        return $query->update([
            $columName => null
        ]);
    }

    /**
     * Get soft deleted query
     *
     * @return QueryBuilder
     */
    public function softDeletedQuery()
    {
        return $this->whereNotNull($this->getDeletedColumn());
    }

    /**
     * Permanently delete all soft deleted models
     *
     * @return boolean
     */
    public function clearDeleted()
    {
        return $this->softDeletedQuery()->delete();
    }

    /**
     * Get not deleted query
     *
     * @return QueryBuilder
     */
    public function getNotDeletedQuery()
    {
        return $this->whereNull($this->getDeletedColumn());
    }

    /**
     * Get not deleted scope
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeGetNotDeleted($query)
    {
        return $query->whereNull($this->getDeletedColumn());
    }

    /**
     * Get deleted scope
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeGetDeleted($query)
    {
        return $query->whereNotNull($this->getDeletedColumn());
    }

    /**
     * Get uuid attribute name
     *
     * @return string
     */
    public function getDeletedColumn()
    {
        return $this->softDeleteColumn ?? 'date_deleted';
    } 
}
