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
     * @var string
     */
    protected $name;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    protected $relDiscountGroups;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    protected $products;

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

    /**
     * Add products
     *
     * @param \Cx\Modules\Shop\Model\Entity\Products $products
     */
    public function addProducts(\Cx\Modules\Shop\Model\Entity\Products $products)
    {
        $this->products[] = $products;
    }

    /**
     * Remove products
     *
     * @param \Cx\Modules\Shop\Model\Entity\Products $products
     */
    public function removeProduct(\Cx\Modules\Shop\Model\Entity\Products $product)
    {
        $this->products->removeElement($product);
    }

    /**
     * Get products
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getProducts()
    {
        return $this->products;
    }
}
