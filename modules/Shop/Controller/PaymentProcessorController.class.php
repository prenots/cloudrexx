<?php
/**
 * Cloudrexx
 *
 * @link      http://www.cloudrexx.com
 * @copyright Cloudrexx AG 2007-2018
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
 * PaymentProcessorController to handle payments
 *
 * @copyright   Cloudrexx AG
 * @author      Sam Hawkes <info@cloudrexx.com>
 * @package     cloudrexx
 * @subpackage  module_shop
 */
namespace Cx\Modules\Shop\Controller;

/**
 * PaymentProcessorController to handle payments
 *
 * @copyright   Cloudrexx AG
 * @author      Sam Hawkes <info@cloudrexx.com>
 * @package     cloudrexx
 * @subpackage  module_shop
 */
class PaymentProcessorController extends \Cx\Core\Core\Model\Entity\Controller
{
    /**
     * Get ViewGenerator options for Payment entity
     *
     * @param $options array predefined ViewGenerator options
     * @return array includes ViewGenerator options for Payment entity
     */
    public function getViewGeneratorOptions($options)
    {
        global $_ARRAYLANG;

        $options['functions']['edit'] = false;
        $options['functions']['delete'] = false;

        $options['fields'] = array(
            'id' => array(
                'showOverview' => false,
            ),
            'type' => array(
                'showOverview' => false,
            ),
            'name' => array(
                'table' => array(
                    'parse' => function($value) {
                        global $_ARRAYLANG;
                        return $_ARRAYLANG['TXT_SHOP_PSP_' . strtoupper($value)];
                    },
                    'attributes' => array(
                        'class' => 'payment-processor-name'
                    )
                ),
            ),
            'status' => array(
                'table' => array(
                    'attributes' => array(
                        'class' => 'payment-processor-status'
                    )
                ),
            ),
            'description' => array(
                'header' => $_ARRAYLANG['TXT_STATEMENT'],
                'table' => array(
                    'parse' => function($value, $rowData) {
                        return $this->getPaymentProcessorDetails($rowData);
                    }
                ),
            ),
            'companyUrl' => array(
                'showOverview' => false,
            ),
            'picture' => array(
                'showOverview' => false,
            ),
            'payments' => array(
                'showOverview' => false,
            ),
        );

        return $options;
    }

    protected function getPaymentProcessorDetails($rowData)
    {
        \Cx\Core\Setting\Controller\Setting::init('Shop', 'config');

        $details = new \Cx\Core\Html\Model\Entity\HtmlElement('div');
        $editDetail = $this->getEditDetail($rowData['name']);

        $information = array();

        $methodName = 'get' .
            ucfirst(
                explode(
                    '_', $rowData['name']
                )[0]
            ) .'Details';
        if (method_exists($this, $methodName)) {
            $information = $this->$methodName();
        }
        $table = new \Cx\Core\Html\Model\Entity\HtmlElement('div');
        foreach ($information as $info) {
            $this->addSingleDetailFields($info, $table);
        }

        $table->setAttributes(
            array(
                'id' => $rowData['name'],
                'class' => 'payment-processor-info hide',
            )
        );

        $details->addChildren(array($editDetail, $table));
        return $details;
    }

    protected function getEditDetail($processor)
    {
        global $_ARRAYLANG;

        $editDetail = new \Cx\Core\Html\Model\Entity\HtmlElement('div');
        $label = new \Cx\Core\Html\Model\Entity\HtmlElement('label');
        $text = new \Cx\Core\Html\Model\Entity\TextElement(
            $_ARRAYLANG['TXT_SHOP_EDIT']
        );
        $label->setAttribute('for', $processor . '_swapper');

        $input = new \Cx\Core\Html\Model\Entity\DataElement(
            $processor . '_swapper',
            1
        );
        $input->setAttributes(
            array(
                'id' => $processor . '_swapper',
                'type' => 'checkbox',
                'onclick' => 'swapBlock("'. $processor .'");'
            )
        );
        $editDetail->setAttribute('class', 'payment-processor-edit');

        $label->addChild($text);
        $editDetail->addChildren(array($label, $input));

        return $editDetail;
    }

    protected function addSingleDetailFields($info, $table)
    {
        $titleWrapper = new \Cx\Core\Html\Model\Entity\HtmlElement('div');
        $inputWrapper = new \Cx\Core\Html\Model\Entity\HtmlElement('div');

        if ($info['recursive']) {
            $input = new \Cx\Core\Html\Model\Entity\HtmlElement('div');
            foreach ($info['children'] as $child) {
                $input->addChild($this->getDetailInput($child));
            }
        } else {
            $input = $this->getDetailInput($info);
        }

        $titleWrapper->setAttributes(
            array(
                'class' => 'title'
            )
        );
        $inputWrapper->setAttributes(
            array(
                'class' => 'input'
            )
        );

        $titleWrapper->addChild($this->getDetailTitle($info['title']));
        $inputWrapper->addChild($input);
        $table->addChild($titleWrapper);
        $table->addChild($inputWrapper);

        return $table;
    }

    protected function getDetailTitle($title)
    {
        $tdTitle = new \Cx\Core\Html\Model\Entity\HtmlElement('div');
        $title = new \Cx\Core\Html\Model\Entity\TextElement($title);
        $tdTitle->addChild($title);
        return $tdTitle;
    }

    protected function getDetailInput($info)
    {
        $type = $info['type'];

        if ($info['type'] == 'text') {
            return new \Cx\Core\Html\Model\Entity\TextElement('');
        }

        if ($info['type'] == 'checkbox' || $info['type'] == 'radio') {
            $type = 'input';
        }

        $input = new \Cx\Core\Html\Model\Entity\DataElement(
            $info['name'],
            $info['value'],
            $type,
            null,
            empty($info['validValues']) ? array() : $info['validValues']
        );

        if ($info['id']) {
            $input->setAttribute('id', $info['id']);
        }
        if ($info['maxlength']) {
            $input->setAttribute('maxlength', $info['maxlength']);
        }

        if ($info['type'] == 'checkbox' || $info['type'] == 'radio') {
            $input->setAttribute('type', $info['type']);
            if ($info['checked']) {
                $input->setAttribute('checked', 'checked');
            }
            if ($info['type'] == 'radio') {
                $wrapper = new \Cx\Core\Html\Model\Entity\HtmlElement('div');
                $label = new \Cx\Core\Html\Model\Entity\HtmlElement('label');
                $labelInfo = new \Cx\Core\Html\Model\Entity\TextElement($info['title']);

                $label->setAttribute('for', $info['id']);

                $label->addChild($labelInfo);
                $wrapper->addChildren(array($input, $label));
                return $wrapper;
            }
        }

        return $input;
    }

    protected function getPaypalDetails()
    {
        global $_ARRAYLANG;

        return array(
            array(
                'title' => $_ARRAYLANG['TXT_PAYPAL_EMAIL_ACCOUNT'],
                'type' => 'input',
                'name' => 'paypal_account_email',
                'value' => contrexx_raw2xhtml(
                    \Cx\Core\Setting\Controller\Setting::getValue(
                        'paypal_account_email',
                        'Shop'
                    )
                ),
                'maxlength' => '254'
            ),
            array(
                'title' => $_ARRAYLANG['TXT_SHOP_PAYPAL_DEFAULT_CURRENCY'],
                'type' => 'select',
                'name' => 'paypal_default_currency',
                'value' => \Cx\Core\Setting\Controller\Setting::getValue(
                    'paypal_default_currency',
                    'Shop'
                ),
                'validValues' => \PayPal::getAcceptedCurrencyCodeArray(),
            ),
        );
    }

    protected function getSaferpayDetails()
    {
        global $_ARRAYLANG;

        return array(
            array(
                'title' => $_ARRAYLANG['TXT_ACCOUNT_ID'],
                'type' => 'input',
                'name' => 'saferpay_id',
                'value' => \Cx\Core\Setting\Controller\Setting::getValue(
                    'saferpay_id',
                    'Shop'
                ),
                'maxlength' => '60'
            ),
            array(
                'title' => $_ARRAYLANG['TXT_USE_TEST_ACCOUNT'],
                'type' => 'checkbox',
                'name' => 'saferpay_use_test_account',
                'value' => 1,
                'checked' => \Cx\Core\Setting\Controller\Setting::getValue(
                    'saferpay_use_test_account',
                    'Shop'
                ),
            ),
            array(
                'title' => $_ARRAYLANG['TXT_FINALIZE_PAYMENT'],
                'type' => 'checkbox',
                'name' => 'saferpay_finalize_payment',
                'value' => 1,
                'checked' => \Cx\Core\Setting\Controller\Setting::getValue(
                    'saferpay_finalize_payment',
                    'Shop'
                ),
            ),
            array(
                'title' => $_ARRAYLANG['TXT_INDICATE_PAYMENT_WINDOW_AS'],
                'type' => 'select',
                'name' => 'saferpay_window_option',
                'value' => \Cx\Core\Setting\Controller\Setting::getValue(
                    'saferpay_window_option',
                    'Shop'
                ),
                'validValues' => \Saferpay::getWindowIds(),
            ),
        );
    }

    protected function getYellowpayDetails()
    {
        global $_ARRAYLANG;

        return array(
            array(
                'title' => $_ARRAYLANG['TXT_SHOP_YELLOWPAY_PSPID'],
                'type' => 'input',
                'name' => 'postfinance_shop_id',
                'value' => \Cx\Core\Setting\Controller\Setting::getValue(
                    'postfinance_shop_id',
                    'Shop'
                ),
                'maxlength' => '50'
            ),
            array(
                'title' => $_ARRAYLANG['TXT_SHOP_YELLOWPAY_HASH_SIGNATURE_IN'],
                'type' => 'input',
                'name' => 'postfinance_hash_signature_in',
                'value' => contrexx_raw2xhtml(
                    \Cx\Core\Setting\Controller\Setting::getValue(
                        'postfinance_hash_signature_in',
                        'Shop'
                    )
                ),
                'maxlength' => '50'
            ),
            array(
                'title' => $_ARRAYLANG['TXT_SHOP_YELLOWPAY_HASH_SIGNATURE_OUT'],
                'type' => 'input',
                'name' => 'postfinance_hash_signature_out',
                'value' => contrexx_raw2xhtml(
                    \Cx\Core\Setting\Controller\Setting::getValue(
                        'postfinance_hash_signature_out',
                        'Shop'
                    )
                ),
                'maxlength' => '50'
            ),
            array(
                'title' => $_ARRAYLANG['TXT_AUTORIZATION'],
                'type' => 'select',
                'name' => 'postfinance_authorization_type',
                'value' => \Cx\Core\Setting\Controller\Setting::getValue(
                    'postfinance_authorization_type',
                    'Shop'
                ),
                'validValues' => \Yellowpay::getAuthorizationOptions(),
            ),
            array(
                'title' => $_ARRAYLANG['TXT_SHOP_YELLOWPAY_USE_TESTSERVER'],
                'type' => 'checkbox',
                'name' => 'postfinance_use_testserver',
                'value' => 1,
                'checked' => \Cx\Core\Setting\Controller\Setting::getValue(
                    'postfinance_use_testserver',
                    'Shop'
                ),
            ),
        );
    }

    protected function getMobilesolutionsDetails()
    {
        global $_ARRAYLANG;

        return array(
            array(
                'title' => $_ARRAYLANG['TXT_SHOP_POSTFINANCE_MOBILE_WEBUSER'],
                'type' => 'input',
                'name' => 'postfinance_mobile_webuser',
                'value' => contrexx_raw2xhtml(
                    \Cx\Core\Setting\Controller\Setting::getValue(
                        'postfinance_mobile_webuser',
                        'Shop'
                    )
                ),
                'maxlength' => '50'
            ),
            array(
                'title' => $_ARRAYLANG['TXT_SHOP_POSTFINANCE_MOBILE_SIGN'],
                'type' => 'input',
                'name' => 'postfinance_mobile_sign',
                'value' => contrexx_raw2xhtml(
                    \Cx\Core\Setting\Controller\Setting::getValue(
                        'postfinance_mobile_sign',
                        'Shop'
                    )
                ),
                'maxlength' => '50'
            ),
            array(
                'title' => $_ARRAYLANG['TXT_SHOP_POSTFINANCE_MOBILE_IJUSTWANTTOTEST'],
                'type' => 'checkbox',
                'name' => 'postfinance_mobile_ijustwanttotest',
                'value' => 1,
                'checked' => \Cx\Core\Setting\Controller\Setting::getValue(
                    'postfinance_mobile_ijustwanttotest',
                    'Shop'
                ),
            ),
        );
    }

    protected function getDatatransDetails()
    {
        global $_ARRAYLANG;

        return array(
            array(
                'title' => $_ARRAYLANG['TXT_SHOP_DATATRANS_MERCHANT_ID'],
                'type' => 'input',
                'name' => 'datatrans_merchant_id',
                'value' => \Cx\Core\Setting\Controller\Setting::getValue(
                    'datatrans_merchant_id',
                    'Shop'
                ),
                'maxlength' => '50'
            ),
            array(
                'title' => $_ARRAYLANG['TXT_SHOP_AUTHORIZATION'],
                'type' => 'select',
                'name' => 'datatrans_request_type',
                'value' => \Cx\Core\Setting\Controller\Setting::getValue(
                    'datatrans_request_type',
                    'Shop'
                ),
                'validValues' => \Datatrans::getReqtypeOptions()
            ),
            array(
                'title' => $_ARRAYLANG['TXT_SHOP_DATATRANS_USE_TESTSERVER'],
                'recursive' => true,
                'children' => array(
                    array(
                        'title' => $_ARRAYLANG['TXT_SHOP_YES'],
                        'type' => 'radio',
                        'name' => 'datatrans_use_testserver',
                        'id' => 'datatrans_use_testserver_yes',
                        'value' => 1,
                        'checked' => \Cx\Core\Setting\Controller\Setting::getValue(
                            'datatrans_use_testserver',
                            'Shop'
                        ),
                    ),
                    array(
                        'title' => $_ARRAYLANG['TXT_SHOP_NO'],
                        'type' => 'radio',
                        'name' => 'datatrans_use_testserver',
                        'id' => 'datatrans_use_testserver_no',
                        'value' => 1,
                        'checked' =>\Cx\Core\Setting\Controller\Setting::getValue(
                            'datatrans_use_testserver',
                            'Shop'
                        ),
                    ),
                ),
            ),
        );
    }

    protected function getPaymillDetails()
    {
        global $_ARRAYLANG;

        return array(
            array(
                'title' => $_ARRAYLANG['TXT_SHOP_MODE'],
                'type' => 'select',
                'name' => 'paymill_use_test_account',
                'value' => \Cx\Core\Setting\Controller\Setting::getValue(
                    'paymill_use_test_account',
                    'Shop'
                ),
                'validValues' => array(
                    $_ARRAYLANG['TXT_SHOP_TEST'],
                    $_ARRAYLANG['TXT_SHOP_LIVE'],
                )
            ),
            array(
                'title' => $_ARRAYLANG['TXT_SHOP_PAYMILL_TEST_ACCOUNT'],
                'type' => 'text',
            ),
            array(
                'title' => $_ARRAYLANG['TXT_SHOP_PAYMILL_PRIVATE_KEY'],
                'type' => 'input',
                'name' => 'paymill_test_private_key',
                'value' => contrexx_raw2xhtml(
                    \Cx\Core\Setting\Controller\Setting::getValue(
                        'paymill_test_private_key',
                        'Shop'
                    )
                ),
                'maxlength' => '254'
            ),
            array(
                'title' => $_ARRAYLANG['TXT_SHOP_PAYMILL_PUBLIC_KEY'],
                'type' => 'input',
                'name' => 'paymill_test_public_key',
                'value' => contrexx_raw2xhtml(
                    \Cx\Core\Setting\Controller\Setting::getValue(
                        'paymill_test_public_key',
                        'Shop'
                    )
                ),
                'maxlength' => '254'
            ),
            array(
                'title' => $_ARRAYLANG['TXT_SHOP_PAYMILL_LIVE_ACCOUNT'],
                'type' => 'text',
            ),
            array(
                'title' => $_ARRAYLANG['TXT_SHOP_PAYMILL_PRIVATE_KEY'],
                'type' => 'input',
                'name' => 'paymill_live_private_key',
                'value' => contrexx_raw2xhtml(
                    \Cx\Core\Setting\Controller\Setting::getValue(
                        'paymill_live_private_key',
                        'Shop'
                    )
                ),
                'maxlength' => '254'
            ),
            array(
                'title' => $_ARRAYLANG['TXT_SHOP_PAYMILL_PUBLIC_KEY'],
                'type' => 'input',
                'name' => 'paymill_live_public_key',
                'value' => contrexx_raw2xhtml(
                    \Cx\Core\Setting\Controller\Setting::getValue(
                        'paymill_live_public_key',
                        'Shop'
                    )
                ),
                'maxlength' => '254'
            ),
        );
    }

    protected function getPayrexxDetails()
    {
        global $_ARRAYLANG;

        return array(
            array(
                'title' => $_ARRAYLANG['TXT_SHOP_PAYMENT_PAYREXX_INSTANCE_NAME'],
                'type' => 'input',
                'name' => 'payrexx_instance_name',
                'value' => \Cx\Core\Setting\Controller\Setting::getValue(
                    'payrexx_instance_name',
                    'Shop'
                ),
                'maxlength' => '50'
            ),
            array(
                'title' => $_ARRAYLANG['TXT_SHOP_PAYMENT_PAYREXX_API_SECRET'],
                'type' => 'input',
                'name' => 'payrexx_api_secret',
                'value' => \Cx\Core\Setting\Controller\Setting::getValue(
                    'payrexx_api_secret',
                    'Shop'
                ),
                'maxlength' => '50'
            ),
        );
    }

    /**
     * Returns the HTML code for the Yellowpay payment method.
     * @return  string  HTML code
     */
    static function _YellowpayProcessor()
    {
        global $_ARRAYLANG;

        $arrShopOrder = array(
// 20111227 - Note that all parameter names should now be uppercase only
            'ORDERID'   => $_SESSION['shop']['order_id'],
            'AMOUNT'    => intval(bcmul($_SESSION['shop']['grand_total_price'], 100, 0)),
            'CURRENCY'  => \Cx\Modules\Shop\Controller\CurrencyController::getActiveCurrencyCode(),
            'PARAMPLUS' => 'section=Shop'.MODULE_INDEX.'&cmd=success&handler=yellowpay',
// Custom code for adding more Customer data to the form.
// Enable as needed.
            // COM          Order description
            // CN           Customer name. Will be pre-initialized (but still editable) in the cardholder name field of the credit card details.
//            'CN' => $_SESSION['shop']['firstname'].' '.$_SESSION['shop']['lastname'],
            // EMAIL        Customer's e-mail address
//            'EMAIL' => $_SESSION['shop']['email'],
            // owneraddress Customer's street name and number
//            'owneraddress' => $_SESSION['shop']['address'],
            // ownerZIP     Customer's ZIP code
//            'ownerZIP' => $_SESSION['shop']['zip'],
            // ownertown    Customer's town/city name
//            'ownertown' => $_SESSION['shop']['city'],
            // ownercty     Customer's country
//            'ownercty' => \Cx\Core\Country\Controller\Country::getNameById($_SESSION['shop']['countryId']),
            // ownertelno   Customer's telephone number
//            'ownertelno' => $_SESSION['shop']['phone'],
        );

        $landingPage = \Env::get('em')->getRepository('Cx\Core\ContentManager\Model\Entity\Page')->findOneByModuleCmdLang('Shop'.MODULE_INDEX, 'success', FRONTEND_LANG_ID);

        $return = \Yellowpay::getForm($arrShopOrder, $_ARRAYLANG['TXT_ORDER_NOW'], false, null, $landingPage);

        if (_PAYMENT_DEBUG && \Yellowpay::$arrError) {
            $strError =
                '<font color="red"><b>'.
                $_ARRAYLANG['TXT_SHOP_PSP_FAILED_TO_INITIALISE_YELLOWPAY'].
                '<br /></b>';
            if (_PAYMENT_DEBUG) {
                $strError .= join('<br />', \Yellowpay::$arrError); //.'<br />';
            }
            return $strError.'</font>';
        }
        if (empty ($return)) {
            foreach (\Yellowpay::$arrError as $error) {
                \DBG::log("Yellowpay Error: $error");
            }
        }
        return $return;
    }
}