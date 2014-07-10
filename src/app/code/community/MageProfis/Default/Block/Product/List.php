<?php

class MageProfis_Default_Block_Product_List
extends Mage_Catalog_Block_Product_Abstract
{

    protected $_product_count = 16;

    /**
     * 
     */
    protected function _construct()
    {
        parent::_construct();
        $this->addData(array('cache_lifetime' => 86400)); // 1 day
        $this->addCacheTag(array(
            Mage_Core_Model_Store::CACHE_TAG,
            Mage_Catalog_Model_Category::CACHE_TAG,
            Mage_Catalog_Model_Product::CACHE_TAG
        ));
    }

    /**
     * 
     * @return array
     */
    public function getCacheKeyInfo()
    {
        return array(
            'LOE_DEFAULT_PRODUCT_LIST',
            Mage::app()->getStore()->getCode(),
            $this->getTemplateFile(),
            'template' => $this->getTemplate(),
            $this->getCategoryId()
        );
    }
    
    /**
     * Retrieve loaded category collection
     *
     * @return Mage_Catalog_Model_Resource_Product_Collection
     */
    protected function _getProductCollection()
    {
        if (is_null($this->_productCollection)) {
            $collection = Mage::getModel('catalog/product')->getCollection()
                    ->addAttributeToSelect(Mage::getSingleton('catalog/config')
                            ->getProductAttributes())
                    ->addMinimalPrice()
                    ->addFinalPrice()
                    ->addTaxPercents()
                    ->addUrlRewrite(0)
                    ->setPage(1, $this->getProductCount());
            /* @var $collection Mage_Catalog_Model_Resource_Product_Collection */
            $this->_addFilterToCollection($collection);
            $this->_productCollection = $collection;
        }
        return $this->_productCollection;
    }

    /**
     * add Filter to Collection
     * 
     * @param Mage_Catalog_Model_Resource_Product_Collection $collection
     * @return Mage_Catalog_Model_Resource_Product_Collection
     */
    protected function _addFilterToCollection($collection)
    {
        Mage::getSingleton('catalog/product_status')
                ->addVisibleFilterToCollection($collection);
        Mage::getSingleton('catalog/product_visibility')
                ->addVisibleInCatalogFilterToCollection($collection);
        $category = Mage::getModel('catalog/category')->load($this->getCategoryId());
        /* @var $category Mage_Catalog_Model_Category */
        if($category->getId())
        {
            $collection->addCategoryFilter($category);
            $collection->addAttributeToSort($category->getDefaultSortBy(), 'ASC');
        }
        return $collection;
    }
    
    /**
     * alias for setProductCount
     * 
     * @param int $limit
     * @return MageProfis_Default_Block_Product_List
     */
    public function setLimit($limit)
    {
        return $this->setProductCount($limit);
    }

    /**
     * set Product limit
     * 
     * @param int $count
     * @return MageProfis_Default_Block_Product_List
     */
    public function setProductCount($count)
    {
        $this->_product_count = intval($count);
        return $this;
    }

    /**
     * get Product Count / Limit
     * 
     * @return int
     */
    public function getProductCount()
    {
        return $this->_product_count;
    }

    /**
     * set Category Id
     * 
     * @param int $id
     * @return MageProfis_Default_Block_Product_List
     */
    public function setCategoryId($id)
    {
        $this->addCacheTag(Mage_Catalog_Model_Category::CACHE_TAG.'_'.$id);
        $this->_category_id = intval($id);
        return $this;
    }

    /**
     * get Category Id
     * 
     * @return int
     */
    public function getCategoryId()
    {
        return $this->getCategoryId();
    }
}