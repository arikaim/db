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

/**
 * Entity permissions relation
*/
trait EntityPermissionsRelation 
{    
    /**
     * Entity permisisons relation
     *
     * @return hasMany
     */
    public function permissions()
    {
        return $this->hasMany($this->entytyPermissionsClass,'entity_id');
    }
    
    /**
     * Return true if model have permissions
     *
     * @return boolean
     */
    public function hasPermissions()
    {
        return ($this->permissions->count() > 0);
    } 

    /**
     * Return true if user have access
     *
     * @param integer $userId
     * @param string|array $access
     * @return boolean
     */
    public function hasAccess($userId, $access)
    {
        $permissions = $this->resolvePermissions($access);
        $model = $this->getPermission($userId);
        if (is_object($model) == false) {
            return false;
        }

        foreach ($permissions as $permission) {               
            if ($model->hasPermission($permission) == false) {              
                return false;
            }
        }
        
        return true;
    }

    /**
     * Get permission model
     *
     * @param integer $userId
     * @param string $type
     * @return Model|null
     */
    public function getPermission($userId, $type = 'user')
    {
        return $this->permissions->where('relation_id','=',$userId)->where('relation_type','=',$type)->first();
    }

    /**
     * Return true if user have assigned permision 
     *
     * @param integer $userId
     * @param string $type
     * @return boolean
     */
    public function hasPermission($userId, $type = 'user')
    {
        return is_object($this->getPermission($userId,$type));
    }
}
