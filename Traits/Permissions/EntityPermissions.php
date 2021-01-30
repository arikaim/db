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

use Arikaim\Core\Models\Permissions;
use Arikaim\Core\Models\UserGroupMembers;

/**
 * Entity permissions
*/
trait EntityPermissions 
{    
    /**
     * Get entity relation
     *
     * @return Relation|null
     */
    public function entity()
    {      
        return $this->belongsTo($this->entytyModelClass,'entity_id');
    }

    /**
     * Get permission relation
     *
     * @return Relation|null
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

        return (\is_object($permission) == true) ? $permission->name : '';
    }

    /**
     * Morphed models
     *     
     * @return Relation|null
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
    public function deleteUserPermission($entityId, $userId): bool
    {
        $model = $this->getPermission($entityId,$userId,'user');

        return (\is_object($model) == true) ? (bool)$model->delete() : true;
    }
    
    /**
     * Delete group permission
     *
     * @param integer $entityId
     * @param integer $groupId
     * @return boolean
     */
    public function deleteGroupPermission($entityId, $groupId): bool
    {
        $model = $this->getPermission($entityId,$groupId,'group');

        return (\is_object($model) == true) ? (bool)$model->delete() : true;
    }

    /**
     * Add user permission
     *
     * @param integer $entityId
     * @param integer $userId
     * @param array $permissions
     * @param integer|null $permissionId
     * @return Model|false
     */
    public function addUserPermission($entityId, $userId, $permissions, $permissionId = null)
    {
        return $this->addPermission($entityId, $userId, $permissions,'user',$permissionId);
    }

    /**
     * Add group permission
     *
     * @param integer $entityId
     * @param integer $groupId
     * @param array $permissions
     * @param integer|null $permissionId
     * @return Model|false
     */
    public function addGroupPermission($entityId, $groupId, $permissions, $permissionId = null)
    {
        return $this->addPermission($entityId,$groupId,$permissions,'group',$permissionId);
    }

    /**
     * Add permission
     *
     * @param integer $entityId
     * @param integer $userId
     * @param array $permissions
     * @param string $type  (user or gorup)
     * @param integer|null $permissionId
     * @return Model|false
     */
    public function addPermission($entityId, $userId, $permissions, $type = 'user', $permissionId = null)
    {
        $permissions = $this->resolvePermissions($permissions);
        $model = $this->getPermission($entityId,$userId,$type);
        if (\is_object($model) == true) {
            return false;
        }

        $permissions['entity_id'] = $entityId;
        $permissions['relation_id'] = $userId;
        $permissions['relation_type'] = $type;
        $permissions['permission_id'] = $permissionId;
        
        return $this->create($permissions);
    }

    /**
     * Add public permission
     *
     * @param integer $entityId
     * @param array $permissions
     * @return Model
     */
    public function addPublicPermission($entityId, $permissions)
    { 
        $model = $this->getPublicPermission($entityId);
        if (\is_object($model) == true) {
            return false;
        }
        $permissions = $this->resolvePermissions($permissions);
        $permissions['entity_id'] = $entityId;
        $permissions['relation_id'] = null;
        $permissions['relation_type'] = 'user';
    
        return $this->create($permissions);          
    }

    /**
     * Get public permission
     *
     * @param integer $entityId
     * @return Model|null
     */
    public function getPublicPermission($entityId)
    {
        $model = $this
            ->where('entity_id','=',$entityId)
            ->whereNull('relation_id')
            ->where('relation_type','=','user')->first();
        
        return $model;
    }
    
    /**
     * Delete public permissions
     *
     * @param integer $entityId
     * @return boolean
     */
    public function deletePublicPermission($entityId): bool
    {
        $model = $this->getPublicPermission($entityId);

        return (\is_object($model) == true) ? (bool)$model->delete() : true;
    }

    /**
     * Get permission model
     *
     * @param integer $entityId
     * @param integer $id
     * @param string $type
     * @return Model|null
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
     * @param int $entityId
     * @param string|null $type
     * @return Builder
     */
    public function getPermissionsQuery($entityId, $type = null)
    {
        $query = $this->where('entity_id','=',$entityId);
        if (empty($type) == false) {
            $query = $query->where('relation_type','=',$type);
        }

        return $query->whereNotNull('relation_id');      
    }

    /**
     * Query for all permissions for user
     *
     * @param Builder $query
     * @param int $userId
     * @return Builder
     */
    public function scopePermissionsForUser($query, $userId)
    {
        $groups = new UserGroupMembers();
        $groups = $groups->userGroups($userId)->pluck('group_id')->toArray();
        
        $query = $query->where(function($query) use ($userId) {
            // user
            $query->where('relation_id','=',$userId);
            $query->where('relation_type','=','user');
        })->orWhere(function($query) use ($groups) {
            // groups
            $query->whereIn('relation_id',$groups);
            $query->where('relation_type','=','group');
        })->orWhere(function($query) {
            // public
            $query->whereNull('relation_id');           
        });

        return $query;
    }

    /**
     * Get user permissions query
     *
     * @param Builder $query
     * @return Builder
    */
    public function scopeUserPermissions($query)
    {
        return $query->where('relation_type','=','user');
    } 

    /**
     * Get group permissions query
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeGroupPermissions($query)
    {
        return $query->where('relation_type','=','group');
    } 
}
