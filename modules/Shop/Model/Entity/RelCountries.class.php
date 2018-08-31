<?php

namespace Cx\Modules\Shop\Model\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * RelCountries
 */
class RelCountries extends \Cx\Model\Base\EntityBase {
    /**
     * @var integer
     */
    protected $zoneId;

    /**
     * @var integer
     */
    protected $countryId;

    /**
     * @var \Cx\Modules\Shop\Model\Entity\Zones
     */
    protected $zones;


    /**
     * Set zoneId
     *
     * @param integer $zoneId
     * @return RelCountries
     */
    public function setZoneId($zoneId)
    {
        $this->zoneId = $zoneId;

        return $this;
    }

    /**
     * Get zoneId
     *
     * @return integer 
     */
    public function getZoneId()
    {
        return $this->zoneId;
    }

    /**
     * Set countryId
     *
     * @param integer $countryId
     * @return RelCountries
     */
    public function setCountryId($countryId)
    {
        $this->countryId = $countryId;

        return $this;
    }

    /**
     * Get countryId
     *
     * @return integer 
     */
    public function getCountryId()
    {
        return $this->countryId;
    }

    /**
     * Set zones
     *
     * @param \Cx\Modules\Shop\Model\Entity\Zones $zones
     * @return RelCountries
     */
    public function setZones(\Cx\Modules\Shop\Model\Entity\Zones $zones = null)
    {
        $this->zones = $zones;

        return $this;
    }

    /**
     * Get zones
     *
     * @return \Cx\Modules\Shop\Model\Entity\Zones 
     */
    public function getZones()
    {
        return $this->zones;
    }
}
