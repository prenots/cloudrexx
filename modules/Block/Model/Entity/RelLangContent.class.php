<?php

namespace Cx\Modules\Block\Model\Entity;

/**
 * Cx\Modules\Block\Model\Entity\RelLangContent
 */
class RelLangContent extends \Cx\Model\Base\EntityBase
{
    /**
     * @var integer $blockId
     */
    protected $blockId;

    /**
     * @var integer $langId
     */
    protected $langId;

    /**
     * @var text $content
     */
    protected $content;

    /**
     * @var integer $active
     */
    protected $active;

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
     * Set langId
     *
     * @param integer $langId
     */
    public function setLangId($langId)
    {
        $this->langId = $langId;
    }

    /**
     * Get langId
     *
     * @return integer $langId
     */
    public function getLangId()
    {
        return $this->langId;
    }

    /**
     * Set content
     *
     * @param text $content
     */
    public function setContent($content)
    {
        $this->content = $content;
    }

    /**
     * Get content
     *
     * @return text $content
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set active
     *
     * @param integer $active
     */
    public function setActive($active)
    {
        $this->active = $active;
    }

    /**
     * Get active
     *
     * @return integer $active
     */
    public function getActive()
    {
        return $this->active;
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