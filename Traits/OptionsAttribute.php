<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Intersoft <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Core\Db\Traits;

use Arikaim\Core\Collection\Arrays;

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
     * Get options
     *
     * @param string|null $columnName
     * @return array
     */
    public function getOptions(?string $columnName = null): array
    {
        $columnName = $columnName ?? $this->getOptionsColumnName();
        $options = $this->attributes[$columnName] ?? null;

        return (empty($options) == true) ? [] : \json_decode($options,true);
    }

    /**
     * Mutator (get) for options attribute.
     *
     * @return array
     */
    public function getOptionsAttribute()
    {
        return $this->getOptions();
    }

    /**
     * Get option from options array
     *
     * @param string $path - separator /
     * @param mixed $default
     * @param string|null $columnName
     * @return mixed
     */
    public function getOption(string $path, $default = null, ?string $columnName = null)
    {
        $options = $this->getOptions($columnName);

        return Arrays::getValue($options,$path) ?? $default;
    }

    /**
     * Save option
     *
     * @param string $path - separator /
     * @param mixed $value
     * @param string|null $columnName
     * @return boolean
     */
    public function saveOption(string $path, $value, ?string $columnName = null): bool
    {
        $columnName = $columnName ?? $this->getOptionsColumnName();

        $options = $this->getOptions($columnName);
        $options = Arrays::setValue($options,$path,$value);
       
        $encoded = \json_encode(
            $options,
            JSON_PRETTY_PRINT | 
            JSON_UNESCAPED_UNICODE | 
            JSON_UNESCAPED_SLASHES |
            JSON_NUMERIC_CHECK 
        );

        $result = $this->update([
            $columnName => $encoded
        ]);

        return ($result !== false);
    }

    /**
     * Save options
     *
     * @param array $options
     * @param string|null $columnName
     * @return boolean
     */
    public function saveOptions(array $options, ?string $columnName = null): bool
    {
        $columnName = $columnName ?? $this->getOptionsColumnName();

        $encoded = \json_encode(
            $options,
            JSON_PRETTY_PRINT | 
            JSON_UNESCAPED_UNICODE | 
            JSON_UNESCAPED_SLASHES |
            JSON_NUMERIC_CHECK 
        );

        $result = $this->update([
            $columnName => $encoded
        ]);

        return ($result !== false);
    }
}
