<?php
namespace OM\StaticSubcategories\Block;

class Subcategories extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magento\Framework\Registry
     */
    protected $_registry;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * Vertnav constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $collectionFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $collectionFactory,
        array $data
    ) {
        $this->_registry = $registry;
        $this->_collectionFactory = $collectionFactory;
        parent::__construct($context, $data);
    }

    /**
     * @return mixed
     */
    public function getCurrentCategory()
    {
        return $this->_registry->registry('current_category');
    }

    /**
     * @return bool|int
     */
    public function getParentId()
    {
        $current = $this->getCurrentCategory();

        if (!$current) {
            return false;
        }

        if ($current->getLevel() == 2) {
            return $current->getId();
        }

        if ($current->getLevel() == 3) {
            return $current->getParentId();
        }
    }

    /**
     * @param $category \Magento\Catalog\Model\Category
     * @return bool
     */
    public function isCurrent($category)
    {
        return ($category->getId() == $this->getCurrentCategory()->getId() ? true : false);
    }

    /**
     * @return bool|\Magento\Catalog\Model\ResourceModel\Category\Collection
     */
    public function getCategories()
    {
        $parent_id = $this->getParentId();

        if (!$parent_id) {
            return false;
        }

        $collection = $this->_collectionFactory->create();
        $collection
            ->addAttributeToSelect('*')
            ->addAttributeToFilter('parent_id', $parent_id)
            ->addIsActiveFilter()
        ;

        return $collection;
    }

    /**
     * @param $category
     * @return mixed
     */
    public function getCount($category)
    {
        return $category->getProductCollection()->Count();
    }

    /**
     * @param $category \Magento\Catalog\Model\Category
     * @return string
     */
    public function getClasses($category)
    {
        $classes = array();
        $classes[] = 'level' . $category->getLevel();

        if ($this->isCurrent($category)) {
            $classes[] = 'active';
        }

        //level0-inactive level0 inactive

        return implode(' ', $classes);
    }
}