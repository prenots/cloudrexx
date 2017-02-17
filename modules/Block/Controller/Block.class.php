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
 * Block
 * @copyright   CLOUDREXX CMS - CLOUDREXX AG
 * @author      Cloudrexx Development Team <info@cloudrexx.com>
 * @version     1.0.0
 * @package     cloudrexx
 * @subpackage  module_block
 * @todo        Edit PHP DocBlocks!
 */

namespace Cx\Modules\Block\Controller;

/**
 * Block
 *
 * block module class
 * @copyright   CLOUDREXX CMS - CLOUDREXX AG
 * @author      Cloudrexx Development Team <info@cloudrexx.com>
 * @access      public
 * @version     1.0.0
 * @package     cloudrexx
 * @subpackage  module_block
 */
class Block extends \Cx\Modules\Block\Controller\BlockLibrary
{

    /**
     * 
     * @param array   $arrBlocks array of blocks
     * @param string  $code      code
     * @param integer $pageId    page ID
     */
    function setBlock($arrBlocks, &$code, $pageId)
    {
        foreach ($arrBlocks as $blockId) {
            $this->_setBlock(intval($blockId), $code, $pageId);
        }
    }

    /**
     * Set category block
     *
     * @param array   $arrCategoryBlocks array of category blocks
     * @param string  $code              code
     * @param integer $pageId            page ID
     */
    function setCategoryBlock($arrCategoryBlocks, &$code, $pageId)
    {
        foreach ($arrCategoryBlocks as $blockId) {
            $this->_setCategoryBlock(intval($blockId), $code, $pageId);
        }
    }

    /**
     * Set block global
     *
     * @param string  $code   Code
     * @param integer $pageId page ID
     */
    function setBlockGlobal(&$code, $pageId)
    {
        $this->_setBlockGlobal($code, $pageId);
    }
}
