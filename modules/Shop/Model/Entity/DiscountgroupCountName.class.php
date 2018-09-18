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

use Doctrine\ORM\Mapping as ORM;

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
class DiscountgroupCountName extends \Cx\Model\Base\EntityBase implements \Gedmo\Translatable\Translatable {
    /**
     * @var string
     */
    protected $locale;
    /**
     * @var integer
     */
    protected $id;

    /**
     * @var integer
     */
    protected $cumulative;

    /**
     * @var string
     */
    protected $unit;

    /**
     * @var string
     */
    protected $name;

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
     * Set translatable locale
     *
     * @param $locale
     */
    public function setTranslatableLocale($locale)
    {
        $this->locale = $locale;
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
     * @return DiscountgroupCountName
     */
    public function setCumulative($cumulative)
    {
        $this->cumulative = $cumulative;

        return $this;
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
     * Set unit
     *
     * @param string $unit
     */
    public function setUnit($unit)
    {
        $this->unit = $unit;
    }

    /**
     * Get unit
     *
     * @return string
     */
    public function getUnit()
    {
        return $this->unit;
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
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Add discountgroupCountRates
     *
     * @param \Cx\Modules\Shop\Model\Entity\DiscountgroupCountRate $discountgroupCountRates
     * @return DiscountgroupCountName
     */
    public function addDiscountgroupCountRate(\Cx\Modules\Shop\Model\Entity\DiscountgroupCountRate $discountgroupCountRates)
    {
        $this->discountgroupCountRates[] = $discountgroupCountRates;

        return $this;
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
