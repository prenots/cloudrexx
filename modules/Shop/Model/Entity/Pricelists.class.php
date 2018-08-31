<?php

namespace Cx\Modules\Shop\Model\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Pricelists
 */
class Pricelists extends \Cx\Model\Base\EntityBase {
    /**
     * @var integer
     */
    protected $id;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var integer
     */
    protected $langId;

    /**
     * @var boolean
     */
    protected $borderOn;

    /**
     * @var boolean
     */
    protected $headerOn;

    /**
     * @var string
     */
    protected $headerLeft;

    /**
     * @var string
     */
    protected $headerRight;

    /**
     * @var boolean
     */
    protected $footerOn;

    /**
     * @var string
     */
    protected $footerLeft;

    /**
     * @var string
     */
    protected $footerRight;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    protected $categories;

    /**
     * @var \Cx\Core\Locale\Model\Entity\Locale
     */
    protected $lang;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->categories = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return Pricelists
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
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
     * Set langId
     *
     * @param integer $langId
     * @return Pricelists
     */
    public function setLangId($langId)
    {
        $this->langId = $langId;

        return $this;
    }

    /**
     * Get langId
     *
     * @return integer 
     */
    public function getLangId()
    {
        return $this->langId;
    }

    /**
     * Set borderOn
     *
     * @param boolean $borderOn
     * @return Pricelists
     */
    public function setBorderOn($borderOn)
    {
        $this->borderOn = $borderOn;

        return $this;
    }

    /**
     * Get borderOn
     *
     * @return boolean 
     */
    public function getBorderOn()
    {
        return $this->borderOn;
    }

    /**
     * Set headerOn
     *
     * @param boolean $headerOn
     * @return Pricelists
     */
    public function setHeaderOn($headerOn)
    {
        $this->headerOn = $headerOn;

        return $this;
    }

    /**
     * Get headerOn
     *
     * @return boolean 
     */
    public function getHeaderOn()
    {
        return $this->headerOn;
    }

    /**
     * Set headerLeft
     *
     * @param string $headerLeft
     * @return Pricelists
     */
    public function setHeaderLeft($headerLeft)
    {
        $this->headerLeft = $headerLeft;

        return $this;
    }

    /**
     * Get headerLeft
     *
     * @return string 
     */
    public function getHeaderLeft()
    {
        return $this->headerLeft;
    }

    /**
     * Set headerRight
     *
     * @param string $headerRight
     * @return Pricelists
     */
    public function setHeaderRight($headerRight)
    {
        $this->headerRight = $headerRight;

        return $this;
    }

    /**
     * Get headerRight
     *
     * @return string 
     */
    public function getHeaderRight()
    {
        return $this->headerRight;
    }

    /**
     * Set footerOn
     *
     * @param boolean $footerOn
     * @return Pricelists
     */
    public function setFooterOn($footerOn)
    {
        $this->footerOn = $footerOn;

        return $this;
    }

    /**
     * Get footerOn
     *
     * @return boolean 
     */
    public function getFooterOn()
    {
        return $this->footerOn;
    }

    /**
     * Set footerLeft
     *
     * @param string $footerLeft
     * @return Pricelists
     */
    public function setFooterLeft($footerLeft)
    {
        $this->footerLeft = $footerLeft;

        return $this;
    }

    /**
     * Get footerLeft
     *
     * @return string 
     */
    public function getFooterLeft()
    {
        return $this->footerLeft;
    }

    /**
     * Set footerRight
     *
     * @param string $footerRight
     * @return Pricelists
     */
    public function setFooterRight($footerRight)
    {
        $this->footerRight = $footerRight;

        return $this;
    }

    /**
     * Get footerRight
     *
     * @return string 
     */
    public function getFooterRight()
    {
        return $this->footerRight;
    }

    /**
     * Add categories
     *
     * @param \Cx\Modules\Shop\Model\Entity\Categories $categories
     * @return Pricelists
     */
    public function addCategory(\Cx\Modules\Shop\Model\Entity\Categories $categories)
    {
        $this->categories[] = $categories;

        return $this;
    }

    /**
     * Remove categories
     *
     * @param \Cx\Modules\Shop\Model\Entity\Categories $categories
     */
    public function removeCategory(\Cx\Modules\Shop\Model\Entity\Categories $categories)
    {
        $this->categories->removeElement($categories);
    }

    /**
     * Get categories
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getCategories()
    {
        return $this->categories;
    }

    /**
     * Set currencies
     *
     * @param \Cx\Core\Locale\Model\Entity\Locale $lang
     * @return Pricelists
     */
    public function setLang(\Cx\Core\Locale\Model\Entity\Locale $lang = null)
    {
        $this->currencies = $lang;

        return $this;
    }

    /**
     * Get lang
     *
     * @return \Cx\Core\Locale\Model\Entity\Locale
     */
    public function getLang()
    {
        return $this->lang;
    }
}
