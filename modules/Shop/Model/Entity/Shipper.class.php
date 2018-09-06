<?php

namespace Cx\Modules\Shop\Model\Entity;

/**
 * Shipper
 */
class Shipper extends \Cx\Model\Base\EntityBase {
    /**
     * @var integer
     */
    protected $id;

    /**
     * @var boolean
     */
    protected $active;

    /**
     * @var integer
     */
    protected $ord;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    protected $orders;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    protected $shipmentCosts;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    protected $zones;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->orders = new \Doctrine\Common\Collections\ArrayCollection();
        $this->shipmentCosts = new \Doctrine\Common\Collections\ArrayCollection();
        $this->zones = new \Doctrine\Common\Collections\ArrayCollection();
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

    /**
     * Add shipmentCosts
     *
     * @param \Cx\Modules\Shop\Model\Entity\ShipmentCost $shipmentCosts
     */
    public function addShipmentCost(\Cx\Modules\Shop\Model\Entity\ShipmentCost $shipmentCosts)
    {
        $this->shipmentCosts[] = $shipmentCosts;
    }

    /**
     * Remove shipmentCosts
     *
     * @param \Cx\Modules\Shop\Model\Entity\ShipmentCost $shipmentCosts
     */
    public function removeShipmentCost(\Cx\Modules\Shop\Model\Entity\ShipmentCost $shipmentCosts)
    {
        $this->shipmentCosts->removeElement($shipmentCosts);
    }

    /**
     * Get shipmentCosts
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getShipmentCosts()
    {
        return $this->shipmentCosts;
    }

    /**
     * Add zones
     *
     * @param \Cx\Modules\Shop\Model\Entity\Zones $zones
     */
    public function addZone(\Cx\Modules\Shop\Model\Entity\Zones $zones)
    {
        $this->zones[] = $zones;
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
