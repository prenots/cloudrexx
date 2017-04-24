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
 * Class EsiWidgetController
 *
 * @copyright   CLOUDREXX CMS - Cloudrexx AG Thun
 * @author      Project Team SS4U <info@cloudrexx.com>
 * @package     cloudrexx
 * @subpackage  module_block
 * @version     1.0.0
 */

namespace Cx\Modules\Block\Controller;

/**
 * JsonAdapter Controller to handle EsiWidgets
 * Usage:
 * - Create a subclass that implements parseWidget()
 * - Register it as a Controller in your ComponentController
 *
 * @copyright   CLOUDREXX CMS - Cloudrexx AG Thun
 * @author      Project Team SS4U <info@cloudrexx.com>
 * @package     cloudrexx
 * @subpackage  module_block
 * @version     1.0.0
 */

class EsiWidgetController extends \Cx\Core_Modules\Widget\Controller\EsiWidgetController {
    /**
     * Parses a widget
     *
     * @param string                                 $name     Widget name
     * @param \Cx\Core\Html\Sigma                    $template Widget template
     * @param \Cx\Core\Routing\Model\Entity\Response $response Response object
     * @param array                                  $params   Get parameters
     */
    public function parseWidget($name, $template, $response, $params)
    {
        if (
            \Cx\Core\Setting\Controller\Setting::getValue(
                'blockStatus',
                'Config'
            ) != '1'
        ) {
            return;
        }

        // Parse blocks [[BLOCK_<ID>]]
        $block        = new Block();
        $matches      = null;
        $code         = '{' . $name . '}';
        $pageId       = $params['page']->getId();
        $blockPattern = '/^' . $block->blockNamePrefix . '([0-9]+)$/';
        if (preg_match($blockPattern, $name, $matches)) {
            $block->setBlock(array($matches[1]), $code, $pageId);
            $template->setVariable($name, $code);
            return;
        }

        // parse global block [[BLOCK_GLOBAL]]
        if ($name == $block->blockNamePrefix . 'GLOBAL') {
            $block->setBlockGlobal($code, $pageId);
            $template->setVariable($name, $code);
            return;
        }

        // Set category blocks [[BLOCK_CAT_<ID>]]
        $catMatches = null;
        $catPattern = '/^' . $block->blockNamePrefix . 'CAT_([0-9]+)$/';
        if (preg_match($catPattern, $name, $catMatches)) {
            $block->setCategoryBlock(array($catMatches[1]), $code, $pageId);
            $template->setVariable($name, $code);
            return;
        }

        // Parse random blocks [[BLOCK_RANDOMIZER]], [[BLOCK_RANDOMIZER_2]],
        //                     [[BLOCK_RANDOMIZER_3]], [[BLOCK_RANDOMIZER_4]]
        if (
            \Cx\Core\Setting\Controller\Setting::getValue(
                'blockRandom',
                'Config'
            ) != '1'
        ) {
            return;
        }

        if (
            !preg_match(
                '/^' . $block->blockNamePrefix . 'RANDOMIZER(_([2-4])|$)/',
                $name
            )
        ) {
            return;
        }
        $code = '{' . $params['randomName'] . '}';
        if (preg_match($blockPattern, $params['randomName'], $matches)) {
            $block->setBlock(array($matches[1]), $code, $pageId);
            $template->setVariable($name, $code);
        }
    }
}
