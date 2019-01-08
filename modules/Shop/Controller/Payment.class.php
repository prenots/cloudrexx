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
        foreach (\Cx\Modules\Shop\Controller\PaymentController::getArray() as $payment_id => $arrPayment) {
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

}
