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

/**
 * Options relation table trait
*/
trait OptionsRelation 
{   
    /**
     * Get reference column name
     * @return string|null
     */
    public function getReferenceColumn(): ?string
    {
        return $this->referenceColumn ?? 'reference_id';
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
     * Options relation
     *
     * @return mixed
     */
    public function options()
    {
        return $this->hasMany($this->getOptionsClass(),$this->getReferenceColumn());       
    }

    /**
     * Create options_list attribute used for better collection serialization key => value 
     *
     * @return mixed
     */
    public function getOptionsListAttribute()
    {
        return $this->options()->get()->keyBy('key')->map(function ($item, $key) {
            return $item['value'];
        });
    }

    /**
     * Get option
     *
     * @param string $key
     * @return object|null
     */
    public function getOption(string $key): ?object
    {
        return $this->options()->where('key','=',$key)->first();
    }

    /**
     * Retrun true if option exist
     * @param string $key
     * @return bool
     */
    public function hasOption(string $key): bool
    {
        return ($this->getOption($key) != null);
    }

    /**
     * Save option
     * @param string $key
     * @param mixed $value
     * @return bool
     */
    public function saveOption(string $key, $value): bool
    {
        if ($this->hasOption($key) == true) {
            $result = $this->options()->where('key','=',$key)->update([
                'value' => $value
            ]);

            return ($result !== false);
        }

        $option = $this->options()->create([
            'key'   => $key,
            'value' => $value
        ]);
        
        return ($option !== null);
    }   
    
    /**
     * Get option value
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function getOptionValue(string $key, $default = null)
    {
        $option = $this->getOption($key);

        return ($option == null) ? $default : $option->value;
    }
}
