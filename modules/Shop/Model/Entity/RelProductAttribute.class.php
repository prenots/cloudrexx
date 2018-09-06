<?php

namespace Cx\Modules\Shop\Model\Entity;

/**
 * RelProductAttribute
 */
class RelProductAttribute extends \Cx\Model\Base\EntityBase {
    /**
     * @var integer
     */
    protected $productId;

    /**
     * @var integer
     */
    protected $optionId;

    /**
     * @var integer
     */
    protected $ord;

    /**
     * @var \Cx\Modules\Shop\Model\Entity\Products
     */
    protected $products;

    /**
     * @var \Cx\Modules\Shop\Model\Entity\Option
     */
    protected $option;


    /**
     * Set productId
     *
     * @param integer $productId
     */
    public function setProductId($productId)
    {
        $this->productId = $productId;
    }

    /**
     * Get productId
     *
     * @return integer 
     */
    public function getProductId()
    {
        return $this->productId;
    }

    /**
     * Set optionId
     *
     * @param integer $optionId
     */
    public function setOptionId($optionId)
    {
        $this->optionId = $optionId;
    }

    /**
     * Get optionId
     *
     * @return integer 
     */
    public function getOptionId()
    {
        return $this->optionId;
    }

    /**
     * Set ord
     *
     * @param integer $ord
     */
    public function setOrd($ord)
    {
        $this->ord = $ord;
    }

    /**
     * Get ord
     *
     * @return integer 
     */
    public function getOrd()
    {
        return $this->ord;
    }

    /**
     * Set products
     *
     * @param \Cx\Modules\Shop\Model\Entity\Products $products
     */
    public function setProducts(\Cx\Modules\Shop\Model\Entity\Products $products = null)
    {
        $this->products = $products;
    }

    /**
     * Get products
     *
     * @return \Cx\Modules\Shop\Model\Entity\Products 
     */
    public function getProducts()
    {
        return $this->products;
    }

    /**
     * Set option
     *
     * @param \Cx\Modules\Shop\Model\Entity\Option $option
     */
    public function setOption(\Cx\Modules\Shop\Model\Entity\Option $option = null)
    {
        $this->option = $option;
    }

    /**
     * Get option
     *
     * @return \Cx\Modules\Shop\Model\Entity\Option 
     */
    public function getOption()
    {
        return $this->option;
    }
}
