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

use Arikaim\Core\Utils\Uuid;

/**
 *  Polymorphic Relations (Many To Many) trait      
*/
trait PolymorphicRelations 
{           
    /**
     * Get relation model class
     *
     * @return string
     */
    public function getRelationModelClass()
    {
        return (isset($this->relationModelClass) == true) ? $this->relationModelClass : null;
    }

    /**
     * Get relation attribute name
     *
     * @return string
     */
    public function getRelationAttributeName()
    {
        return (isset($this->relationColumnName) == true) ? $this->relationColumnName : null;
    }

    /**
     * Morphed model
     * 
     * @param string|null $type
     * @return void
     */
    public function related($type = null)
    {
        return $this->morphTo('relation',$type);      
    }

    /**
     * Relations
     *
     * @return void
     */
    public function relations()
    {    
        return $this->morphToMany($this->getRelationModelClass(),'relation');
    }

    /**
     * Get relations
     *
     * @param integer $id
     * @param string|null $type
     * @return Builder
     */
    public function getItemsQuery($id, $type = null) 
    {
        $relationField = $this->getRelationAttributeName();
        $query = (empty($id) == false) ? $this->where($relationField,'=',$id) : $this;

        if (empty($type) == false) {
            $query = $query->where('relation_type','=',$type);
        }

        return $query;
    }

    /**
     * Return true if related items > 0 
     *
     * @param integer $id
     * @param string|null $type
     * @return boolean
     */
    public function hasRelatedItems($id, $type = null)
    {
        $query = $this->getItemsQuery($id,$type);

        return ($query->count() > 0);
    }

    /**
     * Get relations items
     *
     * @param integer $relationId
     * @param string $type
     * @return Collection|null
     */
    public function getRelatedItems($relationId, $type = null)
    {
        $relationField = $this->getRelationAttributeName();
        $query = $this->getRelationsQuery($relationId,$type);
        
        return $query->get($relationField)->pluck($relationField);
    }

    /**
     * Get relations query for model id
     *
     * @param integer $relation_id
     * @param string|null $type
     * @return Builder
     */
    public function getRelationsQuery($relationId, $type = null) 
    {      
        $query = $this->where('relation_id','=',$relationId);
        if (empty($type) == false) {
            $query = $query->where('relation_type','=',$type);
        }

        return $query;
    }

    /**
     *  Delete relation
     *
     * @param integer|string|null $id
     * @return boolean
     */
    public function deleteRelation($id = null)
    {
        $model = (empty($id) == true) ? $this : $this->findByid($id);

        return (\is_object($model) == true) ? $model->delete() : false;
    }

    /**
     * Delete relations
     *
     * @param integer $id
     * @param string|null $type
     * @param integer|null $relationId
     * @return boolean
     */
    public function deleteRelations($id, $type = null, $relationId = null)
    {
        $relationField = $this->getRelationAttributeName();
        $model = $this->where($relationField,'=',$id);
        
        if (empty($type) == false) {
            $model = $model->where('relation_type','=',$type);
        }
        if (empty($relationId) == false) {
            $model = $model->where('relation_id','=',$relationId);
        }
               
        return (bool)$model->delete();
    }

    /**
     * Save relation
     *
     * @param integer $id
     * @param string  $type
     * @param integer $relationId
     * @return Model|boolean
     */
    public function saveRelation($id, $type, $relationId)
    {
        if (empty($relationId) == true || empty($id) == true) {
            return false;
        }
        $relationField = $this->getRelationAttributeName();
        $data = [           
            $relationField  => $id,
            'relation_id'   => $relationId,
            'relation_type' => $type,
        ];    
    
        $model = $this->getRelationModel($id,$type,$relationId);
        if ($model === false) {
            $data['uuid'] = Uuid::create();
            return $this->create($data);
        }

        return $model->update($data);
    }

    /**
     * Return true if relation exist
     *
     * @param integer $id
     * @param string  $type
     * @param integer $relationId
     * @return boolean
     */
    public function hasRelation($id, $type, $relationId)
    {
        $relationField = $this->getRelationAttributeName();
        $model = $this
            ->where($relationField,'=',$id)
            ->where('relation_type','=',$type)
            ->where('relation_id','=',$relationId)->first();
        
        return \is_object($model);
    }

    /**
     * Get relation
     *
     * @param integer $id
     * @param string  $type
     * @param integer $relationId
     * @return Model|false
     */
    public function getRelationModel($id, $type, $relationId)
    {
        $relationField = $this->getRelationAttributeName();
        $model = $this
            ->where($relationField,'=',$id)
            ->where('relation_type','=',$type)
            ->where('relation_id','=',$relationId)->first();
        
        return (\is_object($model) == true) ? $model : false;
    }

    /**
     * Save relations
     *
     * @param integer $id
     * @param string  $type
     * @param integer $relationId
     * @return array
     */
    public function saveRelations(array $items, $type, $relationId)
    {
        $added = [];
        foreach ($items as $item) {           
            $result = $this->saveRelation($item,$type,$relationId);
            if ($result !== false) {
               $added[] = $item;
            }
        }

        return $added;
    }
}
