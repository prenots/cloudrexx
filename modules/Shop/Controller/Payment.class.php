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
 * Payment service manager
 * @package     cloudrexx
 * @copyright   CLOUDREXX CMS - CLOUDREXX AG
 * @subpackage  module_shop
 * @version     3.0.0
 */

namespace Cx\Modules\Shop\Controller;

/**
 * Payment service manager
 * @package     cloudrexx
 * @copyright   CLOUDREXX CMS - CLOUDREXX AG
 * @subpackage  module_shop
 * @version     3.0.0
 */
class Payment
{
    /**
     * Text keys
     */
    const TEXT_NAME = 'payment_name';

    /**
     * Array of available payment service data
     * @var     array
     * @access  private
     * @static
     */
    private static $arrPayments = null;


    /**
     * Set up the payment array
     * @author  Reto Kohli <reto.kohli@comvation.com>
     * @since   2.1.0
     */
    static function init()
    {
        $cx = \Cx\Core\Core\Controller\Cx::instanciate();
        $repo = $cx->getDb()->getEntityManager()->getRepository(
            'Cx\Modules\Shop\Model\Entity\Payment'
        );

        $payments = $repo->findBy(array(), array('ord' => 'ASC'));
        foreach ($payments as $payment) {
            self::$arrPayments[$payment->getId()] = array(
                'id' => $payment->getId(),
                'processor_id' => $payment->getProcessorId(),
                'name' => $payment->getName(),
                'fee' => $payment->getFee(),
                'free_from' => $payment->getFreeFrom(),
                'ord' => $payment->getOrd(),
                'active' => $payment->getActive(),
            );
        }
        return true;
    }


    /**
     * Returns the array of available Payment service data
     * @see     Payment::init()
     * @return  array
     * @author  Reto Kohli <reto.kohli@comvation.com>
     * @since   2.1.0
     */
    static function getArray()
    {
        if (empty(self::$arrPayments)) self::init();
        return self::$arrPayments;
    }


    /**
     * Returns the array of available Payment names
     *
     * The array is indexed by the Payment IDs.
     * @see     Payment::init()
     * @return  array           The array of Payment names
     * @author  Reto Kohli <reto.kohli@comvation.com>
     * @since   3.0.0
     */
    static function getNameArray()
    {
        if (is_null(self::$arrPayments)) self::init();
        $arrPaymentName = array();
        foreach (self::$arrPayments as $payment_id => $arrPayment) {
            $arrPaymentName[$payment_id] = $arrPayment['name'];
        }
        return $arrPaymentName;
    }


    /**
     * Returns the named property for the given Payment service
     * @param   integer   $payment_id       The Payment service ID
     * @param   string    $property_name    The property name
     * @return  string                      The property value
     * @author  Reto Kohli <reto.kohli@comvation.com>
     * @since   2.1.0
     */
    static function getProperty($payment_id, $property_name)
    {
        if (is_null(self::$arrPayments)) self::init();
        return
            (   isset(self::$arrPayments[$payment_id])
             && isset(self::$arrPayments[$payment_id][$property_name])
              ? self::$arrPayments[$payment_id][$property_name]
              : false
            );
    }


    /**
     * Return HTML code for the payment dropdown menu
     *
     * See {@see getPaymentMenuoptions()} for details.
     * @param   string  $selectedId     Optional preselected payment ID
     * @param   string  $onchange       Optional onchange function
     * @param   integer $countryId      Country ID
     * @return  string                  HTML code for the dropdown menu
     * @global  array   $_ARRAYLANG     Language array
     */
    static function getPaymentMenu($selectedId=0, $onchange='', $countryId=0)
    {
        return \Html::getSelectCustom('paymentId',
            self::getPaymentMenuoptions($selectedId, $countryId),
            FALSE, $onchange);
    }


    /**
     * Return HTML code for the payment dropdown menu options
     *
     * If no valid payment is selected, an additional option representing
     * "please choose" is prepended.
     * @param   string  $selectedId     Optional preselected payment ID
     * @param   integer $countryId      Country ID
     * @return  string                  HTML code for the dropdown menu options
     * @global  array   $_ARRAYLANG     Language array
     */
    static function getPaymentMenuoptions($selectedId=0, $countryId=0)
    {
        global $_ARRAYLANG;

        $paymentMethods = self::getPaymentMethods($countryId);
        if (empty($paymentMethods[$selectedId]) && count($paymentMethods) > 1) {
            $paymentMethods[0] = $_ARRAYLANG['TXT_SHOP_PLEASE_SELECT'];
        }
        return \Html::getOptions($paymentMethods, $selectedId);
    }

    /**
     * Get the payment methods based on the country id
     *
     * @param integer $countryId Country ID
     *
     * @return array array of payment methods
     */
    static function getPaymentMethods($countryId = 0)
    {
        if (is_null(self::$arrPayments)) {
            self::init();
        }
        $cx = \Cx\Core\Core\Controller\Cx::instanciate();
        $repo = $cx->getDb()->getEntityManager()->getRepository(
            'Cx\Modules\Shop\Model\Entity\Payment'
        );
        // Get Payment IDs available in the selected country, if any, or all.
        $arrPaymentIds = ($countryId
            ? $repo->getCountriesRelatedPaymentIdArray(
                $countryId,
                \Cx\Modules\Shop\Controller\CurrencyController::getCurrencyArray())
            : array_keys(self::$arrPayments));

        if (empty($arrPaymentIds)) {
            return array();
        }

        $paymentMethods = array();
        foreach ($arrPaymentIds as $id) {
            $paymentMethods[$id] = self::$arrPayments[$id]['name'];
        }
        return $paymentMethods;
    }


    /**
     * Returns the ID of the payment processor for the given payment ID
     * @static
     * @param   integer   $paymentId    The payment ID
     * @return  integer                 The payment processor ID on success,
     *                                  false otherwise
     * @global  ADONewConnection  $objDatabase    Database connection object
     */
    static function getPaymentProcessorId($paymentId)
    {
        global $objDatabase;

        $query = "
            SELECT `processor_id`
              FROM `".DBPREFIX."module_shop".MODULE_INDEX."_payment`
             WHERE `id`=$paymentId";
        $objResult = $objDatabase->Execute($query);
        if ($objResult && !$objResult->EOF)
            return $objResult->fields['processor_id'];
        return false;
    }


    /**
     * Clear the Payments stored in the class
     *
     * Call this after updating the database.  The Payments will be
     * reinitialized on demand.
     */
    static function reset()
    {
        self::$arrPayments = null;
    }


    /**
     * Sets up the Payment settings view
     * @param   \Cx\Core\Html\Sigma $objTemplate    The optional Template,
     *                                              by reference
     * @return  boolean                             True on success,
     *                                              false otherwise
     */
    static function view_settings(&$objTemplate=null)
    {
        if (!$objTemplate) {
            $objTemplate = new \Cx\Core\Html\Sigma();
            $objTemplate->loadTemplateFile('module_shop_settings_payment.html');
        } else {
            $objTemplate->addBlockfile('SHOP_SETTINGS_FILE',
                'settings_block', 'module_shop_settings_payment.html');
        }
        $i = 0;
        foreach (Payment::getArray() as $payment_id => $arrPayment) {
            $zone_id = Zones::getZoneIdByPaymentId($payment_id);
            $objTemplate->setVariable(array(
                'SHOP_PAYMENT_STYLE' => 'row'.(++$i % 2 + 1),
                'SHOP_PAYMENT_ID' => $arrPayment['id'],
                'SHOP_PAYMENT_NAME' => $arrPayment['name'],
                'SHOP_PAYMENT_HANDLER_MENUOPTIONS' =>
                    PaymentProcessing::getMenuoptions($arrPayment['processor_id']),
                'SHOP_PAYMENT_COST' => $arrPayment['fee'],
                'SHOP_PAYMENT_COST_FREE_SUM' => $arrPayment['free_from'],
                'SHOP_ZONE_SELECTION' => Zones::getMenu(
                    $zone_id, "zone_id[$payment_id]"),
                'SHOP_PAYMENT_STATUS' => (intval($arrPayment['active'])
                    ? \Html::ATTRIBUTE_CHECKED : ''),
            ));
            $objTemplate->parse('shopPayment');
        }
        $objTemplate->setVariable(array(
            'SHOP_PAYMENT_HANDLER_MENUOPTIONS_NEW' =>
                // Selected PSP ID is -1 to disable the "please select" option
                PaymentProcessing::getMenuoptions(-1),
            'SHOP_ZONE_SELECTION_NEW' => Zones::getMenu(0, 'zone_id_new'),
        ));
        $cx = \Cx\Core\Core\Controller\Cx::instanciate();
        $defaultCurrency = $cx->getDb()->getEntityManager()->getRepository(
            '\Cx\Modules\Shop\Model\Entity\Currency'
        )->getDefaultCurrency();

        // Payment Service Providers
        $objTemplate->setVariable(array(
            'SHOP_PAYMILL_STATUS' => \Cx\Core\Setting\Controller\Setting::getValue('paymill_active','Shop') ? \Html::ATTRIBUTE_CHECKED : '',
            'SHOP_PAYMILL_TEST_SELECTED' => \Cx\Core\Setting\Controller\Setting::getValue('paymill_use_test_account','Shop') == 0 ? \Html::ATTRIBUTE_SELECTED : '',
            'SHOP_PAYMILL_LIVE_SELECTED' => \Cx\Core\Setting\Controller\Setting::getValue('paymill_use_test_account','Shop') == 1 ? \Html::ATTRIBUTE_SELECTED : '',
            'SHOP_PAYMILL_TEST_PRIVATE_KEY' => contrexx_raw2xhtml(\Cx\Core\Setting\Controller\Setting::getValue('paymill_test_private_key','Shop')),
            'SHOP_PAYMILL_TEST_PUBLIC_KEY' => contrexx_raw2xhtml(\Cx\Core\Setting\Controller\Setting::getValue('paymill_test_public_key','Shop')),
            'SHOP_PAYMILL_LIVE_PRIVATE_KEY' => contrexx_raw2xhtml(\Cx\Core\Setting\Controller\Setting::getValue('paymill_live_private_key','Shop')),
            'SHOP_PAYMILL_LIVE_PUBLIC_KEY' => contrexx_raw2xhtml(\Cx\Core\Setting\Controller\Setting::getValue('paymill_live_public_key','Shop')),
            'SHOP_PAYMILL_PRIVATE_KEY' => contrexx_raw2xhtml(\Cx\Core\Setting\Controller\Setting::getValue('paymill_private_key','Shop')),
            'SHOP_PAYMILL_PUBLIC_KEY' => contrexx_raw2xhtml(\Cx\Core\Setting\Controller\Setting::getValue('paymill_public_key','Shop')),
            'SHOP_SAFERPAY_ID' => \Cx\Core\Setting\Controller\Setting::getValue('saferpay_id','Shop'),
            'SHOP_SAFERPAY_STATUS' => (\Cx\Core\Setting\Controller\Setting::getValue('saferpay_active','Shop') ? \Html::ATTRIBUTE_CHECKED : ''),
            'SHOP_SAFERPAY_TEST_ID' => \Cx\Core\Setting\Controller\Setting::getValue('saferpay_use_test_account','Shop'),
            'SHOP_SAFERPAY_TEST_STATUS' => (\Cx\Core\Setting\Controller\Setting::getValue('saferpay_use_test_account','Shop') ? \Html::ATTRIBUTE_CHECKED : ''),
            'SHOP_SAFERPAY_FINALIZE_PAYMENT' => (\Cx\Core\Setting\Controller\Setting::getValue('saferpay_finalize_payment','Shop')
                ? \Html::ATTRIBUTE_CHECKED : ''),
            'SHOP_SAFERPAY_WINDOW_MENUOPTIONS' => \Saferpay::getWindowMenuoptions(
                \Cx\Core\Setting\Controller\Setting::getValue('saferpay_window_option','Shop')),
            'SHOP_PAYREXX_INSTANCE_NAME' => \Cx\Core\Setting\Controller\Setting::getValue('payrexx_instance_name','Shop'),
            'SHOP_PAYREXX_API_SECRET' => \Cx\Core\Setting\Controller\Setting::getValue('payrexx_api_secret','Shop'),
            'SHOP_PAYREXX_STATUS' =>
                (\Cx\Core\Setting\Controller\Setting::getValue('payrexx_active','Shop')
                    ? \Html::ATTRIBUTE_CHECKED : ''),
            'SHOP_YELLOWPAY_SHOP_ID' => \Cx\Core\Setting\Controller\Setting::getValue('postfinance_shop_id','Shop'),
            'SHOP_YELLOWPAY_STATUS' =>
                (\Cx\Core\Setting\Controller\Setting::getValue('postfinance_active','Shop')
                    ? \Html::ATTRIBUTE_CHECKED : ''),
//                    'SHOP_YELLOWPAY_HASH_SEED' => \Cx\Core\Setting\Controller\Setting::getValue('postfinance_hash_seed','Shop'),
// Replaced by
            'SHOP_YELLOWPAY_HASH_SIGNATURE_IN' => contrexx_raw2xhtml(\Cx\Core\Setting\Controller\Setting::getValue('postfinance_hash_signature_in','Shop')),
            'SHOP_YELLOWPAY_HASH_SIGNATURE_OUT' => contrexx_raw2xhtml(\Cx\Core\Setting\Controller\Setting::getValue('postfinance_hash_signature_out','Shop')),
// OBSOLETE
//            'SHOP_YELLOWPAY_ACCEPTED_PAYMENT_METHODS_CHECKBOXES' =>
//                \Yellowpay::getKnownPaymentMethodCheckboxes(
//                    \Cx\Core\Setting\Controller\Setting::getValue('postfinance_accepted_payment_methods','Shop')),
            'SHOP_YELLOWPAY_AUTHORIZATION_TYPE_OPTIONS' =>
                \Yellowpay::getAuthorizationMenuoptions(
                    \Cx\Core\Setting\Controller\Setting::getValue('postfinance_authorization_type','Shop')),
            'SHOP_YELLOWPAY_USE_TESTSERVER_CHECKED' =>
                (\Cx\Core\Setting\Controller\Setting::getValue('postfinance_use_testserver','Shop')
                    ? \Html::ATTRIBUTE_CHECKED : ''),
            // Added 20100222 -- Reto Kohli
            'SHOP_POSTFINANCE_MOBILE_WEBUSER' => contrexx_raw2xhtml(\Cx\Core\Setting\Controller\Setting::getValue('postfinance_mobile_webuser','Shop')),
            'SHOP_POSTFINANCE_MOBILE_SIGN' => contrexx_raw2xhtml(\Cx\Core\Setting\Controller\Setting::getValue('postfinance_mobile_sign','Shop')),
            'SHOP_POSTFINANCE_MOBILE_IJUSTWANTTOTEST_CHECKED' =>
                (\Cx\Core\Setting\Controller\Setting::getValue('postfinance_mobile_ijustwanttotest','Shop')
                  ? \Html::ATTRIBUTE_CHECKED : ''),
            'SHOP_POSTFINANCE_MOBILE_STATUS' =>
                (\Cx\Core\Setting\Controller\Setting::getValue('postfinance_mobile_status','Shop')
                  ? \Html::ATTRIBUTE_CHECKED : ''),
            'SHOP_DATATRANS_AUTHORIZATION_TYPE_OPTIONS' => \Datatrans::getReqtypeMenuoptions(\Cx\Core\Setting\Controller\Setting::getValue('datatrans_request_type','Shop')),
            'SHOP_DATATRANS_MERCHANT_ID' => \Cx\Core\Setting\Controller\Setting::getValue('datatrans_merchant_id','Shop'),
            'SHOP_DATATRANS_STATUS' => (\Cx\Core\Setting\Controller\Setting::getValue('datatrans_active','Shop') ? \Html::ATTRIBUTE_CHECKED : ''),
            'SHOP_DATATRANS_USE_TESTSERVER_YES_CHECKED' =>
                (\Cx\Core\Setting\Controller\Setting::getValue('datatrans_use_testserver','Shop') ? \Html::ATTRIBUTE_CHECKED : ''),
            'SHOP_DATATRANS_USE_TESTSERVER_NO_CHECKED' =>
                (\Cx\Core\Setting\Controller\Setting::getValue('datatrans_use_testserver','Shop') ? '' : \Html::ATTRIBUTE_CHECKED),
            // Not supported
            //'SHOP_DATATRANS_ACCEPTED_PAYMENT_METHODS_CHECKBOXES' => 0,
            'SHOP_PAYPAL_EMAIL' => contrexx_raw2xhtml(\Cx\Core\Setting\Controller\Setting::getValue('paypal_account_email','Shop')),
            'SHOP_PAYPAL_STATUS' => (\Cx\Core\Setting\Controller\Setting::getValue('paypal_active','Shop') ? \Html::ATTRIBUTE_CHECKED : ''),
            'SHOP_PAYPAL_DEFAULT_CURRENCY_MENUOPTIONS' => \PayPal::getAcceptedCurrencyCodeMenuoptions(
                \Cx\Core\Setting\Controller\Setting::getValue('paypal_default_currency','Shop')),
            // LSV settings
            'SHOP_PAYMENT_LSV_STATUS' => (\Cx\Core\Setting\Controller\Setting::getValue('payment_lsv_active','Shop') ? \Html::ATTRIBUTE_CHECKED : ''),
            'SHOP_PAYMENT_DEFAULT_CURRENCY' => $defaultCurrency->getSymbol(),
            'SHOP_CURRENCY_CODE' => $defaultCurrency->getCode(),
        ));
        return true;
    }


    /**
     * Handles any kind of database errors
     *
     * Includes updating the payments table (I guess from version 1.2.0(?),
     * note that this is unconfirmed) to the current structure
     * @return  boolean               False.  Always.
     * @throws  Cx\Lib\Update_DatabaseException
     */
    static function errorHandler()
    {
// Payment
        // Fix the Text and Zones tables first
        \Text::errorHandler();
        Zones::errorHandler();
        \Yellowpay::errorHandler();

        $table_name = DBPREFIX.'module_shop_payment';
        $table_structure = array(
            'id' => array('type' => 'INT(10)', 'unsigned' => true, 'auto_increment' => true, 'primary' => true),
            'processor_id' => array('type' => 'INT(10)', 'unsigned' => true, 'default' => '0'),
            'fee' => array('type' => 'DECIMAL(9,2)', 'unsigned' => true, 'default' => '0', 'renamefrom' => 'costs'),
            'free_from' => array('type' => 'DECIMAL(9,2)', 'unsigned' => true, 'default' => '0', 'renamefrom' => 'costs_free_sum'),
            'ord' => array('type' => 'INT(5)', 'unsigned' => true, 'default' => '0', 'renamefrom' => 'sort_order'),
            'active' => array('type' => 'TINYINT(1)', 'unsigned' => true, 'default' => '1', 'renamefrom' => 'status'),
        );
        $table_index = array();
        $default_lang_id = \FWLanguage::getDefaultLangId();
        if (\Cx\Lib\UpdateUtil::table_exist($table_name)) {
            if (\Cx\Lib\UpdateUtil::column_exist($table_name, 'name')) {
                // Migrate all Payment names to the Text table first
                \Text::deleteByKey('Shop', self::TEXT_NAME);
                $query = "
                    SELECT `id`, `name`
                      FROM `$table_name`";
                $objResult = \Cx\Lib\UpdateUtil::sql($query);
                if (!$objResult) {
                    throw new \Cx\Lib\Update_DatabaseException(
                        "Failed to query Payment names", $query);
                }
                while (!$objResult->EOF) {
                    $id = $objResult->fields['id'];
                    $name = $objResult->fields['name'];
                    if (!\Text::replace($id, $default_lang_id,
                        'Shop', self::TEXT_NAME, $name)) {
                        throw new \Cx\Lib\Update_DatabaseException(
                            "Failed to migrate Payment name '$name'");
                    }
                    $objResult->MoveNext();
                }
            }
        }
        \Cx\Lib\UpdateUtil::table($table_name, $table_structure, $table_index);

        // Update Payments that use obsolete PSPs:
        //  - 05, 'Internal_CreditCard'
        //  - 06, 'Internal_Debit',
        // Uses 04, Internal
        \Cx\Lib\UpdateUtil::sql(
            "UPDATE $table_name
                SET `processor_id`=4 WHERE `processor_id` IN (5, 6)");
        // - 07, 'Saferpay_Mastercard_Multipay_CAR',
        // - 08, 'Saferpay_Visa_Multipay_CAR',
        // Uses 01, Saferpay
        \Cx\Lib\UpdateUtil::sql(
            "UPDATE $table_name
                SET `processor_id`=1 WHERE `processor_id` IN (7, 8)");

        $table_name = DBPREFIX.'module_shop_rel_payment';
        $table_structure = array(
            'payment_id' => array('type' => 'INT(10)', 'unsigned' => true, 'default' => '0', 'primary' => true),
            'zone_id' => array('type' => 'INT(10)', 'unsigned' => true, 'default' => '0', 'primary' => true, 'renamefrom' => 'zones_id'),
        );
        $table_index = array();
        \Cx\Lib\UpdateUtil::table($table_name, $table_structure, $table_index);

        // Always
        return false;
    }

}
