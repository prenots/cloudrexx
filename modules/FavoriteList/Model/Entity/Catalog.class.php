<?php

namespace Cx\Modules\FavoriteList\Model\Entity;

/**
 * Cx\Modules\FavoriteList\Model\Entity\Catalog
 */
class Catalog extends \Cx\Model\Base\EntityBase
{
    /**
     * @var integer $id
     */
    protected $id;

    /**
     * @var string $sessionId
     */
    protected $sessionId;

    /**
     * @var string $name
     */
    protected $name;

    /**
     * @var date $date
     */
    protected $date;

    /**
     * @var string $meta
     */
    protected $meta;

    /**
     * @var integer $counterMail
     */
    protected $counterMail;

    /**
     * @var integer $counterPrint
     */
    protected $counterPrint;

    /**
     * @var integer $counterRecommendation
     */
    protected $counterRecommendation;

    /**
     * @var integer $counterInquiry
     */
    protected $counterInquiry;

    /**
     * @var Cx\Modules\FavoriteList\Model\Entity\Favorite
     */
    protected $favorites;

    public function __construct()
    {
        $this->favorites = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set id
     *
     * @param integer $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * Get sessionId
     *
     * @return string $sessionId
     */
    public function getSessionId()
    {
        return $this->sessionId;
    }

    /**
     * Set sessionId
     *
     * @param string $sessionId
     */
    public function setSessionId($sessionId)
    {
        $this->sessionId = $sessionId;
    }

    /**
     * Get date
     *
     * @return date $date
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set date
     *
     * @param date $date
     */
    public function setDate($date)
    {
        $this->date = $date;
    }

    /**
     * Get meta
     *
     * @return string $meta
     */
    public function getMeta()
    {
        return $this->meta;
    }

    /**
     * Set meta
     *
     * @param string $meta
     */
    public function setMeta($meta)
    {
        $this->meta = $meta;
    }

    /**
     * Get counterMail
     *
     * @return integer $counterMail
     */
    public function getCounterMail()
    {
        return $this->counterMail;
    }

    /**
     * Set counterMail
     *
     * @param integer $counterMail
     */
    public function setCounterMail($counterMail)
    {
        $this->counterMail = $counterMail;
    }

    /**
     * Adds offset to counterMail
     *
     * @param integer $offset
     */
    public function addCounterMail($offset)
    {
        $this->counterMail += $offset;
    }

    /**
     * Get counterPrint
     *
     * @return integer $counterPrint
     */
    public function getCounterPrint()
    {
        return $this->counterPrint;
    }

    /**
     * Set counterPrint
     *
     * @param integer $counterPrint
     */
    public function setCounterPrint($counterPrint)
    {
        $this->counterPrint = $counterPrint;
    }

    /**
     * Adds offset to counterPrint
     *
     * @param integer $offset
     */
    public function addCounterPrint($offset)
    {
        $this->counterPrint += $offset;
    }

    /**
     * Get counterRecommendation
     *
     * @return integer $counterRecommendation
     */
    public function getCounterRecommendation()
    {
        return $this->counterRecommendation;
    }

    /**
     * Set counterRecommendation
     *
     * @param integer $counterRecommendation
     */
    public function setCounterRecommendation($counterRecommendation)
    {
        $this->counterRecommendation = $counterRecommendation;
    }

    /**
     * Adds offset to counterRecommendation
     *
     * @param integer $offset
     */
    public function addCounterRecommendation($offset)
    {
        $this->counterRecommendation += $offset;
    }

    /**
     * Get counterInquiry
     *
     * @return integer $counterInquiry
     */
    public function getCounterInquiry()
    {
        return $this->counterInquiry;
    }

    /**
     * Set counterInquiry
     *
     * @param integer $counterInquiry
     */
    public function setCounterInquiry($counterInquiry)
    {
        $this->counterInquiry = $counterInquiry;
    }

    /**
     * Adds offset to counterInquiry
     *
     * @param integer $offset
     */
    public function addCounterInquiry($offset)
    {
        $this->counterInquiry += $offset;
    }

    /**
     * Add favorites
     *
     * @param Cx\Modules\FavoriteList\Model\Entity\Favorite $favorites
     */
    public function addFavorites(\Cx\Modules\FavoriteList\Model\Entity\Favorite $favorites)
    {
        $this->favorites[] = $favorites;
    }

    /**
     * Get favorites
     *
     * @return Doctrine\Common\Collections\Collection $favorites
     */
    public function getFavorites()
    {
        return $this->favorites;
    }

    public function __toString()
    {
        return $this->getName();
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
     * Set name
     *
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }
}
