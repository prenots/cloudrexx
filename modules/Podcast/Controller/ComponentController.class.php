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

class JsonPodcastException extends \Exception {}

/**
 * Main controller for Podcast
 *
 * @copyright   cloudrexx
 * @author      Project Team SS4U <info@cloudrexx.com>
 * @package     cloudrexx
 * @subpackage  module_podcast
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
     * Returns a list of JsonAdapter class names
     *
     * @return array list of JsonAdapter class names
     */
    public function getControllersAccessableByJson()
    {
        return array('ComponentController');
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
                \Env::get('cx')->getPage()->setContent($objPodcast->getPage($podcastFirstBlock));
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
    * Do something before content is loaded from DB
    *
    * @param \Cx\Core\ContentManager\Model\Entity\Page $page The resolved page
    */
    public function preContentLoad(
        \Cx\Core\ContentManager\Model\Entity\Page $page
    ) {
        global $_CONFIG, $page_template, $themesPages, $plainSection;

        // get latest podcast entries
        if (empty($_CONFIG['podcastHomeContent'])) {
            return;
        }
        $cache   = $this->cx->getComponent('Cache');
        $content = $page->getContent();
        $this->replaceEsiContent($cache, $content, $page);
        $this->replaceEsiContent($cache, $page_template);
        $isFirstBlock = 0;
        if (
            $plainSection === 'Podcast' &&
            preg_match('/\{PODCAST_FILE\}/i', $themesPages['index'])
        ) {
            $podcastBlockPos = strpos($themesPages['index'], '{PODCAST_FILE}');
            $contentPos      = strpos($themesPages['index'], '{CONTENT_FILE}');
            $isFirstBlock    = $podcastBlockPos < $contentPos ? 1 : 0;
        }
        $this->replaceEsiContent($cache, $themesPages['index'], null, $isFirstBlock);
    }
    /**
     * Replace esi content in given content
     *
     * @param \Cx\Core_Modules\Cache\Controller\ComponentController $cache        object of cache component
     * @param string                                                $content      page content
     * @param \Cx\Core\ContentManager\Model\Entity\Page             $page         The resolved page
     * @param boolean                                               $isFirstBlock podcast firstblock show/hide
     *
     * @return null
     */
    protected function replaceEsiContent(
        \Cx\Core_Modules\Cache\Controller\ComponentController $cache,
        &$content,
        $page = null,
        $isFirstBlock = 0
    ) {
        global $_LANGID;
        $pattern = '/\{PODCAST_FILE\}/i';
        if (!preg_match($pattern, $content)) {
            return;
        }

        $content = preg_replace(
            $pattern,
            $cache->getEsiContent(
                'Podcast',
                'getPodcastContent',
                array(
                    'file'         => 'podcast.html',
                    'isFirstBlock' => $isFirstBlock,
                    'lang'         => $_LANGID,
                    'template'     => \Env::get('init')->getCurrentThemeId()
                )
            ),
            $content
        );

        if ($page instanceof \Cx\Core\ContentManager\Model\Entity\Page) {
            $page->setContent($content);
        }
    }

    /**
     * Json data for getting podcast result
     *
     * @param array $params Request parameters
     *
     * @return array
     */
    public function getPodcastContent($params)
    {
        $file         = !empty($params['get']['file'])
            ? contrexx_input2raw($params['get']['file']) : '';
        $langId       = !empty($params['get']['lang'])
            ? contrexx_input2int($params['get']['lang']) : 0;
        $isFirstBlock = !empty($params['get']['isFirstBlock'])
            ? contrexx_input2int($params['get']['isFirstBlock']) : 0;

        if (empty($file) || empty($langId)) {
            return array('content' => '');
        }
        try {
            $theme   = $this->getThemeFromInput($params);
            $podcast = new PodcastHomeContent(
                            $theme->getContentFromFile($file)
                        );
            return array('content' => $podcast->getContent($isFirstBlock));
        } catch (\Exception $ex) {
            \DBG::log($ex->getMessage());
            return array('content' => '');
        }
    }

    /**
     * Get theme from the user input
     *
     * @param array $params User input array
     * @return \Cx\Core\View\Model\Entity\Theme Theme instance
     * @throws JsonPodcastException When theme id empty or theme does not exits in the system
     */
    protected function getThemeFromInput($params)
    {
        $themeId  = !empty($params['get']['template'])
            ? contrexx_input2int($params['get']['template']) : 0;
        if (empty($themeId)) {
            throw new JsonPodcastException('The theme id is empty in the request');
        }
        $themeRepository = new \Cx\Core\View\Model\Repository\ThemeRepository();
        $theme           = $themeRepository->findById($themeId);
        if (!$theme) {
            throw new JsonPodcastException('The theme id '. $themeId .' does not exists.');
        }
        return $theme;
    }

    /**
     * Returns an array of method names accessable from a JSON request
     *
     * @return array List of method names
     */
    public function getAccessableMethods()
    {
        return array('getPodcastContent');
    }

    /**
     * Returns default permission as object
     *
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
     *
     * @return String HTML encoded error messages
     */
    public function getMessagesAsString() {
        return '';
    }

    /**
     * Wrapper to __call()
     *
     * @return string ComponentName
     */
    public function getName() {
        return parent::getName();
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
        $this->cx->getEvents()->addEventListener('clearEsiCache', $eventListener);
    }
}
