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
 * Quantity column prototype class
*/
class Quantity implements BlueprintPrototypeInterface
{
    /**
     * Build column
     *
     * @param \Arikaim\Core\Db\TableBlueprint $table
     * @param mixed $options
     * @return void
     */
    public function build($table,...$options)
    {
        $default = $options[0] ?? 0.00;
        $columnName = $options[1] ?? 'quantity';

        $table->unsignedDecimal($columnName,15,4)->nullable(false)->default($default); 
        $table->index($columnName);   
    }
}
