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
 * Class DiscountgroupCountName
 *
 * @copyright   CLOUDREXX CMS - Cloudrexx AG Thun
 * @author      Sam Hawkes <info@cloudrexx.com>
 * @package     cloudrexx
 * @subpackage  module_shop
 * @version     5.0.0
 */
namespace Cx\Modules\Shop\Model\Entity;

/**
 * DiscountgroupCountName contains of a name, an unit and if it is cumulative.
 * If it isn't cumulative, the discount group can only be used on one product.
 *
 * @copyright   CLOUDREXX CMS - Cloudrexx AG Thun
 * @author      Sam Hawkes <info@cloudrexx.com>
 * @package     cloudrexx
 * @subpackage  module_shop
 * @version     5.0.0
 */
class DiscountgroupCountName extends \Cx\Model\Base\EntityBase {
    /**
     * @var integer
     */
    protected $id;

    /**
     * @var integer
     */
    protected $cumulative;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    protected $discountgroupCountRates;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->discountgroupCountRates = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set cumulative
     *
     * @param integer $cumulative
     */
    public function setCumulative($cumulative)
    {
        $this->cumulative = $cumulative;
    }

    /**
     * Get cumulative
     *
     * @return integer 
     */
    public function getCumulative()
    {
        return $this->cumulative;
    }

    /**
     * Add discountgroupCountRates
     *
     * @param \Cx\Modules\Shop\Model\Entity\DiscountgroupCountRate $discountgroupCountRates
     */
    public function addDiscountgroupCountRate(\Cx\Modules\Shop\Model\Entity\DiscountgroupCountRate $discountgroupCountRates)
    {
        $this->discountgroupCountRates[] = $discountgroupCountRates;
    }

    /**
     * Remove discountgroupCountRates
     *
     * @param \Cx\Modules\Shop\Model\Entity\DiscountgroupCountRate $discountgroupCountRates
     */
    public function removeDiscountgroupCountRate(\Cx\Modules\Shop\Model\Entity\DiscountgroupCountRate $discountgroupCountRates)
    {
        $this->discountgroupCountRates->removeElement($discountgroupCountRates);
    }

    /**
     * Get discountgroupCountRates
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getDiscountgroupCountRates()
    {
        return $this->discountgroupCountRates;
    }
}
