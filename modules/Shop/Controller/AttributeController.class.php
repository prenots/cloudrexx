<?php
/**
 * Cloudrexx
 *
 * @link      http://www.cloudrexx.com
 * @copyright Cloudrexx AG 2007-2019
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
 * CategoryController to handle product attributes
 *
 * @copyright   Cloudrexx AG
 * @author      Sam Hawkes <info@cloudrexx.com>
 * @package     cloudrexx
 * @subpackage  module_shop
 */
namespace Cx\Modules\Shop\Controller;

/**
 * CategoryController to handle product attributes
 *
 * @copyright   Cloudrexx AG
 * @author      Sam Hawkes <info@cloudrexx.com>
 * @package     cloudrexx
 * @subpackage  module_shop
 */
class AttributeController extends \Cx\Core\Core\Model\Entity\Controller
{
    /**
     * Get ViewGenerator options for Category entity
     *
     * @param $options array predefined ViewGenerator options
     * @return array includes ViewGenerator options for Category entity
     */
    public function getViewGeneratorOptions($options)
    {
        $options['order']['overview'] = array(
            'id',
            'name',
            'options',
            'type',
        );
        $options['order']['form'] = array(
            'id',
            'name',
            'options',
            'type',
        );

        $options['functions']['sorting'] = false;
        $options['functions']['editable'] = true;

        $options['fields'] = array(
            'name' => array(
                'editable' => true,
            ),
            'options' => array(
                'table' => array(
                    'parse' => array(
                        'adapter' => 'Attribute',
                        'method' => 'getOptions'
                    ),
                ),
                'formfield' => array(
                    'adapter' => 'Attribute',
                    'method' => 'getOptionsDetail'
                ),
                'storecallback' => array(
                    'adapter' => 'Attribute',
                    'method' => 'storeOptions'
                ),
                'mode' => 'associate'
            ),
            'type' => array(
                'editable' => true,
                'table' => array(
                    'parse' => array(
                        'adapter' => 'Attribute',
                        'method' => 'getTypes'
                    ),
                ),
                'formfield' => array(
                    'adapter' => 'Attribute',
                    'method' => 'getTypesDetail'
                ),
                'storecallback' => array(
                    'adapter' => 'Attribute',
                    'method' => 'storeType'
                )
            ),
        );
        return $options;
    }
}