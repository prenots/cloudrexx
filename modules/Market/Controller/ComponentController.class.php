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
 * Main controller for Market
 *
 * @copyright   Cloudrexx AG
 * @author      Project Team SS4U <info@cloudrexx.com>
 * @package     cloudrexx
 * @subpackage  module_market
 */

namespace Cx\Modules\Market\Controller;

/**
 * Main controller for Market
 *
 * @copyright   Cloudrexx AG
 * @author      Project Team SS4U <info@cloudrexx.com>
 * @package     cloudrexx
 * @subpackage  module_market
 */
class ComponentController extends \Cx\Core\Core\Model\Entity\SystemComponentController {
    public function getControllerClasses() {
        // Return an empty array here to let the component handler know that there
        // does not exist a backend, nor a frontend controller of this component.
        return array();
    }

    /**
    * Returns a list of JsonAdapter class names
    *
    * @return array List of ComponentController classes
    */
    public function getControllersAccessableByJson()

    {
        return array('JsonMarket');
    }

    /**
     * Load your component.
     *
     * @param \Cx\Core\ContentManager\Model\Entity\Page $page       The resolved page
     */
    public function load(\Cx\Core\ContentManager\Model\Entity\Page $page) {
        global $subMenuTitle, $_CORELANG, $objTemplate;
        switch ($this->cx->getMode()) {
            case \Cx\Core\Core\Controller\Cx::MODE_FRONTEND:
                $market = new Market(\Env::get('cx')->getPage()->getContent());
                \Env::get('cx')->getPage()->setContent($market->getPage());
                break;

            case \Cx\Core\Core\Controller\Cx::MODE_BACKEND:
                $this->cx->getTemplate()->addBlockfile('CONTENT_OUTPUT', 'content_master', 'LegacyContentMaster.html');
                $objTemplate = $this->cx->getTemplate();

                \Permission::checkAccess(98, 'static');
                $subMenuTitle = $_CORELANG['TXT_CORE_MARKET_TITLE'];
                $objMarket = new MarketManager();
                $objMarket->getPage();
                break;
        }
    }

    /**
     * Do something after content is loaded from DB
     *
     * @param \Cx\Core\ContentManager\Model\Entity\Page $page       The resolved page
     */
    public function postContentLoad(\Cx\Core\ContentManager\Model\Entity\Page $page) {
        global $objTemplate, $themesPages, $page_template, $section;;
        switch ($this->cx->getMode()) {
            case \Cx\Core\Core\Controller\Cx::MODE_FRONTEND:
                // Market Show Latest
                $marketCheck = $objTemplate->blockExists('marketLatest');
                if ($marketCheck) {
                    $objTemplate->setVariable('TXT_MARKET_LATEST', $_CORELANG['TXT_MARKET_LATEST']);
                    $themesPages = $this->replace($themesPages);
                    $page_template = $this->replace($page_template);
                    $page->setContent($this->replace(
                            $page->getContent(),
                            $page
                    ));
                }
                break;
        }
    }

    /**
     * Do the replacements
     * @param string $data The pages on which the replacement should be done
     * @return string
     */
    public function replace($data, $page = null)
    {
       global $plainSection;
       if (
            $page != null &&
            ($page instanceof \Cx\Core\ContentManager\Model\Entity\Page)
        ) {
            if (
                $page->getType() == \Cx\Core\ContentManager\Model\Entity\Page::TYPE_APPLICATION) {
                $content = \Cx\Core\Core\Controller\Cx::instanciate()
                    ->getContentTemplateOfPage($page);
                $data = $this->replaceEsiContent($content, '', $page);
            } else {
                $data = $this->replaceEsiContent($data, '', $page);
            }
        } else if (is_array($data)) {
            foreach ($data as $key => $value) {
                $data[$key] = $this->replaceEsiContent($value, $key . '.html');
            }
        } else {
            $tplName = '';
            if ($plainSection == 'Home') {
                $tplName = !\Env::get('init')->hasCustomContent()
                    ? 'home.html' : 'content.html';
            }
            $data = $this->replaceEsiContent($data, $tplName);
        }

        return $data;
    }

    /**
     * Replace esi content in given content
     *
     * @param string                                    $content Content
     * @param string                                    $tplName Theme file name
     * @param \Cx\Core\ContentManager\Model\Entity\Page $page    Page instance
     * @param string                                    $block   Template Block name
     *
     * @return string Replaced content
     */
    public function replaceEsiContent(
        $content,
        $tplName  = '',
        $page = null,
        $block = 'marketLatest',
        $apiMethod = 'getMarketLatest'
    ) {

        $matches = array();

        if (!preg_match(
           '/<!--\s+BEGIN\s+('. $block .')\s+-->(.*)<!--\s+END\s+\1\s+-->/s',
            $content,
            $arrMatches
        )) {
            return $content;
        }
        if (
            $page != null &&
            ($page instanceof \Cx\Core\ContentManager\Model\Entity\Page)
        ) {
            $params = array(
                'page' => $page->getId()
            );
        } else if (!empty ($tplName)) {
            $themeRepository = new \Cx\Core\View\Model\Repository\ThemeRepository();
            $theme = $themeRepository->findById(\Env::get('init')->getCurrentThemeId());
            if (!$theme) {
                return $content;
            }
            $params = array(
                'theme' => $theme->getId(),
                'file' => $tplName
            );
        }
        $cache = \Cx\Core\Core\Controller\Cx::instanciate()
             ->getComponent('Cache');
        $esiContent = $cache->getEsiContent(
            'Market',
            $apiMethod,
            $params
        );
        $replacedContent = preg_replace(
            '/<!--\s+BEGIN\s+('. $block .')\s+-->(.*)<!--\s+END\s+\1\s+-->/s'   ,
            $esiContent,
            $content
        );

        return $replacedContent;
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
         $eventListener = new \Cx\Modules\Market\Model\Event\MarketEventListener($this->cx);
         $this->cx->getEvents()->addEventListener('clearEsiCache', $eventListener);
     }
}
