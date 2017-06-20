<?php

/**
 * Contrexx
 *
 * @link      http://www.contrexx.com
 * @copyright Comvation AG 2007-2014
 * @version   Contrexx 4.0
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
 * "Contrexx" is a registered trademark of Comvation AG.
 * The licensing of the program under the AGPLv3 does not imply a
 * trademark license. Therefore any rights, title and interest in
 * our trademarks remain entirely with us.
 */

/**
 * FWCaptcha
 *
 * @copyright   CONTREXX CMS - COMVATION AG
 * @author      COMVATION Development Team <info@comvation.com>
 * @package     contrexx
 * @subpackage  lib_framework
 */

/**
 * FWCaptcha
 *
 * @copyright   CONTREXX CMS - COMVATION AG
 * @author      COMVATION Development Team <info@comvation.com>
 * @package     contrexx
 * @subpackage  lib_framework
 */
class FWCaptcha {
    private $objCaptcha = null;

    private function __construct($config)
    {
        global $_CONFIG, $sessionObj;

        if (!isset($sessionObj) || !is_object($sessionObj)) $sessionObj = \cmsSession::getInstance();

        $captchaMethod = $_CONFIG['captchaMethod'];
        switch ($captchaMethod) {
            case 'reCaptcha':
                $this->objCaptcha = new \Cx\Core_Modules\Captcha\Controller\ReCaptcha();
                break;

            case 'contrexxCaptcha':
            default:
                $this->objCaptcha = new \Cx\Core_Modules\Captcha\Controller\ContrexxCaptcha($config);
                break;
        }
    }

    public function __call($name, $args)
    {
        return call_user_func_array(array($this->objCaptcha, $name), $args);
    }

    public static function getInstance()
    {
        static $objCaptcha = null;

        if (!isset($objCaptcha)) {
            $objCaptcha = new self(\Env::get('config'));
        }

        return $objCaptcha;
    }
}

