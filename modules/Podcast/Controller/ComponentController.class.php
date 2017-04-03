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
 * Main controller for Podcast
 *
 * @copyright   cloudrexx
 * @author      Project Team SS4U <info@cloudrexx.com>
 * @package     cloudrexx
 * @subpackage  module_podcast
 */

namespace Cx\Modules\Podcast\Controller;

/**
 * Main controller for Podcast
 *
 * @copyright   cloudrexx
 * @author      Project Team SS4U <info@cloudrexx.com>
 * @package     cloudrexx
 * @subpackage  module_podcast
 */
class ComponentController extends \Cx\Core\Core\Model\Entity\SystemComponentController {
    /**
     * Returns all Controller class names for this component (except this)
     *
     * Be sure to return all your controller classes if you add your own
     * @return array List of Controller class names (without namespace)
     */
    public function getControllerClasses()
    {
        return array('EsiWidget');
    }

    /**
     * Returns a list of JsonAdapter class names
     *
     * The array values might be a class name without namespace. In that case
     * the namespace \Cx\{component_type}\{component_name}\Controller is used.
     * If the array value starts with a backslash, no namespace is added.
     *
     * Avoid calculation of anything, just return an array!
     * @return array List of ComponentController classes
     */
    public function getControllersAccessableByJson()
    {
        return array('EsiWidgetController');
    }

    /**
     * Load the component Podcast.
     *
     * @param \Cx\Core\ContentManager\Model\Entity\Page $page       The resolved page
     */
    public function load(\Cx\Core\ContentManager\Model\Entity\Page $page) {
        global $subMenuTitle, $objTemplate, $_CORELANG;

        switch ($this->cx->getMode()) {
            case \Cx\Core\Core\Controller\Cx::MODE_FRONTEND:
                $objPodcast = new Podcast(\Env::get('cx')->getPage()->getContent());
                \Env::get('cx')->getPage()->setContent($objPodcast->getPage());
                break;

            case \Cx\Core\Core\Controller\Cx::MODE_BACKEND:
                $this->cx->getTemplate()->addBlockfile('CONTENT_OUTPUT', 'content_master', 'LegacyContentMaster.html');
                $objTemplate = $this->cx->getTemplate();

                \Permission::checkAccess(87, 'static');
                $subMenuTitle = $_CORELANG['TXT_PODCAST'];
                $objPodcast = new PodcastManager();
                $objPodcast->getPage();
                break;

            default:
                break;
        }
    }

    /**
     * Do something after system initialization
     *
     * This event must be registered in the postInit-Hook definition
     * file config/postInitHooks.yml.
     * @param \Cx\Core\Core\Controller\Cx $cx The instance of \Cx\Core\Core\Controller\Cx
     */
    public function postInit(\Cx\Core\Core\Controller\Cx $cx)
    {
        $widgetController = $this->getComponent('Widget');

        $params = array();
        if (isset($_GET['section'])) {
            $params['section'] = contrexx_input2raw($_GET['section']);
        }
        $widget = new \Cx\Core_Modules\Widget\Model\Entity\EsiWidget(
            $this,
            'PODCAST_FILE',
            false,
            '',
            '',
            $params
        );
        $widget->setEsiVariable(
            \Cx\Core_Modules\Widget\Model\Entity\EsiWidget::ESI_VAR_ID_THEME |
            \Cx\Core_Modules\Widget\Model\Entity\EsiWidget::ESI_VAR_ID_CHANNEL
        );
        $widgetController->registerWidget(
            $widget
        );
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
        $eventListener = new \Cx\Modules\Podcast\Model\Event\PodcastEventListener($this->cx);
        $this->cx->getEvents()->addEventListener('SearchFindContent', $eventListener);
        $this->cx->getEvents()->addEventListener('mediasource.load', $eventListener);
    }
}
