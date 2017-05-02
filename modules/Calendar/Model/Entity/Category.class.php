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
 * Category
 *
 * @copyright   Cloudrexx AG
 * @author      Project Team SS4U <info@cloudrexx.com>
 * @package     cloudrexx
 * @subpackage  module_calendar
*/
namespace Cx\Modules\Calendar\Model\Entity;

/**
 * Category
 *
 * @SWG\Definition(definition="Category", type= "object")
 * @SWG\Get(
 *     path="/calendar-category",
 *     tags={"categories"},
 *     summary="Lists categories",
 *     @SWG\Parameter(
 *         name="order",
 *         in="query",
 *         type="string",
 *         required=false,
 *         description="Order of the Category"
 *     ),
 *     @SWG\Parameter(
 *         name="filter",
 *         in="query",
 *         type="string",
 *         required=false,
 *         description="filter by Category"
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
 *         description="A list of all categories",
 *         @SWG\Schema(
 *             allof={
 *                 @SWG\Schema(ref="#/definitions/apiResponse"),
 *                 @SWG\Schema(
 *                     @SWG\Property(
 *                         property="data",
 *                         type="array",
 *                         @SWG\Items(ref="#/definitions/Category")
 *                     )
 *                 )
 *             }
 *         )
 *     )
 * )
 * @SWG\Get(
 *     path="/calendar-category/{id}",
 *     tags={"category"},
 *     summary="Fetch a Category",
 *     @SWG\Parameter(
 *         name="id",
 *         in="path",
 *         type="string",
 *         required=true,
 *         description="ID of the Category"
 *     ),
 *     @SWG\Response(
 *         response=200,
 *         description="Category description",
 *         @SWG\Schema(
 *             allof={
 *                 @SWG\Schema(ref="#/definitions/apiResponse"),
 *                 @SWG\Schema(
 *                     @SWG\Property(
 *                         property="data",
 *                         type="object",
 *                         ref="#/definitions/Category"
 *                     )
 *                 )
 *             }
 *         )
 *     )
 * )
 * @SWG\Post(
 *     path="/calendar-category",
 *     tags={"category"},
 *     summary="Create a new Category",
 *     @SWG\Parameter(
 *         name="event",
 *         in="body",
 *         required=true,
 *         @SWG\Schema(ref="#/definitions/Category")
 *     ),
 *     @SWG\Response(
 *         response=200,
 *         description="Category added",
 *         @SWG\Schema(
 *             allof={
 *                 @SWG\Schema(ref="#/definitions/apiResponse"),
 *                 @SWG\Schema(
 *                     @SWG\Property(
 *                         property="data",
 *                         type="object",
 *                         ref="#/definitions/Category"
 *                     )
 *                 )
 *             }
 *         )
 *     )
 * )
 * @SWG\Put(
 *     path="/calendar-category/{id}",
 *     tags={"category"},
 *     summary="Update a Category",
 *     @SWG\Parameter(
 *         name="id",
 *         in="path",
 *         type="string",
 *         required=true,
 *         description="ID of the category"
 *     ),
 *     @SWG\Parameter(
 *         name="category",
 *         in="body",
 *         required=true,
 *         @SWG\Schema(ref="#/definitions/Category")
 *     ),
 *     @SWG\Response(
 *         response=200,
 *         description="Category updated",
 *         @SWG\Schema(
 *             allof={
 *                 @SWG\Schema(ref="#/definitions/apiResponse"),
 *                 @SWG\Schema(
 *                     @SWG\Property(
 *                         property="data",
 *                         type="object",
 *                         ref="#/definitions/Category"
 *                     )
 *                 )
 *             }
 *         )
 *     )
 * )
 * @SWG\Delete(
 *     path="/calendar-category/{id}",
 *     tags={"category"},
 *     summary="Delete a Category",
 *     @SWG\Parameter(
 *         name="id",
 *         in="path",
 *         type="string",
 *         required=true,
 *         description="ID of the category"
 *     ),
 *     @SWG\Response(
 *         response=200,
 *         description="Category deleted",
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
class Category extends \Cx\Model\Base\EntityBase {
    /**
     * @SWG\Property(
     *     type="integer",
     *     format="int64",
     *     description="Unique identifier representing a specific category"
     * )
     *
     * @var integer $id
     */
    protected $id;

    /**
     * @SWG\Property(type="integer", format="int32")
     *
     * @var integer $pos
     */
    protected $pos;

    /**
     * @SWG\Property(type="boolean")
     *
     * @var integer $status
     */
    protected $status;

    /**
     * @SWG\Property(
     *     type="array",
     *     @SWG\Items(ref="#/definitions/CategoryName")
     * )
     *
     * @var Cx\Modules\Calendar\Model\Entity\CategoryName
     */
    protected $categoryNames;

    /**
     * @SWG\Property(
     *     type="array",
     *     @SWG\Items(ref="#/definitions/Event")
     * )
     *
     * @var Cx\Modules\Calendar\Model\Entity\Event
     */
    protected $events;

    public function __construct()
    {
        $this->pos = 0;
        $this->status = 0;
        $this->categoryNames = new \Doctrine\Common\Collections\ArrayCollection();
        $this->events = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set pos
     *
     * @param integer $pos
     */
    public function setPos($pos)
    {
        $this->pos = $pos;
    }

    /**
     * Get pos
     *
     * @return integer $pos
     */
    public function getPos()
    {
        return $this->pos;
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
     * Add categoryName
     *
     * @param Cx\Modules\Calendar\Model\Entity\CategoryName $categoryName
     */
    public function addCategoryName(\Cx\Modules\Calendar\Model\Entity\CategoryName $categoryName)
    {
        $this->categoryNames[] = $categoryName;
    }

    /**
     * Set categoryNames
     *
     * @param Doctrine\Common\Collections\Collection $categoryNames
     */
    public function setCategoryNames($categoryNames)
    {
        $this->categoryNames = $categoryNames;
    }

    /**
     * Get getCategoryNameByLangId
     *
     * @param integer $langId lang id
     *
     * @return null|\Cx\Modules\Calendar\Model\Entity\CategoryName
     */
    public function getCategoryNameByLangId($langId)
    {
        if (!$this->categoryNames) {
            return null;
        }

        foreach ($this->categoryNames as $categoryName) {
            if ($categoryName->getLangId() == $langId) {
                return $categoryName;
            }
        }

        return null;
    }

    /**
     * Get categoryNames
     *
     * @return Doctrine\Common\Collections\Collection $categoryNames
     */
    public function getCategoryNames()
    {
        return $this->categoryNames;
    }

    /**
     * Add event
     *
     * @param Cx\Modules\Calendar\Model\Entity\Event $event
     */
    public function addEvent(\Cx\Modules\Calendar\Model\Entity\Event $event)
    {
        $this->events[] = $event;
    }

    /**
     * Set events
     *
     * @param Doctrine\Common\Collections\Collection $events
     */
    public function setEvents($events)
    {
        $this->events = $events;
    }

    /**
     * Get events
     *
     * @return Doctrine\Common\Collections\Collection $events
     */
    public function getEvents()
    {
        return $this->events;
    }
}