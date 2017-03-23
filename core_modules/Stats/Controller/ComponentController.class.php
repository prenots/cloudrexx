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
 * Main controller for Stats
 *
 * @copyright   cloudrexx
 * @author      Project Team SS4U <info@cloudrexx.com>
 * @package cloudrexx
 * @subpackage coremodule_stats
 */

namespace Cx\Core_Modules\Stats\Controller;

/**
 * Main controller for Stats
 *
 * @copyright   cloudrexx
 * @author      Project Team SS4U <info@cloudrexx.com>
 * @package cloudrexx
 * @subpackage coremodule_stats
 */
class ComponentController extends \Cx\Core\Core\Model\Entity\SystemComponentController {

    /**
     * Instance of StatsLibrary
     *
     * @var StatsLibrary
     */
    protected $counter;

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
     * Load the component Stats.
     *
     * @param \Cx\Core\ContentManager\Model\Entity\Page $page       The resolved page
     */
    public function load(\Cx\Core\ContentManager\Model\Entity\Page $page) {
        global $subMenuTitle, $objTemplate, $_CORELANG;

        \Permission::checkAccess(163, 'static');
        $this->cx->getTemplate()->addBlockfile('CONTENT_OUTPUT', 'content_master', 'LegacyContentMaster.html');
        $objTemplate = $this->cx->getTemplate();

        $subMenuTitle = $_CORELANG['TXT_STATISTIC'];
        $statistic= new \Cx\Core_Modules\Stats\Controller\Stats();
        $statistic->getContent();
    }

     /**
     * Do something before content is loaded from DB
     *
     * @param \Cx\Core\ContentManager\Model\Entity\Page $page       The resolved page
     */
    public function preContentLoad(\Cx\Core\ContentManager\Model\Entity\Page $page) {
        // Initialize counter and track search engine robot
        $this->getCounterInstance()->checkForSpider();
    }

    /**
     * Do something after system initialization
     *
     * USE CAREFULLY, DO NOT DO ANYTHING COSTLY HERE!
     * CALCULATE YOUR STUFF AS LATE AS POSSIBLE.
     * This event must be registered in the postInit-Hook definition
     * file config/postInitHooks.yml.
     *
     * @param \Cx\Core\Core\Controller\Cx $cx The instance of \Cx\Core\Core\Controller\Cx
     */
    public function postInit(\Cx\Core\Core\Controller\Cx $cx)
    {
        $widgetController = $this->getComponent('Widget');
        $params = array();

        if (isset($_GET['term']) && !empty($_GET['term'])) {
            $params['term'] = contrexx_input2raw($_GET['term']);
        }

        if (isset($_GET['section']) && !empty($_GET['section'])) {
            $params['section'] = contrexx_input2raw($_GET['section']);
        }

        foreach (
            array(
                'ONLINE_USERS',
                'VISITOR_NUMBER',
                'COUNTER',
                'GOOGLE_ANALYTICS'
            ) as $widgetName
        ) {
            $parameter = array();
            if ($widgetName == 'COUNTER') {
                $parameter = $params;
            }
            $widget = new \Cx\Core_Modules\Widget\Model\Entity\EsiWidget(
                $this,
                $widgetName,
                false,
                '',
                '',
                $parameter
            );
            $widget->setEsiVariable(
                \Cx\Core_Modules\Widget\Model\Entity\EsiWidget::ESI_VAR_ID_LANG |
                \Cx\Core_Modules\Widget\Model\Entity\EsiWidget::ESI_VAR_ID_PAGE
            );
            $widgetController->registerWidget(
                $widget
            );
        }
    }

    /**
     * Get the Counter instance, if instance already created use the existing one
     *
     * @return \Cx\Core_Modules\Stats\Controller\StatsLibrary
     */
    public function getCounterInstance()
    {

        if (!$this->counter) {
            $this->counter = new \Cx\Core_Modules\Stats\Controller\StatsLibrary();
        }

        return $this->counter;
    }

}
