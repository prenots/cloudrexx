<?php

namespace Cx\Modules\Shop\Model\Entity;

/**
 * Currencies
 */
class Currencies extends \Cx\Model\Base\EntityBase {
    /**
     * @var integer
     */
    protected $id;

    /**
     * @var string
     */
    protected $code;

    /**
     * @var string
     */
    protected $symbol;

    /**
     * @var string
     */
    protected $rate;

    /**
     * @var integer
     */
    protected $ord;

    /**
     * @var boolean
     */
    protected $active;

    /**
     * @var boolean
     */
    protected $default;

    /**
     * @var string
     */
    protected $increment;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    protected $orders;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->orders = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set code
     *
     * @param string $code
     */
    public function setCode($code)
    {
        $this->code = $code;
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
     * Set symbol
     *
     * @param string $symbol
     */
    public function setSymbol($symbol)
    {
        $this->symbol = $symbol;
    }

    /**
     * Get symbol
     *
     * @return string 
     */
    public function getSymbol()
    {
        return $this->symbol;
    }

    /**
     * Set rate
     *
     * @param string $rate
     */
    public function setRate($rate)
    {
        $this->rate = $rate;
    }

    /**
     * Get rate
     *
     * @return string 
     */
    public function getRate()
    {
        return $this->rate;
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
     * Set default
     *
     * @param boolean $default
     */
    public function setDefault($default)
    {
        $this->default = $default;
    }

    /**
     * Get default
     *
     * @return boolean 
     */
    public function getDefault()
    {
        return $this->default;
    }

    /**
     * Set increment
     *
     * @param string $increment
     */
    public function setIncrement($increment)
    {
        $this->increment = $increment;
    }

    /**
     * Get increment
     *
     * @return string 
     */
    public function getIncrement()
    {
        return $this->increment;
    }

    /**
     * Add orders
     *
     * @param \Cx\Modules\Shop\Model\Entity\Orders $orders
     */
    public function addOrder(\Cx\Modules\Shop\Model\Entity\Orders $orders)
    {
        $this->orders[] = $orders;
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
}
