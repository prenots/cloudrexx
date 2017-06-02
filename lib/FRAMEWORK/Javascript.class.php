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
     * @var \Cx\Core\JavaScript\Controller\JavaScript
     */
    protected static $instances = array();
    
    /**
     * Returns the current instance
     *
     * @param boolean $forceNew
     * @return \Cx\Core\JavaScript\Controller\JavaScript Current instance
     */
    public static function getInstance($forceNew = false)
    {
        if (!count(static::$instances) || $forceNew) {
            static::$instances[] = new \Cx\Core\JavaScript\Controller\JavaScript();
        }
        return end(static::$instances);
    }

    /**
     * Adds a new instance to the stack. This instance will be used as current
     * instance.
     *
     * @param \Cx\Core\JavaScript\Controller\JavaScript $instance (optional) If left empty a new instance will be created
     */
    public static function push($instance = null) {
        if ($instance) {
            static::$instances[] = $instance;
            return;
        }
        static::getInstance(true);
    }

    /**
     * Drops the current instance from the stack and returns it
     * @return \Cx\Core\JavaScript\Controller\JavaScript
     */
    public static function pop() {
        return array_pop(static::$instances);
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
