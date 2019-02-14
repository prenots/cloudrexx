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
 * DiscountCouponController to handle discount coupons
 *
 * @copyright   Cloudrexx AG
 * @author      Sam Hawkes <info@cloudrexx.com>
 * @package     cloudrexx
 * @subpackage  module_shop
 */
namespace Cx\Modules\Shop\Controller;

/**
 * DiscountCouponController to handle discount coupons
 *
 * @copyright   Cloudrexx AG
 * @author      Sam Hawkes <info@cloudrexx.com>
 * @package     cloudrexx
 * @subpackage  module_shop
 */
class DiscountCouponController extends \Cx\Core\Core\Model\Entity\Controller
{
    const USES_UNLIMITED = 1e10;

    /**
     * Get ViewGenerator options for DiscountCoupon entity
     *
     * @param $options array predefined ViewGenerator options
     * @return array includes ViewGenerator options for Manufacturer entity
     */
    public function getViewGeneratorOptions($options)
    {
        global $_ARRAYLANG;

        $this->setJavaScriptVariables();
        \JS::registerJS(
            $this->cx->getModuleFolderName() . '/Shop/View/Script/DiscountCoupon.js'
        );

        $options['order']['overview'] = array(
            'code',
            'startTime',
            'endTime',
            'minimumAmount',
            'discountRate',
            'discountAmount',
            'uses',
            'global',
            'customer',
            'product',
            'payment',
            'link'
        );

        $options['order']['form'] = array(
            'customer',
            'code',
            'startTime',
            'endTime',
            'minimumAmount',
            'type',
            'discountRate',
            'discountAmount',
            'uses',
            'global',
            'product',
            'payment',
        );

        $defaultCurrency = $this->cx->getDb()->getEntityManager()->getRepository(
            '\Cx\Modules\Shop\Model\Entity\Currency'
        )->getDefaultCurrency();

        $options['fields'] = array(
            'id' => array(
                'showOverview' => false,
            ),
            'customerId' => array(
                'showOverview' => false,
                'showDetail' => false,
            ),
            'paymentId' => array(
                'showOverview' => false,
                'showDetail' => false,
            ),
            'productId' => array(
                'showOverview' => false,
                'showDetail' => false,
            ),
            'code' => array(
                'header' => $_ARRAYLANG['TXT_SHOP_DISCOUNT_COUPON_CODE']
            ),
            'customer' => array(
                'type' => 'hidden',
                'table' => array(
                    'parse' => function($value) {
                        global $_ARRAYLANG;
                        if (empty($value)) {
                            return $_ARRAYLANG['TXT_SHOP_PRODUCT_ANY'];
                        }
                        $user = \FWUser::getFWUserObject()->objUser->getUser($value->getId());
                        return $user->getUsername() . ' (' . $value . ')';
                    }
                ),
            ),
            'startTime' => array(
                'type' => 'date',
                'valueCallback' => function($fieldvalue) {
                    if (!empty($fieldvalue)) {
                        $fieldvalue = date(ASCMS_DATE_FORMAT_DATE, $fieldvalue);
                    }
                    return $fieldvalue;
                },
                'table' => array(
                    'parse' => function($value) {
                        if (empty($value)) {
                            return '-';
                        }
                        return $value;
                    }
                ),
                'storecallback' => function($value) {
                    return strtotime($value);
                },
            ),
            'payment' => array(
                'table' => array(
                    'parse' => function($value) {
                        global $_ARRAYLANG;
                        if (empty($value)) {
                            return $_ARRAYLANG['TXT_SHOP_PAYMENT_ANY'];
                        }
                        return $value  . ' (' . $value->getId() . ')';
                    }
                ),
                'formfield' => function($fieldname, $fieldtype, $fieldlength, $fieldvalue) {
                    return $this->getDropdownWithOtherDefault(
                        $fieldname,
                        $fieldvalue,
                        'Payment'
                    );
                }
            ),
            'product' => array(
                'table' => array(
                    'parse' => function($value) {
                        global $_ARRAYLANG;
                        if (empty($value)) {
                            return $_ARRAYLANG['TXT_SHOP_PRODUCT_ANY'];
                        }
                        return $value . ' (' . $value->getId() . ')';
                    }
                ),
                'formfield' => function($fieldname, $fieldtype, $fieldlength, $fieldvalue) {
                    return $this->getDropdownWithOtherDefault(
                        $fieldname,
                        $fieldvalue,
                        'Product'
                    );
                }
            ),
            'endTime' => array(
                'type' => 'date',
                'table' => array(
                    'parse' => function($value) {
                        global $_ARRAYLANG;
                        if (empty($value)) {
                            return $_ARRAYLANG['TXT_SHOP_DISCOUNT_COUPON_END_TIME_UNLIMITED'];
                        }
                        return $value;
                    }
                ),
                'formfield' => function($fieldname, $fieldtype, $fieldlength, $fieldvalue) {
                    global $_ARRAYLANG;
                    return $this->addUnlimitedCheckbox(
                        $fieldname,
                        $fieldtype,
                        $fieldvalue,
                        $_ARRAYLANG['TXT_SHOP_DISCOUNT_COUPON_END_TIME_UNLIMITED']
                    );
                },
                'valueCallback' => function($fieldvalue) {
                    if (!empty($fieldvalue)) {
                        $fieldvalue = date(ASCMS_DATE_FORMAT_DATE, $fieldvalue);
                    }
                    return $fieldvalue;
                },
                'storecallback' => function($value) {
                    return strtotime($value);
                },
            ),
            'minimumAmount' => array(
                'attributes' => array(
                    'style' => 'text-align: right'
                ),
                'header' => sprintf(
                    $_ARRAYLANG['minimumAmount'],
                    $defaultCurrency->getCode()
                ),
                'table' => array(
                    'parse' => function($value) {
                        if ($value == '0.00') {
                            return '-.--';
                        }
                        return $value;
                    }
                ),
            ),
            'discountRate' => array(
                'attributes' => array(
                    'style' => 'text-align: right'
                ),
                'storecallback' => function($value) {
                    if (
                        $this->cx->getRequest()->hasParam('coupon_type', false) &&
                        $this->cx->getRequest()->getParam('coupon_type', false) !=
                        'discountRate'
                    ) {
                        return '';
                    }
                    return $value;
                },
                'table' => array(
                    'parse' => function($value) {
                        if (empty($value)) {
                            return '-';
                        }
                        return $value;
                    }
                ),
            ),
            'discountAmount' => array(
                'attributes' => array(
                    'style' => 'text-align: right;'
                ),
                'header' => sprintf(
                    $_ARRAYLANG['discountAmount'],
                    $defaultCurrency->getCode()
                ),
                'storecallback' => function($value) {
                    if (
                        $this->cx->getRequest()->hasParam('coupon_type', false) &&
                        $this->cx->getRequest()->getParam('coupon_type', false) !=
                        'discountAmount'
                    ) {
                        return '';
                    }
                    return $value;
                },
                'table' => array(
                    'parse' => function($value) {
                        if ($value == '0.00') {
                            return '-.--';
                        }
                        return $value;
                    }
                ),
                'tooltip' => '<strong>' .
                    $_ARRAYLANG['TXT_SHOP_DISCOUNTS_SALE_NOTE_TITLE'] .
                    '</strong><br/>' . $_ARRAYLANG['TXT_SHOP_DISCOUNTS_SALE_NOTE_TEXT'] .
                    '<br/><br/><strong>' .
                    $_ARRAYLANG['TXT_SHOP_DISCOUNTS_MULTIPLE_VAT_NOTE_TITLE'] .
                    '</strong> <br/>' .
                    $_ARRAYLANG['TXT_SHOP_DISCOUNTS_MULTIPLE_VAT_NOTE_TEXT']
            ),
            'uses' => array(
                'formfield' => function($fieldname, $fieldtype, $fieldlength, $fieldvalue) {
                    global $_ARRAYLANG;
                    return $this->addUnlimitedCheckbox(
                        $fieldname,
                        $fieldtype,
                        $fieldvalue,
                        $_ARRAYLANG['TXT_SHOP_DISCOUNT_COUPON_USES_UNLIMITED']
                    );
                },
                'storecallback' => function($value) {
                    if (empty($value)) {
                        return self::USES_UNLIMITED;
                    }
                    return $value;
                },
                'table' => array(
                    'parse' => function($value, $rowData) {
                        return $this->getUseStatus($value, $rowData['id']);
                    }
                ),
            ),
            'global' => array(
                'formfield' => function($name, $type, $length, $value) {
                    return $this->getGlobalAndUserCheckboxes($name, $value);
                },
                'storecallback' => function($value) {
                    return $value == 'couponGlobal';
                },
                'table' => array(
                    'parse' => function($value) {
                        global $_ARRAYLANG;
                        if (empty($value)) {
                            return $_ARRAYLANG['TXT_SHOP_DISCOUNT_COUPON_PER_CUSTOMER'];
                        }
                        return $_ARRAYLANG['TXT_SHOP_DISCOUNT_COUPON_GLOBALLY'];
                    }
                ),
            ),
            'type' => array(
                'custom' => true,
                'header' => $_ARRAYLANG['TXT_SHOP_DISCOUNT_COUPON_TYPE'],
                'showOverview' => false,
                'formfield' => function() {
                    global $_ARRAYLANG;
                    $types = array(
                        'discountRate' => $_ARRAYLANG['TXT_SHOP_DISCOUNT_COUPON_TYPE_RATE'],
                        'discountAmount' => $_ARRAYLANG['TXT_SHOP_DISCOUNT_COUPON_TYPE_AMOUNT']
                    );
                    $default = !empty($rowData['discountAmount']) ? 'discountAmount' : 'discountRate';
                    return $this->getCustomRadioButtons(
                        'coupon_type',
                        $types,
                        $default
                    );
                }
            ),
            'link' => array(
                'custom' => true,
                'showDetail' => false,
                'table' => array(
                    'parse' => function($value, $rowData) {
                        return $this->getCouponLink($rowData);
                    },
                    'attributes' => array(
                        'class' => 'shop-coupon-link',
                    ),
                ),
            ),
        );

        return $options;
    }

    protected function getCustomRadioButtons($name, $options, $default)
    {
        $div = new \Cx\Core\Html\Model\Entity\HtmlElement('div');
        foreach ($options as $option=>$text) {
            $wrapper = new \Cx\Core\Html\Model\Entity\HtmlElement('div');
            $input = new \Cx\Core\Html\Model\Entity\DataElement($name, $option);
            $label = new \Cx\Core\Html\Model\Entity\HtmlElement('label');
            $labelText = new \Cx\Core\Html\Model\Entity\TextElement($text);
            $input->setAttributes(
                array(
                    'type' => 'radio',
                    'id' => $option,
                )
            );
            $label->setAttribute('for', $option);

            if ($option == $default) {
                $input->setAttribute('checked', true);
            }

            $label->addChild($labelText);
            $wrapper->addChild($input);
            $wrapper->addChild($label);
            $div->addChild($wrapper);
        }

        return $div;
    }

    protected function setJavaScriptVariables()
    {
        global $_ARRAYLANG;

        $cxJs = \ContrexxJavascript::getInstance();
        $scope = 'Shop';
        $cxJs->setVariable(
            'SHOP_GET_NEW_DISCOUNT_COUPON',
            \Cx\Modules\Shop\Controller\DiscountCouponController::getNewCode(),
            $scope
        );
        $cxJs->setVariable(
            'TXT_SHOP_GENERATE_NEW_CODE',
            $_ARRAYLANG['TXT_SHOP_DISCOUNT_COUPON_CODE_NEW'],
            $scope
        );
    }

    protected function addUnlimitedCheckbox($name, $type, $value, $labelText)
    {
        $wrapper = new \Cx\Core\Html\Model\Entity\HtmlElement('div');
        $input = new \Cx\Core\Html\Model\Entity\DataElement($name);
        $input->setAttribute('type', 'text');

        if ($type == 'date') {
            $input->addClass('datepicker');
            $isChecked = !empty($value);
        } else {
            $isChecked = $value > self::USES_UNLIMITED;
        }

        $text = new \Cx\Core\Html\Model\Entity\TextElement($labelText);
        $label = new \Cx\Core\Html\Model\Entity\HtmlElement('label');
        $label->setAttribute('for', $name . '-unlimited');
        $checkbox = new \Cx\Core\Html\Model\Entity\DataElement($name . '-unlimited');
        $checkbox->setAttributes(
            array(
                'type' => 'checkbox',
                'id' => $name . '-unlimited',
                'checked' => $isChecked,
            )
        );
        $checkbox->addClass('shop-unlimited');

        $label->addChild($text);
        $wrapper->addChildren(array($input, $checkbox, $label));

        return $wrapper;
    }

    protected function getGlobalAndUserCheckboxes($name, $value)
    {
        global $_ARRAYLANG;

        $wrapper = new \Cx\Core\Html\Model\Entity\HtmlElement('div');

        $types = array(
            'couponGlobal' => $_ARRAYLANG['TXT_SHOP_DISCOUNT_COUPON_GLOBALLY'],
            'couponCustomer' => $_ARRAYLANG['TXT_SHOP_DISCOUNT_COUPON_PER_CUSTOMER']
        );

        $default = $value || $value === '' ? 'couponGlobal' : 'couponCustomer';

        $checkboxes = $this->getCustomRadioButtons(
            $name,
            $types,
            $default
        );

        $wrapper->addChild($checkboxes);

        \FWUser::getUserLiveSearch(
            array(
                'minLength' => 3,
                'canCancel' => true,
                'canClear' => true
            )
        );

        $couponId = 0;
        // Until we know how to get the editId without the $_GET param
        if ($this->cx->getRequest()->hasParam('editid')) {
            $couponId = explode(
                '}',
                explode(
                    ',',
                    $this->cx->getRequest()->getParam('editid')
                )[1]
            )[0];
        }

        $customerId = 0;
        $customerName = '';

        $coupon = $this->cx->getDb()->getEntityManager()->getRepository(
            'Cx\Modules\Shop\Model\Entity\DiscountCoupon'
        )->find($couponId);

        if (!empty($coupon) && !empty($coupon->getCustomer())) {
            $customer = $coupon->getCustomer();
            $customerId = $customer->getId();
            $customerName = $customer->getUsername();
        }

        $customerWidget = new \Cx\Core\Html\Model\Entity\HtmlElement('div');
        $customerId = new \Cx\Core\Html\Model\Entity\DataElement('customer', $customerId);
        $customerName = new \Cx\Core\Html\Model\Entity\DataElement('customer_name', $customerName);

        $widgetDisplay = $value || $value === '' ? 'none' : 'block';
        $customerWidget->setAttributes(
            array(
                'style' => 'display:' . $widgetDisplay,
                'id' => 'user-live-search'
            )
        );
        $customerId->setAttribute('id', 'customer');
        $customerId->addClass('live-search-user-id');
        $customerName->setAttribute('id', 'customer_name');
        $customerName->addClass('live-search-user-name');

        $customerWidget->addChildren(array($customerId, $customerName));
        $wrapper->addChild($customerWidget);

        return $wrapper;
    }

    protected function getUseStatus($value, $couponId)
    {
        global $_ARRAYLANG;

        $coupon = $this->cx->getDb()->getEntityManager()->getRepository(
            'Cx\Modules\Shop\Model\Entity\DiscountCoupon'
        )->find($couponId);

        if (empty($coupon)) {
            return '';
        }

        $uses = $coupon->getUsedCount();
        $max = $value;
        if ($value > 1e9) {
            $max = $_ARRAYLANG['TXT_SHOP_DISCOUNT_COUPON_USES_UNLIMITED'];
        }

        return $uses .' / '. $max;
    }

    protected function getDropdownWithOtherDefault($name, $value, $entityName)
    {
        global $_ARRAYLANG;

        $entities = $this->cx->getDb()->getEntityManager()->getRepository(
            'Cx\\Modules\\Shop\\Model\\Entity\\' . $entityName
        )->findAll();

        $validValues = array($_ARRAYLANG['TXT_SHOP_PAYMENT_ANY']);
        foreach ($entities as $entity) {
            $validValues[$entity->getId()] = $entity;
        }

        $dropdown = new \Cx\Core\Html\Model\Entity\DataElement(
            $name,
            $value->getId(),
            'select',
            null,
            $validValues
        );

        return $dropdown;
    }

    protected function getCouponLink($rowData)
    {
        $url = \Cx\Core\Routing\Url::fromModuleAndCmd('Shop');

        $wrapper = new \Cx\Core\Html\Model\Entity\HtmlElement('div');
        $icon = new \Cx\Core\Html\Model\Entity\HtmlElement('div');
        $input = new \Cx\Core\Html\Model\Entity\DataElement(
            'coupon-url-link',
            $url->toString() . '?coupon_code=' . $rowData['code']
        );

        $wrapper->addClass('coupon-url');
        $icon->addClass('coupon-url-icon icon_url');
        $input->addClass('coupon-url-link');
        $icon->allowDirectClose(true);
        $icon->addChild(new \Cx\Core\Html\Model\Entity\TextElement(''));

        $wrapper->addChildren(array($icon, $input));

        return $wrapper;
    }

    /**
     * Returns a unique Coupon code with eight characters
     * @return    string            The Coupon code
     * @see       User::make_password()
     */
    static function getNewCode()
    {
        $code = null;
        while (true) {
            $code = \User::make_password(8, false);
            if (!self::codeExists($code)) break;
        }
        return $code;
    }

}