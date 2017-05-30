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
 * @subpackage  module_shop
 * @version     1.0.0
 */

namespace Cx\Modules\Shop\Controller;

/**
 * JsonAdapter Controller to handle EsiWidgets
 * Usage:
 * - Create a subclass that implements parseWidget()
 * - Register it as a Controller in your ComponentController
 *
 * @copyright   CLOUDREXX CMS - Cloudrexx AG Thun
 * @author      Project Team SS4U <info@comvation.com>
 * @package     cloudrexx
 * @subpackage  module_shop
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
        $arrayLang = \Env::get('init')->getComponentSpecificLanguageData(
            'Shop',
            true,
            $params['lang']
        );

        $page        = $params['page'];
        $shopConfig  = \Cx\Core\Setting\Controller\Setting::init('Shop', 'config');
        $showShopNav = \Cx\Core\Setting\Controller\Setting::getValue(
            'shopnavbar_on_all_pages',
            'Shop'
        );

        if (
            $name == 'shopJsCart' &&
            \Cx\Core\Setting\Controller\Setting::getValue('use_js_cart', 'Shop') &&
            (
                $showShopNav ||
                (
                    !\FWValidator::isEmpty($page->getModule()) &&
                    $page->getModule() == 'Shop' . MODULE_INDEX &&
                    (
                        \FWValidator::isEmpty($page->getCmd()) ||
                        in_array($page->getCmd(), array('discounts', 'details'))
                    )
                )
            )
        ) {
            Shop::setJsCart($template, $arrayLang);
            return;
        }

        if (!Shop::isInitialized()) {
            Shop::init();
        }

        $matches = null;
        if (
            preg_match('/^SHOPNAVBAR(\d{0,1})_FILE$/', $name, $matches) &&
            $showShopNav
        ) {
            if (!$params['theme']) {
                return;
            }

            $navContent = Shop::getNavbar(
                $template,
                $params['theme']->getFilePath(
                    $params['theme']->getFolderName() . '/shopnavbar' . $matches[1] . '.html'
                ),
                $arrayLang
            );
            return;
        }

        $catMatches = null;
        if (
            $shopConfig &&
            preg_match(
                '/^' .  Shop::block_shop_products . '(?:_category_(\d+))?$/',
                $name,
                $catMatches
            )
        ) {
            $catId = 0;
            if (isset($catMatches[1]) && !empty($catMatches[1])) {
                $catId = $catMatches[1];
            }
            Shop::view_product_overview(null, $catId, $template);
        }
    }

    /**
     * Get file content
     *
     * @param \Cx\Core\View\Model\Entity\Theme $theme    theme object
     * @param string                           $fileName name of the file
     *
     * @return string
     */
    protected function getFileContent($theme, $fileName)
    {
        if (!($theme instanceof \Cx\Core\View\Model\Entity\Theme)) {
            return;
        }

        return file_get_contents(
            $theme->getFilePath($theme->getFolderName() . '/' . $fileName)
        );
    }
}
