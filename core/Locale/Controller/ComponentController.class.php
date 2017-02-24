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
 * This is the locale component controller
 *
 * @copyright   Cloudrexx AG
 * @author      Manuel Schenk <manuel.schenk@comvation.com>
 * @author      Nicola Tommasi <nicola.tommasi@comvation.com>
 * @package     cloudrexx
 * @subpackage  core_locale
 * @version     5.0.0
 */

namespace Cx\Core\Locale\Controller;

/**
 * This is the locale component controller
 *
 * @copyright   Cloudrexx AG
 * @author      Manuel Schenk <manuel.schenk@comvation.com>
 * @author      Nicola Tommasi <nicola.tommasi@comvation.com>
 * @package     cloudrexx
 * @subpackage  core_locale
 * @version     5.0.0
 */
class ComponentController extends \Cx\Core\Core\Model\Entity\SystemComponentController  implements \Cx\Core\Event\Model\Entity\EventListener {

    /**
     * Returns all Controller class names for this component (except this)
     *
     * Be sure to return all your controller classes if you add your own
     * @return array List of Controller class names (without namespace)
     */
    public function getControllerClasses() {
        return array('Backend','JsonLocale');
    }


    public function registerEventListeners() {
        $this->cx->getEvents()->addEventListener('preComponent', $this);
    }

    /**
     * Event handler to load component language
     * @param string $eventName Name of triggered event, should always be static::EVENT_NAME
     * @param array $eventArgs Supplied arguments, should be an array (see DBG message below)
     */
    public function onEvent($eventName, array $eventArgs) {
        global $_ARRAYLANG;

        // we might be in a hook where lang is not yet initialized (before resolve)
        if (!count($_ARRAYLANG)) {
            $_ARRAYLANG = array();
        }

        $frontend = $this->cx->getMode() == \Cx\Core\Core\Controller\Cx::MODE_FRONTEND;
        $languageId = 0;
        if ($frontend) {
            if (defined('FRONTEND_LANG_ID')) {
                $languageId = FRONTEND_LANG_ID;
            }
        } else {
            if (defined('BACKEND_LANG_ID')) {
                $languageId = BACKEND_LANG_ID;
            }
        }
        $objInit = \Env::get('init');
        if (!$objInit || !$languageId) {
            return;
        }
        switch ($eventName) {
            case 'preComponent':
                // Skip if this component's lang already is in $_ARRAYLANG
                if (
                in_array(
                    $eventArgs['componentName'],
                    $this->componentsWithLoadedLang
                )
                ) {
                    return;
                }

                $_ARRAYLANG = array_merge(
                    $_ARRAYLANG,
                    $objInit->getComponentSpecificLanguageData(
                        $eventArgs['componentName'],
                        $frontend
                    )
                );
                $this->componentsWithLoadedLang[] = $eventArgs['componentName'];
                break;
        }
    }

    /**
     * Do something after all active components are loaded
     * USE CAREFULLY, DO NOT DO ANYTHING COSTLY HERE!
     * CALCULATE YOUR STUFF AS LATE AS POSSIBLE.
     */
    public function postComponentLoad() {
        global $objInit;
        // Initialize base system for language and theme
        // TODO: Get rid of InitCMS class
        $objInit = new \InitCMS($this->cx->getMode() == \Cx\Core\Core\Controller\Cx::MODE_FRONTEND ? 'frontend' : 'backend', \Env::get('em'));
        \Env::set('init', $objInit);
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
    public function getControllersAccessableByJson() {
        return array('JsonLocaleController');
    }

}
