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
 * A wrapper for the new JavaScript class
 * @deprecated
 * @author      Michael Ritter <michael.ritter@cloudrexx.com>
 * @copyright   CLOUDREXX CMS - CLOUDREXX AG
 * @package     cloudrexx
 * @subpackage  lib_framework
 */

/**
 * A wrapper for the new JavaScript class
 * @deprecated
 * @author      Michael Ritter <michael.ritter@cloudrexx.com>
 * @copyright   CLOUDREXX CMS - CLOUDREXX AG
 * @package     cloudrexx
 * @subpackage  lib_framework
 */
class JS
{
    /**
     * The main instance of the JavaScript class
     * @var \Cx\Lib\JavaScript\Controller\JavaScript
     */
    protected static $instance = null;
    
    /**
     * Returns the main instance of the JavaScript class
     * @return \Cx\Lib\JavaScript\Controller\JavaScript
     */
    public static function getInstance() {
        if (!static::$instance) {
            static::$instance = new \Cx\Lib\JavaScript\Controller\JavaScript();
        }
        return static::$instance;
    }
    
    /**
     * Re-routes all static calls to this class to the main instance of the
     * JavaScript class
     * @param string $name Name of the method being called
     * @param array $arguments An enumerated array containing the parameters passed to the method
     * @return mixed Return value of the re-routed method
     */
    public static function __callStatic($name, $arguments) {
        return call_user_func_array(array(static::getInstance(), $name), $arguments);
    }
}
