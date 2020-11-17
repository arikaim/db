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

use Arikaim\Core\Http\Session;

/**
 * Translations trait      
*/
trait Translations 
{           
    /**
     * Current language
     *
     * @var string|null
     */
    protected $currentLanguage;

    /**
     * Boot trait.
     *
     * @return void
     */
    public static function bootTranslations()
    {
        static::retrieved(function($model) {           
            $language = $model->getCurrentLanguage();
            if (empty($language) == false) {
                $model->translateAttributes($language);
            }          
        });        
    }

    /**
     * Get current language
     *
     * @return string
     */
    public function getCurrentLanguage()
    {
        return $this->currentLanguage ?? Session::get('language','en');
    }

    /**
     * Return translated value
     *
     * @param string $attribute
     * @param string $language
     * @return string|null
     */
    public function translateAttribute($attribute, $language = null)
    {
        $language = $language ?? $this->getCurrentLanguage();

        $translation = $this->translation($language);
        if (\is_object($translation) == false) {
            return null;
        }

        return $translation->$attribute ?? null;
    }

    /**
     * Translate attributes
     *
     * @param string $language
     * @return boolean
     */
    public function translateAttributes($language)
    {
        $translation = $this->translation($language);
        if (\is_object($translation) == false) {
            return false;
        }

        $list = $this->getTranslatedAttributes();
        foreach ($list as $attribute) {     
            $translatedValue = (empty($translation->$attribute) == false) ? $translation->$attribute : $this->attributes[$attribute] ?? null;    
            $this->attributes[$attribute] = $translatedValue;
        }

        return true;
    }

    /**
     * Get translation attributes
     *
     * @return array
     */
    public function getTranslatedAttributes()
    {
        return $this->translatedAttributes ?? [];
    }

    /**
     * Get translation refernce attribute name 
     *
     * @return string|null
     */
    public function getTranslationReferenceAttributeName()
    {
        return $this->translationReference ?? null;
    }

    /**
     * Get translation miodel class
     *
     * @return string|null
     */
    public function getTranslationModelClass()
    {
        return $this->translationModelClass ?? null;
    }

    /**
     * HasMany relation
     *
     * @return mixed
     */
    public function translations()
    {       
        return $this->hasMany($this->getTranslationModelClass(),$this->getTranslationReferenceAttributeName());
    }

    /**
     * Get translations query
     *
     * @param string|mull $language
     * @return Builder
     */
    public function getTranslationsQuery($language = null)
    {
        $class = $this->getTranslationModelClass();
        $model = new $class();
        $language = $language ?? $this->getCurrentLanguage();
        
        return $model->where('language','=',$language);
    }

    /**
     * Get translation model
     *
     * @param string $language
     * @return Model|false
     */
    public function translation($language = null, $query = false)
    {
        $language = $language ?? $this->getCurrentLanguage();
        $model = $this->translations()->getQuery()->where('language','=',$language);
        $model = ($query == false) ? $model->first() : $model;

        return (\is_object($model) == false) ? false : $model;
    }

    /**
     * Create or update translation 
     *
     * @param string|integer|null $id
     * @param array $data
     * @param string $language
     * @param string|integer|null $id 
     * @return Model
     */
    public function saveTranslation(array $data, $language = null, $id = null)
    {
        $language = $language ?? $this->getCurrentLanguage();
        $model = (empty($id) == true) ? $this : $this->findById($id);     
        $reference = $this->getTranslationReferenceAttributeName();

        $data['language'] = $language;
        $data[$reference] = $model->id;

        $translation = $model->translation($language);
    
        if ($translation === false) {
            return $model->translations()->create($data);
        } 
        $translation->update($data);  
        
        return $translation;
    }

    /**
     * Delete translation
     *
     * @param string|integer|null $id
     * @param string $language
     * @return boolean
     */
    public function removeTranslation($id = null, $language = null)
    {
        $language = $language ?? $this->getCurrentLanguage();
        $model = (empty($id) == true) ? $this : $this->findById($id);     
        $model = $model->translation($language);

        return (\is_object($model) == true) ? $model->delete() : false;
    }

    /**
     * Delete all translations
     *
     * @param string|integer|null $id
     * @return boolean
     */
    public function removeTranslations($id = null)
    {
        $model = (empty($id) == true) ? $this : $this->findById($id);
        $model = $model->translations();

        return (\is_object($model) == true) ? $model->delete() : false;
    }

    /**
     * Find Translation
     *
     * @param string $attributeName
     * @param mixed $value
     * @return Model|null
     */
    public function findTranslation($attributeName, $value)
    {     
        $class = $this->getTranslationModelClass();
        $model = new $class();
        $model = $model->whereIgnoreCase($attributeName,trim($value));

        return $model->first();
    }

    /**
     * Get meta tags values
     *
     * @param string|null $language
     * @return array
     */
    public function getMetaTags($language = null)
    {
        $translation = $this->translation($language);
        
        return [
            'title'       => \is_object($translation) ? $translation->meta_title : null,
            'description' => \is_object($translation) ? $translation->meta_description : null,
            'keywords'    => \is_object($translation) ? $translation->meta_keywords : null,
        ];  
    }
}
