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

use Arikaim\Core\Utils\Uuid as UuidFactory;

/**
 * Update UUID field
 *      
*/
trait Uuid 
{    
    /**
     * Init model events.
     *
     * @return void
     */
    public static function bootUuid()
    {
        static::creating(function($model) {   
            $columnName = $model->getUuidAttributeName();
            if (empty($model->$columnName) == true) {                
                $model->attributes[$columnName] = UuidFactory::create();
            }
        });
    }

    /**
     * Get uuid attribute name
     *
     * @return string
     */
    public function getUuidAttributeName()
    {
        return (isset($this->uuidColumn) == true) ? $this->uuidColumn : 'uuid';
    }

    /**
     * Init empty uuid column
     *
     * @return void
     */
    public function initUuid()
    {
        $columnName = $this->getUuidAttributeName();
        if (empty($this->$columnName) == true) { 
            $this->$columnName = UuidFactory::create();
        }
    }
}
