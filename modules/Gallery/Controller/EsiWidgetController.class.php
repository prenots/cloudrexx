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
 * @subpackage  module_gallery
 * @version     1.0.0
 */

namespace Cx\Modules\Gallery\Controller;

/**
 * JsonAdapter Controller to handle EsiWidgets
 * Usage:
 * - Create a subclass that implements parseWidget()
 * - Register it as a Controller in your ComponentController
 *
 * @copyright   CLOUDREXX CMS - Cloudrexx AG Thun
 * @author      Project Team SS4U <info@comvation.com>
 * @package     cloudrexx
 * @subpackage  module_gallery
 * @version     1.0.0
 */
class EsiWidgetController extends \Cx\Core_Modules\Widget\Controller\EsiWidgetController {

    /**
     * Random name for random gallery
     *
     * @var string
     */
    protected $randomName;

    /**
     * Parses a widget
     *
     * @param string                     $name     Widget name
     * @param \Cx\Core\Html\Sigma Widget $template Template
     * @param string                     $locale   RFC 3066 locale identifier
     */
    public function parseWidget($name, $template, $locale)
    {
        global $_LANGID;

        $this->getComponent('Session')->getSession();
        $_LANGID = \FWLanguage::getLangIdByIso639_1($locale);
        $gallery = new GalleryHomeContent();
        if ($name === 'GALLERY_LATEST' && $gallery->checkLatest()) {
            $template->setVariable($name, $gallery->getLastImage());
            return;
        }

        $matches = null;
        if (
            $name === 'GALLERY_RANDOM' &&
            $gallery->checkRandom() &&
            preg_match('/\d+/', $this->randomName, $matches)
        ) {
            $template->setVariable($name, $gallery->getImageById($matches[0]));
        }
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
        $widgetName = '';
        if (isset($params['get']) && isset($params['get']['name'])) {
            $widgetName = $params['get']['name'];
        }
        if ($widgetName === 'GALLERY_RANDOM') {
            if (!isset($params['get']) || !isset($params['get']['randomName'])) {
                return;
            }
            $this->randomName = $params['get']['randomName'];
        }
        return parent::getWidget($params);
    }
}
