<?php

/**
 * Cloudrexx
 *
 * @link      http://www.cloudrexx.com
 * @copyright Cloudrexx AG 2007-2015
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
 * UserAttributeValue
 *
 * @copyright   Cloudrexx AG
 * @author      Project Team SS4U <info@cloudrexx.com>
 * @package     cloudrexx
 * @subpackage  core_user
 */

namespace Cx\Core\User\Model\Entity;

/**
 * UserAttributeValue
 *
 * @copyright   Cloudrexx AG
 * @author      Project Team SS4U <info@cloudrexx.com>
 * @package     cloudrexx
 * @subpackage  core_user
 */
class UserAttributeValue extends \Cx\Model\Base\EntityBase {
    /**
     * @var integer $attributeId
     */
    protected $attributeId;

    /**
     * @var integer $userId
     */
    protected $userId;

    /**
     * @var integer $historyId
     */
    protected $historyId;

    /**
     * @var string $value
     */
    protected $value;

    /**
     * @var Cx\Core\User\Model\Entity\UserAttribute
     */
    protected $userAttribute;

    /**
     * @var Cx\Core\User\Model\Entity\UserProfile
     */
    protected $userProfile;

    /**
     * Set attributeId
     *
     * @param integer $attributeId
     */
    public function setAttributeId($attributeId)
    {
        $this->attributeId = $attributeId;
    }

    /**
     * Get attributeId
     *
     * @return integer $attributeId
     */
    public function getAttributeId()
    {
        return $this->attributeId;
    }

    /**
     * Set userId
     *
     * @param integer $userId
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;
    }

    /**
     * Get userId
     *
     * @return integer $userId
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * Get historyId
     *
     * @return integer $historyId
     */
    public function getHistoryId()
    {
        return $this->historyId;
    }

    /**
     * Set historyId
     *
     * @param integer $historyId
     */
    public function setHistoryId($historyId)
    {
        $this->historyId = $historyId;
    }

    /**
     * Set value
     *
     * @param string $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    /**
     * Get value
     *
     * @return string $value
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set userAttribute
     *
     * @param Cx\Core\User\Model\Entity\UserAttribute $userAttribute
     */
    public function setUserAttribute(\Cx\Core\User\Model\Entity\UserAttribute $userAttribute)
    {
        $this->userAttribute = $userAttribute;
    }

    /**
     * Get userAttribute
     *
     * @return Cx\Core\User\Model\Entity\UserAttribute $userAttribute
     */
    public function getUserAttribute()
    {
        return $this->userAttribute;
    }

    /**
     * Set userProfile
     *
     * @param Cx\Core\User\Model\Entity\UserProfile $userProfile
     */
    public function setUserProfile(\Cx\Core\User\Model\Entity\UserProfile $userProfile)
    {
        $this->userProfile = $userProfile;
    }

    /**
     * Get userProfile
     *
     * @return Cx\Core\User\Model\Entity\UserProfile $userProfile
     */
    public function getUserProfile()
    {
        return $this->userProfile;
    }
}
