<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Core\Db\Traits\Options;

use Arikaim\Core\Db\Model;
use Arikaim\Core\Utils\Uuid;

/**
 * Options table trait
*/
trait Options 
{  
    /**
     * Get option type model class
     *
     * @return string|null
     */
    public function getOptionTypeClass(): ?string
    {
        return $this->optionTypeClass ?? null;
    }
    
    /**
     * Get optins definition model class
     *
     * @return string|null
     */
    public function getOptionsDefinitionClass(): ?string
    {
        return $this->optionsDefinitionClass ?? null;
    }

    /**
     * Mutator (get) for value attribute.
     *
     * @return mixed
     */
    public function getValAttribute()
    {
        return $this->attributes['value'] ?? null;
    }

    /**
     * Option type relation
     *
     * @return mixed
     */
    public function type()
    {
        $optionTypeClass = $this->getOptionTypeClass();
        if (empty($optionTypeClass) == true) {
            return false;
        }

        return $this->belongsTo($optionTypeClass,'type_id');
    }

    /**
     * Create option
     *
     * @param integer|null $referenceId
     * @param string|integer $key Option type key or id
     * @param mixed $value
     * @return Model|null
     */
    public function createOption($referenceId, $key, $value = null): ?object
    {
        if ($this->hasOption($key,$referenceId) == true) {     
            return null;
        }

        $optionType = $this->getOptionType($key);
        if ($optionType == null) {           
            return null;
        }

        return $this->create([
            'reference_id' => $referenceId,
            'uuid'         => Uuid::create(),
            'type_id'      => $optionType->id,
            'key'          => $optionType->key,
            'value'        => ($value == null) ? $optionType->default : $value,        
        ]);      
    }

    /**
     * Create options
     *
     * @param integer $referenceId
     * @param string $typeName
     * @param string|null $branch
     * @return boolean
     */
    public function createOptions($referenceId, $typeName, ?string $branch = null): bool
    {
        $optionsList = Model::create($this->getOptionsDefinitionClass());
        if ($optionsList == null) {
            return false;
        }

        $list = $optionsList->getItems($typeName,$branch);
        foreach ($list as $item) {                  
            $this->createOption($referenceId,$item->key);          
        }

        return true;
    }

    /**
     * Get option type
     *
     * @param string|integer $key Type key or id
     * @return Model|null
     */
    public function getOptionType($key): ?object
    {
        $optionType = Model::create($this->getOptionTypeClass());
        if ($optionType == null) {           
            return null;
        }

        return (\is_numeric($key) == false) ? $optionType->where('key','=',$key)->first() : $optionType->where('id','=',$key)->first();
    }

    /**
     * Get option
     *
     * @param string $key
     * @param integer|null $referenceId
     * @param string|integer $key Option typekey or id     
     * @return Model|null
     */
    public function getOption($key, $referenceId = null): ?object
    {
        if ($this->getOptionType($key) == null) {
            return null;
        }
      
        $referenceId = (empty($referenceId) == true) ? $this->reference_id : $referenceId;
        $model = $this->where('reference_id','=',$referenceId);

        return (\is_numeric($key) == true) ? $model->where('type_id','=',$key)->first() : $model->where('key','=',$key)->first();                             
    }

    /**
     * Get option value
     *
     * @param string  $key
     * @param mixed $referenceId
     * @param mixed $default
     * @return mixed
     */
    public function getOptionValue($key, $referenceId = null, $default = null) 
    {
        $model = $this->getOption($key,$referenceId);

        return ($model == null) ? $default : $model->value; 
    }

    /**
     * Get options query
     *
     * @param integer $referenceId
     * @param array|null $onlyTypes
     * @return QueryBuilder
     */
    public function getOptionsQuery($referenceId, array $onlyKeys = null)
    {
        $query = $this->where('reference_id','=',$referenceId);
        
        return (empty($onlyKeys) == false) ? $query->whereIn('key',$onlyKeys) : $query;          
    }

    /**
     * Get options list
     *
     * @param integer $referenceId
     * @param array|null $onlyTypes
     * @return Model|null
     */
    public function getOptions($referenceId, ?array $onlyKeys = null)
    {
        return $this->getOptionsQuery($referenceId,$onlyKeys)->get();
    }

    /**
     * Return true if option name exist
     *
     * @param integer|null $referenceId
     * @param string $key
     * @return boolean
     */
    public function hasOption($key, $referenceId = null): bool
    {      
        return ($this->getOption($key,$referenceId) !== null);
    }

    /**
     * Save option
     *
     * @param integer $referenceId
     * @param string $key
     * @param mixed $value
     * @return boolean
     */
    public function saveOption($referenceId, $key, $value) 
    {
        if ($this->hasOption($key,$referenceId) == false) {          
            return $this->createOption($referenceId,$key,$value);
        }

        $optionType = $this->getOptionType($key);
        if ($optionType == null) {
            return false;
        }

        return $this
            ->where('reference_id','=',$referenceId)
            ->where('type_id','=',$optionType->id)
            ->update(['value' => $value]);       
    }

    /**
     * Save options
     *
     * @param integer $referenceId
     * @param array $data
     * @return boolean
     */
    public function saveOptions($referenceId, array $data)
    {
        $errors = 0;
        foreach ($data as $key => $value) {
            $result = $this->saveOption($referenceId,$key,$value);
            $errors += ($result !== false) ? 0 : 1; 
        }      
        
        return ($errors == 0);
    }
}
