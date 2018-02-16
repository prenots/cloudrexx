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
 * This file loads everything needed to load Cloudrexx. Just require this file
 * and execute \init($mode); while $mode is optional. $mode can be one of
 * 'frontend', 'backend', 'cli' and 'minimal'
 *
 * This is just a wrapper to load the cloudrexx class
 * It is used in order to display a proper error message on hostings without
 * PHP 7.0 or newer.
 *
 * DO NOT USE NAMESPACES WITHIN THIS FILE or else the error message won't be
 * displayed on these hostings.
 *
 * Checks PHP version, loads debugger and initial config, checks if installed
 * and loads the Cloudrexx class
 *
 * @copyright   Cloudrexx AG
 * @author      Michael Ritter <michael.ritter@comvation.com>
 * @package     cloudrexx
 * @subpackage  core_core
 * @version     3.1.0
 */

// Check php version (7.0 or newer is required)
$php = phpversion();
if (version_compare($php, '7.0.0') < 0) {
    die('Das Cloudrexx CMS ben&ouml;tigt mindestens PHP in der Version 7.0.<br />Auf Ihrem System l&auml;uft PHP '.$php);
}

// identify path of this file
$thisFilePath = dirname(__FILE__, 3);

// load default base config
$configFilePath = $thisFilePath . '/config/configuration.php';
if (file_exists($configFilePath)) {
    global $_PATHCONFIG;
    /**
     * @ignore
     */
    include_once $configFilePath;
} else {
    // redirect to webinstaller in case no base config was found
    \header('Location: http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . 'installer/index.php');
    exit;
}

/**
 * @ignore
 */
require_once $thisFilePath . '/lib/FRAMEWORK/DBG/DBG.php';

/**
 * Activate debugging to file
 */
\DBG::activate(DBG_PHP | DBG_LOG_FILE);

/**
 * @ignore
 */
require_once $thisFilePath . '/core/Core/Controller/Cx.class.php';
