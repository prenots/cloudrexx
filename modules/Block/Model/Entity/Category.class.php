<?php

/**
 * Cloudrexx
 *
 * @link      https://www.cloudrexx.com
 * @copyright Cloudrexx AG 2007-2017
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
 * Cx\Modules\Block
 *
 * @copyright   CLOUDREXX CMS - CLOUDREXX AG
 * @author      Cloudrexx Development Team <info@cloudrexx.com>
 * @version     1.0.0
 * @package     cloudrexx
 * @subpackage  module_block
 */

namespace Cx\Modules\Block\Model\Entity;

/**
 * Cx\Modules\Block\Model\Entity\Category
 *
 * @copyright   CLOUDREXX CMS - CLOUDREXX AG
 * @author      Manuel Schenk <manuel.schenk@comvation.com>
 * @version     1.0.0
 * @package     cloudrexx
 * @subpackage  module_block
 */
class Category extends \Cx\Model\Base\EntityBase
{
    /**
     * @var integer $id
     */
    protected $id;

    /**
     * @var string $name
     */
    protected $name;

    /**
     * @var string $seperator
     */
    protected $seperator;

    /**
     * @var integer $order
     */
    protected $order;

    /**
     * @var boolean $status
     */
    protected $status;

    /**
     * @var Doctrine\Common\Collections\Collection $blocks
     */
    protected $blocks;

    /**
     * @var Doctrine\Common\Collections\Collection $categories
     */
    protected $categories;

    /**
     * @var Cx\Modules\Block\Model\Entity\Category
     */
    protected $parent;


    public function __construct()
    {
        $this->blocks = new \Doctrine\Common\Collections\ArrayCollection();
        $this->categories = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return string $name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set seperator
     *
     * @param string $seperator
     */
    public function setSeperator($seperator)
    {
        $this->seperator = $seperator;
    }

    /**
     * Get seperator
     *
     * @return string $seperator
     */
    public function getSeperator()
    {
        return $this->seperator;
    }

    /**
     * Set order
     *
     * @param integer $order
     */
    public function setOrder($order)
    {
        $this->order = $order;
    }

    /**
     * Get order
     *
     * @return integer $order
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * Set status
     *
     * @param boolean $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * Get status
     *
     * @return boolean $status
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Add block
     *
     * @param Cx\Modules\Block\Model\Entity\Block $block
     */
    public function addBlock(\Cx\Modules\Block\Model\Entity\Block $block)
    {
        $this->blocks[] = $block;
    }

    /**
     * Set blocks
     *
     * @param Doctrine\Common\Collections\Collection $blocks
     */
    public function setBlocks($blocks)
    {
        $this->blocks = $blocks;
    }

    /**
     * Get blocks
     *
     * @return Doctrine\Common\Collections\Collection $blocks
     */
    public function getBlocks()
    {
        return $this->blocks;
    }

    /**
     * Add category
     *
     * @param Cx\Modules\Block\Model\Entity\Category $category
     */
    public function addCategory(\Cx\Modules\Block\Model\Entity\Category $category)
    {
        $this->categories[] = $category;
    }

    /**
     * Set categories
     *
     * @param Doctrine\Common\Collections\Collection $categories
     */
    public function setCategories($categories)
    {
        $this->categories = $categories;
    }

    /**
     * Get categories
     *
     * @return Doctrine\Common\Collections\Collection $categories
     */
    public function getCategories()
    {
        return $this->categories;
    }

    /**
     * Set parent
     *
     * @param Cx\Modules\Block\Model\Entity\Category $parent
     */
    public function setParent($parent)
    {
        $this->parent = $parent;
    }

    /**
     * Get parent
     *
     * @return Cx\Modules\Block\Model\Entity\Category $parent
     */
    public function getParent()
    {
        return $this->parent;
    }
}
