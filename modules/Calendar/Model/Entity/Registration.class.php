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
 * Registration
 *
 * @copyright   Cloudrexx AG
 * @author      Project Team SS4U <info@cloudrexx.com>
 * @package     cloudrexx
 * @subpackage  module_calendar
*/
namespace Cx\Modules\Calendar\Model\Entity;

/**
 * Registration
 *
 * @SWG\Definition(definition="Registration", type= "object")
 * @SWG\Get(
 *     path="/calendar-registration",
 *     tags={"registrations"},
 *     summary="Lists Registrations",
 *     @SWG\Parameter(
 *         name="order",
 *         in="query",
 *         type="string",
 *         required=false,
 *         description="Order of a Registration"
 *     ),
 *     @SWG\Parameter(
 *         name="filter",
 *         in="query",
 *         type="string",
 *         required=false,
 *         description="filter by Registration"
 *     ),
 *     @SWG\Parameter(
 *         name="limit",
 *         in="query",
 *         type="integer",
 *         format="int32",
 *         required=false,
 *         description="maximum number of results to return"
 *     ),
 *     @SWG\Response(
 *         response=200,
 *         description="A list of all registration",
 *         @SWG\Schema(
 *             allof={
 *                 @SWG\Schema(ref="#/definitions/apiResponse"),
 *                 @SWG\Schema(
 *                     @SWG\Property(
 *                         property="data",
 *                         type="array",
 *                         @SWG\Items(ref="#/definitions/Registration")
 *                     )
 *                 )
 *             }
 *         )
 *     )
 * )
 * @SWG\Get(
 *     path="/calendar-registration/{id}",
 *     tags={"registration"},
 *     summary="Fetch a registration",
 *     @SWG\Parameter(
 *         name="id",
 *         in="path",
 *         type="string",
 *         required=true,
 *         description="ID of the registration"
 *     ),
 *     @SWG\Response(
 *         response=200,
 *         description="Registration description",
 *         @SWG\Schema(
 *             allof={
 *                 @SWG\Schema(ref="#/definitions/apiResponse"),
 *                 @SWG\Schema(
 *                     @SWG\Property(
 *                         property="data",
 *                         type="object",
 *                         ref="#/definitions/Registration"
 *                     )
 *                 )
 *             }
 *         )
 *     )
 * )
 * @SWG\Post(
 *     path="/calendar-registration",
 *     tags={"registration"},
 *     summary="Create a new Registration",
 *     @SWG\Parameter(
 *         name="registration",
 *         in="body",
 *         required=true,
 *         @SWG\Schema(ref="#/definitions/Registration")
 *     ),
 *     @SWG\Response(
 *         response=200,
 *         description="Registration added",
 *         @SWG\Schema(
 *             allof={
 *                 @SWG\Schema(ref="#/definitions/apiResponse"),
 *                 @SWG\Schema(
 *                     @SWG\Property(
 *                         property="data",
 *                         type="object",
 *                         ref="#/definitions/Registration"
 *                     )
 *                 )
 *             }
 *         )
 *     )
 * )
 * @SWG\Put(
 *     path="/calendar-registration/{id}",
 *     tags={"registration"},
 *     summary="Update a Registration",
 *     @SWG\Parameter(
 *         name="id",
 *         in="path",
 *         type="string",
 *         required=true,
 *         description="ID of the registration"
 *     ),
 *     @SWG\Parameter(
 *         name="registration",
 *         in="body",
 *         required=true,
 *         @SWG\Schema(ref="#/definitions/Registration")
 *     ),
 *     @SWG\Response(
 *         response=200,
 *         description="Registration updated",
 *         @SWG\Schema(
 *             allof={
 *                 @SWG\Schema(ref="#/definitions/apiResponse"),
 *                 @SWG\Schema(
 *                     @SWG\Property(
 *                         property="data",
 *                         type="object",
 *                         ref="#/definitions/Registration"
 *                     )
 *                 )
 *             }
 *         )
 *     )
 * )
 * @SWG\Delete(
 *     path="/calendar-registration/{id}",
 *     tags={"registration"},
 *     summary="Delete a Registration",
 *     @SWG\Parameter(
 *         name="id",
 *         in="path",
 *         type="string",
 *         required=true,
 *         description="ID of the registration"
 *     ),
 *     @SWG\Response(
 *         response=200,
 *         description="Registration deleted",
 *         @SWG\Schema(
 *             ref="#/definitions/apiResponse"
 *         )
 *     )
 * )
 *
 * @copyright   Cloudrexx AG
 * @author      Project Team SS4U <info@cloudrexx.com>
 * @package     cloudrexx
 * @subpackage  module_calendar
*/
class Registration extends \Cx\Model\Base\EntityBase {
    /**
     * @SWG\Property(
     *     type="integer",
     *     format="int64",
     *     description="Unique identifier representing a specific registration"
     * )
     *
     * @var integer $id
     */
    protected $id;

    /**
     * @SWG\Property(type="integer", format="int32")
     *
     * @var integer $date
     */
    protected $date;

    /**
     * @SWG\Property(type="string")
     *
     * @var string $hostName
     */
    protected $hostName;

    /**
     * @SWG\Property(type="string")
     *
     * @var string $ipAddress
     */
    protected $ipAddress;

    /**
     * @SWG\Property(type="integer", format="int32")
     *
     * @var integer $type
     */
    protected $type;

    /**
     * @SWG\Property(type="string")
     *
     * @var string $key
     */
    protected $key;

    /**
     * @SWG\Property(type="integer", format="int32")
     *
     * @var integer $userId
     */
    protected $userId;

    /**
     * @SWG\Property(type="integer", format="int32")
     *
     * @var integer $langId
     */
    protected $langId;

    /**
     * @SWG\Property(type="integer", format="int32")
     *
     * @var integer $export
     */
    protected $export;

    /**
     * @SWG\Property(type="integer", format="int32")
     *
     * @var integer $paymentMethod
     */
    protected $paymentMethod;

    /**
     * @SWG\Property(type="integer", format="int32")
     *
     * @var integer $paid
     */
    protected $paid;

    /**
     * @SWG\Property(
     *     type="array",
     *     @SWG\Items(ref="#/definitions/RegistrationFormFieldValue")
     * )
     *
     * @var Cx\Modules\Calendar\Model\Entity\RegistrationFormFieldValue
     */
    protected $registrationFormFieldValues;

    /**
     * @SWG\Property(
     *     type="object",
     *     ref="#/definitions/Event"
     * )
     *
     * @var Cx\Modules\Calendar\Model\Entity\Event
     */
    protected $event;

    public function __construct()
    {
        $this->registrationFormFieldValues = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set date
     *
     * @param integer $date
     */
    public function setDate($date)
    {
        $this->date = $date;
    }

    /**
     * Get date
     *
     * @return integer $date
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set hostName
     *
     * @param string $hostName
     */
    public function setHostName($hostName)
    {
        $this->hostName = $hostName;
    }

    /**
     * Get hostName
     *
     * @return string $hostName
     */
    public function getHostName()
    {
        return $this->hostName;
    }

    /**
     * Set ipAddress
     *
     * @param string $ipAddress
     */
    public function setIpAddress($ipAddress)
    {
        $this->ipAddress = $ipAddress;
    }

    /**
     * Get ipAddress
     *
     * @return string $ipAddress
     */
    public function getIpAddress()
    {
        return $this->ipAddress;
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
     * @return integer $type
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set key
     *
     * @param string $key
     */
    public function setKey($key)
    {
        $this->key = $key;
    }

    /**
     * Get key
     *
     * @return string $key
     */
    public function getKey()
    {
        return $this->key;
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
     * Set langId
     *
     * @param integer $langId
     */
    public function setLangId($langId)
    {
        $this->langId = $langId;
    }

    /**
     * Get langId
     *
     * @return integer $langId
     */
    public function getLangId()
    {
        return $this->langId;
    }

    /**
     * Set export
     *
     * @param integer $export
     */
    public function setExport($export)
    {
        $this->export = $export;
    }

    /**
     * Get export
     *
     * @return integer $export
     */
    public function getExport()
    {
        return $this->export;
    }

    /**
     * Set paymentMethod
     *
     * @param integer $paymentMethod
     */
    public function setPaymentMethod($paymentMethod)
    {
        $this->paymentMethod = $paymentMethod;
    }

    /**
     * Get paymentMethod
     *
     * @return integer $paymentMethod
     */
    public function getPaymentMethod()
    {
        return $this->paymentMethod;
    }

    /**
     * Set paid
     *
     * @param integer $paid
     */
    public function setPaid($paid)
    {
        $this->paid = $paid;
    }

    /**
     * Get paid
     *
     * @return integer $paid
     */
    public function getPaid()
    {
        return $this->paid;
    }

    /**
     * Add registrationFormFieldValues
     *
     * @param Cx\Modules\Calendar\Model\Entity\RegistrationFormFieldValue $registrationFormFieldValue
     */
    public function addRegistrationFormFieldValue(\Cx\Modules\Calendar\Model\Entity\RegistrationFormFieldValue $registrationFormFieldValue)
    {
        $this->registrationFormFieldValues[] = $registrationFormFieldValue;
    }

    /**
     * set $registrationFormFieldValues
     *
     * @param type $registrationFormFieldValues
     */
    public function setRegistrationFormFieldValues($registrationFormFieldValues) {
        $this->registrationFormFieldValues = $registrationFormFieldValues;
    }

    /**
     * Get RegistrationFormFieldValueByFieldId
     *
     * @param integer $fieldId field id
     *
     * @return null|\Cx\Modules\Calendar\Model\Entity\RegistrationFormFieldValue
     */
    public function getRegistrationFormFieldValueByFieldId($fieldId)
    {
        if (!$fieldId) {
            return null;
        }

        foreach ($this->registrationFormFieldValues as $formFieldValue) {
            $formField = $formFieldValue->getRegistrationFormField();
            if ($formField && ($formField->getId() == $fieldId)) {
                return $formFieldValue;
            }
        }
        return null;
    }

    /**
     * Get registrationFormFieldValues
     *
     * @return Doctrine\Common\Collections\Collection $registrationFormFieldValues
     */
    public function getRegistrationFormFieldValues()
    {
        return $this->registrationFormFieldValues;
    }

    /**
     * Set event
     *
     * @param Cx\Modules\Calendar\Model\Entity\Event $event
     */
    public function setEvent(\Cx\Modules\Calendar\Model\Entity\Event $event)
    {
        $this->event = $event;
    }

    /**
     * Get event
     *
     * @return Cx\Modules\Calendar\Model\Entity\Event $event
     */
    public function getEvent()
    {
        return $this->event;
    }
}