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
        if (\is_object($model) == false) {
            return false;
        }

        foreach ($permissions as $key => $permission) { 
            if ($model->hasPermission($key) == true) {           
                return true;
            }
        }
        
        return false;
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
     * Get public entity permission 
     *
     * @param integer|null $entityId
     * @return Model|false
     */
    public function getPublicPermission($entityId = null)
    {
        $entityId = (empty($entityId) == true) ? $this->id : $entityId;   
        $model = new $this->entytyPermissionsClass();

        $model = $model
                ->where('relation_id','=',null)
                ->where('relation_type','=','user')
                ->where('entity_id','=',$entityId)->first();
        
        return (\is_object($model) == true) ? $model : false;
    } 

    /**
     * Return true if item is public
     *
     * @param integer|null $entityId
     * @return boolean
     */
    public function isPublic($entityId = null)
    {
        $entityId = (empty($entityId) == true) ? $this->id : $entityId;

        return ($this->getPublicPermission($entityId) !== false);
    }
}
