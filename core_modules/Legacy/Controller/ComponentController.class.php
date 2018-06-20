<?php declare(strict_types=1);

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
 * Specific ComponentController for this Component.
 *
 * Use the examples here to easily customize your component. Delete this file
 * if you don't need it. Remove any methods you don't need! 
 * @copyright   Cloudrexx AG
 * @author      Michael Ritter <michael.ritter@cloudrexx.com>
 * @package     cloudrexx
 * @subpackage  core_module_legacy
 */

namespace Cx\Core_Modules\Legacy\Controller;

/**
 * Specific ComponentController for this Component.
 *
 * Use the examples here to easily customize your component. Delete this file
 * if you don't need it. Remove any methods you don't need! 
 * @copyright   Cloudrexx AG
 * @author      Michael Ritter <michael.ritter@cloudrexx.com>
 * @package     cloudrexx
 * @subpackage  core_module_legacy
 */
class ComponentController extends \Cx\Core\Core\Model\Entity\SystemComponentController implements \Cx\Core\Event\Model\Entity\EventListener {

    /**
     * Returns all Controller class names for this component (except this)
     *
     * Be sure to return all your controller classes if you add your own
     * @return array List of Controller class names (without namespace)
     */
    public function getControllerClasses() {
        return array();
    }

    /**
     * Register your event listeners here
     *
     * USE CAREFULLY, DO NOT DO ANYTHING COSTLY HERE!
     * CALCULATE YOUR STUFF AS LATE AS POSSIBLE.
     * Keep in mind, that you can also register your events later.
     * Do not do anything else here than initializing your event listeners and
     * list statements like
     * $this->cx->getEvents()->addEventListener($eventName, $listener);
     */
    public function registerEventListeners() {
        $this->cx->getEvents()->addEventListener('View.Sigma:loadContent', $this);
        $this->cx->getEvents()->addEventListener('View.Sigma:setVariable', $this);
    }

    public function onEvent($eventName, array $eventArgs) {
        $eventArgs['content'] = preg_replace_callback(
            '/(?:{|\[\[)NODE_([A-Z0-9_]+)(?:}|\]\])/',
            function($matches) {
                return 'func_node(' . str_replace('_', ',', $matches[1]) . ')';
            },
            $eventArgs['content']
        );
        if ($eventName == 'View.Sigma:setVariable') {
            // parse widgets again as Sigma does not parse placeholders /
            // callbacks on parse()
            $template = new \Cx\Core_Modules\Widget\Model\Entity\Sigma();
            $template->setTemplate($eventArgs['content']);
            $targetComponent = '';
            $targetEntity = '';
            $targetId = '';
            if ($template->getParseTarget()) {
                $targetComponent = $template->getParseTarget()->getSystemComponent()->getName();
                $targetEntity = get_class($template->getParseTarget());
                $targetId = $template->getParseTarget()->getId();
            }
            $this->getComponent('Widget')->parseWidgets(
                $template,
                $targetComponent,
                $targetEntity,
                $targetId
            );
            $eventArgs['content'] = $template->get();
        }
        // no need to parse widgets for View.Sigma:loadContent again as
        // Sigma parses callback functions in content on parse().
    }
}
