<?php

namespace Cx\Modules\Shop\Model\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CustomerGroup
 */
class CustomerGroup extends \Cx\Model\Base\EntityBase implements \Gedmo\Translatable\Translatable {
    /**
     * @var string
     */
    protected $locale;
    /**
     * @var integer
     */
    protected $id;

    /**
     * @var string
     */
    protected $name;

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
     * Set translatable locale
     *
     * @param $locale
     */
    public function setTranslatableLocale($locale)
    {
        $this->locale = $locale;
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
     * Add relDiscountGroups
     *
     * @param \Cx\Modules\Shop\Model\Entity\RelDiscountGroup $relDiscountGroups
     * @return CustomerGroup
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
