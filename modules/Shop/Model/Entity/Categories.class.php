<?php

namespace Cx\Modules\Shop\Model\Entity;

/**
 * Categories
 */
class Categories extends \Cx\Model\Base\EntityBase {
    /**
     * @var integer
     */
    protected $id;

    /**
     * @var integer
     */
    protected $parentId;

    /**
     * @var integer
     */
    protected $ord;

    /**
     * @var boolean
     */
    protected $active;

    /**
     * @var string
     */
    protected $picture;

    /**
     * @var string
     */
    protected $flags;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    protected $pricelists;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    protected $products;

    /**
     * @var \Cx\Modules\Shop\Model\Entity\Categories
     */
    protected $category;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->pricelists = new \Doctrine\Common\Collections\ArrayCollection();
        $this->products = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set parentId
     *
     * @param integer $parentId
     */
    public function setParentId($parentId)
    {
        $this->parentId = $parentId;
    }

    /**
     * Get parentId
     *
     * @return integer 
     */
    public function getParentId()
    {
        return $this->parentId;
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
     * Set active
     *
     * @param boolean $active
     */
    public function setActive($active)
    {
        $this->active = $active;
    }

    /**
     * Get active
     *
     * @return boolean 
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * Set picture
     *
     * @param string $picture
     */
    public function setPicture($picture)
    {
        $this->picture = $picture;
    }

    /**
     * Get picture
     *
     * @return string 
     */
    public function getPicture()
    {
        return $this->picture;
    }

    /**
     * Set flags
     *
     * @param string $flags
     */
    public function setFlags($flags)
    {
        $this->flags = $flags;
    }

    /**
     * Get flags
     *
     * @return string 
     */
    public function getFlags()
    {
        return $this->flags;
    }

    /**
     * Add pricelists
     *
     * @param \Cx\Modules\Shop\Model\Entity\Pricelists $pricelists
     */
    public function addPricelist(\Cx\Modules\Shop\Model\Entity\Pricelists $pricelists)
    {
        $this->pricelists[] = $pricelists;
    }

    /**
     * Remove pricelists
     *
     * @param \Cx\Modules\Shop\Model\Entity\Pricelists $pricelists
     */
    public function removePricelist(\Cx\Modules\Shop\Model\Entity\Pricelists $pricelists)
    {
        $this->pricelists->removeElement($pricelists);
    }

    /**
     * Get pricelists
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPricelists()
    {
        return $this->pricelists;
    }

    /**
     * Add products
     *
     * @param \Cx\Modules\Shop\Model\Entity\Products $products
     */
    public function addProduct(\Cx\Modules\Shop\Model\Entity\Products $products)
    {
        $this->products[] = $products;
    }

    /**
     * Remove products
     *
     * @param \Cx\Modules\Shop\Model\Entity\Products $products
     */
    public function removeProduct(\Cx\Modules\Shop\Model\Entity\Products $products)
    {
        $this->products->removeElement($products);
    }

    /**
     * Get products
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getProducts()
    {
        return $this->products;
    }

    /**
     * Set parent category
     *
     * @param \Cx\Modules\Shop\Model\Entity\Categories $category
     */
    public function setCategory(\Cx\Modules\Shop\Model\Entity\Categories $category = null)
    {
        $this->category = $category;
    }

    /**
     * Get customer
     *
     * @return \Cx\Modules\Shop\Model\Entity\Categories
     */
    public function getCategory()
    {
        return $this->category;
    }
}
