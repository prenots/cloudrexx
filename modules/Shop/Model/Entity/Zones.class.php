<?php

namespace Cx\Modules\Shop\Model\Entity;

use Doctrine\ORM\Mapping as ORM;

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
     * @var string
     */
    protected $name;

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
     * @return Zones
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
     * Add relCountries
     *
     * @param \Cx\Modules\Shop\Model\Entity\RelCountries $relCountries
     * @return Zones
     */
    public function addRelCountry(\Cx\Modules\Shop\Model\Entity\RelCountries $relCountries)
    {
        $this->relCountries[] = $relCountries;

        return $this;
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
     * @return Zones
     */
    public function addPayment(\Cx\Modules\Shop\Model\Entity\Payment $payments)
    {
        $this->payments[] = $payments;

        return $this;
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
     * @return Zones
     */
    public function addShipper(\Cx\Modules\Shop\Model\Entity\Shipper $shippers)
    {
        $this->shippers[] = $shippers;

        return $this;
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
