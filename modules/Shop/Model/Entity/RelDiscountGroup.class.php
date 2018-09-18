<?php

/**
 * Cloudrexx
 *
 * @link      http://www.cloudrexx.com
 * @copyright Cloudrexx AG 2007-2018
 *
 * According to our dual licensing model, this program can be used either
 * under the terms of the GNU Affero General Public License, version 3,
 * or under a proprietary license.
 *
 * The texts of the GNU Affero General Public License with an additional
 * permission and of our proprietary license can be found at and
 * in the LICENSE file you have received along with this program.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * "Cloudrexx" is a registered trademark of Cloudrexx AG.
 * The licensing of the program under the AGPLv3 does not imply a
 * trademark license. Therefore any rights, title and interest in
 * our trademarks remain entirely with us.
 */

/**
 * Class RelDiscountGroup
 *
 * @copyright   CLOUDREXX CMS - Cloudrexx AG Thun
 * @author      Sam Hawkes <info@cloudrexx.com>
 * @package     cloudrexx
 * @subpackage  module_shop
 * @version     5.0.0
 */
namespace Cx\Modules\Shop\Model\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Contains related article and customer group.
 *
 * @copyright   CLOUDREXX CMS - Cloudrexx AG Thun
 * @author      Sam Hawkes <info@cloudrexx.com>
 * @package     cloudrexx
 * @subpackage  module_shop
 * @version     5.0.0
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
