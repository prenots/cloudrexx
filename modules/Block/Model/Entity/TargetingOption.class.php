<?php

namespace Cx\Modules\Block\Model\Entity;

/**
 * Cx\Modules\Block\Model\Entity\TargetingOption
 */
class TargetingOption extends \Cx\Model\Base\EntityBase
{
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
     * @var Cx\Modules\Block\Model\Entity\Block
     */
    protected $block;


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
     * Set block
     *
     * @param Cx\Modules\Block\Model\Entity\Block $block
     */
    public function setBlock(\Cx\Modules\Block\Model\Entity\Block $block)
    {
        $this->block = $block;
    }

    /**
     * Get block
     *
     * @return Cx\Modules\Block\Model\Entity\Block $block
     */
    public function getBlock()
    {
        return $this->block;
    }
}
