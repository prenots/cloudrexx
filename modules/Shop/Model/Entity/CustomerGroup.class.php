<?php

namespace Cx\Modules\Shop\Model\Entity;

/**
 * CustomerGroup
 */
class CustomerGroup extends \Cx\Model\Base\EntityBase {
    /**
     * @var integer
     */
    protected $id;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    protected $relDiscountGroups;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->relDiscountGroups = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Add relDiscountGroups
     *
     * @param \Cx\Modules\Shop\Model\Entity\RelDiscountGroup $relDiscountGroups
     */
    public function addRelDiscountGroup(\Cx\Modules\Shop\Model\Entity\RelDiscountGroup $relDiscountGroups)
    {
        $this->relDiscountGroups[] = $relDiscountGroups;
    }

    /**
     * Remove relDiscountGroups
     *
     * @param \Cx\Modules\Shop\Model\Entity\RelDiscountGroup $relDiscountGroups
     */
    public function removeRelDiscountGroup(\Cx\Modules\Shop\Model\Entity\RelDiscountGroup $relDiscountGroups)
    {
        $this->relDiscountGroups->removeElement($relDiscountGroups);
    }

    /**
     * Get relDiscountGroups
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getRelDiscountGroups()
    {
        return $this->relDiscountGroups;
    }
}
