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
                'showOverview' => false,
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
}