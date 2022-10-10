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
     * Get document total
     *
     * @param Closure|null $itemCalc
     * @return float
     */
    public function getItemTotal(?Closure $itemCalc = null): float
    {
        $itemCalc = $itemCalc ?? $this->getItemCalc();
        $result = $itemCalc($this);    
        
        return (\is_numeric($result) == false) ? 0.00 : (float)$result;
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
     * Return true if item exist
     *
     * @param integer $documentId
     * @param integer $productId
     * @return boolean
     */
    public function hasItem(int $documentId, int $productId): bool
    {
        return ($this->itemsQuery($documentId,$productId)->first() !== null);        
    }

    /**
     * Get item
     *
     * @param integer $documentId
     * @param integer $productId
     * @return Model|null
     */
    public function getItem(int $documentId, int $productId)
    {
        return $this->itemsQuery($documentId,$productId)->first();
    } 

    /**
     * Save item
     *
     * @param integer $documentId
     * @param integer $productId
     * @param int  $qty
     * @param float|null   $price
     * @param string|null $productName
     * @return Model
     */
    public function saveItem(int $documentId, int $productId, $qty, ?float $price, ?string $productName = null): ?object
    {
        $item = $this->getItem($documentId,$productId);
        $data = [
            'document_id' => $documentId,
            'product_id'  => $productId,
            'title'       => $productName,
            'qty'         => $qty,
            'price'       => $price ?? 0.00
        ];

        if ($item == null) {
            return $this->create($data);
        }
        // add to existing 
        $data['qty'] = $item['qty'] + $qty;
        $data['price'] = (empty($price) == true) ? $item['price'] : $price;

        $item->update($data);

        return $item;
    }

    /**
     * Items query
     *
     * @param Builder $query
     * @param integer|null $documentId
     * @return Builder
     */
    public function scopeItemsQuery($query, ?int $documentId, ?int $productId = null)
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
