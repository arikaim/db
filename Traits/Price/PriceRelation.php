<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Core\Db\Traits\Price;

/**
 * Price relation table trait
*/
trait PriceRelation 
{  
    /**
     * Get price list class
     *
     * @return string|null
     */
    public function getPriceListClass(): ?string
    {
        return $this->priceListClass ?? null;
    }
    
    /**
     * Return true if item is free
     *
     * @return boolean
     */
    public function getIsFreeAttribute()
    {
        return $this->isFree();
    }

    /**
     * Return true if product is free
     *
     * @return boolean
     */
    public function isFree(): bool
    {
        foreach ($this->price as $item) {
            if ($item->price > 0) {
                return false;
            }
         }
 
         return true;
    }

    /**
     * Price list relation
     *
     * @return Relation|null
     */
    public function price()
    {
        return $this->hasMany($this->getPriceListClass(),'product_id');
    }

    /**
     * Get price
     *   
     * @param string|null $key
     * @param string|null $currency
     * 
     * @return object|null
     */
    public function getPrice(?string $key = null, ?string $currency = null): ?object 
    {                 
        if (\is_object($this->price) == false) {
            return null;
        }
        $curencyId = $this->findCurrency($currency)->id;

        $query = (empty($key) == false) ? $this->price()->where('key','=',$key) : $this->price()->whereNull('key');
        $query = $query->where('currency_id','=',$curencyId);

        return $query->frist();
    }

    /**
     * Get price value
     *
     * @param string|null $key
     * @param string|null $currency
     * 
     * @return float|null
     */
    public function getPriceValue(?string $key = null, ?string $currency = null): ?float 
    {
        $model = $this->getPrice($key,$currency);

        return ($model != null) ? (float)$model->price : null;
    }
}
