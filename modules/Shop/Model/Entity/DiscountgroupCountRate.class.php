<?php

namespace Cx\Modules\Shop\Model\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * DiscountgroupCountRate
 */
class DiscountgroupCountRate extends \Cx\Model\Base\EntityBase {
    /**
     * @var integer
     */
    protected $groupId;

    /**
     * @var integer
     */
    protected $count;

    /**
     * @var string
     */
    protected $rate;

    /**
     * @var \Cx\Modules\Shop\Model\Entity\DiscountgroupCountName
     */
    protected $discountgroupCountName;


    /**
     * Set groupId
     *
     * @param integer $groupId
     * @return DiscountgroupCountRate
     */
    public function setGroupId($groupId)
    {
        $this->groupId = $groupId;

        return $this;
    }

    /**
     * Get groupId
     *
     * @return integer 
     */
    public function getGroupId()
    {
        return $this->groupId;
    }

    /**
     * Set count
     *
     * @param integer $count
     * @return DiscountgroupCountRate
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
     * Set rate
     *
     * @param string $rate
     * @return DiscountgroupCountRate
     */
    public function setRate($rate)
    {
        $this->rate = $rate;

        return $this;
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
     * Set discountgroupCountName
     *
     * @param \Cx\Modules\Shop\Model\Entity\DiscountgroupCountName $discountgroupCountName
     * @return DiscountgroupCountRate
     */
    public function setDiscountgroupCountName(\Cx\Modules\Shop\Model\Entity\DiscountgroupCountName $discountgroupCountName = null)
    {
        $this->discountgroupCountName = $discountgroupCountName;

        return $this;
    }

    /**
     * Get discountgroupCountName
     *
     * @return \Cx\Modules\Shop\Model\Entity\DiscountgroupCountName 
     */
    public function getDiscountgroupCountName()
    {
        return $this->discountgroupCountName;
    }
}
