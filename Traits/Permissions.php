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

/**
 * Permissions
*/
trait Permissions 
{    
    /**
     * Return true if have permission 
     *
     * @param string $name valid values read|write|delete|execute
     * @return boolean
     */
    public function hasPermission($name)
    {
        if (isset($this->attributes[$name]) == true) {
            return ($this->attributes[$name] == 1) ? true : false;
        }

        return false;
    }

    /**
     *Return true if have all permissions
     *
     * @return boolean
     */
    public function hasFull()
    {
        $count = 0;
        $count += ($this->hasPermission('read') == false) ? 0 : 1;
        $count += ($this->hasPermission('write') == false) ? 0 : 1;
        $count += ($this->hasPermission('delete') == false) ? 0 : 1;
        $count += ($this->hasPermission('execute') == false) ? 0 : 1;

        return ($count == 4) ? true : false;
    }

    /**
     * Resolve permissions array
     *
     * @param array $access
     * @return array
     */
    public function resolvePermissions(array $access) 
    {
        return [
            'read'      => in_array('read',$access) ? 1:0,
            'write'     => in_array('write',$access) ? 1:0,
            'delete'    => in_array('delete',$access) ? 1:0,
            'execute'   => in_array('execute',$access) ? 1:0
        ];       
    }
}
