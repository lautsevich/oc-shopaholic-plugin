<?php namespace Lovata\Shopaholic\Components;

use Event;
use Lovata\Toolbox\Traits\Helpers\TraitComponentNotFoundResponse;
use Cms\Classes\ComponentBase;
use Lovata\Shopaholic\Models\Category;

/**
 * Class CategoryPage
 * @package Lovata\Shopaholic\Components
 * @author Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class CategoryPage extends ComponentBase
{
    use TraitComponentNotFoundResponse;

    /** @var null|Category */
    protected $obCategory = null;

    /**
     * @return array
     */
    public function componentDetails()
    {
        return [
            'name'          => 'lovata.shopaholic::lang.component.category_page_name',
            'description'   => 'lovata.shopaholic::lang.component.category_page_description',
        ];
    }

    /**
     * @return array
     */
    public function defineProperties()
    {
        $arProperties = $this->getElementPageProperties();
        return $arProperties;
    }

    /**
     * @return \Illuminate\Http\Response|null
     */
    public function onRun()
    {
        $sCategorySlug =  $this->property('slug');
        if(empty($sCategorySlug)) {
            return $this->getErrorResponse();
        }

        /** @var Category $obCategory */
        $obCategory = Category::active()->getBySlug($sCategorySlug)->first();
        if(empty($obCategory)) {
            return $this->getErrorResponse();
        }
        
        $this->obCategory = $obCategory;

        //Send event
        Event::fire('shopaholic.category.open', [$obCategory]);

        return null;
    }

    /**
     * Get Category data with children
     * @return array
     */
    public function get()
    {
        if(empty($this->obCategory)) {
            return null;
        }

        return Category::getCacheData($this->obCategory->id, $this->obCategory);
    }
}