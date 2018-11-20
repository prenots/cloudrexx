<?php

namespace Cx\Modules\Shop\Model\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Payment
 */
class Payment extends \Cx\Model\Base\EntityBase implements \Gedmo\Translatable\Translatable {
    /**
     * @var string
     */
    protected $locale;
    /**
     * @var integer
     */
    protected $id;

    /**
     * @var integer
     */
    protected $processorId;

    /**
     * @var string
     */
    protected $fee;

    /**
     * @var string
     */
    protected $freeFrom;

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
    protected $name;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    protected $discountCoupons;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    protected $orders;

    /**
     * @var \Cx\Modules\Shop\Model\Entity\PaymentProcessors
     */
    protected $paymentProcessors;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    protected $zones;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->discountCoupons = new \Doctrine\Common\Collections\ArrayCollection();
        $this->orders = new \Doctrine\Common\Collections\ArrayCollection();
        $this->zones = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set translatable locale
     *
     * @param $locale
     */
    public function setTranslatableLocale($locale)
    {
        $this->locale = $locale;
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
     * Set processorId
     *
     * @param integer $processorId
     * @return Payment
     */
    public function setProcessorId($processorId)
    {
        $this->processorId = $processorId;

        return $this;
    }

    /**
     * Get processorId
     *
     * @return integer 
     */
    public function getProcessorId()
    {
        return $this->processorId;
    }

    /**
     * Set fee
     *
     * @param string $fee
     * @return Payment
     */
    public function setFee($fee)
    {
        $this->fee = $fee;

        return $this;
    }

    /**
     * Get fee
     *
     * @return string 
     */
    public function getFee()
    {
        return $this->fee;
    }

    /**
     * Set freeFrom
     *
     * @param string $freeFrom
     * @return Payment
     */
    public function setFreeFrom($freeFrom)
    {
        $this->freeFrom = $freeFrom;

        return $this;
    }

    /**
     * Get freeFrom
     *
     * @return string 
     */
    public function getFreeFrom()
    {
        return $this->freeFrom;
    }

    /**
     * Set ord
     *
     * @param integer $ord
     * @return Payment
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
     * Set active
     *
     * @param boolean $active
     * @return Payment
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
     * Set name
     *
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Add discountCoupons
     *
     * @param \Cx\Modules\Shop\Model\Entity\DiscountCoupon $discountCoupons
     * @return Payment
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
     * Add orders
     *
     * @param \Cx\Modules\Shop\Model\Entity\Orders $orders
     * @return Payment
     */
    public function addOrder(\Cx\Modules\Shop\Model\Entity\Orders $orders)
    {
        $this->orders[] = $orders;

        return $this;
    }

    /**
     * Remove orders
     *
     * @param \Cx\Modules\Shop\Model\Entity\Orders $orders
     */
    public function removeOrder(\Cx\Modules\Shop\Model\Entity\Orders $orders)
    {
        $this->orders->removeElement($orders);
    }

    /**
     * Get orders
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getOrders()
    {
        return $this->orders;
    }

    /**
     * Set paymentProcessors
     *
     * @param \Cx\Modules\Shop\Model\Entity\PaymentProcessors $paymentProcessors
     * @return Payment
     */
    public function setPaymentProcessors(\Cx\Modules\Shop\Model\Entity\PaymentProcessors $paymentProcessors = null)
    {
        $this->paymentProcessors = $paymentProcessors;

        return $this;
    }

    /**
     * Get paymentProcessors
     *
     * @return \Cx\Modules\Shop\Model\Entity\PaymentProcessors 
     */
    public function getPaymentProcessors()
    {
        return $this->paymentProcessors;
    }

    /**
     * Add zones
     *
     * @param \Cx\Modules\Shop\Model\Entity\Zones $zones
     * @return Payment
     */
    public function addZone(\Cx\Modules\Shop\Model\Entity\Zones $zones)
    {
        $this->zones[] = $zones;

        return $this;
    }

    /**
     * Remove zones
     *
     * @param \Cx\Modules\Shop\Model\Entity\Zones $zones
     */
    public function removeZone(\Cx\Modules\Shop\Model\Entity\Zones $zones)
    {
        $this->zones->removeElement($zones);
    }

    /**
     * Get zones
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getZones()
    {
        return $this->zones;
    }
}
