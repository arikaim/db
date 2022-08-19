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
use Arikaim\Core\Utils\Uuid;

/**
 * Price list table trait
*/
trait PriceList 
{  
    /**
     * Currency relation
     *
     * @return Relatioin|null
     */
    public function currency()
    {
        return $this->belongsTo($this->getCurrencyClass(),'currency_id');
    } 

    /**
     * Get currency id
     *
     * @param string|null $code
     * @return integer|null
     */
    public function getCurrency(?string $code = null): ?int
    {
        $currency = Model::create($this->getCurrencyClass());
        if ($currency == null) {
            return null;
        }
        $model = (empty($code) == false) ? $currency->findByColumn('code',$code) : $currency->getDefault();
        $model = $model->first();

        return ($model !== null) ? $model->id : null;       
    }

    /**
     * Get currency model class
     *
     * @return string|null
     */
    public function getCurrencyClass(): ?string
    {
        return $this->currencyClass ?? null;
    }

    /**
     * Get price type model class
     *
     * @return string|null
     */
    public function getPriceTypeClass(): ?string
    {
        return $this->priceTypeClass ?? null;
    }
    
    /**
     * Get price list definition model class
     *
     * @return string|null
     */
    public function getPriceListDefinitionClass(): ?string
    {
        return $this->priceListDefinitionClass ?? null;
    }

    /**
     * Get price type
     *
     * @param string|null $key
     * @return Model|null
     */
    public function getType(?string $key = null)
    {       
        $priceType = Model::create($this->getPriceTypeClass());
        if (empty($priceType) == true) {
            return false;
        }
        $key = $key ?? $this->key;

        return $priceType->where('key','=',$key)->first();
    }

    /**
     * Create price
     *
     * @param integer $productId
     * @param string $key
     * @param string|null $currencyCode
     * @param mixed $price
     * @return Model|false
     */
    public function createPrice(int $productId, $key, ?string $currencyCode = null, $price = null)
    {
        $price = $price ?? 0;
        if ($this->hasPrice($key,$productId) == true) {     
            return false;
        }
       
        return $this->create([
            'product_id'  => $productId,
            'currency_id' => $this->getCurrency($currencyCode),
            'uuid'        => Uuid::create(),          
            'key'         => $key,
            'price'       => $price     
        ]);      
    }

    /**
     * Create price list
     *
     * @param integer $productId
     * @param string|null $typeName
     * @param string|null $currencyCode
     * @return boolean
     */
    public function createPiceList(int $productId, ?string $typeName, ?string $currencyCode = null): bool
    {
        $optionsList = Model::create($this->getPriceListDefinitionClass());
        if ($optionsList == null) {
            return false;
        }
      
        $list = $optionsList->getItems($typeName,'price');
      
        foreach ($list as $item) {                  
            $this->createPrice($productId,$item->key,$currencyCode);          
        }

        return true;
    }

    /**
     * Get price
     *
     * @param integer $productId
     * @param string $key
     * @return Model|null
     */
    public function getPrice(string $key, int $productId): ?object
    {      
        return $this->where('product_id','=',$productId)->where('key','=',$key)->first();                          
    }

    /**
     * Get price list query
     *
     * @param integer $productId
     * @return QueryBuilder
     */
    public function getPriceListQuery($productId)
    {
        return $this->where('product_id','=',$productId);
    }

    /**
     * Get price list
     *
     * @param integer $productId
     * @return object|null
     */
    public function getPriceList($productId)
    {
        return $this->getPriceListQuery($productId)->get();
    }

    /**
     * Return true if price exist
     *
     * @param integer $productId
     * @param string $key
     * @return boolean
     */
    public function hasPrice(string $key, int $productId): bool
    {
        return ($this->getPrice($key,$productId) !== null);       
    }

    /**
     * Save price
     *
     * @param integer $productId
     * @param string $key
     * @param float $price
     * @param string|null $currency
     * @return boolean
     */
    public function savePrice(int $productId, string $key, $price, ?string $currency) 
    {
        $price = (empty($price) == true) ? 0 : $price;

        if ($this->hasPrice($key,$productId) == false) {          
            return $this->createPrice($productId,$key,$currency,$price);
        }
        $model = $this->where('product_id','=',$productId)->where('key','=',$key);

        return $model->update(['price' => $price]);  
    }

    /**
     * Save price list
     *
     * @param integer $productId
     * @param string|null $currency
     * @param array $data
     * @return boolean
     */
    public function savePriceList($productId, array $data, $currency = null)
    {
        $errors = 0;

        foreach ($data as $key => $price) {
            $result = $this->savePrice($productId,$key,$price,$currency);
            $errors += ($result !== false) ? 0 : 1; 
        }      
        
        return ($errors == 0);
    }
}
