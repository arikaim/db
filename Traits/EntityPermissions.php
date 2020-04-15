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

use Arikaim\Core\Models\Permissions;

/**
 * Entity permissions
*/
trait EntityPermissions 
{    
    /**
     * Get entity relation
     *
     * @return mixed
     */
    public function entity()
    {      
        return $this->belongsTo($this->entytyModelClass,'entity_id');
    }

    /**
     * Get permission relation
     *
     * @return mixed
     */
    public function permission()
    {      
        return $this->belongsTo(Permissions::class,'permission_id');
    }

    /**
     * Get permission name attribute
     *
     * @return string
     */
    public function getNameAttribute()
    {
        $permission = $this->permission();

        return (is_object($permission) == true) ? $permission->name : '';
    }

    /**
     * Morphed model
     *     
     * @return void
     */
    public function related()
    {
        return $this->morphTo('relation');      
    }


    /**
     * Delete user permission
     *
     * @param integer $entityId
     * @param integer $userId
     * @return boolean
     */
    public function deleteUserPermission($entityId, $userId)
    {
        $model = $this->getPermission($entityId,$userId,'user');

        return (is_object($model) == true) ? $model->delete() : true;
    }

    /**
     * Add user permission
     *
     * @param integer $entityId
     * @param integer $userId
     * @param array $permissions
     * @return Model|false
     */
    public function addUserPermission($entityId, $userId, $permissions)
    {
        $permissions = $this->resolvePermissions($permissions);
        $model = $this->getPermission($entityId,$userId,'user');
        if (is_object($model) == true) {
            return false;
        }

        $permissions['entity_id'] = $entityId;
        $permissions['relation_id'] = $userId;
        $permissions['relation_type'] = 'user';
        
        return $this->create($permissions);
    }

    /**
     * Get permission model
     *
     * @param integer $entityId
     * @param integer $id
     * @param string $type
     * @return Model
     */
    public function getPermission($entityId, $id, $type = 'user')
    {
        $model = $this
            ->where('entity_id','=',$entityId)
            ->where('relation_id','=',$id)
            ->where('relation_type','=',$type)->first();
        
        return $model;
    }

    /**
     * Get permissions query 
     *
     * @param ineteger $entityId
     * @return Builder
     */
    public function getPermissionsQuery($entityId)
    {
        return $this->where('entity_id','=',$entityId);      
    }
}
