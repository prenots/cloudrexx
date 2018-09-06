<?php

namespace Cx\Modules\Shop\Model\Entity;

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
     */
    public function setCustomerGroupId($customerGroupId)
    {
        $this->customerGroupId = $customerGroupId;
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
     */
    public function setArticleGroupId($articleGroupId)
    {
        $this->articleGroupId = $articleGroupId;
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
     */
    public function setRate($rate)
    {
        $this->rate = $rate;
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
     */
    public function setCustomerGroup(\Cx\Modules\Shop\Model\Entity\CustomerGroup $customerGroup = null)
    {
        $this->customerGroup = $customerGroup;
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
     */
    public function setArticleGroup(\Cx\Modules\Shop\Model\Entity\ArticleGroup $articleGroup = null)
    {
        $this->articleGroup = $articleGroup;
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
