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
 * @author      Project Team SS4U <info@cloudrexx.com>
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
 * @author      Project Team SS4U <info@cloudrexx.com>
 * @package     cloudrexx
 * @subpackage  module_mediadir
 * @version     1.0.0
 */

class EsiWidgetController extends \Cx\Core_Modules\Widget\Controller\EsiWidgetController {
    /**
     * Parses a widget
     *
     * @param string                                 $name     Widget name
     * @param \Cx\Core\Html\Sigma                    $template Widget template
     * @param \Cx\Core\Routing\Model\Entity\Response $response Response object
     * @param array                                  $params   Get parameters
     */
    public function parseWidget($name, $template, $response, $params)
    {
        // Show Level/Category Navbar or Show Latest Entries
        if ($name === 'MEDIADIR_NAVBAR' || $name === 'MEDIADIR_LATEST') {
            $mediaDirPlaceholders = new MediaDirectoryPlaceholders($this->getSystemComponentController()->getName());
            if ($name === 'MEDIADIR_NAVBAR') {
                $content = $mediaDirPlaceholders->getNavigationPlacholder();
            } else {
                $content = $mediaDirPlaceholders->getLatestPlacholder();
            }
            $template->setVariable($name, $content);
            return;
        }

        // Show Latest MediaDir entries
        $mediadir = new MediaDirectory('', $this->getSystemComponentController()->getName());
        $matches  = array();
        if (
            preg_match(
                '/^mediadirLatest_row_(\d{1,2})_(\d{1,2})$/',
                $name,
                $matches
            )
        ) {
            $coreLang = \Env::get('init')->getComponentSpecificLanguageData(
                'Core',
                true,
                $params['lang']
            );
            $arrlang = \Env::get('init')->getComponentSpecificLanguageData(
                $this->getSystemComponentController()->getName(),
                true,
                $params['lang']
            );
            $template->setVariable(
                'TXT_MEDIADIR_LATEST',
                $coreLang['TXT_DIRECTORY_LATEST']
            );
            $mediadir->getHeadlines($template, $matches[1], $matches[2], $arrlang);
            return;
        }

        // Show the Latest MediaDir entries or show latest entries based on the MediaDir Form
        if ($name === 'mediadirLatest') {
            $mediadirForms = new MediaDirectoryForm(null, $this->getSystemComponentController()->getName());
            $foundOne      = false;
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

            // for the backward compatibility
            if (!$foundOne) {
                $mediadir->getLatestEntries($template);
            }
            return;
        }

        // Show MediaDir nav tree
        if ($name === 'mediadirNavtree') {
            $mediadir->getNavtree($params['cid'], $params['lid'], $template);
            return;
        }

        // Parse entries of specific form, category and/or level.
        // Entries are listed in custom set order
        if ($name === 'mediadirList') {
            // hold information if a specific block has been parsed
            $foundOne = false;

            // fetch mediadir object data
            $objMediadirForm     = new MediaDirectoryForm(null, $this->getSystemComponentController()->getName());
            $objMediadirCategory = new MediaDirectoryCategory(null, null, 0, $this->getSystemComponentController()->getName());
            $objMediadirLevel    = new MediaDirectoryLevel(null, null, 1, $this->getSystemComponentController()->getName());

            // put all object data into one array
            $objects = array(
                'form'     => array_keys($objMediadirForm->getForms()),
                'category' => array_keys($objMediadirCategory->arrCategories),
                'level'    => array_keys($objMediadirLevel->arrLevels),
            );

            // check for form specific entry listing
            foreach ($objects as $objectType => $arrObjectList) {
                foreach ($arrObjectList as $objectId) {
                    // the specific block to parse. I.e.:
                    // mediadirList_form_3
                    // mediadirList_category_4
                    // mediadirList_level_5
                    $block = 'mediadirList_'.$objectType.'_'.$objectId;
                    if (!$template->blockExists($block)) {
                        continue;
                    }
                    $filter = MediaDirectoryLibrary::fetchMediaDirListConfigFromTemplate(
                        $block,
                        $template
                    );
                    $filter['filter'][$objectType] = $objectId;
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
    }
}
