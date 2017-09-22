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
 * Main controller for Block
 *
 * @copyright   Cloudrexx AG
 * @author      Project Team SS4U <info@cloudrexx.com>
 * @package     cloudrexx
 * @subpackage  module_block
 */

namespace Cx\Modules\Block\Controller;

/**
 * Main controller for Block
 *
 * @copyright   Cloudrexx AG
 * @author      Project Team SS4U <info@cloudrexx.com>
 * @package     cloudrexx
 * @subpackage  module_block
 */
class ComponentController extends \Cx\Core\Core\Model\Entity\SystemComponentController {
    public function getControllerClasses() {
        // Return an empty array here to let the component handler know that there
        // does not exist a backend, nor a frontend controller of this component.
        return array('JsonBlock', 'EsiWidget', 'RandomEsiWidget');
    }

    public function getControllersAccessableByJson() {
        return array(
            'JsonBlockController',
            'EsiWidgetController',
            'RandomEsiWidgetController'
        );
    }

     /**
     * Load your component.
     *
     * @param \Cx\Core\ContentManager\Model\Entity\Page $page       The resolved page
     */
    public function load(\Cx\Core\ContentManager\Model\Entity\Page $page) {
        global $_CORELANG, $subMenuTitle, $objTemplate;

        switch ($this->cx->getMode()) {
            case \Cx\Core\Core\Controller\Cx::MODE_BACKEND:
                $this->cx->getTemplate()->addBlockfile('CONTENT_OUTPUT', 'content_master', 'LegacyContentMaster.html');
                $objTemplate = $this->cx->getTemplate();

                \Permission::checkAccess(76, 'static');
                $subMenuTitle = $_CORELANG['TXT_BLOCK_SYSTEM'];
                $objBlock = new \Cx\Modules\Block\Controller\BlockManager();
                $objBlock->getPage();
                break;
        }
    }

    /**
     * Do something after system initialization
     *
     * USE CAREFULLY, DO NOT DO ANYTHING COSTLY HERE!
     * CALCULATE YOUR STUFF AS LATE AS POSSIBLE.
     * This event must be registered in the postInit-Hook definition
     * file config/postInitHooks.yml.
     * @param \Cx\Core\Core\Controller\Cx   $cx The instance of \Cx\Core\Core\Controller\Cx
     */
    public function postInit(\Cx\Core\Core\Controller\Cx $cx)
    {
        $block            = new Block();
        $widgetController = $this->getComponent('Widget');

        // Set blocks [[BLOCK_<ID>]]
        $blockNames = $block->getBlockNamesByCriteria('block');
        if ($blockNames) {
            foreach ($blockNames as $blockName) {
                $widget = new \Cx\Core_Modules\Widget\Model\Entity\EsiWidget(
                    $this,
                    $blockName
                );
                $widget->setEsiVariable(
                    \Cx\Core_Modules\Widget\Model\Entity\EsiWidget::ESI_VAR_ID_THEME |
                    \Cx\Core_Modules\Widget\Model\Entity\EsiWidget::ESI_VAR_ID_CHANNEL
                );
                $widgetController->registerWidget($widget);
            }
        }

        // Set global block [[BLOCK_GLOBAL]]
        $widget = new \Cx\Core_Modules\Widget\Model\Entity\EsiWidget(
            $this,
            $block->blockNamePrefix . 'GLOBAL'
        );
        $widget->setEsiVariable(
            \Cx\Core_Modules\Widget\Model\Entity\EsiWidget::ESI_VAR_ID_THEME |
            \Cx\Core_Modules\Widget\Model\Entity\EsiWidget::ESI_VAR_ID_CHANNEL
        );
        $widgetController->registerWidget($widget);

        // Set category blocks [[BLOCK_CAT_<ID>]]
        $catBlockNames = $block->getBlockNamesByCriteria('categoryBlock');
        foreach ($catBlockNames as $catBlockName) {
            $widget = new \Cx\Core_Modules\Widget\Model\Entity\EsiWidget(
                $this,
                $catBlockName
            );
            $widget->setEsiVariable(
                \Cx\Core_Modules\Widget\Model\Entity\EsiWidget::ESI_VAR_ID_THEME |
                \Cx\Core_Modules\Widget\Model\Entity\EsiWidget::ESI_VAR_ID_CHANNEL
            );
            $widgetController->registerWidget($widget);
        }

        // Set random blocks [[BLOCK_RANDOMIZER]], [[BLOCK_RANDOMIZER_2]],
        //                   [[BLOCK_RANDOMIZER_3]], [[BLOCK_RANDOMIZER_4]]
        $randomizerNames = $block->getBlockNamesByCriteria('randomizerBlock');
        foreach ($randomizerNames as $key => $randomizerName) {
            if ($key > 0) {
                $key += 1;
            }
            $widgets = array(
                new \Cx\Core_Modules\Widget\Model\Entity\RandomEsiWidget(
                    $this,
                    $randomizerName
                )
            );
            // this is used for parsing the randomized sub-widgets:
            $widgets[] = new \Cx\Core_Modules\Widget\Model\Entity\EsiWidget(
                $this,
                'block_content_' . $key,
                \Cx\Core_Modules\Widget\Model\Entity\Widget::TYPE_CALLBACK
            );
            foreach ($widgets as $widget) {
                $widget->setEsiVariable(
                    \Cx\Core_Modules\Widget\Model\Entity\EsiWidget::ESI_VAR_ID_THEME |
                    \Cx\Core_Modules\Widget\Model\Entity\EsiWidget::ESI_VAR_ID_CHANNEL
                );
                $widgetController->registerWidget($widget);
            }
        }
    }
}
