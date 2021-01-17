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

/**
 * Document table trait
*/
trait Document 
{ 
    /**
     * Get document model class
     *
     * @return string|null
     */
    public function getDocumentItemsClass(): ?string
    {
        return $this->documentItemsModel ?? null;
    }

    /**
     * Get external document 
     *
     * @param string $externalId
     * @param string $driverName
     * @return Model|null
     */
    public function getExternal(string $externalId, string $driverName)
    {
        return $this->where('external_id','=',$externalId)->where('api_driver','=',$driverName)->first();
    }

    /**
     * Return true if external document exists
     *
     * @param string $externalId
     * @param string $driverName
     * @return boolean
     */
    public function hasExternal(string $externalId, string $driverName): bool
    {
        $model = $this->getExternal($externalId,$driverName);

        return \is_object($model);
    }
    
    /**
     * Document items relation
     *
     * @return Relation|null
     */
    public function items()
    {
        $class = $this->getDocumentItemsClass();
        if (empty($class) == true) {
            return null;
        }

        return $this->hasMany($class,'document_id');
    }

    /**
     * Items array
     *
     * @return array
     */
    public function itemsToArray(): array
    {
        $items = $this->items();

        return (empty($items) == true) ? [] : $items->get()->toArray();
    } 

    /**
     * Get document total
     *
     * @return float
     */
    public function getTotal(): float
    {
        $items = $this->items()->get();
        $total = 0.00;

        foreach($items as $item) {              
            $total += $item->getItemTotal();
        }

        return $total; 
    }

    /**
     * total attribute
     *
     * @return float
     */
    public function getTotalAttribute()
    {
        return $this->getTotal();
    }
}
