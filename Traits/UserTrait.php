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

use Arikaim\Core\Models\Users;
use Arikaim\Core\Arikaim;

/**
 * 
 * Get current logged user trait
 *      
*/
trait UserTrait 
{    
    /**
     * Get logged user details
     * 
     * @param mixed $userId
     * @return array|null 
     */
    public function getLoggedUser($userId = null): ?array
    {
        if (empty($userId) == false){
            $user = $this->getUserDetails($userId);
            return (\is_object($user) == true) ? $user->toArray() : null;
        }

        $user = Arikaim::get('access')->getUser();
        if (\is_array($user) == true) {
            return $user;
        }

        return null;
    }

    /**
     * Get user details
     *
     * @param mixed $id
     * @return Model|null
     */
    public function getUserDetails($id)
    {
        $users = new Users();

        return $users->findById($id);
    } 

    /**
     * Get logged user id
     *
     * @param mixed $userId
     * @return mixed
     */
    public function getLoggedUserId($userId = null)
    {      
        return (empty($userId) == true) ? Arikaim::get('access')->getId() : $userId;
    }
}
