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
     * Returns the formatted amount in a non-localized notation
     *
     * The optional $length is inserted into the sprintf()
     * format string and determines the maximum length of the number.
     * If present, the optional $padding character is inserted into the
     * sprintf() format string.
     * The optional $increment parameter overrides the increment value
     * of the *active* Currency, which is used by default.
     * The $increment value limits the number of digits printed after the
     * decimal point.
     * Currently, the number is formatted as a float, using no thousands,
     * and '.' as decimal separator.
     * @todo    Localize!  Create language and country dependant
     *          settings in the database, and make this behave accordingly.
     * @author  Reto Kohli <reto.kohli@comvation.com>
     * @static
     * @param   double  $price      The amount
     * @param   string  $length     The optional number length
     * @param   string  $padding    The optional padding character
     * @param   float   $increment  The optional increment
     * @return  double            The formatted amount
     */
    static function formatPrice($price, $length='', $padding='', $increment=null)
    {
//\DBG::log("formatPrice($price, $length, $padding, $increment): Entered");
        $decimals = 2;
        if (empty ($increment)) {
            if (!is_array(self::$arrCurrency)) self::init();
            $increment =
                self::$arrCurrency[self::$activeCurrencyId]['increment'];
        }
        $increment = floatval($increment);
        if ($increment > 0) {
            $decimals = max(0, -floor(log10($increment)));
            $price = round($price/$increment)*$increment;
        }
        $price = sprintf('%'.$padding.$length.'.'.$decimals.'f', $price);
//\DBG::log("formatPrice($price, $length, $padding, $increment): Decimals: $decimals");
        return $price;
    }


    /**
     * Returns the amount in a non-localized notation in cents,
     * rounded to one cent.
     *
     * Note that the amount argument is supposed to be in decimal format
     * with decimal separator and the appropriate number of decimal places,
     * as returned by {@link formatPrice()}, but it also works for integer
     * values like the ones returned by itself.
     * Removes underscores (_) as well as decimal (.) and thousands (')
     * separators, and replaces dashes (-) by zeroes (0).
     * @author  Reto Kohli <reto.kohli@comvation.com>
     * @static
     * @param   string    $amount   The amount in decimal format
     * @return  integer             The amount in cents, rounded to one cent
     * @since   2.1.0
     * @version 3.0.0
     */
    static function formatCents($amount)
    {
        $amount = preg_replace('/[_\\.\']/', '', $amount);
        $amount = preg_replace('/-/', '0', $amount);
        return intval($amount);
    }


    /**
     * Set up the Currency navbar
     * @author  Reto Kohli <reto.kohli@comvation.com>
     * @return  string            The HTML code for the Currency navbar
     * @access  public
     * @static
     */
    static function getCurrencyNavbar()
    {
        if (!is_array(self::$arrCurrency)) self::init();
        $strCurNavbar = '';
        $uri = $_SERVER['REQUEST_URI'];
        \Html::stripUriParam($uri, 'currency');
        foreach (self::$arrCurrency as $id => $arrCurrency) {
            if (!$arrCurrency['active']) continue;
            $strCurNavbar .=
                '<a class="'.($id == self::$activeCurrencyId
                    ? self::STYLE_NAME_ACTIVE : self::STYLE_NAME_INACTIVE
                ).
                '" href="'.htmlspecialchars(
                    $uri, ENT_QUOTES, CONTREXX_CHARSET
                ).
                '&amp;currency='.$id.'" title="'.$arrCurrency['code'].'">'.
                $arrCurrency['code'].
                '</a>';
        }
        return $strCurNavbar;
    }


    /**
     * Return the currency code for the ID given
     *
     * Mind that some methods rely on the return value being NULL for
     * unknown Currencies, see {@see PaymentProcessing::checkIn()}.
     * @author  Reto Kohli <reto.kohli@comvation.com>
     * @static
     * @param   integer   $currencyId   The currency ID
     * @return  mixed                   The currency code on success,
     *                                  NULL otherwise
     * @global  ADONewConnection
     */
    static function getCodeById($currencyId)
    {
        if (!is_array(self::$arrCurrency)) self::init();
        if (isset(self::$arrCurrency[$currencyId]['code']))
            return self::$arrCurrency[$currencyId]['code'];
        return NULL;
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

    /**
     * Handles database errors
     *
     * Also migrates old Currency names to the Text class,
     * and inserts default Currencyes if necessary
     * @return  boolean     false       Always!
     * @throws  Cx\Lib\Update_DatabaseException
     */
    static function errorHandler()
    {
        global $objDatabase;

// Currency
        \Text::errorHandler();

        $table_name = DBPREFIX.'module_shop_currencies';
        $table_structure = array(
            'id' => array('type' => 'INT(10)', 'unsigned' => true, 'notnull' => true, 'auto_increment' => true, 'primary' => true),
            'code' => array('type' => 'CHAR(3)', 'notnull' => true, 'default' => ''),
            'symbol' => array('type' => 'VARCHAR(20)', 'notnull' => true, 'default' => ''),
            'rate' => array('type' => 'DECIMAL(10,4)', 'unsigned' => true, 'notnull' => true, 'default' => '1.0000'),
// TODO: Changed default increment to '0.01'.  Apply to installation database!
            'increment' => array('type' => 'DECIMAL(6,5)', 'unsigned' => true, 'notnull' => true, 'default' => '0.01'),
            'ord' => array('type' => 'INT(5)', 'unsigned' => true, 'notnull' => true, 'default' => '0', 'renamefrom' => 'sort_order'),
            'active' => array('type' => 'TINYINT(1)', 'unsigned' => true, 'notnull' => true, 'default' => '1', 'renamefrom' => 'status'),
            'default' => array('type' => 'TINYINT(1)', 'unsigned' => true, 'notnull' => true, 'default' => '0', 'renamefrom' => 'is_default'),
        );
        $table_index = array();

        $default_lang_id = \FWLanguage::getDefaultLangId();
        if (\Cx\Lib\UpdateUtil::table_exist($table_name)) {
            if (\Cx\Lib\UpdateUtil::column_exist($table_name, 'name')) {
                // Migrate all Currency names to the Text table first
                \Text::deleteByKey('Shop', self::TEXT_NAME);
                $query = "
                    SELECT `id`, `code`, `name`
                      FROM `$table_name`";
                $objResult = \Cx\Lib\UpdateUtil::sql($query);
                if (!$objResult) {
                    throw new \Cx\Lib\Update_DatabaseException(
                       "Failed to query Currency names", $query);
                }
                while (!$objResult->EOF) {
                    $id = $objResult->fields['id'];
                    $name = $objResult->fields['name'];
                    if (!\Text::replace($id, $default_lang_id,
                        'Shop', self::TEXT_NAME, $name)) {
                        throw new \Cx\Lib\Update_DatabaseException(
                           "Failed to migrate Currency name '$name'");
                    }
                    $objResult->MoveNext();
                }
            }
            \Cx\Lib\UpdateUtil::table($table_name, $table_structure, $table_index);
            return false;
        }

        // If the table did not exist, insert defaults
        $arrCurrencies = array(
            'Schweizer Franken' => array('CHF', 'sFr.', 1.000000, '0.05', 1, 1, 1),
// TODO: I dunno if I'm just lucky, or if this will work with any charsets
// configured for PHP and mySQL?
// Anyway, neither entering the Euro-E literally nor various hacks involving
// utf8_decode()/utf8_encode() did the trick...
            'Euro' => array('EUR', html_entity_decode("&euro;"), 1.180000, '0.01', 2, 1, 0),
            'United States Dollars' => array('USD', '$', 0.880000, '0.01', 3, 1, 0),
        );
        // There is no previous version of this table!
        \Cx\Lib\UpdateUtil::table($table_name, $table_structure);
        // And there aren't even records to migrate, so
        foreach ($arrCurrencies as $name => $arrCurrency) {
            $query = "
                INSERT INTO `contrexx_module_shop_currencies` (
                    `code`, `symbol`, `rate`, `increment`,
                    `ord`, `active`, `default`
                ) VALUES (
                    '".join("','", $arrCurrency)."'
                )";
            $objResult = \Cx\Lib\UpdateUtil::sql($query);
            if (!$objResult) {
                throw new \Cx\Lib\Update_DatabaseException(
                    "Failed to insert default Currencies");
            }
            $id = $objDatabase->Insert_ID();
            if (!\Text::replace($id, FRONTEND_LANG_ID, 'Shop',
                self::TEXT_NAME, $name)) {
                throw new \Cx\Lib\Update_DatabaseException(
                    "Failed to add Text for default Currency name '$name'");
            }
        }

        // Always
        return false;
    }

}
