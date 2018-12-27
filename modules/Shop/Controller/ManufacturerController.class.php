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
 * ManufacturerController to handle manufacturers
 *
 * @copyright   Cloudrexx AG
 * @author      Sam Hawkes <info@cloudrexx.com>
 * @package     cloudrexx
 * @subpackage  module_shop
 */
namespace Cx\Modules\Shop\Controller;

/**
 * ManufacturerController to handle manufacturers
 *
 * @copyright   Cloudrexx AG
 * @author      Sam Hawkes <info@cloudrexx.com>
 * @package     cloudrexx
 * @subpackage  module_shop
 */
class ManufacturerController extends \Cx\Core\Core\Model\Entity\Controller
{
    /**
     * Get ViewGenerator options for Manufacturer entity
     *
     * @param $options array predefined ViewGenerator options
     * @return array includes ViewGenerator options for Manufacturer entity
     */
    public function getViewGeneratorOptions($options)
    {
        global $_ARRAYLANG;

        $options['order']['overview'] = array(
            'id',
            'name',
            'uri'
        );
        $options['order']['form'] = array(
            'name',
            'uri'
        );

        $options['multiActions']['delete'] = array(
            'title' => $_ARRAYLANG['TXT_DELETE'],
            'jsEvent' => 'delete:manufacturer'
        );

        // Delete Event
        $scope = 'order';
        \ContrexxJavascript::getInstance()->setVariable(
            'CSRF_PARAM',
            \Cx\Core\Csrf\Controller\Csrf::code(),
            $scope
        );
        \ContrexxJavascript::getInstance()->setVariable(
            'TXT_CONFIRM_DELETE_MANUFACTURER',
            $_ARRAYLANG['TXT_CONFIRM_DELETE_MANUFACTURER'],
            $scope
        );
        \ContrexxJavascript::getInstance()->setVariable(
            'TXT_ACTION_IS_IRREVERSIBLE',
            $_ARRAYLANG['TXT_ACTION_IS_IRREVERSIBLE'],
            $scope
        );

        $options['fields'] = array(
            'id' => array(
                'table' => array(
                    'attributes' => array(
                        'class' => 'manufacturer-id',
                    ),
                ),
            ),
            'name' => array(
                'table' => array(
                    'attributes' => array(
                        'class' => 'manufacturer-name',
                    ),
                ),
                'sorting' => false,
            ),
            'uri' => array(
                'table' => array(
                    'attributes' => array(
                        'class' => 'manufacturer-uri',
                    ),
                ),
                'sorting' => false,
            ),
            'products' => array(
                'showOverview' => false,
                'showDetail' => false,
            ),
        );
        return $options;
    }
}