<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Core\Db\Traits\Permissions;

use Arikaim\Core\Collection\Arrays;

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
    public function hasPermission($name): bool
    {
        $permission = $this->attributes[$name] ?? null;
       
        return ($permission == 1);
    }

    /**
     * Check for permissions
     *
     * @param array $permissions
     * @return boolean
     */
    public function verifyPermissions(array $permissions): bool
    {
        foreach ($permissions as $key => $value) {
            $success = ($value == 1) ? $this->hasPermission($key) : true;
            if ($success == false) {
                return false;
            }
        }

        return true;
    }

    /**
     *Return true if have all permissions
     *
     * @return boolean
     */
    public function hasFull(): bool
    {
        $count = 0;
        $count += ($this->hasPermission('read') == false) ? 0 : 1;
        $count += ($this->hasPermission('write') == false) ? 0 : 1;
        $count += ($this->hasPermission('delete') == false) ? 0 : 1;
        $count += ($this->hasPermission('execute') == false) ? 0 : 1;

        return ($count == 4);
    }

    /**
     * Resolve permissions array
     *
     * @param array|string $access
     * @return array
     */
    public function resolvePermissions($access): array 
    {
        if (\is_string($access) == true) {
            $access = \strtolower($access);
            $access = ($access == 'full') ? ['read','write','delete','execute'] : Arrays::toArray($access,',');
        }

        return [
            'read'    => \in_array('read',$access) ? 1 : 0,
            'write'   => \in_array('write',$access) ? 1 : 0,
            'delete'  => \in_array('delete',$access) ? 1 : 0,
            'execute' => \in_array('execute',$access) ? 1 : 0
        ];       
    }
}
