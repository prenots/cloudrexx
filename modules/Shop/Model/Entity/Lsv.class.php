<?php

namespace Cx\Modules\Shop\Model\Entity;

use Doctrine\ORM\Mapping as ORM;

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
     * @return Lsv
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
     * Set holder
     *
     * @param string $holder
     * @return Lsv
     */
    public function setHolder($holder)
    {
        $this->holder = $holder;

        return $this;
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
     * @return Lsv
     */
    public function setBank($bank)
    {
        $this->bank = $bank;

        return $this;
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
     * @return Lsv
     */
    public function setBlz($blz)
    {
        $this->blz = $blz;

        return $this;
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
     * @return Lsv
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
}
