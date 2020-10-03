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
    public function getSlugPrefix()
    {
        return (isset($this->slugPrefix) == true) ? \trim($this->slugPrefix) : '';
    }

    /**
     * Get slug suffix
     *
     * @return string
    */
    public function getSlugSuffix()
    {
        return (isset($this->slugSuffix) == true) ? \trim($this->slugSuffix) : '';
    }

    /**
     * Get slug attribute name
     *
     * @return string
     */
    public function getSlugColumn()
    {
        return (isset($this->slugColumn) == true) ? $this->slugColumn : 'slug';
    }

    /**
     * Get slug source attribute name
     *
     * @return string
     */
    public function getSlugSourceColumn()
    {
        return (isset($this->slugSourceColumn) == true) ? $this->slugSourceColumn : 'title';
    }

    /**
     * Get slug separator
     *
     * @return void
     */
    public function getSlugSeparator()
    {
        return (isset($this->slugSeparator) == true) ? $this->slugSeparator : '-';
    }

    /**
     * Get slug value
     *
     * @return string
     */
    public function getSlug()
    {
        $slugColumn = $this->getSlugColumn();

        return $this->getSlugPrefix() . $this->attributes[$slugColumn] . $this->getSlugSuffix();
    }

    /**
     * Save slug
     *
     * @param string $text
     * @param string $options
     * @return string
     */
    public static function saveSlug($model)
    {
        $slugColumn = $model->getSlugColumn();
        $slugSourceColumn = $model->getSlugSourceColumn();
        $separator = $model->getSlugSeparator(); 

        if (\is_null($model->$slugSourceColumn) == false) {                   
            $model->attributes[$slugColumn] = Utils::slug($model->$slugSourceColumn,$separator);
        }              
       
        return $model;
    }

    /**
     * Set slug field
     *
     * @param string|null $text
     * @return void
     */
    public function setSlug($text = null)
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
     * @param string $separator
     * @return string
     */
    public function createSlug($text, $separator = null)
    {
        $separator = (empty($separator) == true) ? $this->getSlugSeparator() : $separator;

        return Utils::slug($text,$separator);
    }

    /**
     * Find model by slug
     *
     * @param string $slug
     * @return Model
     */
    public function findBySlug($slug)
    {
        $slugColumn = $this->getSlugColumn();
        $model = $this->where($slugColumn,'=',$slug)->orWhere($slugColumn,'=',$this->createSlug($slug))->first();
      
        return (\is_object($model) == true) ? $model : false;
    }
}
