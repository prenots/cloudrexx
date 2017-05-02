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
 * Mail
 *
 * @copyright   Cloudrexx AG
 * @author      Project Team SS4U <info@cloudrexx.com>
 * @package     cloudrexx
 * @subpackage  module_calendar
*/
namespace Cx\Modules\Calendar\Model\Entity;

/**
 * Mail
 *
 * @SWG\Definition(definition="Mail", type= "object")
 * @SWG\Get(
 *     path="/calendar-mail",
 *     tags={"mails"},
 *     summary="Lists Mails",
 *     @SWG\Parameter(
 *         name="order",
 *         in="query",
 *         type="string",
 *         required=false,
 *         description="Order of a Mail"
 *     ),
 *     @SWG\Parameter(
 *         name="filter",
 *         in="query",
 *         type="string",
 *         required=false,
 *         description="filter by Mail"
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
 *         description="A list of all mail",
 *         @SWG\Schema(
 *             allof={
 *                 @SWG\Schema(ref="#/definitions/apiResponse"),
 *                 @SWG\Schema(
 *                     @SWG\Property(
 *                         property="data",
 *                         type="array",
 *                         @SWG\Items(ref="#/definitions/Mail")
 *                     )
 *                 )
 *             }
 *         )
 *     )
 * )
 * @SWG\Get(
 *     path="/calendar-mail/{id}",
 *     tags={"mail"},
 *     summary="Fetch a mail",
 *     @SWG\Parameter(
 *         name="id",
 *         in="path",
 *         type="string",
 *         required=true,
 *         description="ID of the mail"
 *     ),
 *     @SWG\Response(
 *         response=200,
 *         description="Mail description",
 *         @SWG\Schema(
 *             allof={
 *                 @SWG\Schema(ref="#/definitions/apiResponse"),
 *                 @SWG\Schema(
 *                     @SWG\Property(
 *                         property="data",
 *                         type="object",
 *                         ref="#/definitions/Mail"
 *                     )
 *                 )
 *             }
 *         )
 *     )
 * )
 * @SWG\Post(
 *     path="/calendar-mail",
 *     tags={"mail"},
 *     summary="Create a new Mail",
 *     @SWG\Parameter(
 *         name="mail",
 *         in="body",
 *         required=true,
 *         @SWG\Schema(ref="#/definitions/Mail")
 *     ),
 *     @SWG\Response(
 *         response=200,
 *         description="Mail added",
 *         @SWG\Schema(
 *             allof={
 *                 @SWG\Schema(ref="#/definitions/apiResponse"),
 *                 @SWG\Schema(
 *                     @SWG\Property(
 *                         property="data",
 *                         type="object",
 *                         ref="#/definitions/Mail"
 *                     )
 *                 )
 *             }
 *         )
 *     )
 * )
 * @SWG\Put(
 *     path="/calendar-mail/{id}",
 *     tags={"mail"},
 *     summary="Update a Mail",
 *     @SWG\Parameter(
 *         name="id",
 *         in="path",
 *         type="string",
 *         required=true,
 *         description="ID of the mail"
 *     ),
 *     @SWG\Parameter(
 *         name="mail",
 *         in="body",
 *         required=true,
 *         @SWG\Schema(ref="#/definitions/Mail")
 *     ),
 *     @SWG\Response(
 *         response=200,
 *         description="Mail updated",
 *         @SWG\Schema(
 *             allof={
 *                 @SWG\Schema(ref="#/definitions/apiResponse"),
 *                 @SWG\Schema(
 *                     @SWG\Property(
 *                         property="data",
 *                         type="object",
 *                         ref="#/definitions/Mail"
 *                     )
 *                 )
 *             }
 *         )
 *     )
 * )
 * @SWG\Delete(
 *     path="/calendar-mail/{id}",
 *     tags={"mail"},
 *     summary="Delete a Mail",
 *     @SWG\Parameter(
 *         name="id",
 *         in="path",
 *         type="string",
 *         required=true,
 *         description="ID of the mail"
 *     ),
 *     @SWG\Response(
 *         response=200,
 *         description="Mail deleted",
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
class Mail extends \Cx\Model\Base\EntityBase {
    /**
     * @SWG\Property(
     *     type="integer",
     *     format="int64",
     *     description="Unique identifier representing a specific mail"
     * )
     *
     * @var integer $id
     */
    protected $id;

    /**
     * @SWG\Property(type="string")
     *
     * @var string $title
     */
    protected $title;

    /**
     * @SWG\Property(type="string")
     *
     * @var text $contentText
     */
    protected $contentText;

    /**
     * @SWG\Property(type="string")
     *
     * @var text $contentHtml
     */
    protected $contentHtml;

    /**
     * @SWG\Property(type="string")
     *
     * @var text $recipients
     */
    protected $recipients;

    /**
     * @SWG\Property(type="integer", format="int32")
     *
     * @var integer $langId
     */
    protected $langId;

    /**
     * @SWG\Property(type="integer", format="int32")
     *
     * @var integer $actionId
     */
    protected $actionId;

    /**
     * @SWG\Property(type="boolean")
     *
     * @var integer $isDefault
     */
    protected $isDefault;

    /**
     * @SWG\Property(type="boolean")
     *
     * @var integer $status
     */
    protected $status;

    /**
     * @SWG\Property(type="integer", format="int32")
     *
     * @var integer $eventLangId
     */
    protected $eventLangId;

    /**
     * Constructor
     */
    public function __construct() {
        $this->status    = 0;
        $this->isDefault = 0;
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
     * Set title
     *
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * Get title
     *
     * @return string $title
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set contentText
     *
     * @param text $contentText
     */
    public function setContentText($contentText)
    {
        $this->contentText = $contentText;
    }

    /**
     * Get contentText
     *
     * @return text $contentText
     */
    public function getContentText()
    {
        return $this->contentText;
    }

    /**
     * Set contentHtml
     *
     * @param text $contentHtml
     */
    public function setContentHtml($contentHtml)
    {
        $this->contentHtml = $contentHtml;
    }

    /**
     * Get contentHtml
     *
     * @return text $contentHtml
     */
    public function getContentHtml()
    {
        return $this->contentHtml;
    }

    /**
     * Set recipients
     *
     * @param text $recipients
     */
    public function setRecipients($recipients)
    {
        $this->recipients = $recipients;
    }

    /**
     * Get recipients
     *
     * @return text $recipients
     */
    public function getRecipients()
    {
        return $this->recipients;
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
     * Set actionId
     *
     * @param integer $actionId
     */
    public function setActionId($actionId)
    {
        $this->actionId = $actionId;
    }

    /**
     * Get actionId
     *
     * @return integer $actionId
     */
    public function getActionId()
    {
        return $this->actionId;
    }

    /**
     * Set isDefault
     *
     * @param integer $isDefault
     */
    public function setIsDefault($isDefault)
    {
        $this->isDefault = $isDefault;
    }

    /**
     * Get isDefault
     *
     * @return integer $isDefault
     */
    public function getIsDefault()
    {
        return $this->isDefault;
    }

    /**
     * Set status
     *
     * @param integer $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * Get status
     *
     * @return integer $status
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set eventLangId
     *
     * @param integer $eventLangId
     */
    public function setEventLangId($eventLangId)
    {
        $this->eventLangId = $eventLangId;
    }

    /**
     * Get eventLangId
     *
     * @return integer $eventLangId
     */
    public function getEventLangId()
    {
        return $this->eventLangId;
    }
}