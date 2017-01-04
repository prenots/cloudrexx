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
     * Load your component.
     *
     * @param \Cx\Core\ContentManager\Model\Entity\Page $page       The resolved page
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
     * @param \Cx\Core\ContentManager\Model\Entity\Page $page       The resolved page
     */
    public function preContentLoad(\Cx\Core\ContentManager\Model\Entity\Page $page) {
        global $objMadiadirPlaceholders, $page_template, $themesPages;
        switch ($this->cx->getMode()) {
            case \Cx\Core\Core\Controller\Cx::MODE_FRONTEND:
                $objMadiadirPlaceholders = new MediaDirectoryPlaceholders($this->getName());
                // Level/Category Navbar
                if (preg_match('/{MEDIADIR_NAVBAR}/', \Env::get('cx')->getPage()->getContent())) {
                    \Env::get('cx')->getPage()->setContent(str_replace('{MEDIADIR_NAVBAR}', $objMadiadirPlaceholders->getNavigationPlacholder(), \Env::get('cx')->getPage()->getContent()));
                }
                if (preg_match('/{MEDIADIR_NAVBAR}/', $page_template)) {
                    $page_template = str_replace('{MEDIADIR_NAVBAR}', $objMadiadirPlaceholders->getNavigationPlacholder(), $page_template);
                }
                if (preg_match('/{MEDIADIR_NAVBAR}/', $themesPages['index'])) {
                    $themesPages['index'] = str_replace('{MEDIADIR_NAVBAR}', $objMadiadirPlaceholders->getNavigationPlacholder(), $themesPages['index']);
                }
                if (preg_match('/{MEDIADIR_NAVBAR}/', $themesPages['sidebar'])) {
                    $themesPages['sidebar'] = str_replace('{MEDIADIR_NAVBAR}', $objMadiadirPlaceholders->getNavigationPlacholder(), $themesPages['sidebar']);
                }
                // Latest Entries
                if (preg_match('/{MEDIADIR_LATEST}/', \Env::get('cx')->getPage()->getContent())) {
                    \Env::get('cx')->getPage()->setContent(str_replace('{MEDIADIR_LATEST}', $objMadiadirPlaceholders->getLatestPlacholder(), \Env::get('cx')->getPage()->getContent()));
                }
                if (preg_match('/{MEDIADIR_LATEST}/', $page_template)) {
                    $page_template = str_replace('{MEDIADIR_LATEST}', $objMadiadirPlaceholders->getLatestPlacholder(), $page_template);
                }
                if (preg_match('/{MEDIADIR_LATEST}/', $themesPages['index'])) {
                    $themesPages['index'] = str_replace('{MEDIADIR_LATEST}', $objMadiadirPlaceholders->getLatestPlacholder(), $themesPages['index']);
                }
                if (preg_match('/{MEDIADIR_LATEST}/', $themesPages['sidebar'])) {
                    $themesPages['sidebar'] = str_replace('{MEDIADIR_LATEST}', $objMadiadirPlaceholders->getLatestPlacholder(), $themesPages['sidebar']);
                }

                break;

            default:
                break;
        }
    }

    /**
     * Do something after content is loaded from DB
     *
     * @param \Cx\Core\ContentManager\Model\Entity\Page $page       The resolved page
     */
    public function postContentLoad(\Cx\Core\ContentManager\Model\Entity\Page $page) {
        global $mediadirCheck, $objTemplate, $_CORELANG, $objInit;

        if ($this->cx->getMode() != \Cx\Core\Core\Controller\Cx::MODE_FRONTEND) {
            return;
        }

        $mediadirCheck = array();
        for ($i = 1; $i <= 10; ++$i) {
            if ($objTemplate->blockExists('mediadirLatest_row_'.$i)){
                array_push($mediadirCheck, $i);
            }
        }
        if ($mediadirCheck || $objTemplate->blockExists('mediadirLatest') || $objTemplate->blockExists('mediadirList')) {
            $objInit->loadLanguageData('MediaDir');

            $objMediadir = new MediaDirectory('', $this->getName());
            $objTemplate->setVariable('TXT_MEDIADIR_LATEST', $_CORELANG['TXT_DIRECTORY_LATEST']);
        }
        if ($mediadirCheck) {
            $objMediadir->getHeadlines($mediadirCheck);
        }
        if ($objTemplate->blockExists('mediadirLatest')){
            $objMediadirForms = new \Cx\Modules\MediaDir\Controller\MediaDirectoryForm(null, 'MediaDir');
            $foundOne = false;
            foreach ($objMediadirForms->getForms() as $key => $arrForm) {
                if ($objTemplate->blockExists('mediadirLatest_form_'.$arrForm['formCmd'])) {
                    $objMediadir->getLatestEntries($key, 'mediadirLatest_form_'.$arrForm['formCmd']);
                    $foundOne = true;
                }
            }
            //for the backward compatibility
            if(!$foundOne) {
                $objMediadir->getLatestEntries();
            }
        }

        // Parse entries of specific form, category and/or level.   
        // Entries are listed in custom set order
        if ($objTemplate->blockExists('mediadirList')) {
            // hold information if a specific block has been parsed
            $foundOne = false;

            // function to match for placeholders in template that act as a filter. I.e.:
            //     MEDIADIR_FILTER_FORM_3
            //     MEDIADIR_FILTER_CATEGORY_4
            //     MEDIADIR_FILTER_LEVEL_5
            $fetchMediaDirListFilters = function($block) use ($objTemplate) {
                $filter = array();
                $placeholderList = join("\n", $objTemplate->getPlaceholderList($block));
                if (preg_match_all('/MEDIADIR_FILTER_(FORM|CATEGORY|LEVEL)_([0-9]+)/', $placeholderList, $match)) {
                    foreach ($match[1] as $idx => $key) {
                        $filterKey = strtolower($key);
                        $filter[$filterKey] = intval($match[2][$idx]);
                    }
                }
                return $filter;
            };

            // fetch mediadir object data
            $objMediadirForm = new \Cx\Modules\MediaDir\Controller\MediaDirectoryForm(null, $this->getName());
            $objMediadirCategory = new MediaDirectoryCategory(null, null, 0, $this->getName());
            $objMediadirLevel = new MediaDirectoryLevel(null, null, 1, $this->getName());

            // put all object data into one array
            $objects = array(
                'form' => array_keys($objMediadirForm->getForms()),
                'category' => array_keys($objMediadirCategory->arrCategories),
                'level' => array_keys($objMediadirLevel->arrLevels),
            );

            // check for form specific entry listing
            foreach ($objects as $objectType => $arrObjectList) {
                foreach ($arrObjectList as $objectId) {
                    // the specific block to parse. I.e.:
                    //    mediadirList_form_3
                    //    mediadirList_category_4
                    //    mediadirList_level_5
                    $block = 'mediadirList_'.$objectType.'_'.$objectId;
                    if ($objTemplate->blockExists($block)) {
                        $filter = $fetchMediaDirListFilters($block);
                        $filter[$objectType] = $objectId;
                        $objMediadir->parseEntries($objTemplate, $block, $filter);
                        $foundOne = true;
                    }
                }
            }

            // fallback, no specific block has been parsed
            // -> parse all entries now (use template block mediadirList)
            if(!$foundOne) {
                $objMediadir->parseEntries($objTemplate);
            }
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
    }
}
