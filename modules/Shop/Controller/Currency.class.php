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
 * Currency class
 * @copyright   CLOUDREXX CMS - CLOUDREXX AG
 * @package     cloudrexx
 * @subpackage  module_shop
 * @author      Reto Kohli <reto.kohli@comvation.com>
 * @version     3.0.0
 * @todo        Edit PHP DocBlocks!
 */

namespace Cx\Modules\Shop\Controller;

/**
 * Currency related static methods
 * @copyright   CLOUDREXX CMS - CLOUDREXX AG
 * @package     cloudrexx
 * @subpackage  module_shop
 * @author      Reto Kohli <reto.kohli@comvation.com>
 * @version     3.0.0
 */
class Currency
{
    /**
     * Text key
     */
    const TEXT_NAME = 'currency_name';


    /**
     * Resets the $arrCurrency class array to null to enforce
     * reinitialisation
     *
     * Call this after changing the database table
     */
    static function reset()
    {
        self::$arrCurrency = null;
    }

    /**
     * Return the currency symbol for the ID given
     * @author  Reto Kohli <reto.kohli@comvation.com>
     * @static
     * @param   integer   $currencyId   The currency ID
     * @return  mixed                   The currency symbol on success,
     *                                  false otherwise
     * @global  ADONewConnection
     */
    static function getSymbolById($currencyId)
    {
        if (!is_array(self::$arrCurrency)) self::init();
        if (isset(self::$arrCurrency[$currencyId]['symbol']))
            return self::$arrCurrency[$currencyId]['symbol'];
        return false;
    }

}
