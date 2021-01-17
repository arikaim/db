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
use Illuminate\Database\Eloquent\Relations\Relation;

use PDOException;
use Exception;
use PDO;

/**
 * Manage database connections
*/
class Db  
{
    /**
     * Capsule manager object
     *
     * @var Illuminate\Database\Capsule\Manager
     */
    private $capsule;

    /**
     * PDO options
     *
     * @var array
    */
    protected $pdoOptions = [
        PDO::ATTR_CASE              => PDO::CASE_NATURAL,
        PDO::ATTR_ERRMODE           => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_ORACLE_NULLS      => PDO::NULL_NATURAL,
        PDO::ATTR_STRINGIFY_FETCHES => false,
        PDO::ATTR_EMULATE_PREPARES  => false,
    ];

    /**
     * Database config
     *
     * @var array
     */
    protected $config;

    /**
     * Has db error
     *
     * @var boolean
     */
    protected $hasError = false;

    /**
     * Constructor
     *
     * @param array $config
     * @param array|null $relations
     */
    public function __construct(array $config, ?array $relations = null) 
    {
        $config['options'] = $config['options'] ?? $this->pdoOptions;
        
        $this->config = $config;
        $this->capsule = new Manager();

        // options 
        $this->init($config); 

        // init relations morph map
        if (\is_array($relations) == true) {                   
            Relation::morphMap($relations);                          
        }
    }

    /**
     * Get database name
     *
     * @return string|null
    */
    public function getDatabaseName(): ?string
    {
        return $this->config['database'] ?? null;
    }

    /**
     * Get relations morph map
     *
     * @return array|null
     */
    public function getRelationsMap(): ?array
    {
        return Relation::$morphMap;
    }

    /**
     * Reboot connection
     *
     * @param array|null $config
     * @return void
     */
    public function reboot(?array $config = null): void
    {
        $this->capsule->getDatabaseManager()->purge();
        if (empty($config) == false) {
            $this->config = $config;
        }
        $this->init($this->config);        
    }

    /**
     * Create db connection and boot Eloquent
     *
     * @param array $config
     * @param bool $showError
     * @return boolean
     */
    public function init(array $config, bool $showError = false): bool
    {
        try {                    
            $this->capsule->setEventDispatcher(new \Illuminate\Events\Dispatcher());

            $this->capsule->addConnection($config);
            $connection = $this->capsule->getConnection();

            if (empty($connection) == false) {
                $connection->getPdo();            
                $this->capsule->setAsGlobal();
                // schema db             
                $this->initSchemaConnection($config); 
                $this->hasError = false;        
            } else {
                $this->hasError = true; 
                return false;               
            }
           
            $this->capsule->bootEloquent();
        }  
        catch(PDOException $e) {
            $this->hasError = true;
            if ($showError == true) {
                echo $e->getMessage();
            }
            return false;
        }   
        catch(Exception $e) {    
            $this->hasError = true; 
            if ($showError == true) {
                echo $e->getMessage();
            }      
            return false;
        }   
        
        return true;
    }

    /**
     * Init db connection
     *
     * @param array $config
     * @param string $name
     * @return boolean
     */
    public function initConnection(array $config, string $name = 'default'): bool
    {
        try {
            $this->capsule->addConnection($config,$name);
            $connection = $this->capsule->getConnection($name);

            if (empty($connection) == true) {
                $this->hasError = true;  
                return false;
            } 
            $connection->reconnect();   
            $this->capsule->getDatabaseManager()->setDefaultConnection($name);
            $this->capsule->setAsGlobal();

            $this->capsule->bootEloquent();
            $this->hasError = false;
        } 
        catch(PDOException $e) {   
            $this->hasError = true;
            return false;
        }   
        catch(Exception $e) {
            $this->hasError = true;
            return false;
        }

        return true;
    }

    /**
     * Return true if has db error
     *
     * @return boolean
     */
    public function hasError(): bool
    {
        return $this->hasError;
    }

    /**
     * Return capsule object
     *
     * @return Illuminate\Database\Capsule
     */
    public function getCapsule()
    {
        return $this->capsule;
    }

    /**
     * Get DatabaseManager
     *
     * @return @return \Illuminate\Database\DatabaseManager
     */
    public function getDatabaseManager()
    {
        return $this->capsule->getDatabaseManager();
    }

    /**
     *  Check if database exist
     *
     * @param string $databaseName
     * @return boolean
     */
    public function has(string $databaseName): bool
    {   
        try {
            $connection = $this->capsule->getConnection('schema');
            $connection = $connection ?? $this->initSchemaConnection();

            $sql = "SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '$databaseName'";
            $result = $connection->select($sql);            
        } 
        catch(PDOException $e) {
            return false;
        }
        catch(Exception $e) {
            return false;
        }

        return (isset($result[0]->SCHEMA_NAME) == true);         
    }

    /**
     * Get row format
     *
     * @param string $tableName
     * @return string|false
     */
    public function getRowFormat(string $tableName)
    {
        try {
            $connection = $this->capsule->getConnection('schema');
            $connection = $connection ?? $this->initSchemaConnection();

            $db = $this->getDatabaseName();
            $sql = "SELECT row_format FROM information_schema.tables WHERE table_schema = '$db' AND table_name='$tableName' LIMIT 1";          
            $result = $connection->select($sql);            
        }      
        catch(PDOException $e) {
            return false;
        }
        catch(Exception $e) {
            return false;
        }

        return $result[0]->row_format ?? false;          
    }

    /**
     * Return true if conneciton is valid
     *
     * @param string|null $name
     * @return boolean
     */
    public function isValidConnection(?string $name = null): bool
    {
        try {
            $connection = $this->capsule->getDatabaseManager()->connection($name);
            $pdo = $connection->getPdo();
        }        
        catch(PDOException $e) {
            return false;
        } 
        catch(Exception $e) {
            return false;
        } 

        return \is_object($pdo);
    }

    /**
     * Return true if connection is valid
     *
     * @param array|null $config
     * @return boolean
     */
    public function isValidPdoConnection(?array $config = null): bool
    {
        $config = ($config == null) ? $this->config : $config;
        $dsn = $config['driver'] . ':dbname=' . $config['database'] . ';host=' . $config['host'];
    
        try {
            $pdo = new PDO($dsn,$config['username'],$config['password'],[
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            ]);
        } 
        catch(PDOException $e) {
            return false;
        }
        catch(Exception $e) {
            return false;
        }

        return \is_object($pdo);
    }

    /**
     * Create database
     *
     * @param string $databaseName
     * @param string|null $charset
     * @param string|null $collation
     * @return boolean
     */
    public function createDb(string $databaseName, ?string $charset = null, ?string $collation = null) 
    {    
        if ($this->has($databaseName) == true) {
            return true;
        }

        $connection = $this->capsule->getConnection('schema');
        $connection = $connection ?? $this->initSchemaConnection();
       
        try {
            $charset = ($charset != null) ? 'CHARACTER SET ' . $charset : '';
            $collation = ($charset != null) ? 'COLLATE ' . $collation : '';

            $result = $connection->statement('CREATE DATABASE ' . $databaseName . ' ' . ' ' . $charset . ' ' . $collation);
        } 
        catch(PDOException $e) {
            return false;
        }
        catch(Exception $e) {
            return false;
        }

        return $result;
    }

    /**
     * Verify db connection
     *
     * @param object $connection
     * @return boolean
     */
    public static function checkConnection($connection): bool
    {
        try {
            $connection->statement('SELECT 1');
        } 
        catch(PDOException $e) {
            return false;
        }
        catch(Exception $e) {
            return false;
        }

        return true;
    }

    /**
     * Test db connection
     *
     * @param array $config
     * @return bool
     */
    public function testConnection(array $config)
    {                
        try {
            $connection = $this->initSchemaConnection($config);     
            $connection->reconnect();

            $result = $this->checkConnection($connection);      
        } 
        catch(PDOException $e) {   
            return false;
        }     
        catch(Exception $e) {
            return false;
        }

        return $result;
    }

    /**
     * Add db schema conneciton
     *
     * @param array|null $config
     * @return Connection
     */
    private function initSchemaConnection(?array $config = null)
    {
        $config = $config ?? $this->config;      
        $config['database'] = 'information_schema';  
    
        $this->capsule->addConnection($config,'schema');
    
        return $this->capsule->getConnection('schema');
    }

    /**
     * Get database info
     *
     * @return array
     */
    public function getInfo(): array 
    {        
        $pdo = $this->capsule->connection()->getPdo();
        return [
            'driver'      => \is_object($pdo) ? $pdo->getAttribute(\PDO::ATTR_DRIVER_NAME) : '',
            'server_info' => \is_object($pdo) ? $pdo->getAttribute(\PDO::ATTR_SERVER_INFO) : '',
            'version'     => \is_object($pdo) ? substr($pdo->getAttribute(\PDO::ATTR_SERVER_VERSION),0,6) : '',
            'name'        => null
        ];      
    }
}
