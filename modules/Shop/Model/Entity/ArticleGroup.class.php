<?php

namespace Cx\Modules\Shop\Model\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ArticleGroup
 */
class ArticleGroup extends \Cx\Model\Base\EntityBase {
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
     * @return ArticleGroup
     */
    public function addRelDiscountGroup(\Cx\Modules\Shop\Model\Entity\RelDiscountGroup $relDiscountGroups)
    {
        $this->relDiscountGroups[] = $relDiscountGroups;

        return $this;
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
