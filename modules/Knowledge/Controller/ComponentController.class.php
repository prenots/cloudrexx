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
 * Main controller for Knowledge
 *
 * @copyright   Cloudrexx AG
 * @author      Project Team SS4U <info@cloudrexx.com>
 * @package     cloudrexx
 * @subpackage  module_knowledge
 */

namespace Cx\Modules\Knowledge\Controller;

/**
 * Main controller for Knowledge
 *
 * @copyright   Cloudrexx AG
 * @author      Project Team SS4U <info@cloudrexx.com>
 * @package     cloudrexx
 * @subpackage  module_knowledge
 */
class ComponentController extends \Cx\Core\Core\Model\Entity\SystemComponentController implements \Cx\Core\Json\JsonAdapter {
    /**
     * Get controller classes
     *
     * @return array
     */
    public function getControllerClasses() {
        // Return an empty array here to let the component handler know that there
        // does not exist a backend, nor a frontend controller of this component.
        return array();
    }

    /**
     * Returns a list of JsonAdapter class names
     *
     * @return array list of JsonAdapter class names
     */
    public function getControllersAccessableByJson()
    {
        return array('ComponentController');
    }

    /**
     * Load your component.
     *
     * @param \Cx\Core\ContentManager\Model\Entity\Page $page The resolved page
     */
    public function load(\Cx\Core\ContentManager\Model\Entity\Page $page) {
        global $subMenuTitle, $_CORELANG, $objTemplate;
        switch ($this->cx->getMode()) {
            case \Cx\Core\Core\Controller\Cx::MODE_FRONTEND:
                $objKnowledge = new Knowledge(\Env::get('cx')->getPage()->getContent());
                \Env::get('cx')->getPage()->setContent($objKnowledge->getPage());
                if (!empty($objKnowledge->pageTitle)) {
                    \Env::get('cx')->getPage()->setTitle($objKnowledge->pageTitle);
                    \Env::get('cx')->getPage()->setContentTitle($objKnowledge->pageTitle);
                    \Env::get('cx')->getPage()->setMetaTitle($objKnowledge->pageTitle);
                }
                break;

            case \Cx\Core\Core\Controller\Cx::MODE_BACKEND:
                $this->cx->getTemplate()->addBlockfile('CONTENT_OUTPUT', 'content_master', 'LegacyContentMaster.html');
                $objTemplate = $this->cx->getTemplate();

                if (file_exists($this->cx->getClassLoader()->getFilePath($this->getDirectory() . '/View/Style/backend.css'))) {
                    \JS::registerCSS(substr($this->getDirectory(false, true) . '/View/Style/backend.css', 1));
                }

                \Permission::checkAccess(129, 'static');
                $subMenuTitle = $_CORELANG['TXT_KNOWLEDGE'];
                $objKnowledge = new KnowledgeAdmin();
                $objKnowledge->getPage();
                break;
        }
    }

    /**
     * Do something before content is loaded from DB
     *
     * @param \Cx\Core\ContentManager\Model\Entity\Page $page The resolved page
     */
    public function preContentLoad(
        \Cx\Core\ContentManager\Model\Entity\Page $page
    ) {
        global $page_template, $themesPages;

        if ($this->cx->getMode() != \Cx\Core\Core\Controller\Cx::MODE_FRONTEND) {
            return;
        }

        // get knowledge content
        \Cx\Core\Setting\Controller\Setting::init('Config', 'component','Yaml');
        if (
            MODULE_INDEX > 2 ||
            !\Cx\Core\Setting\Controller\Setting::getValue(
                'useKnowledgePlaceholders',
                'Config'
            )
        ) {
            return;
        }

        $knowledgeInterface = new KnowledgeInterface();
        if (
            preg_match(
                '/{KNOWLEDGE_[A-Za-z0-9_]+}/i',
                $this->cx->getPage()->getContent()
            )
        ) {
            $knowledgeInterface->parse(
                $this->cx->getPage()->getContent(),
                $this->cx->getPage()
            );
        }
        if (preg_match('/{KNOWLEDGE_[A-Za-z0-9_]+}/i', $page_template)) {
            $knowledgeInterface->parse($page_template);
        }
        if (preg_match('/{KNOWLEDGE_[A-Za-z0-9_]+}/i', $themesPages['index'])) {
            $knowledgeInterface->parse($themesPages['index']);
        }
    }

    /**
     * Returns an array of method names accessable from a JSON request
     * @return array List of method names
     */
    public function getAccessableMethods()
    {
        return array('getArticlesOrTags');
    }

    /**
     * Returns default permission as object
     * @return Object
     */
    public function getDefaultPermissions() {
        return new \Cx\Core_Modules\Access\Model\Entity\Permission(
            null,
            null,
            false
        );
    }

    /**
     * Returns all messages as string
     * @return String HTML encoded error messages
     */
    public function getMessagesAsString() {
        return '';
    }

    /**
     * Wrapper to __call()
     * @return string ComponentName
     */
    public function getName() {
        return parent::getName();
    }

    /**
     * Get best rated/most viewed Articles or tag cloud
     *
     * @param array $params all given params from http request
     *
     * @return type
     */
    public function getArticlesOrTags($params)
    {
        $langId = !empty($params['get']['lang'])
                 ? contrexx_input2int($params['get']['lang']) : 0;
        $method = !empty($params['get']['method'])
                 ? contrexx_input2raw($params['get']['method']) : '';
        if (empty($langId) || empty($method)) {
            return array('content' => '');
        }

        try {
            $knowledgeInterface = new KnowledgeInterface();
            if (!method_exists($knowledgeInterface, $method)) {
                return array('content' => '');
            }
            return array('content' => $knowledgeInterface->$method($langId));
        } catch (\Exception $ex) {
            \DBG::log($ex->getMessage());
            return array('content' => '');
        }
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
         $eventListener = new \Cx\Modules\Knowledge\Model\Event\KnowledgeEventListener($this->cx);
         $this->cx->getEvents()->addEventListener('clearEsiCache', $eventListener);
     }
}
