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
     * Get options column name
     *
     * @return string
     */
    public function getOptionsColumnName(): string
    {
        return $this->optionsColumnName ?? 'options';
    }

    /**
     * Mutator (set) for options attribute.
     *
     * @param array $value
     * @return void
     */
    public function setOptionsAttribute($value)
    {
        $column = $this->getOptionsColumnName();
        $value = (\is_array($value) == true) ? $value : [$value];    
        $this->attributes[$column] = \json_encode($value);
    }

    /**
     * Mutator (get) for options attribute.
     *
     * @return array
     */
    public function getOptionsAttribute()
    {
        $column = $this->getOptionsColumnName();
        $options = $this->attributes[$column] ?? null;

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

    /**
     * Save option
     *
     * @param string $key
     * @param mixed $value
     * @return boolean
     */
    public function saveOption(string $key, $value): bool
    {
        $options = $this->options;
        $options[$key] = $value;
        $column = $this->getOptionsColumnName();

        $result = $this->update([
            $column => \json_encode($options)
        ]);

        return ($result !== false);
    }

    /**
     * Save options
     *
     * @param array $options
     * @return boolean
     */
    public function saveOptions(array $options): bool
    {
        $column = $this->getOptionsColumnName();
        $result = $this->update([
            $column => \json_encode($options)
        ]);

        return ($result !== false);
    }
}
