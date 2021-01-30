<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Core\Db\Interfaces;

use Arikaim\Core\Db\TableBlueprint;

/**
 * Db Column prototype interface
 */
interface BlueprintPrototypeInterface
{    
    /**
     * Build column
     *
     * @param TableBlueprint $table
     * @param mixed $options
     * @return void
     */
    public function build($table, ...$options); 
}
