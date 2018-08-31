<?php

namespace Cx\Modules\Shop\Model\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * RelCustomerCoupon
 */
class RelCustomerCoupon extends \Cx\Model\Base\EntityBase {
    /**
     * @var string
     */
    protected $code;

    /**
     * @var integer
     */
    protected $customerId;

    /**
     * @var integer
     */
    protected $orderId;

    /**
     * @var integer
     */
    protected $count;

    /**
     * @var string
     */
    protected $amount;

    /**
     * @var \Cx\Modules\Shop\Model\Entity\Orders
     */
    protected $orders;

    /**
     * @var \Cx\Core\User\Model\Entity\User
     */
    protected $customer;

    /**
     * Set code
     *
     * @param string $code
     * @return RelCustomerCoupon
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code
     *
     * @return string 
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set customerId
     *
     * @param integer $customerId
     * @return RelCustomerCoupon
     */
    public function setCustomerId($customerId)
    {
        $this->customerId = $customerId;

        return $this;
    }

    /**
     * Get customerId
     *
     * @return integer 
     */
    public function getCustomerId()
    {
        return $this->customerId;
    }

    /**
     * Set orderId
     *
     * @param integer $orderId
     * @return RelCustomerCoupon
     */
    public function setOrderId($orderId)
    {
        $this->orderId = $orderId;

        return $this;
    }

    /**
     * Get orderId
     *
     * @return integer 
     */
    public function getOrderId()
    {
        return $this->orderId;
    }

    /**
     * Set count
     *
     * @param integer $count
     * @return RelCustomerCoupon
     */
    public function setCount($count)
    {
        $this->count = $count;

        return $this;
    }

    /**
     * Get count
     *
     * @return integer 
     */
    public function getCount()
    {
        return $this->count;
    }

    /**
     * Set amount
     *
     * @param string $amount
     * @return RelCustomerCoupon
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * Get amount
     *
     * @return string 
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * Set orders
     *
     * @param \Cx\Modules\Shop\Model\Entity\Orders $orders
     * @return RelCustomerCoupon
     */
    public function setOrders(\Cx\Modules\Shop\Model\Entity\Orders $orders = null)
    {
        $this->orders = $orders;

        return $this;
    }

    /**
     * Get orders
     *
     * @return \Cx\Modules\Shop\Model\Entity\Orders 
     */
    public function getOrders()
    {
        return $this->orders;
    }

    /**
     * Set customer
     *
     * @param \Cx\Core\User\Model\Entity\User $customer
     * @return CustomerGroup
     */
    public function setCustomer(\Cx\Core\User\Model\Entity\User $customer = null)
    {
        $this->customer = $customer;

        return $this;
    }

    /**
     * Get customer
     *
     * @return \Cx\Core\User\Model\Entity\User
     */
    public function getCustomer()
    {
        return $this->customer;
    }
}
