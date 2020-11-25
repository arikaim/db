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
 * Update Status field
 * Change default status column name in model:
 *      protected $statusColumn = 'column name';
*/
trait Status 
{        
    /**
     * Disabled
     */
    static $DISABLED = 0;

    /**
     * Active
     */
    static $ACTIVE = 1;
    
    /**
     * Completed
     */
    static $COMPLETED = 2;  

    /**
     * Published
     */
    static $PUBLISHED = 3;  

    /**
     * Pending activation
     */
    static $PENDING = 4;

    /**
     *  Suspended
     */
    static $SUSPENDED = 5;

    /**
     * Return active value
     *
     * @return integer
     */
    public function ACTIVE()
    {
        return Self::$ACTIVE;
    }

    /**
     * Return disabled value
     *
     * @return integer
     */
    public function DISABLED()
    {
        return Self::$DISABLED;
    }

    /**
     * Return completed value
     *
     * @return integer
     */
    public function COMPLETED()
    {
        return Self::$COMPLETED;
    }

    /**
     * Pending activation
     *
     * @return integer
     */
    public function PENDING()
    {
        return Self::$PENDING;
    }

    /**
     * Suspended
     *
     * @return integer
     */
    public function SUSPENDED()
    {
        return Self::$SUSPENDED;
    }

    /**
     * Status text
     *
     * @var array
     */
    protected $statusText = [
        'disabled',
        'active',
        'completed',
        'published',
        'pemding',
        'suspended'
    ];

    /**
     * Status scope
     *
     * @param Builder $query
     * @param mixed $items
     * @return Builder
     */
    public function scopeStatus($query, $items)
    {
        $column = $this->getStatusColumn();
        if (\is_array($items) == true) {       
            return $query->whereIn($column,$items);
        }

        return $query->where($column,'=',$items);
    }

    /**
     * Get status column name
     *
     * @return string
     */
    public function getStatusColumn()
    {
        return $this->statusColumn ?? 'status';
    }

    /**
     * Resolve status id
     *
     * @param string $status
     * @return integer|false
     */
    public function resolveStatusText($status) 
    {
        if (\is_numeric($status) == true) {
            return $status;
        }

        return \array_search($status,$this->statusText);
    } 

    /**
     * Return active model query builder
     *
     * @return Builder
     */
    public function getActive()
    {
        $model = $this->where($this->getStatusColumn(),'=',Self::$ACTIVE);
        if (\method_exists($model,'getNotDeletedQuery') == true) {
            $model = $model->getNotDeletedQuery();
        }
        
        return $model;        
    }
    
    /**
     * Active status scope
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeActiveQuery($query)
    {       
        return $query->where($this->getStatusColumn(),'=',Self::$ACTIVE);
    }

    /**
     * Return disabled model query builder
     *
     * @return void
     */
    public function getDisabled()
    {
        return $this->where($this->getStatusColumn(),'=',Self::$DISABLED);
    }

    /**
     * Set model status
     *
     * @param integer|string|null $status
     * @return bool
     */
    public function setStatus($status = null)
    {
        $columnName = $this->getStatusColumn();
        $this->$columnName = $this->resolveStatusValue($status);

        return $this->save();         
    }

    /**
     * Get status value
     *
     * @param integer|null|string $status
     * @return integer
     */
    public function resolveStatusValue($status = null)
    {
        $columnName = $this->getStatusColumn();
        if ($status === 'toggle') {     
            return ($this->$columnName == 1) ? 0 : 1;
        }

        return (empty($status) == true) ? 0 : $status;
    }
}
