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
     * currentThemeId
     *
     * @var integer
     */
    protected $currentThemeId;

    /**
     * current page ID
     *
     * @var integer
     */
    protected $currentPageId;

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

        $langId    = \FWLanguage::getLangIdByIso639_1($locale);
        //The global $_ARRAYLANG is required by the method Shop::view_product_overview()
        $arrayLang = array_merge(
            $_ARRAYLANG,
            \Env::get('init')->getComponentSpecificLanguageData(
                'Shop',
                true,
                $langId
            )
        );

        $pageRepo    = $this->cx->getDb()->getEntityManager()->getRepository(
            '\Cx\Core\ContentManager\Model\Entity\Page'
        );
        $page        = $pageRepo->find($this->currentPageId);
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
            $template->setTemplate($template->getUnparsedBlock($name), false, false);
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
            $themeRepository = new \Cx\Core\View\Model\Repository\ThemeRepository();
            $theme           = $themeRepository->findById($this->currentThemeId);
            if (!$theme) {
                return;
            }

            $content = $this->getFileContent(
                $theme,
                'shopnavbar' . $matches[1] . '.html'
            );
            if (!$content) {
                return;
            }

            $navContent = Shop::getNavbar($content, $arrayLang);
            $pattern    = '@(<ul\s*id=["\']shopJsCart["\'][^>]*>)([\n\r].*)(</ul>)@s';
            $matches    = null;
            if (preg_match($pattern, $content, $matches)) {
                $navContent = preg_replace(
                    $pattern,
                    $matches[1] . $matches[2] . $matches[3],
                    $navContent
                );
            }
            $template->setTemplate($navContent, false, false);
            return;
        }

        $catMatches = null;
        if (
            !$shopConfig ||
            !preg_match(
                '/^' .  Shop::block_shop_products . '(?:_category_(\d+))?$/',
                $name,
                $catMatches
            )
        ) {
            return;
        }

        $_ARRAYLANG = array_merge(
            $arrayLang,
            \Env::get('init')->getComponentSpecificLanguageData(
                'core',
                true,
                $langId
            )
        );
        Shop::view_product_overview(null, $catMatches[1], $template);
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
            if (isset($params['get']['page'])) {
                $this->currentPageId = $params['get']['page'];
            }
        }
        return parent::getWidget($params);
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
