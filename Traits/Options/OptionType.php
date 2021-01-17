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

use Arikaim\Core\Db\Interfaces\OptionTypeInterface;

/**
 * Option type table trait
*/
trait OptionType 
{    
    /**
     *  Option type text
     */
    static $TYPES_LIST = [
        'text',
        'checkbox',
        'dropdown',
        'textarea',
        'relation',
        'number',
        'image',
        'price',
        'file',
        'markdown',
        'date',
        'time-interval',
        'user-group',
        'permission'
    ];

    /**
     * Get option type id
     *
     * @param string $type
     * @return integer|null
     */
    public static function getOptionTypeId($type)
    {
        $result = \array_search($type,Self::$TYPES_LIST);

        return ($result == false) ? 0 : $result;
    }

    /**
     * Get option type name
     *
     * @param int|null $type
     * @return string|false
     */
    public function getTypeText($type = null)
    {
        $type = $type ?? $this->type;

        return (isset(Self::$TYPES_LIST[$type]) == true) ? Self::$TYPES_LIST[$type] : false;
    }

    /**
     * Boot trait
     *
     * @return void
     */
    public static function bootOptionType()
    {
        $fillable = [
            'key',
            'title',
            'description',
            'hidden',
            'readonly',
            'default',
            'items',
            'items_type',
            'data_source',
            'data_source_type'      
        ];

        static::retrieved(function($model) use ($fillable) {
            $model->fillable = \array_merge($model->fillable,$fillable);
        });

        static::saving(function($model) use ($fillable) {
            $model->fillable = \array_merge($model->fillable,$fillable);
        });
    }

    /**
     * Time interval option type
     *
     * @return integer
     */
    public function TIME_INTERVAL()
    {
        return OptionTypeInterface::TIME_INTERVAL;
    }

    /**
     * Date time option type
     *
     * @return integer
     */
    public function DATE()
    {
        return OptionTypeInterface::DATE;
    }

    /**
     * User group option type
     *
     * @return integer
     */
    public function USERGROUP()
    {
        return OptionTypeInterface::USER_GROUP;
    }

    /**
     * Permission option type
     *
     * @return integer
    */
    public function PERMISSION()
    {
        return OptionTypeInterface::PERMISSION;
    }

    /**
     * Text type option
     *
     * @return integer
     */
    public function TEXT()
    {
        return OptionTypeInterface::TEXT;
    }

    /**
     * Checkbox type option
     *
     * @return integer
     */
    public function CHECKBOX()
    {
        return OptionTypeInterface::CHECKBOX;
    }

    /**
     * Dropdown type option
     *
     * @return integer
     */
    public function DROPDOWN()
    {
        return OptionTypeInterface::DROPDOWN;
    }

    /**
     * Text area type option
     *
     * @return integer
     */
    public function TEXTAREA()
    {
        return OptionTypeInterface::TEXT_AREA;
    }

    /**
     * Relation type option
     *
     * @return integer
     */
    public function RELATION()
    {
        return OptionTypeInterface::RELATION;
    }

    /**
     * Number type option
     *
     * @return integer
     */
    public function NUMBER()
    {
        return OptionTypeInterface::NUMBER;
    }

    /**
     * Price type option
     *
     * @return integer
     */
    public function PRICE()
    {
        return OptionTypeInterface::PRICE;
    }
    
    /**
     * Mutator (set) for items attribute.
     *
     * @param array $value
     * @return void
     */
    public function setItemsAttribute($value)
    {
        $value = (\is_array($value) == true) ? $value : [$value];    
        $this->attributes['items'] = \json_encode($value);
    }

    /**
     * Mutator (get) for items attribute.
     *
     * @return array
     */
    public function getItemsAttribute()
    {
        return (empty($this->attributes['items']) == true) ? [] : \json_decode($this->attributes['items'],true);
    }

    /**
     * Get option type
     *
     * @param string $key
     * @return mixed
     */
    public function getByKey($key)
    {
        return $this->findByColumn($key,'key');
    }
}
