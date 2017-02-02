<?php

namespace Cx\Modules\Block\Model\Entity;

/**
 * Cx\Modules\Block\Model\Entity\Category
 */
class Category extends \Cx\Model\Base\EntityBase
{
    /**
     * @var integer $id
     */
    protected $id;

    /**
     * @var string $name
     */
    protected $name;

    /**
     * @var string $seperator
     */
    protected $seperator;

    /**
     * @var integer $order
     */
    protected $order;

    /**
     * @var boolean $status
     */
    protected $status;

    /**
     * @var Cx\Modules\Block\Model\Entity\Block
     */
    protected $blocks;

    /**
     * @var Cx\Modules\Block\Model\Entity\Category
     */
    protected $categories;

    /**
     * @var Cx\Modules\Block\Model\Entity\Category
     */
    protected $parent;

    public function __construct()
    {
        $this->blocks = new \Doctrine\Common\Collections\ArrayCollection();
        $this->categories = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Get id
     *
     * @return integer $id
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
     * @return string $name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set seperator
     *
     * @param string $seperator
     */
    public function setSeperator($seperator)
    {
        $this->seperator = $seperator;
    }

    /**
     * Get seperator
     *
     * @return string $seperator
     */
    public function getSeperator()
    {
        return $this->seperator;
    }

    /**
     * Set order
     *
     * @param integer $order
     */
    public function setOrder($order)
    {
        $this->order = $order;
    }

    /**
     * Get order
     *
     * @return integer $order
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * Set status
     *
     * @param boolean $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * Get status
     *
     * @return boolean $status
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Add blocks
     *
     * @param Cx\Modules\Block\Model\Entity\Block $blocks
     */
    public function addBlocks(\Cx\Modules\Block\Model\Entity\Block $blocks)
    {
        $this->blocks[] = $blocks;
    }

    /**
     * Get blocks
     *
     * @return Doctrine\Common\Collections\Collection $blocks
     */
    public function getBlocks()
    {
        return $this->blocks;
    }

    /**
     * Add categories
     *
     * @param Cx\Modules\Block\Model\Entity\Category $categories
     */
    public function addCategories(\Cx\Modules\Block\Model\Entity\Category $categories)
    {
        $this->categories[] = $categories;
    }

    /**
     * Get categories
     *
     * @return Doctrine\Common\Collections\Collection $categories
     */
    public function getCategories()
    {
        return $this->categories;
    }

    /**
     * Set parent
     *
     * @param Cx\Modules\Block\Model\Entity\Category $parent
     */
    public function setParent($parent)
    {
        $this->parent = $parent;
    }

    /**
     * Get parent
     *
     * @return Cx\Modules\Block\Model\Entity\Category $parent
     */
    public function getParent()
    {
        return $this->parent;
    }
}
