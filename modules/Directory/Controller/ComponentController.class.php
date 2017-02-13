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
 * Main controller for Directory
 *
 * @copyright   Cloudrexx AG
 * @author      Project Team SS4U <info@cloudrexx.com>
 * @package     cloudrexx
 * @subpackage  module_directory
 */

namespace Cx\Modules\Directory\Controller;

/**
 * Main controller for Directory
 *
 * @copyright   Cloudrexx AG
 * @author      Project Team SS4U <info@cloudrexx.com>
 * @package     cloudrexx
 * @subpackage  module_directory
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
     * Load your component.
     *
     * @param \Cx\Core\ContentManager\Model\Entity\Page $page       The resolved page
     */
    public function load(\Cx\Core\ContentManager\Model\Entity\Page $page) {
        global $_CORELANG, $subMenuTitle, $objTemplate;
        switch ($this->cx->getMode()) {
            case \Cx\Core\Core\Controller\Cx::MODE_FRONTEND:
                $objDirectory = new Directory(\Env::get('cx')->getPage()->getContent());
                \Env::get('cx')->getPage()->setContent($objDirectory->getPage());
                $directory_pagetitle = $objDirectory->getPageTitle();
                if (!empty($directory_pagetitle)) {
                    \Env::get('cx')->getPage()->setTitle($directory_pagetitle);
                    \Env::get('cx')->getPage()->setContentTitle($directory_pagetitle);
                    \Env::get('cx')->getPage()->setMetaTitle($directory_pagetitle);
                }
                if ($_GET['cmd'] == 'detail' && isset($_GET['id'])) {
                    $objTemplate->setVariable(array(
                        'DIRECTORY_ENTRY_ID' => intval($_GET['id']),
                    ));
                }
                break;

            case \Cx\Core\Core\Controller\Cx::MODE_BACKEND:

                $this->cx->getTemplate()->addBlockfile('CONTENT_OUTPUT', 'content_master', 'LegacyContentMaster.html');
                $objTemplate = $this->cx->getTemplate();

                $subMenuTitle = $_CORELANG['TXT_LINKS_MODULE_DESCRIPTION'];
                $objDirectoryManager = new DirectoryManager();
                $objDirectoryManager->getPage();
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
    public function postInit()
    {
        //Get Directory Homecontent
        $lId = isset($_GET['lid']) ? contrexx_input2raw($_GET['lid']) : '';
        $cId = isset($_GET['cid']) ? contrexx_input2raw($_GET['cid']) : '';
        $widgetController = $this->getComponent('Widget');
        $widget = new \Cx\Core_Modules\Widget\Model\Entity\EsiWidget(
            $this,
            'DIRECTORY_FILE',
            false,
            '',
            '',
            array('lid' => $lId, 'cid' => $cId)
        );
        $widgetController->registerWidget(
            $widget
        );

        //Show Latest Directory entries
        for ($i = 1; $i <= 10; $i++) {
            $widget = new \Cx\Core_Modules\Widget\Model\Entity\EsiWidget(
                $this,
                'directoryLatest_row_' . $i,
                true
            );
            $widgetController->registerWidget(
                $widget
            );
        }
    }

    /**
     * Do something for search the content
     *
     * @param \Cx\Core\ContentManager\Model\Entity\Page $page       The resolved page
     */
    public function preContentParse(\Cx\Core\ContentManager\Model\Entity\Page $page) {
        $this->cx->getEvents()->addEventListener('SearchFindContent', new \Cx\Modules\Directory\Model\Event\DirectoryEventListener());
   }
}
