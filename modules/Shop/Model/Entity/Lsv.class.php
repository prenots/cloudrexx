<?php

namespace Cx\Modules\Shop\Model\Entity;

/**
 * Lsv
 */
class Lsv extends \Cx\Model\Base\EntityBase {
    /**
     * @var integer
     */
    protected $orderId;

    /**
     * @var string
     */
    protected $holder;

    /**
     * @var string
     */
    protected $bank;

    /**
     * @var string
     */
    protected $blz;

    /**
     * @var \Cx\Modules\Shop\Model\Entity\Orders
     */
    protected $orders;


    /**
     * Set orderId
     *
     * @param integer $orderId
     */
    public function setOrderId($orderId)
    {
        $this->orderId = $orderId;
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
     * Set holder
     *
     * @param string $holder
     */
    public function setHolder($holder)
    {
        $this->holder = $holder;
    }

    /**
     * Get holder
     *
     * @return string 
     */
    public function getHolder()
    {
        return $this->holder;
    }

    /**
     * Set bank
     *
     * @param string $bank
     */
    public function setBank($bank)
    {
        $this->bank = $bank;
    }

    /**
     * Get bank
     *
     * @return string 
     */
    public function getBank()
    {
        return $this->bank;
    }

    /**
     * Set blz
     *
     * @param string $blz
     */
    public function setBlz($blz)
    {
        $this->blz = $blz;
    }

    /**
     * Get blz
     *
     * @return string 
     */
    public function getBlz()
    {
        return $this->blz;
    }

    /**
     * Set orders
     *
     * @param \Cx\Modules\Shop\Model\Entity\Orders $orders
     */
    public function setOrders(\Cx\Modules\Shop\Model\Entity\Orders $orders = null)
    {
        $this->orders = $orders;
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
}
