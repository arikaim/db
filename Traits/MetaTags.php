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
    public function getCurrentLanguage(): string
    {
        return $this->currentLanguage ?? Session::get('language','en');
    }

    /**
     * Get meta tags values
     *
     * @param string|null $language
     * @param Builder|null $query
     * @return array
     */
    public function getMetaTags(?string $language = null, $query = null): array
    {
        $model = $query ?? $this;
        $language = $language ?? $this->getCurrentLanguage();
        $model = $model->where('language','=',$language)->first();
        
        return $this->getMetaTagsArray($model);        
    }

    /**
     * Get meta tags field values
     *
     * @param Model|null $model
     * @param array|null $default
     * @return array
     */
    public function getMetaTagsArray($model = null, ?array $default = []): array
    {
        $model = $model ?? $this;

        $title = (empty($model->meta_title) == false) ? $model->meta_title : $default['title'] ?? '';
        $description = (empty($model->meta_description) == false) ? $model->meta_description : $default['description'] ?? '';
        $keywords = (empty($model->meta_keywords) == false) ? $model->meta_keywords : $default['keywords'] ?? '';

        return [
            'title'       => $title,
            'description' => $description,
            'keywords'    => $keywords
        ];
    }
}
