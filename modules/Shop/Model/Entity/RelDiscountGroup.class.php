<?php

namespace Cx\Modules\Shop\Model\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * RelDiscountGroup
 */
class RelDiscountGroup extends \Cx\Model\Base\EntityBase {
    /**
     * @var integer
     */
    protected $customerGroupId;

    /**
     * @var integer
     */
    protected $articleGroupId;

    /**
     * @var string
     */
    protected $rate;

    /**
     * @var \Cx\Modules\Shop\Model\Entity\CustomerGroup
     */
    protected $customerGroup;

    /**
     * @var \Cx\Modules\Shop\Model\Entity\ArticleGroup
     */
    protected $articleGroup;


    /**
     * Set customerGroupId
     *
     * @param integer $customerGroupId
     * @return RelDiscountGroup
     */
    public function setCustomerGroupId($customerGroupId)
    {
        $this->customerGroupId = $customerGroupId;

        return $this;
    }

    /**
     * Get customerGroupId
     *
     * @return integer 
     */
    public function getCustomerGroupId()
    {
        return $this->customerGroupId;
    }

    /**
     * Set articleGroupId
     *
     * @param integer $articleGroupId
     * @return RelDiscountGroup
     */
    public function setArticleGroupId($articleGroupId)
    {
        $this->articleGroupId = $articleGroupId;

        return $this;
    }

    /**
     * Get articleGroupId
     *
     * @return integer 
     */
    public function getArticleGroupId()
    {
        return $this->articleGroupId;
    }

    /**
     * Set rate
     *
     * @param string $rate
     * @return RelDiscountGroup
     */
    public function setRate($rate)
    {
        $this->rate = $rate;

        return $this;
    }

    /**
     * Get rate
     *
     * @return string 
     */
    public function getRate()
    {
        return $this->rate;
    }

    /**
     * Set customerGroup
     *
     * @param \Cx\Modules\Shop\Model\Entity\CustomerGroup $customerGroup
     * @return RelDiscountGroup
     */
    public function setCustomerGroup(\Cx\Modules\Shop\Model\Entity\CustomerGroup $customerGroup = null)
    {
        $this->customerGroup = $customerGroup;

        return $this;
    }

    /**
     * Get customerGroup
     *
     * @return \Cx\Modules\Shop\Model\Entity\CustomerGroup 
     */
    public function getCustomerGroup()
    {
        return $this->customerGroup;
    }

    /**
     * Set articleGroup
     *
     * @param \Cx\Modules\Shop\Model\Entity\ArticleGroup $articleGroup
     * @return RelDiscountGroup
     */
    public function setArticleGroup(\Cx\Modules\Shop\Model\Entity\ArticleGroup $articleGroup = null)
    {
        $this->articleGroup = $articleGroup;

        return $this;
    }

    /**
     * Get articleGroup
     *
     * @return \Cx\Modules\Shop\Model\Entity\ArticleGroup 
     */
    public function getArticleGroup()
    {
        return $this->articleGroup;
    }
}
