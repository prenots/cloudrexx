<?php

namespace Cx\Modules\Block\Model\Entity;

/**
 * Cx\Modules\Block\Model\Entity\Blocks
 */
class Blocks extends \Cx\Model\Base\EntityBase
{
    /**
     * @var integer $id
     */
    protected $id;

    /**
     * @var integer $cat
     */
    protected $cat;

    /**
     * @var integer $start
     */
    protected $start;

    /**
     * @var integer $end
     */
    protected $end;

    /**
     * @var string $name
     */
    protected $name;

    /**
     * @var integer $random
     */
    protected $random;

    /**
     * @var integer $random2
     */
    protected $random2;

    /**
     * @var integer $random3
     */
    protected $random3;

    /**
     * @var integer $random4
     */
    protected $random4;

    /**
     * @var integer $global
     */
    protected $global;

    /**
     * @var integer $category
     */
    protected $category;

    /**
     * @var integer $direct
     */
    protected $direct;

    /**
     * @var integer $active
     */
    protected $active;

    /**
     * @var integer $order
     */
    protected $order;

    /**
     * @var integer $wysiwygEditor
     */
    protected $wysiwygEditor;

    /**
     * @var Cx\Modules\Block\Model\Entity\RelLangContent
     */
    protected $relLangContents;

    /**
     * @var Cx\Modules\Block\Model\Entity\RelPages
     */
    protected $relPages;

    /**
     * @var Cx\Modules\Block\Model\Entity\TargetingOption
     */
    protected $targetingOptions;

    /**
     * @var Cx\Modules\Block\Model\Entity\Categories
     */
    protected $categories;

    public function __construct()
    {
        $this->relLangContents = new \Doctrine\Common\Collections\ArrayCollection();
        $this->relPages = new \Doctrine\Common\Collections\ArrayCollection();
        $this->targetingOptions = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set cat
     *
     * @param integer $cat
     */
    public function setCat($cat)
    {
        $this->cat = $cat;
    }

    /**
     * Get cat
     *
     * @return integer $cat
     */
    public function getCat()
    {
        return $this->cat;
    }

    /**
     * Set start
     *
     * @param integer $start
     */
    public function setStart($start)
    {
        $this->start = $start;
    }

    /**
     * Get start
     *
     * @return integer $start
     */
    public function getStart()
    {
        return $this->start;
    }

    /**
     * Set end
     *
     * @param integer $end
     */
    public function setEnd($end)
    {
        $this->end = $end;
    }

    /**
     * Get end
     *
     * @return integer $end
     */
    public function getEnd()
    {
        return $this->end;
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
     * Set random
     *
     * @param integer $random
     */
    public function setRandom($random)
    {
        $this->random = $random;
    }

    /**
     * Get random
     *
     * @return integer $random
     */
    public function getRandom()
    {
        return $this->random;
    }

    /**
     * Set random2
     *
     * @param integer $random2
     */
    public function setRandom2($random2)
    {
        $this->random2 = $random2;
    }

    /**
     * Get random2
     *
     * @return integer $random2
     */
    public function getRandom2()
    {
        return $this->random2;
    }

    /**
     * Set random3
     *
     * @param integer $random3
     */
    public function setRandom3($random3)
    {
        $this->random3 = $random3;
    }

    /**
     * Get random3
     *
     * @return integer $random3
     */
    public function getRandom3()
    {
        return $this->random3;
    }

    /**
     * Set random4
     *
     * @param integer $random4
     */
    public function setRandom4($random4)
    {
        $this->random4 = $random4;
    }

    /**
     * Get random4
     *
     * @return integer $random4
     */
    public function getRandom4()
    {
        return $this->random4;
    }

    /**
     * Set global
     *
     * @param integer $global
     */
    public function setGlobal($global)
    {
        $this->global = $global;
    }

    /**
     * Get global
     *
     * @return integer $global
     */
    public function getGlobal()
    {
        return $this->global;
    }

    /**
     * Set category
     *
     * @param integer $category
     */
    public function setCategory($category)
    {
        $this->category = $category;
    }

    /**
     * Get category
     *
     * @return integer $category
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Set direct
     *
     * @param integer $direct
     */
    public function setDirect($direct)
    {
        $this->direct = $direct;
    }

    /**
     * Get direct
     *
     * @return integer $direct
     */
    public function getDirect()
    {
        return $this->direct;
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
     * Set wysiwygEditor
     *
     * @param integer $wysiwygEditor
     */
    public function setWysiwygEditor($wysiwygEditor)
    {
        $this->wysiwygEditor = $wysiwygEditor;
    }

    /**
     * Get wysiwygEditor
     *
     * @return integer $wysiwygEditor
     */
    public function getWysiwygEditor()
    {
        return $this->wysiwygEditor;
    }

    /**
     * Add relLangContents
     *
     * @param Cx\Modules\Block\Model\Entity\RelLangContent $relLangContents
     */
    public function addRelLangContents(\Cx\Modules\Block\Model\Entity\RelLangContent $relLangContents)
    {
        $this->relLangContents[] = $relLangContents;
    }

    /**
     * Get relLangContents
     *
     * @return Doctrine\Common\Collections\Collection $relLangContents
     */
    public function getRelLangContents()
    {
        return $this->relLangContents;
    }

    /**
     * Add relPages
     *
     * @param Cx\Modules\Block\Model\Entity\RelPages $relPages
     */
    public function addRelPages(\Cx\Modules\Block\Model\Entity\RelPages $relPages)
    {
        $this->relPages[] = $relPages;
    }

    /**
     * Get relPages
     *
     * @return Doctrine\Common\Collections\Collection $relPages
     */
    public function getRelPages()
    {
        return $this->relPages;
    }

    /**
     * Add targetingOptions
     *
     * @param Cx\Modules\Block\Model\Entity\TargetingOption $targetingOptions
     */
    public function addTargetingOptions(\Cx\Modules\Block\Model\Entity\TargetingOption $targetingOptions)
    {
        $this->targetingOptions[] = $targetingOptions;
    }

    /**
     * Get targetingOptions
     *
     * @return Doctrine\Common\Collections\Collection $targetingOptions
     */
    public function getTargetingOptions()
    {
        return $this->targetingOptions;
    }

    /**
     * Set categories
     *
     * @param Cx\Modules\Block\Model\Entity\Categories $categories
     */
    public function setCategories(\Cx\Modules\Block\Model\Entity\Categories $categories)
    {
        $this->categories = $categories;
    }

    /**
     * Get categories
     *
     * @return Cx\Modules\Block\Model\Entity\Categories $categories
     */
    public function getCategories()
    {
        return $this->categories;
    }
}