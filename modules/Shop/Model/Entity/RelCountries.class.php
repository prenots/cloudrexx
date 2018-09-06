<?php

namespace Cx\Modules\Shop\Model\Entity;

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
     */
    public function setZoneId($zoneId)
    {
        $this->zoneId = $zoneId;
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
     */
    public function setCountryId($countryId)
    {
        $this->countryId = $countryId;
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
     */
    public function setZones(\Cx\Modules\Shop\Model\Entity\Zones $zones = null)
    {
        $this->zones = $zones;
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
