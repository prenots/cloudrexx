<?php

namespace Cx\Modules\Shop\Model\Entity;

use Doctrine\ORM\Mapping as ORM;

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
     * @return RelProductAttribute
     */
    public function setProductId($productId)
    {
        $this->productId = $productId;

        return $this;
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
     * @return RelProductAttribute
     */
    public function setOptionId($optionId)
    {
        $this->optionId = $optionId;

        return $this;
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
     * @return RelProductAttribute
     */
    public function setOrd($ord)
    {
        $this->ord = $ord;

        return $this;
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
     * @return RelProductAttribute
     */
    public function setProducts(\Cx\Modules\Shop\Model\Entity\Products $products = null)
    {
        $this->products = $products;

        return $this;
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
     * @return RelProductAttribute
     */
    public function setOption(\Cx\Modules\Shop\Model\Entity\Option $option = null)
    {
        $this->option = $option;

        return $this;
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
