<?php

namespace Cx\Modules\Shop\Model\Entity;

/**
 * Zones
 */
class Zones extends \Cx\Model\Base\EntityBase {
    /**
     * @var integer
     */
    protected $id;

    /**
     * @var boolean
     */
    protected $active;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    protected $relCountries;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    protected $payments;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    protected $shippers;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->relCountries = new \Doctrine\Common\Collections\ArrayCollection();
        $this->payments = new \Doctrine\Common\Collections\ArrayCollection();
        $this->shippers = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Add relCountries
     *
     * @param \Cx\Modules\Shop\Model\Entity\RelCountries $relCountries
     */
    public function addRelCountry(\Cx\Modules\Shop\Model\Entity\RelCountries $relCountries)
    {
        $this->relCountries[] = $relCountries;
    }

    /**
     * Remove relCountries
     *
     * @param \Cx\Modules\Shop\Model\Entity\RelCountries $relCountries
     */
    public function removeRelCountry(\Cx\Modules\Shop\Model\Entity\RelCountries $relCountries)
    {
        $this->relCountries->removeElement($relCountries);
    }

    /**
     * Get relCountries
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getRelCountries()
    {
        return $this->relCountries;
    }

    /**
     * Add payments
     *
     * @param \Cx\Modules\Shop\Model\Entity\Payment $payments
     */
    public function addPayment(\Cx\Modules\Shop\Model\Entity\Payment $payments)
    {
        $this->payments[] = $payments;
    }

    /**
     * Remove payments
     *
     * @param \Cx\Modules\Shop\Model\Entity\Payment $payments
     */
    public function removePayment(\Cx\Modules\Shop\Model\Entity\Payment $payments)
    {
        $this->payments->removeElement($payments);
    }

    /**
     * Get payments
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPayments()
    {
        return $this->payments;
    }

    /**
     * Add shippers
     *
     * @param \Cx\Modules\Shop\Model\Entity\Shipper $shippers
     */
    public function addShipper(\Cx\Modules\Shop\Model\Entity\Shipper $shippers)
    {
        $this->shippers[] = $shippers;
    }

    /**
     * Remove shippers
     *
     * @param \Cx\Modules\Shop\Model\Entity\Shipper $shippers
     */
    public function removeShipper(\Cx\Modules\Shop\Model\Entity\Shipper $shippers)
    {
        $this->shippers->removeElement($shippers);
    }

    /**
     * Get shippers
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getShippers()
    {
        return $this->shippers;
    }
}
