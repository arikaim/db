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
     * @param float   $price
     * @param string|null $productName
     * @return Model
     */
    public function saveItem(int $documentId, int $productId, $qty, float $price, ?string $productName = null)
    {
        $item = $this->getItem($documentId,$productId);
        $data = [
            'document_id' => $documentId,
            'product_id'  => $productId,
            'title'       => $productName,
            'qty'         => $qty,
            'price'       => $price
        ];

        if (\is_object($item) == false) {
            return $this->create($data);
        }
        // add to existing 
        $data['qty'] += $qty;
        $data['price'] = $price;

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
