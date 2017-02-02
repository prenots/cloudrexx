<?php

namespace Cx\Modules\Block\Model\Entity;

/**
 * Cx\Modules\Block\Model\Entity\RelPage
 */
class RelPage extends \Cx\Model\Base\EntityBase
{
    /**
     * @var string $placeholder
     */
    protected $placeholder;

    /**
     * @var Cx\Modules\Block\Model\Entity\Block
     */
    protected $block;

    /**
     * @var Cx\Core\ContentManager\Model\Entity\Page
     */
    protected $contentPage;


    /**
     * Set placeholder
     *
     * @param string $placeholder
     */
    public function setPlaceholder($placeholder)
    {
        $this->placeholder = $placeholder;
    }

    /**
     * Get placeholder
     *
     * @return string $placeholder
     */
    public function getPlaceholder()
    {
        return $this->placeholder;
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
     * Set contentPage
     *
     * @param Cx\Core\ContentManager\Model\Entity\Page $contentPage
     */
    public function setContentPage(\Cx\Core\ContentManager\Model\Entity\Page $contentPage)
    {
        $this->contentPage = $contentPage;
    }

    /**
     * Get contentPage
     *
     * @return Cx\Core\ContentManager\Model\Entity\Page $contentPage
     */
    public function getContentPage()
    {
        return $this->contentPage;
    }
}
