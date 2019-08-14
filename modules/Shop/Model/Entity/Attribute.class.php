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
 * Class Attribute
 *
 * @copyright   CLOUDREXX CMS - Cloudrexx AG Thun
 * @author      Sam Hawkes <info@cloudrexx.com>
 * @package     cloudrexx
 * @subpackage  module_shop
 * @version     5.0.0
 */

namespace Cx\Modules\Shop\Model\Entity;

/**
 * Attribute
 *
 * These may be associated with zero or more Products.
 * Each attribute consists of a name part
 * (module_shop_attribute) and zero or more value parts (module_shop_option).
 * Each of the values can be associated with an arbitrary number of Products
 * by inserting the respective record into the relations table
 * module_shop_products_attributes.
 * The type determines the kind of relation between a Product and the attribute
 * values, that is, whether it is optional or mandatory, and whether single
 * or multiple attributes may be chosen at a time.  See {@link ?} for details.
 *
 * @copyright   CLOUDREXX CMS - Cloudrexx AG Thun
 * @author      Sam Hawkes <info@cloudrexx.com>
 * @package     cloudrexx
 * @subpackage  module_shop
 * @version     5.0.0
 */
class Attribute extends \Cx\Model\Base\EntityBase implements \Gedmo\Translatable\Translatable {
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
    protected $type = 1;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    protected $options;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->options = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set translatable locale
     *
     * @param $locale
     */
    public function setTranslatableLocale($locale)
    {
        if (!is_string($locale) || !strlen($locale)) {
            $this->locale = $locale;
        }
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
     * Set type
     *
     * @param integer $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * Get type
     *
     * @return integer
     */
    public function getType()
    {
        return $this->type;
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
     * Add options
     *
     * @param \Cx\Modules\Shop\Model\Entity\Option $option
     */
    public function addOption(\Cx\Modules\Shop\Model\Entity\Option $option)
    {
        $this->options[] = $option;
    }

    /**
     * Remove options
     *
     * @param \Cx\Modules\Shop\Model\Entity\Option $option
     */
    public function removeOption(\Cx\Modules\Shop\Model\Entity\Option $option)
    {
        $this->options->removeElement($option);
    }

    /**
     * Get options
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Returns a regular expression for the verification of options
     *
     * The regex returned depends on the value of the $type parameter.
     * Mind that the regex is also applicable to some optional types!
     * For types that need not be verified, the empty string is returned.
     * @param   integer     $type       The Attribute type
     * @return  string                  The regex
     */
    static function getVerificationRegex($type)
    {
        switch ($type) {
            case self::TYPE_TEXT_MANDATORY:
            case self::TYPE_TEXTAREA_MANDATORY:
                return '.+';
// TODO: Improve the regex for file names
            case self::TYPE_UPLOAD_OPTIONAL:
                return '(^$|^.+\..+$)';
            case self::TYPE_UPLOAD_MANDATORY:
                return '^.+\..+$';
            case self::TYPE_EMAIL_OPTIONAL:
                return '(^$|^'.\FWValidator::REGEX_EMAIL.'$)';
            case self::TYPE_EMAIL_MANDATORY:
                return '^'.\FWValidator::REGEX_EMAIL.'$';
            case self::TYPE_URL_OPTIONAL:
                return '(^$|^'.\FWValidator::REGEX_URI.'$)';
            case self::TYPE_URL_MANDATORY:
                return '^'.\FWValidator::REGEX_URI.'$';
            // Note: The date regex is defined based on the value of the
            // ASCMS_DATE_FORMAT_DATE constant and may thus be localized.
            case self::TYPE_DATE_OPTIONAL:
                return
                    '(^$|^'.
                    \DateTimeTools::getRegexForDateFormat(ASCMS_DATE_FORMAT_DATE).
                    '$)';
            case self::TYPE_DATE_MANDATORY:
                return
                    '^'.
                    \DateTimeTools::getRegexForDateFormat(ASCMS_DATE_FORMAT_DATE).
                    '$';
            // Note: Number formats are somewhat arbitrary and should be defined
            // more closely resembling IEEE standards (or whatever).
            case self::TYPE_NUMBER_INT_OPTIONAL:
                return '^\d{0,10}$';
            case self::TYPE_NUMBER_INT_MANDATORY:
                return '^\d{1,10}$';
            case self::TYPE_NUMBER_FLOAT_OPTIONAL:
                return '^\d{0,10}[\d\.]?\d*$';
            case self::TYPE_NUMBER_FLOAT_MANDATORY:
                return '^\d{0,10}[\d\.]\d*$';
            // Not applicable:
            //self::TYPE_MENU_OPTIONAL
            //self::TYPE_RADIOBUTTON
            //self::TYPE_CHECKBOX
            //self::TYPE_MENU_MANDATORY
            //self::TYPE_TEXT_OPTIONAL
            //self::TYPE_TEXTAREA_OPTIONAL
        }
        return '';
    }
}
