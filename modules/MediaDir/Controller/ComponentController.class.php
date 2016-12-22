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
 * Main controller for MediaDir
 *
 * @copyright   Cloudrexx AG
 * @author      Project Team SS4U <info@cloudrexx.com>
 * @package     cloudrexx
 * @subpackage  module_mediadir
 */

namespace Cx\Modules\MediaDir\Controller;
use Cx\Modules\MediaDir\Model\Event\MediaDirEventListener;

/**
 * Main controller for MediaDir
 *
 * @copyright   Cloudrexx AG
 * @author      Project Team SS4U <info@cloudrexx.com>
 * @package     cloudrexx
 * @subpackage  module_mediadir
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
     * @return array list of JsonAdapter class names
     */
    public function getControllersAccessableByJson()
    {
        return array('JsonMediaDir');
    }

    /**
     * Load your component.
     *
     * @param \Cx\Core\ContentManager\Model\Entity\Page $page The resolved page
     */
    public function load(\Cx\Core\ContentManager\Model\Entity\Page $page) {
        global $_CORELANG, $subMenuTitle, $objTemplate;
        switch ($this->cx->getMode()) {
            case \Cx\Core\Core\Controller\Cx::MODE_FRONTEND:
                $objMediaDirectory = new MediaDirectory(\Env::get('cx')->getPage()->getContent(), $this->getName());
                $objMediaDirectory->pageTitle = \Env::get('cx')->getPage()->getTitle();
                $pageMetaTitle = \Env::get('cx')->getPage()->getMetatitle();
                $objMediaDirectory->metaTitle = $pageMetaTitle;
                \Env::get('cx')->getPage()->setContent($objMediaDirectory->getPage());
                if ($objMediaDirectory->getPageTitle() != '' && $objMediaDirectory->getPageTitle() != \Env::get('cx')->getPage()->getTitle()) {
                    \Env::get('cx')->getPage()->setTitle($objMediaDirectory->getPageTitle());
                    \Env::get('cx')->getPage()->setContentTitle($objMediaDirectory->getPageTitle());
                    \Env::get('cx')->getPage()->setMetaTitle($objMediaDirectory->getPageTitle());
                }
                if ($objMediaDirectory->getMetaTitle() != '') {
                    \Env::get('cx')->getPage()->setMetatitle($objMediaDirectory->getMetaTitle());
                }
                if ($objMediaDirectory->getMetaDescription() != '') {
                    \Env::get('cx')->getPage()->setMetadesc($objMediaDirectory->getMetaDescription());
                }
                if ($objMediaDirectory->getMetaImage() != '') {
                    \Env::get('cx')->getPage()->setMetaimage($objMediaDirectory->getMetaImage());
                }

                break;

            case \Cx\Core\Core\Controller\Cx::MODE_BACKEND:

                $this->cx->getTemplate()->addBlockfile('CONTENT_OUTPUT', 'content_master', 'LegacyContentMaster.html');
                $objTemplate = $this->cx->getTemplate();
                \Permission::checkAccess(153, 'static');
                $subMenuTitle = $_CORELANG['TXT_MEDIADIR_MODULE'];
                $objMediaDirectory = new MediaDirectoryManager($this->getName());
                $objMediaDirectory->getPage();
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
    public function preContentLoad(\Cx\Core\ContentManager\Model\Entity\Page $page)
    {
        global $page_template, $themesPages, $_LANGID;

        switch ($this->cx->getMode()) {
            case \Cx\Core\Core\Controller\Cx::MODE_FRONTEND:

                // Level/Category Navbar
                $cache  = $this->cx->getComponent('Cache');
                $params = array('lang' => $_LANGID);
                if (isset($_GET['lid']) && !empty($_GET['lid'])) {
                    $params['lid'] = contrexx_input2raw($_GET['lid']);
                }
                if (isset($_GET['cid']) && !empty($_GET['cid'])) {
                    $params['cid'] = contrexx_input2raw($_GET['cid']);
                }
                $placeholders = $cache->getEsiContent(
                    'MediaDir',
                    'getNavigationPlacholder',
                    $params
                );

                $this->parseContentIntoTpl(
                    null,
                    $this->cx->getPage(),
                    $placeholders,
                    '{MEDIADIR_NAVBAR}'
                );
                $page_template = $this->parseContentIntoTpl(
                    $page_template,
                    null,
                    $placeholders,
                    '{MEDIADIR_NAVBAR}'
                );
                $themesPages['index'] = $this->parseContentIntoTpl(
                    $themesPages['index'],
                    null,
                    $placeholders,
                    '{MEDIADIR_NAVBAR}'
                );
                $themesPages['sidebar'] = $this->parseContentIntoTpl(
                    $themesPages['sidebar'],
                    null,
                    $placeholders,
                    '{MEDIADIR_NAVBAR}'
                );

                // Latest Entries
                $latestPlaceholders = $cache->getEsiContent(
                    'MediaDir',
                    'getLatestPlacholder',
                    array('lang' => $_LANGID)
                );
                $this->parseContentIntoTpl(
                    null,
                    $this->cx->getPage(),
                    $latestPlaceholders,
                    '{MEDIADIR_LATEST}'
                );
                $page_template = $this->parseContentIntoTpl(
                    $page_template,
                    null,
                    $latestPlaceholders,
                    '{MEDIADIR_LATEST}'
                );
                $themesPages['index'] = $this->parseContentIntoTpl(
                    $themesPages['index'],
                    null,
                    $latestPlaceholders,
                    '{MEDIADIR_LATEST}'
                );
                $themesPages['sidebar'] = $this->parseContentIntoTpl(
                    $themesPages['sidebar'],
                    null,
                    $latestPlaceholders,
                    '{MEDIADIR_LATEST}'
                );
                break;
            default:
                break;
        }
    }

    /**
     * Parse the mediaDir content into template
     *
     * @param string                                    $template template content
     * @param \Cx\Core\ContentManager\Model\Entity\Page $page     page object
     * @param string                                    $content  parsing content
     * @param string                                    $pattern  pattern
     *
     * @return null|string
     */
    public function parseContentIntoTpl($template, $page, $content, $pattern)
    {
        if ((empty($template) && empty($page)) || empty($pattern)) {
            return;
        }

        if ($page instanceof \Cx\Core\ContentManager\Model\Entity\Page) {
            if (!preg_match('/' . $pattern . '/', $page->getContent())) {
                return;
            }
            $page->setContent(str_replace($pattern, $content, $page->getContent()));
        } else {
            if (!preg_match('/' . $pattern . '/', $template)) {
                return $template;
            }
            return str_replace($pattern, $content, $template);
        }
    }

    /**
     * Do something after content is loaded from DB
     *
     * @param \Cx\Core\ContentManager\Model\Entity\Page $page The resolved page
     */
    public function postContentLoad(\Cx\Core\ContentManager\Model\Entity\Page $page)
    {
        global $objTemplate, $_CORELANG, $_LANGID;

        switch ($this->cx->getMode()) {
            case \Cx\Core\Core\Controller\Cx::MODE_FRONTEND:
                $cache = $this->cx->getComponent('Cache');
                $objTemplate->setVariable(
                    'TXT_MEDIADIR_LATEST',
                    $_CORELANG['TXT_DIRECTORY_LATEST']
                );
                $mediaDirLatestBlocks = array();
                for ($i = 1; $i <= 10; ++$i) {
                    if (!$objTemplate->blockExists('mediadirLatest_row_' . $i)) {
                        continue;
                    }
                    $mediaDirLatestBlocks[] = $i;
                }

                if (empty($mediaDirLatestBlocks)) {
                    goto parseLatestEntries;
                }
                $mediaDirLatestBlocksCnt = count($mediaDirLatestBlocks);
                foreach ($mediaDirLatestBlocks as $position => $blockId) {
                    $blockName = 'mediadirLatest_row_' . $blockId;
                    $params = $cache->getParamsByFindBlockExistsInTpl($blockName);
                    $params['blockId']     = $blockId;
                    $params['position']    = $position + 1;
                    $params['lang']        = $_LANGID;
                    $params['totalBlocks'] = $mediaDirLatestBlocksCnt;
                    $content = $cache->getEsiContent(
                        'MediaDir',
                        'getHeadlines',
                        $params
                    );
                    $objTemplate->replaceBlock($blockName, $content);
                    $objTemplate->touchBlock($blockName);
                }

                parseLatestEntries:
                if (!$objTemplate->blockExists('mediadirLatest')) {
                    break;
                }
                $foundOne         = false;
                $objMediadirForms =
                    new \Cx\Modules\MediaDir\Controller\MediaDirectoryForm(
                        null,
                        'MediaDir'
                    );
                foreach ($objMediadirForms->getForms() as $key => $arrForm) {
                    $blockName = 'mediadirLatest_form_' . $arrForm['formCmd'];
                    if (!$objTemplate->blockExists($blockName)) {
                        continue;
                    }
                    $foundOne = true;
                    $params   = $cache->getParamsByFindBlockExistsInTpl(
                        $blockName
                    );
                    $params['lang']    = $_LANGID;
                    $params['formId']  = $key;
                    $params['block']   = 'mediadirLatest';
                    $content = $cache->getEsiContent(
                        'MediaDir',
                        'getLatestEntries',
                        $params
                    );
                    $objTemplate->replaceBlock($blockName, $content);
                    $objTemplate->touchBlock($blockName);
                }
                //for the backward compatibility
                if (!$foundOne) {
                    $blockName = 'mediadirLatest';
                    $params = $cache->getParamsByFindBlockExistsInTpl(
                        $blockName
                    );
                    $params['lang']  = $_LANGID;
                    $params['block'] = $blockName;
                    $content         = $cache->getEsiContent(
                        'MediaDir',
                        'getLatestEntries',
                        $params
                    );
                    $objTemplate->replaceBlock($blockName, $content);
                    $objTemplate->touchBlock($blockName);
                }
                break;
            default:
                break;
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
        $eventListener = new MediaDirEventListener($this->cx);
        $this->cx->getEvents()->addEventListener('SearchFindContent',$eventListener);
        $this->cx->getEvents()->addEventListener('mediasource.load', $eventListener);
        $this->cx->getEvents()->addEventListener('clearEsiCache', $eventListener);
    }
}
