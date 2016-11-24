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
 * Main controller for Data
 *
 * @copyright  cloudrexx
 * @author     Project Team SS4U <info@cloudrexx.com>
 * @package    cloudrexx
 * @subpackage module_data
 */

namespace Cx\Modules\Data\Controller;

class JsonDataException extends \Exception {}

/**
 * Main controller for Data
 *
 * @copyright  cloudrexx
 * @author     Project Team SS4U <info@cloudrexx.com>
 * @package    cloudrexx
 * @subpackage module_data
 */
class ComponentController extends \Cx\Core\Core\Model\Entity\SystemComponentController implements \Cx\Core\Json\JsonAdapter {

    /**
     * getControllerClasses
     *
     * @return type
     */
    public function getControllerClasses() {
        return array();
    }

     /**
     * {@inheritdoc}
     */
    public function getControllersAccessableByJson() {
        return array('ComponentController');
    }

    /**
     * Load the component Data.
     *
     * @param \Cx\Core\ContentManager\Model\Entity\Page $page       The resolved page
     */
    public function load(\Cx\Core\ContentManager\Model\Entity\Page $page) {
        global $subMenuTitle, $objTemplate, $_CORELANG;

        switch ($this->cx->getMode()) {
            case \Cx\Core\Core\Controller\Cx::MODE_FRONTEND:
                $objData = new \Cx\Modules\Data\Controller\Data(\Env::get('cx')->getPage()->getContent());
                \Env::get('cx')->getPage()->setContent($objData->getPage());
                break;

            case \Cx\Core\Core\Controller\Cx::MODE_BACKEND:
                $this->cx->getTemplate()->addBlockfile('CONTENT_OUTPUT', 'content_master', 'LegacyContentMaster.html');
                $objTemplate = $this->cx->getTemplate();

                \Permission::checkAccess(146, 'static'); // ID !!
                $subMenuTitle = $_CORELANG['TXT_DATA_MODULE'];
                $objData = new \Cx\Modules\Data\Controller\DataAdmin();
                $objData->getPage();
                break;

            default:
                break;
        }
    }

    /**
    * Do something before content is loaded from DB
    *
    * @param \Cx\Core\ContentManager\Model\Entity\Page $page       The resolved page
    */
    public function preContentLoad(\Cx\Core\ContentManager\Model\Entity\Page $page) {
        global $_CONFIG, $cl, $lang, $objInit, $dataBlocks, $lang, $dataBlocks, $themesPages, $page_template;
        // Initialize counter and track search engine robot
        \Cx\Core\Setting\Controller\Setting::init('Config', 'component', 'Yaml');

        if (\Cx\Core\Setting\Controller\Setting::getValue('dataUseModule') && $cl->loadFile(ASCMS_MODULE_PATH . '/Data/Controller/DataBlocks.class.php')) {
            $dataBlocks = new \Cx\Modules\Data\Controller\DataBlocks();
            $page = $this->cx->getPage();
            $page->setContent($dataBlocks->replace($page->getContent(), $page));
            $themesPages = $dataBlocks->replace($themesPages);
            $page_template = $dataBlocks->replace($page_template);
        }

    }

    /**
     * Returns an array of method names accessable from a JSON request
     * @return array List of method names
     */
    public function getAccessableMethods()
    {
        return array('getDataContent');
    }

    /**
     * Returns default permission as object
     * @return Object
     */
    public function getDefaultPermissions()
    {
        return new \Cx\Core_Modules\Access\Model\Entity\Permission(
            null, null, false
        );
    }

    /**
     * Returns all messages as string
     * @return String HTML encoded error messages
     */
    public function getMessagesAsString()
    {
        return '';
    }

    /**
     * Wrapper to __call()
     * @return string ComponentName
     */
    public function getName()
    {
        return parent::getName();
    }

    /**
     * Get Data content
     *
     * @param array $params
     */
    public function getDataContent($params)
    {
        $pageId = isset($params['get']['page'])
            ? contrexx_input2int($params['get']['page']) : 0;
        $placeholder = isset($params['get']['placeholder'])
            ? contrexx_input2raw($params['get']['placeholder']) : 0;
        $lang = isset($params['get']['lang'])
            ? contrexx_input2int($params['get']['lang']) : 0;

        if (empty($placeholder)) {
            return array('content' => '');
        }

        if (!empty($pageId)) {
            $pageRepo = $this->cx
                ->getDb()
                ->getEntityManager()
                ->getRepository('Cx\Core\ContentManager\Model\Entity\Page');
            $result = $pageRepo->findOneById($pageId);
            if (!$result) {
                return array('content' => '');
            }
            if ($result->getType() == \Cx\Core\ContentManager\Model\Entity\Page::TYPE_APPLICATION) {
                $content = $this->cx->getContentTemplateOfPage($result);
            } else {
                $content = $result->getContent();
            }
        } else {
            $theme = $this->getThemeFromInput($params);
            $file  =  !empty($params['get']['file'])
                    ? contrexx_input2raw($params['get']['file']) : '';
            if (empty($file)) {
                throw new JsonDataException(
                    __METHOD__ .': the input file cannot be empty'
                );
            }
            $content = $theme->getContentFromFile($file);
        }

        if (!preg_match('/' . preg_quote($placeholder) . '/', $content)) {
            return array('content' => '');
        }

        $dataBlocks = new \Cx\Modules\Data\Controller\DataBlocks();
        return array('content' => $dataBlocks->getData($placeholder, $lang));
    }

    /**
     * Get theme from the user input
     *
     * @param array $params User input array
     * @return \Cx\Core\View\Model\Entity\Theme Theme instance
     * @throws JsonNewsException When theme id empty or theme does not exits in the system
     */
    protected function getThemeFromInput($params)
    {
        $themeId  = !empty($params['get']['template'])
            ? contrexx_input2int($params['get']['template']) : 0;
        if (empty($themeId)) {
            throw new JsonDataException(
                'The theme id is empty in the request'
            );
        }
        $themeRepository = new \Cx\Core\View\Model\Repository\ThemeRepository();
        $theme           = $themeRepository->findById($themeId);
        if (!$theme) {
            throw new JsonDataException(
                'The theme id '. $themeId .' does not exists.'
            );
        }
        return $theme;
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
         $eventListener = new \Cx\Modules\Data\Model\Event\DataEventListener($this->cx);
         $this->cx->getEvents()->addEventListener('clearEsiCache', $eventListener);
     }
}
