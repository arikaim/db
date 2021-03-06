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

use Arikaim\Core\Db\Model;

/**
 * Price relation table trait
*/
trait PriceRelation 
{  
    /**
     * Boot trait
     *
     * @return void
     */
    public static function bootPriceRelation()
    {
        static::created(function($model) {          
            $model->createPriceList();
        });
    }

    /**
    * Price list
    *
    * @return boolean
    */
    public function createPriceList()
    {
        $priceList = Model::create($this->getPriceListClass());
        $typeName = $this->getOptionsType();
        
        if (\is_object($priceList) == true && empty($typeName) == false) {               
            return $priceList->createPiceList($this->id,$typeName);
        }

        return false;
    }

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
     * Create price_list attribute used for better collection serialization key => value 
     *
     * @return Collection
     */
    public function getPriceListAttribute()
    {
        $options = $this->price()->get()->keyBy('key')->map(function ($item, $key) {
            return $item['price'];
        });

        return $options;
    }

    /**
     * Return true if item is free
     *
     * @return boolean
     */
    public function getIsFreeAttribute()
    {
        return $this->IsFree();
    }

    /**
     * Return true if product is free
     *
     * @return boolean
     */
    public function IsFree(): bool
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
     * @param string $key
     * @return Model|null
     */
    public function getPrice($key) 
    {                 
        if (\is_object($this->price) == false) {
            return null;
        }
        $items = $this->price->keyBy('key');

        return (\is_object($items) == true) ? $items->get($key) : null;
    }

    /**
     * Get price value
     *
     * @param string $key
     * @return float|null
     */
    public function getPriceValue($key) 
    {
        $model = $this->getPrice($key);

        return (\is_object($model) == true) ? $model->price : null;
    }
}
