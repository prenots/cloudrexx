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
 * PaymentController to handle payments
 *
 * @copyright   Cloudrexx AG
 * @author      Sam Hawkes <info@cloudrexx.com>
 * @package     cloudrexx
 * @subpackage  module_shop
 */
namespace Cx\Modules\Shop\Controller;

/**
 * PaymentController to handle payments
 *
 * @copyright   Cloudrexx AG
 * @author      Sam Hawkes <info@cloudrexx.com>
 * @package     cloudrexx
 * @subpackage  module_shop
 */
class PaymentController extends \Cx\Core\Core\Model\Entity\Controller
{
    /**
     * Get ViewGenerator options for Payment entity
     *
     * @param $options array predefined ViewGenerator options
     * @return array includes ViewGenerator options for Payment entity
     */
    public function getViewGeneratorOptions($options)
    {
        $options['functions']['editable'] = true;
        $options['functions']['edit'] = false;
        $options['functions']['sorting'] = false;
        $options['functions']['sortBy'] = array(
            'field' => array('ord' => SORT_ASC)
        );

        $options['order'] = array(
            'overview' => array(
                'name',
                'fee',
                'freeFrom',
                'paymentProcessor',
                'zones',
                'active'
            ),
        );

        $options['fields'] = array(
            'id' => array(
                'showOverview' => false,
            ),
            'processorId' => array(
                'showOverview' => false,
            ),
            'discountCoupons' => array(
                'showOverview' => false,
            ),
            'orders' => array(
                'showOverview' => false,
            ),
            'ord' => array(
                'showOverview' => false,
            ),
            'name' => array(
                'editable' => true,
            ),
            'fee' => array(
                'editable' => true,
            ),
            'freeFrom' => array(
                'editable' => true,
            ),
            'paymentProcessor' => array(
                'editable' => true,
            ),
            'zones' => array(
                'editable' => true,
            ),
        );

        return $options;
    }
}