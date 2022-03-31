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

/**
 * Options relation table trait
*/
trait OptionsRelation 
{   
    /**
     * Boot trait
     *
     * @return void
     */
    public static function bootOptionsRelation()
    {
        static::created(function($model) {      
            $model->createOptions(); 
        });
    }

    /**
     * Get options type name
     *
     * @return string|null
     */
    public function getOptionsType()
    {
        return null;
    }

    /**
     * Create options
     *
     * @param string $typeName
     * @return boolean
     */
    public function createOptions(?string $typeName = null)
    {
        $options = Model::create($this->getOptionsClass());
        $typeName = (empty($typeName) == true) ? $this->getOptionsType() : $typeName;
        $key = $this->getOptionsPrimarykey();

        if (\is_object($options) == true && empty($typeName) == false) {
            return $options->createOptions($this->{$key},$typeName);
        }

        return false;
    } 

    /**
     * Get option model class
     *
     * @return string|null
     */
    public function getOptionsClass(): ?string
    {
        return $this->optionsClass ?? null;
    }

    /**
     * Get options primary key
     *
     * @return string
     */
    public function getOptionsPrimarykey(): string
    {
        return $this->optionsPrimaryKey ?? 'id';
    }

    /**
     * Options relation
     *
     * @return mixed
     */
    public function options()
    {
        return $this->hasMany($this->getOptionsClass(),'reference_id',$this->getOptionsPrimarykey());       
    }

    /**
     * Create options_list attribute used for better collection serialization key => value 
     *
     * @return Collection
     */
    public function getOptionsListAttribute()
    {
        $options = $this->options()->get()->keyBy('key')->map(function ($item, $key) {
            return $item['value'];
        });

        return $options;
    }

    /**
     * Get option
     *
     * @param string $key
     * @return array|null
     */
    public function getOption($key)
    {
        if (\is_object($this->options) == false) {
            return null;
        }        
        $items = $this->options->keyBy('key');

        if (\is_object($items) == true) {
            $item = $items->get($key);
            return (\is_object($item) == true) ? $item->toArray() : null;
        }   

        return null;
    }

    /**
     * Get option value
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function getOptionValue($key, $default = null)
    {
        $option = $this->getOption($key);

        return (empty($option) == false) ? $option['value'] : $default;
    }
}
