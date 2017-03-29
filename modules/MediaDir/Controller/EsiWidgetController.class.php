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
 * Class EsiWidgetController
 *
 * @copyright   CLOUDREXX CMS - Cloudrexx AG Thun
 * @author      Project Team SS4U <info@comvation.com>
 * @package     cloudrexx
 * @subpackage  module_mediadir
 * @version     1.0.0
 */

namespace Cx\Modules\MediaDir\Controller;

/**
 * JsonAdapter Controller to handle EsiWidgets
 * Usage:
 * - Create a subclass that implements parseWidget()
 * - Register it as a Controller in your ComponentController
 *
 * @copyright   CLOUDREXX CMS - Cloudrexx AG Thun
 * @author      Project Team SS4U <info@comvation.com>
 * @package     cloudrexx
 * @subpackage  module_mediadir
 * @version     1.0.0
 */

class EsiWidgetController extends \Cx\Core_Modules\Widget\Controller\EsiWidgetController {
    /**
     * currentThemeId
     *
     * @var integer
     */
    protected $currentThemeId;

    /**
     * category ID
     *
     * @var integer
     */
    protected $catId;

    /**
     * level ID
     *
     * @var integer
     */
    protected $levelId;

    /**
     * Parses a widget
     *
     * @param string              $name     Widget name
     * @param \Cx\Core\Html\Sigma $template Widget template
     * @param string              $locale   RFC 3066 locale identifier
     */
    public function parseWidget($name, $template, $locale)
    {
        global $_ARRAYLANG;

        // Show Level/Category Navbar
        $mediaDirPlaceholders = new MediaDirectoryPlaceholders('MediaDir');
        if ($name === 'MEDIADIR_NAVBAR') {
            $template->setVariable(
                $name,
                $mediaDirPlaceholders->getNavigationPlacholder()
            );
            return;
        }

        //The global $_ARRAYLANG is required by the method MediaDirectoryEntry::getEntries()
        $langId     = \FWLanguage::getLangIdByIso639_1($locale);
        $_ARRAYLANG = array_merge(
            $_ARRAYLANG,
            \Env::get('init')->getComponentSpecificLanguageData(
                'MediaDir',
                true,
                $langId
            )
        );

        // Show Latest Entries
        if ($name === 'MEDIADIR_LATEST') {
            $template->setVariable(
                $name,
                $mediaDirPlaceholders->getLatestPlacholder()
            );
            return;
        }

        //Show Latest MediaDir entries
        $coreLang = \Env::get('init')->getComponentSpecificLanguageData(
            'Core',
            true,
            $langId
        );
        $mediadir = new MediaDirectory('', 'MediaDir');
        $matches  = null;
        if (
            preg_match(
                '/^mediadirLatest_row_(\d{1,2})_(\d{1,2})$/',
                $name,
                $matches
            )
        ) {
            $template->setVariable(
                'TXT_MEDIADIR_LATEST',
                $_ARRAYLANG['TXT_DIRECTORY_LATEST']
            );
            $mediadir->getHeadlines($template, $matches[1], $matches[2], $coreLang);
            return;
        }

        //Show the Latest MediaDir entries or show latest entries based on the MediaDir Form
        if ($name === 'mediadirLatest') {
            $mediadirForms = new \Cx\Modules\MediaDir\Controller\MediaDirectoryForm(
                null,
                'MediaDir'
            );
            $foundOne = false;
            foreach ($mediadirForms->getForms() as $key => $arrForm) {
                if (
                    !$template->blockExists(
                        'mediadirLatest_form_' . $arrForm['formCmd']
                    )
                ) {
                    continue;
                }

                $mediadir->getLatestEntries(
                    $template,
                    $key,
                    'mediadirLatest_form_' . $arrForm['formCmd']
                );
                $foundOne = true;
            }

            //for the backward compatibility
            if (!$foundOne) {
                $mediadir->getLatestEntries($template);
            }
            return;
        }

        // Show MediaDir nav tree
        if ($name === 'mediadirNavtree') {
            $mediadir->getNavtree($this->catId, $this->levelId, $template);
            return;
        }

        // Parse entries of specific form, category and/or level.
        // Entries are listed in custom set order
        if ($name !== 'mediadirList') {
            return;
        }

        // hold information if a specific block has been parsed
        $foundOne = false;

        // fetch mediadir object data
        $objMediadirForm = new \Cx\Modules\MediaDir\Controller\MediaDirectoryForm(null, 'MediaDir');
        $objMediadirCategory = new MediaDirectoryCategory(null, null, 0, 'MediaDir');
        $objMediadirLevel = new MediaDirectoryLevel(null, null, 1, 'MediaDir');

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
                if (!$template->blockExists($block)) {
                    continue;
                }
                $filter = $this->getMediaDirFilterList(
                    join("\n", $template->getPlaceholderList($block))
                );
                $filter[$objectType] = $objectId;
                $mediadir->parseEntries($template, $block, $filter);
                $foundOne = true;
            }
        }

        // fallback, no specific block has been parsed
        // -> parse all entries now (use template block mediadirList)
        if (!$foundOne) {
            $mediadir->parseEntries($template);
        }
    }

    /**
     * Get MediaDir filter list
     *
     * function to match for placeholders in template that act as a filter. I.e.:
     *  MEDIADIR_FILTER_FORM_3
     *  MEDIADIR_FILTER_CATEGORY_4
     *  MEDIADIR_FILTER_LEVEL_5
     *
     * @param array $placeholderList array of placeholders
     *
     * @return null|array
     */
    protected function getMediaDirFilterList($placeholderList)
    {
        if (empty($placeholderList)) {
            return;
        }

        $matches = array();
        if (
            !preg_match_all(
                '/MEDIADIR_FILTER_(FORM|CATEGORY|LEVEL)_([0-9]+)/',
                $placeholderList,
                $matches
            )
        ) {
            return;
        }

        $filter  = array();
        foreach ($matches[1] as $idx => $key) {
            $filterKey          = strtolower($key);
            $filter[$filterKey] = intval($matches[2][$idx]);
        }

        return $filter;
    }

    /**
     * Returns the content of a widget
     *
     * @param array $params JsonAdapter parameters
     *
     * @return array Content in an associative array
     */
    public function getWidget($params)
    {
        if (isset($params['get'])) {
            if (isset($params['get']['theme'])) {
                $this->currentThemeId = $params['get']['theme'];
            }
            if (isset($params['get']['cid'])) {
                $this->catId = $params['get']['cid'];
            }
            if (isset($params['get']['lid'])) {
                $this->levelId = $params['get']['lid'];
            }
        }
        return parent::getWidget($params);
    }
}