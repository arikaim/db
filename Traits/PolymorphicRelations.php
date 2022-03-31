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
     * @return string|null
     */
    public function getRelationModelClass(): ?string
    {
        return $this->relationModelClass ?? null;
    }

    /**
     * Get relation attribute name
     *
     * @return string|null
     */
    public function getRelationAttributeName(): ?string
    {
        return $this->relationColumnName ?? null;
    }

    /**
     * Morphed model
     * 
     * @param string|null $type
     * @return mixed
     */
    public function related(?string $type = null)
    {
        return $this->morphTo('relation',$type);      
    }

    /**
     * Relations
     *
     * @return Relation
     */
    public function relations()
    {    
        return $this->morphToMany($this->getRelationModelClass(),'relation');
    }

    /**
     * Get relations
     *
     * @param integer|null $id
     * @param string|null $type
     * @return Builder
     */
    public function getItemsQuery(?int $id, ?string $type = null) 
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
     * @param integer|null $id
     * @param string|null $type
     * @return boolean
     */
    public function hasRelatedItems(?int $id, ?string $type = null): bool
    {
        $query = $this->getItemsQuery($id,$type);

        return ($query->count() > 0);
    }

    /**
     * Get relations items
     *
     * @param integer|null $relationId
     * @param string|null $type
     * @return Collection|null
     */
    public function getRelatedItems(?int $relationId, ?string $type = null)
    {
        $relationField = $this->getRelationAttributeName();
        $query = $this->getRelationsQuery($relationId,$type);
        
        return $query->get($relationField)->pluck($relationField);
    }

    /**
     * Get relations query for model id
     *
     * @param integer|null $relation_id
     * @param string|null $type
     * @return Builder
     */
    public function getRelationsQuery(?int $relationId, ?string $type = null) 
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
    public function deleteRelation($id = null): bool
    {
        $model = (empty($id) == true) ? $this : $this->findByid($id);

        return (\is_object($model) == true) ? (bool)$model->delete() : false;
    }

    /**
     * Delete relations
     *
     * @param integer|null $id
     * @param string|null $type
     * @param integer|null $relationId
     * @return boolean
     */
    public function deleteRelations(?int $id, ?string $type = null, ?int $relationId = null): bool
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
     * @param integer|null $id
     * @param string|null  $type
     * @param integer|null $relationId
     * @return Model|boolean
     */
    public function saveRelation(?int $id, ?string $type, ?int $relationId)
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
     * @param integer|null $id
     * @param string|null  $type
     * @param integer|null $relationId
     * @return boolean
     */
    public function hasRelation(?int $id, ?string $type, ?int $relationId): bool
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
     * @param integer|null $id
     * @param string|null  $type
     * @param integer|null $relationId
     * @return Model|false
     */
    public function getRelationModel(?int $id, ?string $type, ?int $relationId)
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
     * @param array $items
     * @param string|null  $type
     * @param integer|null $relationId
     * @return array
     */
    public function saveRelations(array $items, ?string $type, ?int $relationId): array
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
