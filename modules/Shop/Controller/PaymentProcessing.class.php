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
 * Payment Service Provider class
 * @package     cloudrexx
 * @subpackage  module_shop
 * @copyright   CLOUDREXX CMS - CLOUDREXX AG
 * @version     3.0.0
 * @author      Reto Kohli <reto.kohli@comvation.com> (parts)
 */

namespace Cx\Modules\Shop\Controller;

/**
 * Debug mode
 */
define('_PAYMENT_DEBUG', 0);

/**
 * Payment Service Provider manager
 *
 * These are the requirements of the current specification
 * for any external payment service provider class:
 * - Any payment method *MUST* be implemented in its own class, with its
 *   constructor and/or methods being called from PaymentProcessing.class.php
 *   using only the two methods checkIn() and checkOut().
 * - Any data needed by the payment service class *MUST* be provided
 *   as arguments to the constructor and/or methods from within the
 *   PaymentProcessing class.
 * - Any code in checkOut() *MUST* return either a valid payment form *OR*
 *   redirect to a payment page of that provider, supplying all necessary
 *   data for a successful payment.
 * - Any code in checkIn() *MUST* return the original order ID of the order
 *   being processed on success, false otherwise (both in the case of failure
 *   and upon cancelling the payment).
 * - A payment provider class *MUST NOT* access the database itself, in
 *   particular it is strictly forbidden to read or change the order status
 *   of any order.
 * - A payment provider class *MUST NOT* store any data in the global session
 *   array.  Instead, it is to rely on the protocol of the payment service
 *   provider to transmit and retrieve all necessary data.
 * - Any payment method providing different return values for different
 *   outcomes of the payment in the consecutive HTTP requests *SHOULD* use
 *   the follwing arguments and values:
 *      Successful payments:            result=1
 *      Successful payments, silent *:  result=-1
 *      Failed payments:                result=0
 *      Aborted payments:               result=2
 *      Aborted payments, silent *:     result=-2
 *   * Some payment services do not only redirect the customer after a
 *     successful payment has completed, but already after the payment
 *     has been authorized.  Yellowpay, as an example, expects an empty
 *     page as a reply to such a request.
 *     Other PSP send the notification even for failed or cancelled
 *     transactions, e.g. Datatrans.  Consult your local PSP for further
 *     information.
 * @package     cloudrexx
 * @subpackage  module_shop
 * @author      Reto Kohli <reto.kohli@comvation.com> (parts)
 * @copyright   CLOUDREXX CMS - CLOUDREXX AG
 * @version     3.0.0
 */
class PaymentProcessing
{
    /**
     * Array of all available payment processors
     * @access  private
     * @static
     * @var     array
     */
    private static $arrPaymentProcessor = false;

    /**
     * The selected processor ID
     * @access  private
     * @static
     * @var     integer
     */
    private static $processorId = false;


    /**
     * Initialize known payment service providers
     */
    static function init()
    {
        global $objDatabase;

        $query = '
            SELECT id, type, name, description,
                   company_url, status, picture
              FROM '.DBPREFIX.'module_shop_payment_processors
          ORDER BY id';
        $objResult = $objDatabase->Execute($query);
        while (!$objResult->EOF) {
            self::$arrPaymentProcessor[$objResult->fields['id']] = array(
                'id' => $objResult->fields['id'],
                'type' => $objResult->fields['type'],
                'name' => $objResult->fields['name'],
                'description' => $objResult->fields['description'],
                'company_url' => $objResult->fields['company_url'],
                'status' => $objResult->fields['status'],
                'picture' => $objResult->fields['picture'],
            );
            $objResult->MoveNext();
        }
        // Verify version 3.0 complete data
        // Fix version 3.0.4 data
        if (   empty(self::$arrPaymentProcessor[11])
            || empty(self::$arrPaymentProcessor[4]['name'])
            || self::$arrPaymentProcessor[4]['name'] != 'internal') {
            self::errorHandler();
            self::init();
        }
    }


    /**
     * Set the active processor ID
     * @return  void
     * @param   integer $processorId    The PSP ID to use
     * @static
     */
    static function initProcessor($processorId)
    {
        if (!is_array(self::$arrPaymentProcessor)) self::init();
        self::$processorId = $processorId;
    }


    /**
     * Returns an array with all the payment processor names indexed
     * by their ID.
     * @return  array             The payment processor name array
     *                            on success, the empty array on failure.
     * @static
     */
    static function getPaymentProcessorNameArray()
    {
        global $_ARRAYLANG;

        if (empty(self::$arrPaymentProcessor)) self::init();
        $arrName = array();
        foreach (self::$arrPaymentProcessor as $id => $arrProcessor) {
            $arrName[$id] =
                $_ARRAYLANG['TXT_SHOP_PSP_'.
                strtoupper($arrProcessor['name'])];
        }
        return $arrName;
    }


    /**
     * Returns the name associated with a payment processor ID.
     *
     * If the optional argument is not set and greater than zero, the value
     * processorId stored in this object is used.  If this is invalid as
     * well, returns the empty string.
     * @param   integer     $processorId    The payment processor ID
     * @return  string                      The payment processors' name,
     *                                      or the empty string on failure.
     * @global  ADONewConnection
     * @static
     */
    static function getPaymentProcessorName($processorId=0)
    {
        // Either the argument or the class variable must be initialized
        if (!$processorId) $processorId = self::$processorId;
        if (!$processorId) return '';
        if (empty(self::$arrPaymentProcessor)) self::init();
        return self::$arrPaymentProcessor[$processorId]['name'];
    }


    /**
     * Returns the processor type associated with a payment processor ID.
     *
     * If the optional argument is not set and greater than zero, the value
     * processorId stored in this object is used.  If this is invalid as
     * well, returns the empty string.
     * Note: Currently supported types are 'internal' and 'external'.
     * @author  Reto Kohli <reto.kohli@comvation.com>
     * @param   integer     $processorId    The payment processor ID
     * @return  string                      The payment processor type,
     *                                      or the empty string on failure.
     * @global  ADONewConnection
     * @static
     */
    static function getCurrentPaymentProcessorType($processorId=0)
    {
        // Either the argument or the object may not be initialized
        if (!$processorId) $processorId = self::$processorId;
        if (!$processorId) return '';
        if (empty(self::$arrPaymentProcessor)) self::init();
        return self::$arrPaymentProcessor[$processorId]['type'];
    }


    /**
     * Check out the payment processor associated with the payment processor
     * selected by {@link initProcessor()}.
     *
     * If the page is redirected, or has already been handled, returns the empty
     * string.
     * In the other cases, returns HTML code for the payment form and to insert
     * a picture representing the payment method.
     * @return  string      Empty string, or HTML code
     * @static
     */
    static function checkOut()
    {
        global $_ARRAYLANG;

        if (!is_array(self::$arrPaymentProcessor)) self::init();
        $return = '';
        // @since 3.0.5: Names are now lowercase, i.e. "internal" instead of "Internal"
        switch (self::getPaymentProcessorName()) {
            case 'internal':
                \Cx\Core\Csrf\Controller\Csrf::redirect(
                    \Cx\Core\Routing\Url::fromModuleAndCmd('Shop'.MODULE_INDEX, 'success', '',
                        array('result' => 1, 'handler' => 'internal')
                    )
                );
            case 'internal_lsv':
                \Cx\Core\Csrf\Controller\Csrf::redirect(
                    \Cx\Core\Routing\Url::fromModuleAndCmd('Shop'.MODULE_INDEX, 'success', '',
                        array('result' => 1, 'handler' => 'internal')
                    )
                );
            case 'internal_creditcard':
                // Not implemented
                \Cx\Core\Csrf\Controller\Csrf::redirect(
                    \Cx\Core\Routing\Url::fromModuleAndCmd('Shop'.MODULE_INDEX, 'success', '',
                        array('result' => 1, 'handler' => 'internal')
                    )
                );
            case 'internal_debit':
                // Not implemented
                \Cx\Core\Csrf\Controller\Csrf::redirect(
                    \Cx\Core\Routing\Url::fromModuleAndCmd('Shop'.MODULE_INDEX, 'success', '',
                        array('result' => 1, 'handler' => 'internal')
                    )
                );
            case 'saferpay':
            case 'saferpay_all_cards':
            case 'saferpay_mastercard_multipay_car': // Obsolete
            case 'saferpay_visa_multipay_car':  // Obsolete
                $return = \Cx\Modules\Shop\Controller\PaymentProcessorController::_SaferpayProcessor();
                break;
            case 'yellowpay': // was: 'PostFinance_DebitDirect'
                $return = \Cx\Modules\Shop\Controller\PaymentProcessorController::_YellowpayProcessor();
                break;
            case 'payrexx':
                $return = \Cx\Modules\Shop\Controller\PaymentProcessorController::_PayrexxProcessor();
                break;
            // Added 20100222 -- Reto Kohli
            case 'mobilesolutions':
                $return = \PostfinanceMobile::getForm(
                    intval(bcmul($_SESSION['shop']['grand_total_price'], 100, 0)),
                    $_SESSION['shop']['order_id']);
                if ($return) {
//DBG::log("Postfinance Mobile getForm() returned:");
//DBG::log($return);
                } else {
\DBG::log("PaymentProcessing::checkOut(): ERROR: Postfinance Mobile getForm() failed");
\DBG::log("Postfinance Mobile error messages:");
foreach (\PostfinanceMobile::getErrors() as $error) {
\DBG::log($error);
}
                }
                break;
            // Added 20081117 -- Reto Kohli
            case 'datatrans':
                $return = \Cx\Modules\Shop\Controller\PaymentProcessorController::getDatatransForm(\Cx\Modules\Shop\Controller\CurrencyController::getActiveCurrencyCode());
                break;
            case 'paypal':
                $cx = \Cx\Core\Core\Controller\Cx::instanciate();
                $currency = $cx->getDb()->getEntityManager()->getRepository(
                    '\Cx\Modules\Shop\Model\Entity\Currency'
                )->find($_SESSION['shop']['currencyId']);
                $order_id = $_SESSION['shop']['order_id'];
                $account_email = \Cx\Core\Setting\Controller\Setting::getValue('paypal_account_email','Shop');
                $item_name = $_ARRAYLANG['TXT_SHOP_PAYPAL_ITEM_NAME'];
                $currency_code = $currency->getCode();
                $amount = $_SESSION['shop']['grand_total_price'];
                $return = \PayPal::getForm($account_email, $order_id,
                    $currency_code, $amount, $item_name);
                break;
            case 'paymill_cc':
            case 'paymill_elv':
            case 'paymill_iban':
                $return =  \Cx\Modules\Shop\Controller\PaymentProcessorController::_PaymillProcessor(self::getPaymentProcessorName());
                break;
            case 'dummy':
                $return = \Dummy::getForm();
                break;
        }
        // shows the payment picture
        $return .= \Cx\Modules\Shop\Controller\PaymentProcessorController::_getPictureCode();
        return $return;
    }


    static function getMenuoptions($selected_id=0)
    {
        global $_ARRAYLANG;

        $arrName = self::getPaymentProcessorNameArray();
        if (empty($selected_id))
            $arrName = array(
                0 => $_ARRAYLANG['TXT_SHOP_PLEASE_SELECT'],
            ) + $arrName;
        return \Html::getOptions($arrName, $selected_id);
    }


    /**
     * Handles all kinds of database errors
     *
     * Creates the processors' table, and creates default entries if necessary
     * @return  boolean                         False. Always.
     * @global  ADOConnection   $objDatabase
     */
    static function errorHandler()
    {
// PaymentProcessing
        $table_name_old = DBPREFIX.'module_shop_processors';
        $table_name_new = DBPREFIX.'module_shop_payment_processors';
        if (\Cx\Lib\UpdateUtil::table_exist($table_name_old)) {
            \Cx\Lib\UpdateUtil::table_rename($table_name_old, $table_name_new);
        }
        $table_structure = array(
            'id' => array('type' => 'INT(10)', 'unsigned' => true, 'auto_increment' => true, 'primary' => true),
            'type' => array('type' => 'ENUM("internal", "external")', 'default' => 'internal'),
            'name' => array('type' => 'VARCHAR(255)', 'default' => ''),
            'description' => array('type' => 'TEXT', 'default' => ''),
            'company_url' => array('type' => 'VARCHAR(255)', 'default' => ''),
            'status' => array('type' => 'TINYINT(1)', 'unsigned' => true, 'default' => 1),
            'picture' => array('type' => 'VARCHAR(255)', 'default' => ''),
        );
        \Cx\Lib\UpdateUtil::table($table_name_new, $table_structure);
        $arrPsp = array(
            array(1, 'external', 'saferpay',
                'Saferpay is a comprehensive Internet payment platform, specially developed for commercial applications. It provides a guarantee of secure payment processes over the Internet for merchants as well as for cardholders. Merchants benefit from the easy integration of the payment method into their e-commerce platform, and from the modularity with which they can take account of current and future requirements. Cardholders benefit from the security of buying from any shop that uses Saferpay.',
                'http://www.saferpay.com/', 1, 'logo_saferpay.gif'),
            array(2, 'external', 'paypal',
                'With more than 40 million member accounts in over 45 countries worldwide, PayPal is the world\'s largest online payment service. PayPal makes sending money as easy as sending email! Any PayPal member can instantly and securely send money to anyone in the U.S. with an email address. PayPal can also be used on a web-enabled cell phone. In the future, PayPal will be available to use on web-enabled pagers and other handheld devices.',
                'http://www.paypal.com/', 1, 'logo_paypal.gif'),
            array(3, 'external', 'yellowpay',
                'PostFinance vereinfacht das Inkasso im Online-Shop.',
                'http://www.postfinance.ch/', 1, 'logo_postfinance.gif'),
            array(4, 'internal', 'internal',
                'Internal no forms',
                '', 1, ''),
            array(9, 'internal', 'internal_lsv',
                'LSV with internal form',
                '', 1, ''),
            array(10, 'external', 'datatrans',
                'Die professionelle und komplette Payment-Lösung',
                'http://datatrans.biz/', 1, 'logo_datatrans.gif'),
            array(11, 'external', 'mobilesolutions',
                'PostFinance Mobile',
                'https://postfinance.mobilesolutions.ch/', 1, 'logo_postfinance_mobile.gif'),
        );
        $query_template = "
            REPLACE INTO `$table_name_new` (
                `id`, `type`, `name`,
                `description`,
                `company_url`, `status`, `picture`
            ) VALUES (
                ?, ?, ?, ?, ?, ?, ?
            )";
        foreach ($arrPsp as $psp) {
            \Cx\Lib\UpdateUtil::sql($query_template, $psp);
        }
        if (\Cx\Lib\UpdateUtil::table_exist($table_name_old)) {
            \Cx\Lib\UpdateUtil::drop_table($table_name_old);
        }

        // Drop obsolete PSPs -- see Payment::errorHandler()
        \Cx\Lib\UpdateUtil::sql("
            DELETE FROM `".DBPREFIX."module_shop_payment_processors`
             WHERE `id` IN (5, 6, 7, 8)");

        // Always
        return false;
    }

}
