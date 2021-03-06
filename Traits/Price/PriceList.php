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
        if (\is_object($currency) == false) {
            return false;
        }
        $model = (empty($code) == false) ? $currency->findByColumn('code',$code) : $currency->getDefault();
        $model = $model->first();

        return (empty($model) == false) ? $model->id : null;       
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
    public function createPrice($productId, $key, ?string $currencyCode = null, $price = null)
    {
        $price = $price ?? 0;

        if ($this->hasPrice($key,$productId) == true) {     
            return false;
        }
        $currencyId = $this->getCurrency($currencyCode);

        return $this->create([
            'product_id'  => $productId,
            'currency_id' => $currencyId,
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
     * @param string $currency
     * @return boolean
     */
    public function createPiceList($productId, $typeName, $currency = null)
    {
        $optionsList = Model::create($this->getPriceListDefinitionClass());
        if (\is_object($optionsList) == false) {
            return false;
        }
      
        $list = $optionsList->getItems($typeName,'price');
      
        foreach ($list as $item) {                  
            $this->createPrice($productId,$item->key,$currency);          
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
    public function getPrice(string $key, $productId) 
    {      
        $model = $this->where('product_id','=',$productId)->where('key','=',$key);
  
        return $model->first();                    
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
     * @return Model|null
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
    public function hasPrice(string $key, $productId): bool
    {
        $model = $this->getPrice($key,$productId);

        return \is_object($model);
    }

    /**
     * Save price
     *
     * @param integer $referenceId
     * @param string $key
     * @param float $price
     * @param string|null $currency
     * @return boolean
     */
    public function savePrice($productId, $key, $price, $currency) 
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
