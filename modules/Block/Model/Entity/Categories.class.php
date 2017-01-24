<?php

namespace Cx\Modules\Block\Model\Entity;

/**
 * Cx\Modules\Block\Model\Entity\Categories
 */
class Categories extends \Cx\Model\Base\EntityBase
{
    /**
     * @var integer $id
     */
    protected $id;

    /**
     * @var integer $parent
     */
    protected $parent;

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
     * @var Cx\Modules\Block\Model\Entity\Blocks
     */
    protected $blocks;

    public function __construct()
    {
        $this->blocks = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set id
     *
     * @param integer $id
     */
    public function setId($id)
    {
        $this->id = $id;
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
     * @param Cx\Modules\Block\Model\Entity\Blocks $blocks
     */
    public function addBlocks(\Cx\Modules\Block\Model\Entity\Blocks $blocks)
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