<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Core\Db\Traits;

/**
 * Options attribute trait
*/
trait OptionsAttribute 
{        
    /**
     * Mutator (set) for options attribute.
     *
     * @param array $value
     * @return void
     */
    public function setOptionsAttribute($value)
    {
        $value = (\is_array($value) == true) ? $value : [$value];    
        $this->attributes['options'] = \json_encode($value);
    }

    /**
     * Mutator (get) for options attribute.
     *
     * @return array
     */
    public function getOptionsAttribute()
    {
        $options = $this->attributes['options'] ?? null;
        return (empty($options) == true) ? [] : \json_decode($options,true);
    }

    /**
     * Get option from options array
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function getOption(string $key, $default = null)
    {
        return $this->options[$key] ?? $default;
    }
}
