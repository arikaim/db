<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Core\Db\Traits\Document;

use Closure;

/**
 * Document items table trait
*/
trait DocumentItems 
{ 
    /**
     * Get item calc closure
     *
     * @return Closure
     */
    public function getItemCalc()
    {
        if (empty($this->calcItemTotal) == false) {
            return $this->calcItemTotal;
        }

        return function($item) {
            return (float)($item->price * $item->qty);
        };
    }

    /**
     * Get documetn total
     *
     * @param Closure|null $itemCalc
     * @return float
     */
    public function getItemTotal(?Closure $itemCalc = null): float
    {
        $itemCalc = $itemCalc ?? $this->getItemCalc();

        return (float)$itemCalc($this);       
    }

    /**
     * item_total attribute
     *
     * @return float
     */
    public function getItemTotalAttribute()
    {
        return $this->getItemTotal();
    }

    /**
     * Return treu if item exist
     *
     * @param integer $documentId
     * @param integer $productId
     * @return boolean
     */
    public function hasItem(int $documentId, int $productId): bool
    {
        $model = $this->itemsQuery($documentId,$productId)->frist();

        return \is_object($model);
    }

    /**
     * Items query
     *
     * @param Builder $query
     * @param integer|null $documentId
     * @return Builder
     */
    public function scopeItemsQuery($query, ?int $documentId, ?int $productId)
    {
        if (empty($documentId) == false) {
            $query = $query->where('document_id','=',$documentId);
        }
        
        if (empty($productId) == false) {
            $query = $query->where('product_id','=',$productId);
        }

        return $query;
    }
}
