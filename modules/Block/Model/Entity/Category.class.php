<?php

namespace Cx\Modules\Block\Model\Entity;

/**
 * Cx\Modules\Block\Model\Entity\Category
 */
class Category extends \Cx\Model\Base\EntityBase {
    /**
     * @var integer $id
     */
    private $id;

    /**
     * @var integer $parent
     */
    private $parent;

    /**
     * @var string $name
     */
    private $name;

    /**
     * @var string $seperator
     */
    private $seperator;

    /**
     * @var integer $order
     */
    private $order;

    /**
     * @var boolean $status
     */
    private $status;

    /**
     * @var Cx\Modules\Block\Model\Entity\Block
     */
    private $blocks;

    public function __construct()
    {
        $this->blocks = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set parent
     *
     * @param integer $parent
     */
    public function setParent($parent)
    {
        $this->parent = $parent;
    }

    /**
     * Get parent
     *
     * @return integer $parent
     */
    public function getParent()
    {
        return $this->parent;
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
}