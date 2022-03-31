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

use Arikaim\Core\Utils\Utils;

/**
 * Create slug
*/
trait Slug 
{    
    /**
     * Set model event on saving
     *
     * @return void
     */
    public static function bootSlug()
    {
        static::saving(function($model) {   
            $model = Self::saveSlug($model);
        });     
        static::creating(function($model) { 
            $model = Self::saveSlug($model);
        });
    }

    /**
     * Get slug prefix
     *
     * @return string
     */
    public function getSlugPrefix(): string
    {
        return (isset($this->slugPrefix) == true) ? \trim($this->slugPrefix) : '';
    }

    /**
     * Get slug suffix
     *
     * @return string
    */
    public function getSlugSuffix(): string
    {
        return (isset($this->slugSuffix) == true) ? \trim($this->slugSuffix) : '';
    }

    /**
     * Get slug attribute name
     *
     * @return string
     */
    public function getSlugColumn(): string
    {
        return  $this->slugColumn ?? 'slug';
    }

    /**
     * Get slug source attribute name
     *
     * @return string
     */
    public function getSlugSourceColumn(): string
    {
        return $this->slugSourceColumn ?? 'title';
    }

    /**
     * Get slug separator
     *
     * @return string
     */
    public function getSlugSeparator(): string
    {
        return $this->slugSeparator ?? '-';
    }

    /**
     * Get slug value
     *
     * @return string
     */
    public function getSlug(): string
    {
        $slugColumn = $this->getSlugColumn();

        return $this->getSlugPrefix() . $this->attributes[$slugColumn] . $this->getSlugSuffix();
    }

    /**
     * Save slug
     *
     * @param Model $model   
     * @return Model
     */
    public static function saveSlug($model)
    {
        $slugColumn = $model->getSlugColumn();
        $slugSourceColumn = $model->getSlugSourceColumn();
        $separator = $model->getSlugSeparator(); 

        if (\is_null($model->$slugSourceColumn) == false) {                   
            $model->$slugColumn = Utils::slug($model->$slugSourceColumn,$separator);
        }              
       
        return $model;
    }

    /**
     * Set slug field
     *
     * @param string|null $text
     * @return void
     */
    public function setSlug(?string $text = null): void
    {
        $slugColumn = $this->getSlugColumn();
        $slugSourceColumn = $this->getSlugSourceColumn();
        $separator = $this->getSlugSeparator();

        $text = (empty($text) == true) ? $this->$slugSourceColumn : $text;

        $this->$slugColumn = Utils::slug($text,$separator);
    }

    /**
     * Create slug from text
     *
     * @param string $text
     * @param string|null $separator
     * @return string
     */
    public function createSlug(string $text, ?string $separator = null): string
    {
        $separator = $separator ?? $this->getSlugSeparator();

        return Utils::slug($text,$separator);
    }

    /**
     * Find model by slug
     *
     * @param string $slug
     * @return Model|false
     */
    public function findBySlug(string $slug)
    {
        $slugColumn = $this->getSlugColumn();
        $model = $this->where($slugColumn,'=',$slug)->orWhere($slugColumn,'=',$this->createSlug($slug))->first();
      
        return (\is_object($model) == true) ? $model : false;
    }
}
