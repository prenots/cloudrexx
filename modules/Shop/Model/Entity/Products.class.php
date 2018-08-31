<?php

namespace Cx\Modules\Shop\Model\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Products
 */
class Products extends \Cx\Model\Base\EntityBase {
    /**
     * @var integer
     */
    protected $id;

    /**
     * @var string
     */
    protected $picture;

    /**
     * @var string
     */
    protected $distribution;

    /**
     * @var string
     */
    protected $normalprice;

    /**
     * @var string
     */
    protected $resellerprice;

    /**
     * @var integer
     */
    protected $stock;

    /**
     * @var boolean
     */
    protected $stockVisible;

    /**
     * @var string
     */
    protected $discountprice;

    /**
     * @var boolean
     */
    protected $discountActive;

    /**
     * @var boolean
     */
    protected $active;

    /**
     * @var boolean
     */
    protected $b2b;

    /**
     * @var boolean
     */
    protected $b2c;

    /**
     * @var \DateTime
     */
    protected $dateStart;

    /**
     * @var \DateTime
     */
    protected $dateEnd;

    /**
     * @var integer
     */
    protected $manufacturerId;

    /**
     * @var integer
     */
    protected $ord;

    /**
     * @var integer
     */
    protected $vatId;

    /**
     * @var integer
     */
    protected $weight;

    /**
     * @var string
     */
    protected $flags;

    /**
     * @var integer
     */
    protected $groupId;

    /**
     * @var integer
     */
    protected $articleId;

    /**
     * @var string
     */
    protected $usergroupIds;

    /**
     * @var integer
     */
    protected $minimumOrderQuantity;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    protected $discountCoupons;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    protected $orderItems;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    protected $relProductAttributes;

    /**
     * @var \Cx\Modules\Shop\Model\Entity\Manufacturer
     */
    protected $manufacturer;

    /**
     * @var \Cx\Modules\Shop\Model\Entity\Vat
     */
    protected $vat;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    protected $categories;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->discountCoupons = new \Doctrine\Common\Collections\ArrayCollection();
        $this->orderItems = new \Doctrine\Common\Collections\ArrayCollection();
        $this->relProductAttributes = new \Doctrine\Common\Collections\ArrayCollection();
        $this->categories = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set picture
     *
     * @param string $picture
     * @return Products
     */
    public function setPicture($picture)
    {
        $this->picture = $picture;

        return $this;
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
     * Set distribution
     *
     * @param string $distribution
     * @return Products
     */
    public function setDistribution($distribution)
    {
        $this->distribution = $distribution;

        return $this;
    }

    /**
     * Get distribution
     *
     * @return string 
     */
    public function getDistribution()
    {
        return $this->distribution;
    }

    /**
     * Set normalprice
     *
     * @param string $normalprice
     * @return Products
     */
    public function setNormalprice($normalprice)
    {
        $this->normalprice = $normalprice;

        return $this;
    }

    /**
     * Get normalprice
     *
     * @return string 
     */
    public function getNormalprice()
    {
        return $this->normalprice;
    }

    /**
     * Set resellerprice
     *
     * @param string $resellerprice
     * @return Products
     */
    public function setResellerprice($resellerprice)
    {
        $this->resellerprice = $resellerprice;

        return $this;
    }

    /**
     * Get resellerprice
     *
     * @return string 
     */
    public function getResellerprice()
    {
        return $this->resellerprice;
    }

    /**
     * Set stock
     *
     * @param integer $stock
     * @return Products
     */
    public function setStock($stock)
    {
        $this->stock = $stock;

        return $this;
    }

    /**
     * Get stock
     *
     * @return integer 
     */
    public function getStock()
    {
        return $this->stock;
    }

    /**
     * Set stockVisible
     *
     * @param boolean $stockVisible
     * @return Products
     */
    public function setStockVisible($stockVisible)
    {
        $this->stockVisible = $stockVisible;

        return $this;
    }

    /**
     * Get stockVisible
     *
     * @return boolean 
     */
    public function getStockVisible()
    {
        return $this->stockVisible;
    }

    /**
     * Set discountprice
     *
     * @param string $discountprice
     * @return Products
     */
    public function setDiscountprice($discountprice)
    {
        $this->discountprice = $discountprice;

        return $this;
    }

    /**
     * Get discountprice
     *
     * @return string 
     */
    public function getDiscountprice()
    {
        return $this->discountprice;
    }

    /**
     * Set discountActive
     *
     * @param boolean $discountActive
     * @return Products
     */
    public function setDiscountActive($discountActive)
    {
        $this->discountActive = $discountActive;

        return $this;
    }

    /**
     * Get discountActive
     *
     * @return boolean 
     */
    public function getDiscountActive()
    {
        return $this->discountActive;
    }

    /**
     * Set active
     *
     * @param boolean $active
     * @return Products
     */
    public function setActive($active)
    {
        $this->active = $active;

        return $this;
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
     * Set b2b
     *
     * @param boolean $b2b
     * @return Products
     */
    public function setB2b($b2b)
    {
        $this->b2b = $b2b;

        return $this;
    }

    /**
     * Get b2b
     *
     * @return boolean 
     */
    public function getB2b()
    {
        return $this->b2b;
    }

    /**
     * Set b2c
     *
     * @param boolean $b2c
     * @return Products
     */
    public function setB2c($b2c)
    {
        $this->b2c = $b2c;

        return $this;
    }

    /**
     * Get b2c
     *
     * @return boolean 
     */
    public function getB2c()
    {
        return $this->b2c;
    }

    /**
     * Set dateStart
     *
     * @param \DateTime $dateStart
     * @return Products
     */
    public function setDateStart($dateStart)
    {
        $this->dateStart = $dateStart;

        return $this;
    }

    /**
     * Get dateStart
     *
     * @return \DateTime 
     */
    public function getDateStart()
    {
        return $this->dateStart;
    }

    /**
     * Set dateEnd
     *
     * @param \DateTime $dateEnd
     * @return Products
     */
    public function setDateEnd($dateEnd)
    {
        $this->dateEnd = $dateEnd;

        return $this;
    }

    /**
     * Get dateEnd
     *
     * @return \DateTime 
     */
    public function getDateEnd()
    {
        return $this->dateEnd;
    }

    /**
     * Set manufacturerId
     *
     * @param integer $manufacturerId
     * @return Products
     */
    public function setManufacturerId($manufacturerId)
    {
        $this->manufacturerId = $manufacturerId;

        return $this;
    }

    /**
     * Get manufacturerId
     *
     * @return integer 
     */
    public function getManufacturerId()
    {
        return $this->manufacturerId;
    }

    /**
     * Set ord
     *
     * @param integer $ord
     * @return Products
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
     * Set vatId
     *
     * @param integer $vatId
     * @return Products
     */
    public function setVatId($vatId)
    {
        $this->vatId = $vatId;

        return $this;
    }

    /**
     * Get vatId
     *
     * @return integer 
     */
    public function getVatId()
    {
        return $this->vatId;
    }

    /**
     * Set weight
     *
     * @param integer $weight
     * @return Products
     */
    public function setWeight($weight)
    {
        $this->weight = $weight;

        return $this;
    }

    /**
     * Get weight
     *
     * @return integer 
     */
    public function getWeight()
    {
        return $this->weight;
    }

    /**
     * Set flags
     *
     * @param string $flags
     * @return Products
     */
    public function setFlags($flags)
    {
        $this->flags = $flags;

        return $this;
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
     * Set groupId
     *
     * @param integer $groupId
     * @return Products
     */
    public function setGroupId($groupId)
    {
        $this->groupId = $groupId;

        return $this;
    }

    /**
     * Get groupId
     *
     * @return integer 
     */
    public function getGroupId()
    {
        return $this->groupId;
    }

    /**
     * Set articleId
     *
     * @param integer $articleId
     * @return Products
     */
    public function setArticleId($articleId)
    {
        $this->articleId = $articleId;

        return $this;
    }

    /**
     * Get articleId
     *
     * @return integer 
     */
    public function getArticleId()
    {
        return $this->articleId;
    }

    /**
     * Set usergroupIds
     *
     * @param string $usergroupIds
     * @return Products
     */
    public function setUsergroupIds($usergroupIds)
    {
        $this->usergroupIds = $usergroupIds;

        return $this;
    }

    /**
     * Get usergroupIds
     *
     * @return string 
     */
    public function getUsergroupIds()
    {
        return $this->usergroupIds;
    }

    /**
     * Set minimumOrderQuantity
     *
     * @param integer $minimumOrderQuantity
     * @return Products
     */
    public function setMinimumOrderQuantity($minimumOrderQuantity)
    {
        $this->minimumOrderQuantity = $minimumOrderQuantity;

        return $this;
    }

    /**
     * Get minimumOrderQuantity
     *
     * @return integer 
     */
    public function getMinimumOrderQuantity()
    {
        return $this->minimumOrderQuantity;
    }

    /**
     * Add discountCoupons
     *
     * @param \Cx\Modules\Shop\Model\Entity\DiscountCoupon $discountCoupons
     * @return Products
     */
    public function addDiscountCoupon(\Cx\Modules\Shop\Model\Entity\DiscountCoupon $discountCoupons)
    {
        $this->discountCoupons[] = $discountCoupons;

        return $this;
    }

    /**
     * Remove discountCoupons
     *
     * @param \Cx\Modules\Shop\Model\Entity\DiscountCoupon $discountCoupons
     */
    public function removeDiscountCoupon(\Cx\Modules\Shop\Model\Entity\DiscountCoupon $discountCoupons)
    {
        $this->discountCoupons->removeElement($discountCoupons);
    }

    /**
     * Get discountCoupons
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getDiscountCoupons()
    {
        return $this->discountCoupons;
    }

    /**
     * Add orderItems
     *
     * @param \Cx\Modules\Shop\Model\Entity\OrderItems $orderItems
     * @return Products
     */
    public function addOrderItem(\Cx\Modules\Shop\Model\Entity\OrderItems $orderItems)
    {
        $this->orderItems[] = $orderItems;

        return $this;
    }

    /**
     * Remove orderItems
     *
     * @param \Cx\Modules\Shop\Model\Entity\OrderItems $orderItems
     */
    public function removeOrderItem(\Cx\Modules\Shop\Model\Entity\OrderItems $orderItems)
    {
        $this->orderItems->removeElement($orderItems);
    }

    /**
     * Get orderItems
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getOrderItems()
    {
        return $this->orderItems;
    }

    /**
     * Add relProductAttributes
     *
     * @param \Cx\Modules\Shop\Model\Entity\RelProductAttribute $relProductAttributes
     * @return Products
     */
    public function addRelProductAttribute(\Cx\Modules\Shop\Model\Entity\RelProductAttribute $relProductAttributes)
    {
        $this->relProductAttributes[] = $relProductAttributes;

        return $this;
    }

    /**
     * Remove relProductAttributes
     *
     * @param \Cx\Modules\Shop\Model\Entity\RelProductAttribute $relProductAttributes
     */
    public function removeRelProductAttribute(\Cx\Modules\Shop\Model\Entity\RelProductAttribute $relProductAttributes)
    {
        $this->relProductAttributes->removeElement($relProductAttributes);
    }

    /**
     * Get relProductAttributes
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getRelProductAttributes()
    {
        return $this->relProductAttributes;
    }

    /**
     * Set manufacturer
     *
     * @param \Cx\Modules\Shop\Model\Entity\Manufacturer $manufacturer
     * @return Products
     */
    public function setManufacturer(\Cx\Modules\Shop\Model\Entity\Manufacturer $manufacturer = null)
    {
        $this->manufacturer = $manufacturer;

        return $this;
    }

    /**
     * Get manufacturer
     *
     * @return \Cx\Modules\Shop\Model\Entity\Manufacturer 
     */
    public function getManufacturer()
    {
        return $this->manufacturer;
    }

    /**
     * Set vat
     *
     * @param \Cx\Modules\Shop\Model\Entity\Vat $vat
     * @return Products
     */
    public function setVat(\Cx\Modules\Shop\Model\Entity\Vat $vat = null)
    {
        $this->vat = $vat;

        return $this;
    }

    /**
     * Get vat
     *
     * @return \Cx\Modules\Shop\Model\Entity\Vat 
     */
    public function getVat()
    {
        return $this->vat;
    }

    /**
     * Add categories
     *
     * @param \Cx\Modules\Shop\Model\Entity\Categories $categories
     * @return Products
     */
    public function addCategory(\Cx\Modules\Shop\Model\Entity\Categories $categories)
    {
        $this->categories[] = $categories;

        return $this;
    }

    /**
     * Remove categories
     *
     * @param \Cx\Modules\Shop\Model\Entity\Categories $categories
     */
    public function removeCategory(\Cx\Modules\Shop\Model\Entity\Categories $categories)
    {
        $this->categories->removeElement($categories);
    }

    /**
     * Get categories
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getCategories()
    {
        return $this->categories;
    }
}
