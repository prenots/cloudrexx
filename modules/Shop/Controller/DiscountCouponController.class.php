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
            ),
            'startTime' => array(
                'type' => 'date',
            ),
            'payment' => array(
            ),
            'product' => array(
            ),
            'endTime' => array(
                'type' => 'date',
            ),
            'minimumAmount' => array(
                'attributes' => array(
                    'style' => 'text-align: right'
                ),
                'header' => sprintf(
                    $_ARRAYLANG['minimumAmount'],
                    $defaultCurrency->getCode()
                ),
            ),
            'discountRate' => array(
                'attributes' => array(
                    'style' => 'text-align: right'
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
            ),
            'uses' => array(
            ),
            'global' => array(
            ),
            'type' => array(
                'custom' => true,
                'header' => $_ARRAYLANG['TXT_SHOP_DISCOUNT_COUPON_TYPE'],
                'showOverview' => false,
            ),
            'link' => array(
                'custom' => true,
                'showDetail' => false,
            ),
        );

        return $options;
    }

}