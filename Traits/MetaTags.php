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

use Arikaim\Core\View\Html\HtmlComponent;

/**
 * Page meta tags trait      
*/
trait MetaTags 
{           
    /**
     * Current language
     *
     * @var string|null
     */
    protected $currentLanguage;

    /**
     * Get current language
     *
     * @return string
     */
    public function getCurrentLanguage()
    {
        return (empty($this->currentLanguage) == true) ? HtmlComponent::getLanguage() : $this->currentLanguage;
    }

    /**
     * Get meta tags values
     *
     * @param string|null $language
     * @param Builder|null $query
     * @return array
     */
    public function getMetaTags($language = null, $query = null)
    {
        $model = (empty($query) == true) ? $this : $query;
        $language = (empty($language) == true) ? $this->getCurrentLanguage() : $language;
        $model = $model->where('language','=',$language)->first();
        
        return $this->getMetaTagsArray($model);        
    }

    /**
     * Get meta tags field values
     *
     * @param Model|null $model
     * @return array
     */
    public function getMetaTagsArray($model = null)
    {
        $model = (empty($model) == true) ? $this : $model;

        return [
            'title'       => \is_object($model) ? $model->meta_title : null,
            'description' => \is_object($model) ? $model->meta_description : null,
            'keywords'    => \is_object($model) ? $model->meta_keywords : null,
        ];
    }
}
