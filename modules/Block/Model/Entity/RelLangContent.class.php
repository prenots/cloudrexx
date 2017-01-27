<?php

namespace Cx\Modules\Block\Model\Entity;

/**
 * Cx\Modules\Block\Model\Entity\RelLangContent
 */
class RelLangContent extends \Cx\Model\Base\EntityBase {
    /**
     * @var integer $blockId
     */
    private $blockId;

    /**
     * @var integer $langId
     */
    private $langId;

    /**
     * @var text $content
     */
    private $content;

    /**
     * @var integer $active
     */
    private $active;

    /**
     * @var Cx\Modules\Block\Model\Entity\Block
     */
    private $block;

    /**
     * @var Cx\Core\Locale\Model\Entity\Locale
     */
    private $locale;


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

    /**
     * Set locale
     *
     * @param Cx\Core\Locale\Model\Entity\Locale $locale
     */
    public function setLocale(\Cx\Core\Locale\Model\Entity\Locale $locale)
    {
        $this->locale = $locale;
    }

    /**
     * Get locale
     *
     * @return Cx\Core\Locale\Model\Entity\Locale $locale
     */
    public function getLocale()
    {
        return $this->locale;
    }
}