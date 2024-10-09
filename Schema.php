<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Core\Db;

use Illuminate\Database\Capsule\Manager;
use Illuminate\Database\Schema\Builder;

use Arikaim\Core\Utils\Factory;
use Arikaim\Core\Db\Seed;
use Arikaim\Core\Db\TableBlueprint;
use Arikaim\Core\Db\TableSchemaDescriptor;
use Arikaim\Core\Collection\Traits\Descriptor;
use Arikaim\Core\Db\Traits\Schema\Import;
use PDOException;
use Exception;

/**
 * Database schema base class
*/
abstract class Schema  
{
    use 
        Descriptor,
        Import;

    /**
     * Table name
     *
     * @var string
     */
    protected $tableName = null;

    /**
     * Db connection name
     *
     * @var string|null
     */
    protected $connection = null;

    /**
     * Db storage engine
     *
     * @var string
     */
    protected $storageEngine = 'InnoDB row_format=dynamic';

    /**
     * Create table
     * 
     * @param  Arikaim\Core\Db\TableBlueprint $table
     * @return void
     */
    abstract public function create($table);

    /**
     * Update existing table
     *
     * @param  Arikaim\Core\Db\TableBlueprint $table
     * @return void
     */
    abstract public function update($table);

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->setDescriptorClass(TableSchemaDescriptor::class);
    }

    /**
     * Set db connection
     *
     * @param string $name
     * @return void
     */
    public function setConnection(string $name): void
    {
        $this->connection = $name;
    }

    /**
     * Insert or update rows in table
     *
     * @param Seed $seed
     * @return void
     */
    public function seeds($seed)
    {
    }

    /**
     * Return table name
     *
     * @return string
     */
    public function getTableName(): string 
    {
        return $this->tableName;
    }
    
    /**
     * Return model table name
     *
     * @param string $class Model class name
     * @return bool|string
     */
    public static function getTable(string $class)
    {
        $instance = Factory::createSchema($class);

        return ($instance == null) ? false : $instance->getTableName();         
    }

    /**
     * Create table
     *    
     * @return void
     */
    public function createTable(): void
    {
        if ($this->tableExists() == true) {  
            // skip if table exsit
            return;
        }    

        $blueprint = new TableBlueprint($this->tableName,null);
        $call = function() use($blueprint) {
            $blueprint->create();

            $this->create($blueprint);  
                
            if (empty($blueprint->engine) == true) {
                $blueprint->engine = $this->storageEngine;               
            }        
        };
        $call(); 

        $this->build($blueprint,Manager::schema($this->connection));             
    } 

    /**
     * Update table 
     *
     * @return void
     */
    public function updateTable(): void 
    {
        if ($this->tableExists() == false) {   
            // skip if table not exist 
            return;
        }

        $blueprint = new TableBlueprint($this->tableName,null);
        $call = function() use($blueprint) {                
            $this->update($blueprint);
            $blueprint->engine = $this->storageEngine;     
        };
        $call(); 
        
        $this->build($blueprint,Manager::schema($this->connection));                    
    } 
    
    /**
     * Execute seeds
     *
     * @return mixed|false
     */
    public function runSeeds()
    {
        if ($this->tableExists() == false) {  
            return false;
        }

        $seed = new Seed($this->tableName);                  
        $this->seeds($seed);
        return true;
    }

    /**
     * Return true if table is empty
     *
     * @return boolean
     */
    public function isEmpty(): bool
    {
        $query = Manager::table($this->tableName); 

        return empty($query->count() == true);
    }

    /**
     * Get query builder for table
     *
     * @param string $tableName
     * @return QueryBuilder
     */
    public static function getQuery(string $tableName)
    {
        return Manager::table($tableName);  
    }

    /**
     * Changes db table row format 
     *
     * @param string $table
     * @param string $format
     * @return boolean
     */
    public static function setRowFormat($table, $format = 'DYNAMIC')
    {      
        return Manager::connection()->statement('ALTER TABLE ' . $table . ' ROW_FORMAT=' . $format);
    }

    /**
     * Execute blueprint.
     *
     * @param  Arikaim\Core\Db\TableBlueprint  $blueprint
     * @param  Illuminate\Database\Schema\Builder  $builder
     * @return void
     */
    public function build($blueprint, Builder $builder)
    {
        $connection = $builder->getConnection();
        $grammar = $connection->getSchemaGrammar();

        $blueprint->build($connection,$grammar);
    }
    
    /**
     * Check if database exist.
     *
     * @param  object|string $model Table name or db model object
     * @param  string|null $connection
     * @return boolean
     */
    public static function hasTable($model, ?string $connection = null): bool
    {      
        $tableName = (\is_object($model) == true) ? $model->getTable() : $model;

        try {      
            if (\is_object(Manager::connection($connection)) == true) {
                $schema = Manager::schema($connection);    
                return (\is_object($schema) == true) ? $schema->hasTable($tableName) : false;
            }
            return false;
        } 
        catch(PDOException $e) {
            return false;
        }
        catch(Exception $e) {           
            return false;
        }  
    }

    /**
     * Drop table
     * 
     * @param boolean $emptyOnly
     * @return boolean
     */
    public function dropTable($emptyOnly = true): bool 
    {
        if ($emptyOnly == true && $this->isEmpty() == true) {                  
            Manager::schema($this->connection)->dropIfExists($this->tableName);
        } 
        if ($emptyOnly == false) {
            Manager::schema($this->connection)->dropIfExists($this->tableName);           
        }

        return !$this->tableExists();
    } 

    /**
     * Get column type
     *
     * @param string $columnName
     * @return string
     */
    public function getColumnType(string $columnName)
    {
        return Manager::schema($this->connection)->getColumnType($this->tableName,$columnName);
    }

    /**
     * Checkif table exist
     *
     * @return bool
     */
    public function tableExists() 
    {
        return Manager::schema($this->connection)->hasTable($this->tableName);
    }

    /**
     * Check if table column exists.
     *
     * @param string $column
     * @return boolean
     */
    public function hasColumn($column) 
    {
        return Manager::schema($this->connection)->hasColumn($this->tableName,$column); 
    }
    
    /**
    * Drop table index
    *
    * @param string $indexName
    * @return boolean
    */
    public function dropIndex(string $indexName): bool 
    {
        if ($this->hasIndex($indexName) == false) {
            return true;
        }

        $sql = 'ALTER TABLE ' . $this->tableName . ' DROP INDEX `' . $indexName . '`';
        Manager::connection()->statement($sql);

       return ($this->hasIndex($indexName) == true);
    }

    /**
     * Return true if index exist
     *
     * @param string $indexName
     * @return boolean
     */
    public function hasIndex(string $indexName): bool
    {
        if (empty($indexName) == true) {
            return false;
        }
        $result = Manager::select('SHOW INDEXES FROM ' . $this->tableName);
      
        foreach ($result as $item) {
            if ($item->Key_name == $indexName) {
                return true;
            }           
        }
    
        return false;
    }

    /**
     * Return shema object
     * 
     * @param string|null $connection
     * @return object
     */
    public static function schema(?string $connection = null) 
    {
        return Manager::schema($connection);
    }

    /**
     * Run Create and Update migration
     *
     * @param string $class
     * @param string $extension
     * @return bool
     */
    public static function install(string $class, ?string $extension = null): bool 
    {                   
        $instance = Factory::createSchema($class,$extension);
        if ($instance == null) {
            return false;
        }

        if ($instance->tableExists() == false) {                 
            $instance->createTable();
        } else {                   
            $instance->updateTable();
        }
        
        $instance->runSeeds();
        
        return $instance->tableExists();               
    }

    /**
     * UnInstall migration
     *
     * @param string $class
     * @param string $extension
     * @param boolean $force Set to true will drop table if have rows.
     * @return bool
     */
    public static function unInstall(string $class, ?string $extension = null, bool $force = false): bool 
    {                   
        $instance = Factory::createSchema($class,$extension);
        if ($instance == null) {
            return false;
        }

        try {
            return $instance->dropTable(!$force);
        } 
        catch(PDOException $e) {
            return false;
        }
        catch(Exception $e) {
            return false;
        }
        
        return false;
    }

    /**
     * Get descriptor
     *
     * @return array
     */
    public function getDescriptor(): array
    {
        $blueprint = new TableBlueprint($this->tableName,null);
            
        $create = function() use($blueprint) {
            $blueprint->create();

            $this->create($blueprint);            
            $blueprint->engine = $this->storageEngine;               
        };
        $create(); 

        $update = function() use($blueprint) {                
            $this->update($blueprint);
        };
        $update(); 

        $descriptor = $this->descriptor();

        $result = [];
        foreach ($blueprint->getColumns() as $column) {
            $column = $column->toArray();
            $property = $descriptor->get($column['name']);
            if ($property != null) {
                $column['property'] = $property->toArray();
            }

            $result[] = $column;
        }

        return $result;
    }
}   
