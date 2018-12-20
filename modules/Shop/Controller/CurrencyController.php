<?php
/**
 * Created by PhpStorm.
 * User: sam
 * Date: 18.12.18
 * Time: 15:06
 */

namespace Cx\Modules\Shop\Controller;


class CurrencyController
{
    /**
     * class suffixes for active/inactive currencies
     */
    const STYLE_NAME_INACTIVE = 'inactive';
    const STYLE_NAME_ACTIVE   = 'active';

    /**
     * Array of available currencies (default null).
     *
     * Use {@link getCurrencyArray()} to access it from outside this class.
     * @access  private
     * @static
     * @var     array
     */
    private static $arrCurrency = null;

    /**
     * Active currency object id (default null).
     *
     * Use {@link getActiveCurrencyId()} to access it from outside this class.
     * @access  private
     * @static
     * @var     integer
     */
    private static $activeCurrencyId = false;

    /**
     * Default currency object id (defaults to null).
     *
     * Use {@link getDefaultCurrencyId()} to access it from outside this class.
     * @access  private
     * @static
     * @var     integer
     */
    private static $defaultCurrencyId = false;

    /**
     * Initialize currencies
     *
     * Sets up the Currency array, and picks the selected Currency from the
     * 'currency' request parameter, if available.
     * @author  Reto Kohli <reto.kohli@comvation.com>
     * @static
     */
    static function init($active_currency_id=0)
    {
        $cx = \Cx\Core\Core\Controller\Cx::instanciate();
        $currencies = $cx->getDb()->getEntityManager()->getRepository(
            '\Cx\Modules\Shop\Model\Entity\Currency'
        )->findBy(array(), array('ord' => 'ASC'));

        foreach ($currencies as $currency) {
            self::$arrCurrency[$currency->getId()] = array(
                'id' => $currency->getId(),
                'code' => $currency->getCode(),
                'symbol' => $currency->getSymbol(),
                'name' => $currency->getName(),
                'rate' => $currency->getRate(),
                'increment' => $currency->getIncrement(),
                'ord' => $currency->getOrd(),
                'active' => $currency->getActive(),
                'default' => $currency->getDefault(),
            );
            if ($currency->getDefault())
                self::$defaultCurrencyId = $currency->getId();
        }

        if (!isset($_SESSION['shop'])) {
            $_SESSION['shop'] = array();
        }
        if (isset($_REQUEST['currency'])) {
            $currency_id = intval($_REQUEST['currency']);
            $_SESSION['shop']['currencyId'] =
                (isset(self::$arrCurrency[$currency_id])
                    ? $currency_id : self::$defaultCurrencyId
                );
        }
        if (!empty($active_currency_id)) {
            $_SESSION['shop']['currencyId'] =
                (isset(self::$arrCurrency[$active_currency_id])
                    ? $active_currency_id : self::$defaultCurrencyId
                );
        }
        if (empty($_SESSION['shop']['currencyId'])) {
            $_SESSION['shop']['currencyId'] = self::$defaultCurrencyId;
        }
        self::$activeCurrencyId = intval($_SESSION['shop']['currencyId']);
        return true;
    }

    /**
     * Returns the currency array
     * @author  Reto Kohli <reto.kohli@comvation.com>
     * @access  public
     * @static
     * @return  array   The currency array
     */
    static function getCurrencyArray()
    {
        if (!is_array(self::$arrCurrency)) self::init();
        return self::$arrCurrency;
    }

    /**
     * Returns the default currency ID
     * @author  Reto Kohli <reto.kohli@comvation.com>
     * @access  public
     * @static
     * @return  integer     The ID of the default currency
     */
    static function getDefaultCurrencyId()
    {
        if (!is_array(self::$arrCurrency)) self::init();
        return self::$defaultCurrencyId;
    }

    /**
     * Returns the default currency symbol
     * @author  Reto Kohli <reto.kohli@comvation.com>
     * @access  public
     * @static
     * @return  string      The string representing the default currency
     */
    static function getDefaultCurrencySymbol()
    {
        if (!is_array(self::$arrCurrency)) self::init();
        return self::$arrCurrency[self::$defaultCurrencyId]['symbol'];
    }

    /**
     * Returns the default currency code
     * @author  Reto Kohli <reto.kohli@comvation.com>
     * @access  public
     * @static
     * @return  string      The string representing the default currency code
     */
    static function getDefaultCurrencyCode()
    {
        if (!is_array(self::$arrCurrency)) self::init();
        return self::$arrCurrency[self::$defaultCurrencyId]['code'];
    }

    /**
     * Returns the active currency ID
     * @author  Reto Kohli <reto.kohli@comvation.com>
     * @access  public
     * @static
     * @return  integer     The ID of the active currency
     */
    static function getActiveCurrencyId()
    {
        if (!is_array(self::$arrCurrency)) self::init();
        return self::$activeCurrencyId;
    }

    /**
     * Set the active currency ID
     * @param   integer     $currency_id    The active Currency ID
     * @author  Reto Kohli <reto.kohli@comvation.com>
     * @access  public
     * @static
     */
    static function setActiveCurrencyId($currency_id)
    {
        if (!is_array(self::$arrCurrency)) self::init($currency_id);
        self::$activeCurrencyId = $currency_id;
    }

    /**
     * Returns the active currency symbol
     *
     * This is a custom Currency name that does not correspond to any
     * ISO standard, like "sFr.", or "Euro".
     * @author  Reto Kohli <reto.kohli@comvation.com>
     * @access  public
     * @static
     * @return  string      The string representing the active currency
     */
    static function getActiveCurrencySymbol()
    {
        if (!is_array(self::$arrCurrency)) self::init();
        return self::$arrCurrency[self::$activeCurrencyId]['symbol'];
    }

    /**
     * Returns the active currency code
     *
     * This usually corresponds to the ISO 4217 code for the Currency,
     * like CHF, or USD.
     * @author  Reto Kohli <reto.kohli@comvation.com>
     * @access  public
     * @static
     * @return  string      The string representing the active currency code
     */
    static function getActiveCurrencyCode()
    {
        if (!is_array(self::$arrCurrency)) self::init();
        return self::$arrCurrency[self::$activeCurrencyId]['code'];
    }

    /**
     * Returns the array of known currencies
     *
     * The array is of the form
     *  array(
     *    array(
     *      0 => ISO 4217 code,
     *      1 => Country and currency name,
     *      2 => number of decimals,
     *    ),
     *    ... more ...
     *  )
     * Note that the code is unique, however, some currencies appear more
     * than once with different codes that may be used.
     * The number of decimals may be non-numeric if unknown.  Ignore these.
     * @return  array           The known currencies array
     * @todo    Add a symbol, symbol position, and other localization
     *          settings (decimal- and thousands separator, etc.) for each
     *          currency
     */
    static function known_currencies()
    {
        return array(
            array('AED', 'United Arab Emirates Dirham', '2', ),
            array('AFN', 'Afghanistan Afghani', '2', ),
            array('ALL', 'Albania Lek', '2', ),
            array('AMD', 'Armenia Dram', '2', ),
            array('ANG', 'Netherlands Antilles Guilder', '2', ),
            array('AOA', 'Angola Kwanza', '2', ),
            array('ARS', 'Argentina Peso', '2', ),
            array('AUD', 'Australia Dollar', '2', ),
            array('AWG', 'Aruba Guilder', '2', ),
            array('AZM', 'Azerbaijan Manat', '2', ),
            array('AZN', 'Azerbaijan New Manat', '2', ),
            array('BAM', 'Bosnia and Herzegovina Convertible Marka', '2', ),
            array('BBD', 'Barbados Dollar', '2', ),
            array('BDT', 'Bangladesh Taka', '2', ),
            array('BGN', 'Bulgaria Lev', '2', ),
            array('BHD', 'Bahrain Dinar', '3', ),
            array('BIF', 'Burundi Franc', '0', ),
            array('BMD', 'Bermuda Dollar', '2', ),
            array('BND', 'Brunei Darussalam Dollar', '2', ),
            array('BOB', 'Bolivia Boliviano', '2', ),
            array('BRL', 'Brazil Real', '2', ),
            array('BSD', 'Bahamas Dollar', '2', ),
            array('BTN', 'Bhutan Ngultrum', '2', ),
            array('BWP', 'Botswana Pula', '2', ),
            array('BYR', 'Belarus Ruble', '0', ),
            array('BZD', 'Belize Dollar', '2', ),
            array('CAD', 'Canada Dollar', '2', ),
            array('CDF', 'Congo/Kinshasa Franc', '2', ),
            array('CHF', 'Switzerland Franc', '2', ),
            array('CLP', 'Chile Peso', '0', ),
            array('CNY', 'China Yuan Renminbi', '2', ),
            array('COP', 'Colombia Peso', '2', ),
            array('CRC', 'Costa Rica Colon', '2', ),
            array('CSD', 'Serbia Dinar', '2', ),
            array('CUC', 'Cuba Convertible Peso', '2', ),
            array('CUP', 'Cuba Peso', '2', ),
            array('CVE', 'Cape Verde Escudo', '2', ),
            array('CYP', 'Cyprus Pound', '2', ),
            array('CZK', 'Czech Republic Koruna', '2', ),
            array('DJF', 'Djibouti Franc', '0', ),
            array('DKK', 'Denmark Krone', '2', ),
            array('DOP', 'Dominican Republic Peso', '2', ),
            array('DZD', 'Algeria Dinar', '2', ),
            array('EEK', 'Estonian Kroon', '2', ),
            array('EGP', 'Egypt Pound', '2', ),
            array('ERN', 'Eritrea Nakfa', '2', ),
            array('ETB', 'Ethiopia Birr', '2', ),
            array('EUR', 'Euro Member Countries', '2', ),
            array('FJD', 'Fiji Dollar', '2', ),
            array('FKP', 'Falkland Islands (Malvinas) Pound', '2', ),
            array('GBP', 'United Kingdom Pound', '2', ),
            array('GEL', 'Georgia Lari', '2', ),
            array('GGP', 'Guernsey Pound', '?', ),
            array('GHC', 'Ghana Cedi', '2', ),
            array('GHS', 'Ghana Cedi', '2', ),
            array('GIP', 'Gibraltar Pound', '2', ),
            array('GMD', 'Gambia Dalasi', '2', ),
            array('GNF', 'Guinea Franc', '0', ),
            array('GTQ', 'Guatemala Quetzal', '2', ),
            array('GYD', 'Guyana Dollar', '2', ),
            array('HKD', 'Hong Kong Dollar', '2', ),
            array('HNL', 'Honduras Lempira', '2', ),
            array('HRK', 'Croatia Kuna', '2', ),
            array('HTG', 'Haiti Gourde', '2', ),
            array('HUF', 'Hungary Forint', '2', ),
            array('IDR', 'Indonesia Rupiah', '2', ),
            array('ILS', 'Israel Shekel', '2', ),
            array('IMP', 'Isle of Man Pound', '?', ),
            array('INR', 'India Rupee', '2', ),
            array('IQD', 'Iraq Dinar', '3', ),
            array('IRR', 'Iran Rial', '2', ),
            array('ISK', 'Iceland Krona', '0', ),
            array('JEP', 'Jersey Pound', '?', ),
            array('JMD', 'Jamaica Dollar', '2', ),
            array('JOD', 'Jordan Dinar', '3', ),
            array('JPY', 'Japan Yen', '0', ),
            array('KES', 'Kenya Shilling', '2', ),
            array('KGS', 'Kyrgyzstan Som', '2', ),
            array('KHR', 'Cambodia Riel', '2', ),
            array('KMF', 'Comoros Franc', '0', ),
            array('KPW', 'Korea (North) Won', '2', ),
            array('KRW', 'Korea (South) Won', '0', ),
            array('KWD', 'Kuwait Dinar', '3', ),
            array('KYD', 'Cayman Islands Dollar', '2', ),
            array('KZT', 'Kazakhstan Tenge', '2', ),
            array('LAK', 'Laos Kip', '2', ),
            array('LBP', 'Lebanon Pound', '2', ),
            array('LKR', 'Sri Lanka Rupee', '2', ),
            array('LRD', 'Liberia Dollar', '2', ),
            array('LSL', 'Lesotho Loti', '2', ),
            array('LTL', 'Lithuania Litas', '2', ),
            array('LVL', 'Latvia Lat', '2', ),
            array('LYD', 'Libya Dinar', '3', ),
            array('MAD', 'Morocco Dirham', '2', ),
            array('MDL', 'Moldova Leu', '2', ),
            array('MGA', 'Madagascar Ariary', '2', ),
            array('MKD', 'Macedonia Denar', '2', ),
            array('MMK', 'Myanmar (Burma) Kyat', '2', ),
            array('MNT', 'Mongolia Tughrik', '2', ),
            array('MOP', 'Macau Pataca', '2', ),
            array('MRO', 'Mauritania Ouguiya', '2', ),
            array('MTL', 'Maltese Lira', '2', ),
            array('MUR', 'Mauritius Rupee', '2', ),
            array('MVR', 'Maldives (Maldive Islands) Rufiyaa', '2', ),
            array('MWK', 'Malawi Kwacha', '2', ),
            array('MXN', 'Mexico Peso', '2', ),
            array('MYR', 'Malaysia Ringgit', '2', ),
            array('MZM', 'Mozambique Metical', '2', ),
            array('MZN', 'Mozambique Metical', '2', ),
            array('NAD', 'Namibia Dollar', '2', ),
            array('NGN', 'Nigeria Naira', '2', ),
            array('NIO', 'Nicaragua Cordoba', '2', ),
            array('NOK', 'Norway Krone', '2', ),
            array('NPR', 'Nepal Rupee', '2', ),
            array('NZD', 'New Zealand Dollar', '2', ),
            array('OMR', 'Oman Rial', '3', ),
            array('PAB', 'Panama Balboa', '2', ),
            array('PEN', 'Peru Nuevo Sol', '2', ),
            array('PGK', 'Papua New Guinea Kina', '2', ),
            array('PHP', 'Philippines Peso', '2', ),
            array('PKR', 'Pakistan Rupee', '2', ),
            array('PLN', 'Poland Zloty', '2', ),
            array('PYG', 'Paraguay Guarani', '0', ),
            array('QAR', 'Qatar Riyal', '2', ),
            array('RON', 'Romania New Leu', '2', ),
            array('RSD', 'Serbia Dinar', '?', ),
            array('RUB', 'Russia Ruble', '2', ),
            array('RWF', 'Rwanda Franc', '0', ),
            array('SAR', 'Saudi Arabia Riyal', '2', ),
            array('SBD', 'Solomon Islands Dollar', '2', ),
            array('SCR', 'Seychelles Rupee', '2', ),
            array('SDD', 'Sudan Dinar', '2', ),
            array('SDG', 'Sudan Pound', '?', ),
            array('SEK', 'Sweden Krona', '2', ),
            array('SGD', 'Singapore Dollar', '2', ),
            array('SHP', 'Saint Helena Pound', '2', ),
            array('SIT', 'Slovenia Tolar', '2', ),
            array('SKK', 'Slovak Koruna', '2', ),
            array('SLL', 'Sierra Leone Leone', '2', ),
            array('SOS', 'Somalia Shilling', '2', ),
            array('SPL', 'Seborga Luigino', '?', ),
            array('SRD', 'Suriname Dollar', '2', ),
            array('STD', 'São Principe and Tome Dobra', '2', ),
            array('SVC', 'El Salvador Colon', '2', ),
            array('SYP', 'Syria Pound', '2', ),
            array('SZL', 'Swaziland Lilangeni', '2', ),
            array('THB', 'Thailand Baht', '2', ),
            array('TJS', 'Tajikistan Somoni', '2', ),
            array('TMM', 'Turkmenistan Manat', '2', ),
            array('TMT', 'Turkmenistan Manat', '2', ),
            array('TND', 'Tunisia Dinar', '3', ),
            array('TOP', 'Tonga Pa\'anga', '2', ),
            array('TRY', 'Turkey Lira', '2', ),
            array('TTD', 'Trinidad and Tobago Dollar', '2', ),
            array('TVD', 'Tuvalu Dollar', '?', ),
            array('TWD', 'Taiwan New Dollar', '2', ),
            array('TZS', 'Tanzania Shilling', '2', ),
            array('UAH', 'Ukraine Hryvna', '2', ),
            array('UGX', 'Uganda Shilling', '2', ),
            array('USD', 'United States Dollar', '2', ),
            array('UYU', 'Uruguay Peso', '2', ),
            array('UZS', 'Uzbekistan Som', '2', ),
            array('VEB', 'Venezuela Bolivar', '2', ),
            array('VEF', 'Venezuela Bolivar Fuerte', '2', ),
            array('VND', 'Viet Nam Dong', '2', ),
            array('VUV', 'Vanuatu Vatu', '0', ),
            array('WST', 'Samoa Tala', '2', ),
            array('XAF', 'Communauté Financière Africaine (BEAC) CFA Franc BEAC', '0', ),
            array('XCD', 'East Caribbean Dollar', '2', ),
            array('XDR', 'International Monetary Fund (IMF) Special Drawing Rights', '5', ),
            array('XOF', 'Communauté Financière Africaine (BCEAO) Franc', '0', ),
            array('XPF', 'Comptoirs Français du Pacifique (CFP) Franc', '0', ),
            array('YER', 'Yemen Rial', '2', ),
            array('ZAR', 'South Africa Rand', '2', ),
            array('ZMK', 'Zambia Kwacha', '2', ),
            array('ZWD', 'Zimbabwe Dollar', '2', ),
        );
    }

    /**
     * Returns the array of names for all known currencies indexed
     * by ISO 4217 code
     *
     * You can specify a custom format for the names using the $format
     * parameter.  It defaults to '%2$s (%1$s)', that is the currency
     * name (%2) followed by the ISO 4217 code (%1) in parentheses.
     * Also, the number of decimals for the currency is available as %3.
     * @param   string  $format     The optional sprintf() format
     * @return  array               The currency name array
     */
    static function get_known_currencies_name_array($format=null)
    {
        if (empty($format)) $format = '%2$s (%1$s)';
        $arrName = array();
        foreach (self::known_currencies() as $currency) {
            $arrName[$currency[0]] = sprintf($format,
                $currency[0], $currency[1], $currency[2]);
        }
        return $arrName;
    }

    /**
     * Returns the array of increments for all known currencies indexed
     * by ISO 4217 code
     * @return  array               The currency increment array
     */
    static function get_known_currencies_increment_array()
    {
        $arrIncrement = array();
        foreach (self::known_currencies() as $currency) {
            $increment = (is_numeric($currency[2])
                ? pow(10, -$currency[2]) : null);
            $arrIncrement[$currency[0]] = $increment;
        }
        return $arrIncrement;
    }
}