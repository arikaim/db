<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Core\Db\Prototypes\Column;

use Arikaim\Core\Db\Interfaces\BlueprintPrototypeInterface;

/**
 * Default multiuser column prototype class
*/
class DefaultMultiuser implements BlueprintPrototypeInterface
{
    /**
     * Build column
     *
     * @param Arikaim\Core\Db\TableBlueprint $table
     * @param mixed $options
     * @return void
     */
    public function build($table,...$options)
    {
        $name = $options[0] ?? 'default';
        
        $table->integer($name)->nullable(true)->default(null); 
        $table->unique([$name,'user_id']);   
    }
}
