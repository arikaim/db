<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Core\Db\Prototypes\Table;

use Arikaim\Core\Db\BlueprintPrototypeInterface;

/**
 * PolymorphicRelations (Many to Many) table prototype class
*/
class PolymorphicRelations implements BlueprintPrototypeInterface
{
    /**
     * Build table
     *
     * @param Arikaim\Core\Db\TableBlueprint $table
     * @param mixed $options (reference feild, reference table, callback)
     * @return void
     */
    public function build($table,...$options)
    {                           
        // columns
        $table->id();
        $table->prototype('uuid');   
        $table->relation($options[0],$options[1],false);     
        $table->string('relation_type')->nullable(false);         
        $table->integer('relation_id')->nullable(false);   

        $table->index('relation_type');
        $table->index('relation_id');
        $table->unique(['relation_id','relation_type',$options[0]],'un_rel_id_type_' . $table->getTable());
        
        $callback = (isset($options[2]) == true) ? $options[2] : null;
        if (\is_callable($callback) == true) {         
            $call = function() use($callback,$table) {
                $callback($table);                                 
            };
            $call($table);
        }
    }
}
