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
 * Main controller for Shop
 *
 * @copyright   Cloudrexx AG
 * @author      Project Team SS4U <info@cloudrexx.com>
 * @package     cloudrexx
 * @subpackage  module_shop
 */

namespace Cx\Modules\Shop\Controller;

/**
 * Main controller for Shop
 *
 * @copyright   Cloudrexx AG
 * @author      Project Team SS4U <info@cloudrexx.com>
 * @package     cloudrexx
 * @subpackage  module_shop
 */
class ComponentController extends \Cx\Core\Core\Model\Entity\SystemComponentController {
    /**
     * Returns all Controller class names for this component (except this)
     *
     * Be sure to return all your controller classes if you add your own
     * @return array List of Controller class names (without namespace)
     */
    public function getControllerClasses()
    {
        return array('EsiWidget');
    }

    /**
     * Returns a list of JsonAdapter class names
     *
     * The array values might be a class name without namespace. In that case
     * the namespace \Cx\{component_type}\{component_name}\Controller is used.
     * If the array value starts with a backslash, no namespace is added.
     *
     * Avoid calculation of anything, just return an array!
     * @return array List of ComponentController classes
     */
    public function getControllersAccessableByJson()
    {
        return array('EsiWidgetController');
    }

    /**
     * Load your component.
     *
     * @param \Cx\Core\ContentManager\Model\Entity\Page $page       The resolved page
     */
    public function load(\Cx\Core\ContentManager\Model\Entity\Page $page) {
        global $_CORELANG, $subMenuTitle, $intAccessIdOffset, $objTemplate;
        switch ($this->cx->getMode()) {
            case \Cx\Core\Core\Controller\Cx::MODE_FRONTEND:
                \Env::get('cx')->getPage()->setContent(Shop::getPage(\Env::get('cx')->getPage()->getContent()));

                // show product title if the user is on the product details page
                if ($page_metatitle = Shop::getPageTitle()) {
                    \Env::get('cx')->getPage()->setTitle($page_metatitle);
                    \Env::get('cx')->getPage()->setContentTitle($page_metatitle);
                    \Env::get('cx')->getPage()->setMetaTitle($page_metatitle);
                }
                $metaImage = Shop::getPageMetaImage();
                if ($metaImage) {
                    \Env::get('cx')->getPage()->setMetaimage($metaImage);
                }
                break;

            case \Cx\Core\Core\Controller\Cx::MODE_BACKEND:
                $this->cx->getTemplate()->addBlockfile('CONTENT_OUTPUT', 'content_master', 'LegacyContentMaster.html');
                $objTemplate = $this->cx->getTemplate();

                \Permission::checkAccess($intAccessIdOffset+13, 'static');
                $subMenuTitle = $_CORELANG['TXT_SHOP_ADMINISTRATION'];
                $objShopManager = new ShopManager();
                $objShopManager->getPage();
                break;
        }
    }

    /**
     * Do something after content is loaded from DB
     *
     * @param \Cx\Core\ContentManager\Model\Entity\Page $page The resolved page
     */
    public function postContentLoad(\Cx\Core\ContentManager\Model\Entity\Page $page)
    {
        if ($this->cx->getMode() !== \Cx\Core\Core\Controller\Cx::MODE_FRONTEND) {
            return;
        }
        // Show the Shop navbar in the Shop, or on every page if configured to do so
        if (!Shop::isInitialized()
        // Optionally limit to the first instance
        // && MODULE_INDEX == ''
        ) {
            \Cx\Core\Setting\Controller\Setting::init('Shop', 'config');
            if (
                !\Cx\Core\Setting\Controller\Setting::getValue(
                    'shopnavbar_on_all_pages',
                    'Shop'
                )
            ) {
                return;
            }
            Shop::init();
        }
    }

    /**
     * Do something with a Response object
     * You may do page alterations here (like changing the metatitle)
     * You may do response alterations here (like set headers)
     * PLEASE MAKE SURE THIS METHOD IS MOCKABLE. IT MAY ONLY INTERACT WITH
     * resolve() HOOK.
     *
     * @param \Cx\Core\Routing\Model\Entity\Response $response Response object to adjust
     */
    public function adjustResponse(\Cx\Core\Routing\Model\Entity\Response $response) {
        $params = $response->getRequest()->getUrl()->getParamArray();
        unset($params['section']);
        unset($params['cmd']);
        $canonicalUrl = \Cx\Core\Routing\Url::fromPage($response->getPage(), $params);
        $response->setHeader(
            'Link',
            '<' . $canonicalUrl->toString() . '>; rel="canonical"'
        );
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
        $eventListener = new \Cx\Modules\Shop\Model\Event\ShopEventListener($this->cx);
        $this->cx->getEvents()->addEventListener('SearchFindContent',$eventListener);
        $this->cx->getEvents()->addEventListener('mediasource.load', $eventListener);
    }

    /**
     * Do something after system initialization
     *
     * USE CAREFULLY, DO NOT DO ANYTHING COSTLY HERE!
     * CALCULATE YOUR STUFF AS LATE AS POSSIBLE.
     * This event must be registered in the postInit-Hook definition
     * file config/postInitHooks.yml.
     *
     * @param \Cx\Core\Core\Controller\Cx $cx The instance of \Cx\Core\Core\Controller\Cx
     */
    public function postInit(\Cx\Core\Core\Controller\Cx $cx)
    {
        //Parse Shop navbar
        $shopLibrary = new ShopLibrary();
        $this->registerWidgets(
            $shopLibrary->getShopWidgetNames('placeholder'),
            \Cx\Core_Modules\Widget\Model\Entity\EsiWidget::TYPE_PLACEHOLDER,
            array('redirect', 'catId', 'productId', 'referer')
        );

        // parse global product blocks and based on its category/shopJsCart
        $this->registerWidgets(
            $shopLibrary->getShopWidgetNames('block'),
            \Cx\Core_Modules\Widget\Model\Entity\EsiWidget::TYPE_BLOCK,
            array('cmd', 'productId', 'referer', 'term', 'catId', 'manufacturerId')
        );
    }

    /**
     * register the widget
     *
     * @param array   $widgetNames      array of widget names
     * @param string  $widgetType       widget type
     * @param array   $additionalParams array of additional parameters
     *
     * @return null
     */
    protected function registerWidgets(
        $widgetNames,
        $widgetType,
        $additionalParams = array()
    ) {
        if (empty($widgetNames)) {
            return;
        }

        $params = array();
        if (!empty($additionalParams)) {
            $requestParams = $this->cx->getRequest()->getUrl()->getParamArray();
            foreach ($additionalParams as $paramName) {
                if (
                    isset($requestParams[$paramName]) &&
                    !empty($requestParams[$paramName])
                ) {
                    $params[$paramName] = $requestParams[$paramName];
                }
            }
        }

        $widgetController = $this->getComponent('Widget');
        foreach ($widgetNames as $widgetName) {
            if ($widgetName === 'shopJsCart') {
                $widget = new \Cx\Core_Modules\Widget\Model\Entity\SingleParseEsiWidget(
                    $this,
                    $widgetName,
                    $widgetType
                );
                $widget->setEsiVariable(
                    \Cx\Core_Modules\Widget\Model\Entity\EsiWidget::ESI_VAR_ID_USER|
                    \Cx\Core_Modules\Widget\Model\Entity\EsiWidget::ESI_VAR_ID_THEME|
                    \Cx\Core_Modules\Widget\Model\Entity\EsiWidget::ESI_VAR_ID_CHANNEL|
                    \Cx\Core_Modules\Widget\Model\Entity\EsiWidget::ESI_VAR_ID_LOCALE
                );
            } else {
                $widget = new \Cx\Core_Modules\Widget\Model\Entity\EsiWidget(
                    $this,
                    $widgetName,
                    $widgetType,
                    '',
                    '',
                    $params
                );
                if ($widgetType == \Cx\Core_Modules\Widget\Model\Entity\EsiWidget::TYPE_PLACEHOLDER) {
                    $widget->setEsiVariable(
                        \Cx\Core_Modules\Widget\Model\Entity\EsiWidget::ESI_VAR_ID_THEME|
                        \Cx\Core_Modules\Widget\Model\Entity\EsiWidget::ESI_VAR_ID_CHANNEL
                    );
                }
                $widget->setEsiVariable(
                    \Cx\Core_Modules\Widget\Model\Entity\EsiWidget::ESI_VAR_ID_CURRENCY|
                    \Cx\Core_Modules\Widget\Model\Entity\EsiWidget::ESI_VAR_ID_USER
                );
            }
            $widgetController->registerWidget(
                $widget
            );
        }
    }
}
