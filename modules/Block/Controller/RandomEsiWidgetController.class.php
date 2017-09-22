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
 * Class RandomEsiWidgetController
 *
 * @copyright   CLOUDREXX CMS - Cloudrexx AG Thun
 * @author      Project Team SS4U <info@cloudrexx.com>
 * @package     cloudrexx
 * @subpackage  module_block
 * @version     1.0.0
 */

namespace Cx\Modules\Block\Controller;

/**
 * JsonAdapter Controller to handle RandomEsiWidgets
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

class RandomEsiWidgetController extends \Cx\Core_Modules\Widget\Controller\RandomEsiWidgetController {
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

        // Parse random blocks [[BLOCK_RANDOMIZER]], [[BLOCK_RANDOMIZER_2]],
        //                     [[BLOCK_RANDOMIZER_3]], [[BLOCK_RANDOMIZER_4]]
        if (
            \Cx\Core\Setting\Controller\Setting::getValue(
                'blockRandom',
                'Config'
            ) != '1' ||
            !preg_match('/^block_content_\d{1}$/', $name)
        ) {
            return;
        }
        $block = new Block();
        $code  = '{BLOCK_' . $params['id'] . '}';
        $block->setBlock(array($params['id']), $code, $params['page']->getId());
        $template->setVariable($name, $code);
    }

    /**
     * {@inheritdoc}
     */
    protected function getRandomEsiWidgetContentInfos($widget, $params)
    {
        $block   = new Block();
        $matches = null;
        if (
            !preg_match(
                '/^' . $block->blockNamePrefix . 'RANDOMIZER(_([2-4])|$)/',
                $widget->getName(),
                $matches
            )
        ) {
            return array();
        }

        $key = 0;
        if (isset($matches[2]) && !empty($matches[2])) {
            $key = $matches[2];
        }
        // fetch all blocks
        $langId = \FWLanguage::getLangIdByIso639_1($params['get']['locale']);
        $blockIds = $block->getBlockNamesForRandomizer($langId, $key);

        // foreach block, get ESI infos:
        $esiInfos = array();
        foreach ($blockIds as $blockId) {
            // adapter name, adapter method, params
            $esiInfos[] = array(
                $this->getName(),
                'getWidget',
                array(
                    'name' => 'block_content_' . $key,
                    'id'   => $blockId,
                    'lang' => $params['get']['locale'],
                    'page' => $params['get']['page']
                ),
            );
        }
        return $esiInfos;
    }
}
