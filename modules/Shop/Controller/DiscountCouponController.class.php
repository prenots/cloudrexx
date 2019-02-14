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
                            return $_ARRAYLANG['TXT_SHOP_CUSTOMER_ANY'];
                        }
                        $user = \FWUser::getFWUserObject()->objUser->getUser(
                            $value->getId()
                        );
                        return $user->getUsername() . ' (' . $value . ')';
                    }
                ),
            ),
            'startTime' => array(
                'type' => 'date',
                'table' => array(
                    'parse' => function($value) {
                        if (empty($value)) {
                            return '-';
                        }
                        return $value;
                    }
                ),
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
                'table' => array(
                    'parse' => function($value) {
                        if ($value == '0.00') {
                            return '-.--';
                        }
                        return $value;
                    }
                ),
            ),
            'uses' => array(
                'table' => array(
                    'parse' => function($value, $rowData) {
                        return $this->getUseStatus($value, $rowData['id']);
                    }
                ),
            ),
            'global' => array(
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
        if ($value < self::USES_UNLIMITED) {
            $max = $_ARRAYLANG['TXT_SHOP_DISCOUNT_COUPON_USES_UNLIMITED'];
        }

        return $uses .' / '. $max;
    }

}