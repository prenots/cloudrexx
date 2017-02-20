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
 * @author      Project Team SS4U <info@comvation.com>
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
 * @author      Project Team SS4U <info@comvation.com>
 * @package     cloudrexx
 * @subpackage  module_block
 * @version     1.0.0
 */

class EsiWidgetController extends \Cx\Core_Modules\Widget\Controller\EsiWidgetController {
    /**
     * Current page ID
     *
     * @var integer 
     */
    protected $currentPageId;

    /**
     * Random name for randomizer block
     *
     * @var string
     */
    protected $randomName;

    /**
     * Parses a widget
     *
     * @param string                     $name     Widget name
     * @param \Cx\Core\Html\Sigma Widget $template Template
     * @param string                     $locale   RFC 3066 locale identifier
     */
    public function parseWidget($name, $template, $locale)
    {
        global $_CONFIG;

        if ($_CONFIG['blockStatus'] != '1') {
            return;
        }

        // Parse blocks [[BLOCK_<ID>]]
        $block        = new Block();
        $matches      = null;
        $code         = '{' . $name . '}';
        $blockPattern = '/^' . $block->blockNamePrefix . '([0-9]+)/';
        if (preg_match($blockPattern, $name, $matches)) {
            $block->setBlock(array($matches[1]), $code, $this->currentPageId);
            $template->setVariable($name, $code);
        }

        // parse global block [[BLOCK_GLOBAL]]
        if ($name == $block->blockNamePrefix . 'GLOBAL') {
            $block->setBlockGlobal($code, $this->currentPageId);
            $template->setVariable($name, $code);
        }

        // Set category blocks [[BLOCK_CAT_<ID>]]
        $catMatches = null;
        $catPattern = '/^' . $block->blockNamePrefix . 'CAT_([0-9]+)/';
        if (preg_match($catPattern, $name, $catMatches)) {
            $block->setCategoryBlock(array($catMatches[1]), $code, $this->currentPageId);
            $template->setVariable($name, $code);
        }

        // Parse random blocks [[BLOCK_RANDOMIZER]], [[BLOCK_RANDOMIZER_2]],
        //                     [[BLOCK_RANDOMIZER_3]], [[BLOCK_RANDOMIZER_4]]
        if ($_CONFIG['blockRandom'] != '1') {
            return;
        }

        if (
            preg_match(
                '/^' . $block->blockNamePrefix . 'RANDOMIZER(_([2-4])|$)/',
                $name
            )
        ) {
            $code = '{' . $this->randomName . '}';
            if (preg_match($blockPattern, $this->randomName, $matches)) {
                $block->setBlock(array($matches[1]), $code, $this->currentPageId);
                $template->setVariable($name, $code);
            }
        }
    }

    /**
     * Returns the content of a widget
     *
     * @param array $params JsonAdapter parameters
     *
     * @return array Content in an associative array
     */
    public function getWidget($params)
    {
        if (isset($params['get']) && isset($params['get']['page'])) {
            $this->currentPageId = $params['get']['page'];
        }

        $widgetName = '';
        if (isset($params['get']) && isset($params['get']['name'])) {
            $widgetName = contrexx_input2raw($params['get']['name']);
        }

        $block = new Block();
        if (
            preg_match(
                '/^' . $block->blockNamePrefix . 'RANDOMIZER(_([2-4])|$)/',
                $widgetName
            )
        ) {
            if (!isset($params['get']) || !isset($params['get']['randomName'])) {
                return;
            }
            $this->randomName = contrexx_input2raw($params['get']['randomName']);
        }
        return parent::getWidget($params);
    }
}
