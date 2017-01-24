<?php

namespace Cx\Modules\Block\Model\Entity;

/**
 * Cx\Modules\Block\Model\Entity\TargetingOption
 */
class TargetingOption extends \Cx\Model\Base\EntityBase
{
    /**
     * @var integer $blockId
     */
    protected $blockId;

    /**
     * @var string $type
     */
    protected $type;

    /**
     * @var string $filter
     */
    protected $filter;

    /**
     * @var text $value
     */
    protected $value;

    /**
     * @var Cx\Modules\Block\Model\Entity\Blocks
     */
    protected $blocks;


    /**
     * Set blockId
     *
     * @param integer $blockId
     */
    public function setBlockId($blockId)
    {
        $this->blockId = $blockId;
    }

    /**
     * Get blockId
     *
     * @return integer $blockId
     */
    public function getBlockId()
    {
        return $this->blockId;
    }

    /**
     * Set type
     *
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * Get type
     *
     * @return string $type
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set filter
     *
     * @param string $filter
     */
    public function setFilter($filter)
    {
        $this->filter = $filter;
    }

    /**
     * Get filter
     *
     * @return string $filter
     */
    public function getFilter()
    {
        return $this->filter;
    }

    /**
     * Set value
     *
     * @param text $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    /**
     * Get value
     *
     * @return text $value
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set blocks
     *
     * @param Cx\Modules\Block\Model\Entity\Blocks $blocks
     */
    public function setBlocks(\Cx\Modules\Block\Model\Entity\Blocks $blocks)
    {
        $this->blocks = $blocks;
    }

    /**
     * Get blocks
     *
     * @return Cx\Modules\Block\Model\Entity\Blocks $blocks
     */
    public function getBlocks()
    {
        return $this->blocks;
    }
}