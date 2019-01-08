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
     * Array of available payment service data
     * @var     array
     * @access  private
     * @static
     */
    private static $arrPayments = null;

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

}