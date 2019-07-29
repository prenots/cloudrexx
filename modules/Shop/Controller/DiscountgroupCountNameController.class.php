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
 * DiscountgroupCountNameController to handle discountgroup count names
 *
 * @copyright   Cloudrexx AG
 * @author      Sam Hawkes <info@cloudrexx.com>
 * @package     cloudrexx
 * @subpackage  module_shop
 */
namespace Cx\Modules\Shop\Controller;

/**
 * DiscountgroupCountNameController to handle discountgroup count names
 *
 * @copyright   Cloudrexx AG
 * @author      Sam Hawkes <info@cloudrexx.com>
 * @package     cloudrexx
 * @subpackage  module_shop
 */
class DiscountgroupCountNameController extends \Cx\Core\Core\Model\Entity\Controller
{
    /**
     * Get ViewGenerator options for Manufacturer entity
     *
     * @param $options array predefined ViewGenerator options
     * @return array includes ViewGenerator options for Manufacturer entity
     */
    public function getViewGeneratorOptions($options)
    {
        $options['functions']['sorting'] = false;

        $options['order']['overview'] = array(
            'name',
            'unit',
        );

        $options['fields'] = array(
            'id' => array(
                'showOverview' => false,
            ),
            'name' => array(
                'table' => array(
                    'attributes' => array(
                        'class' => 'discountgroup-name'
                    ),
                    'parse' => function ($value, $rowData, $vgId) {
                        return $this->wrapLinkAroundElement(
                            $value,
                            $rowData,
                            $vgId
                        );
                    }
                ),
            ),
            'unit' => array(
                'table' => array(
                    'parse' => function ($value, $rowData, $vgId) {
                        return $this->wrapLinkAroundElement(
                            $value,
                            $rowData,
                            $vgId
                        );
                    }
                ),
            ),
            'cumulative' => array(
                'showOverview' => false,
            ),
            'discountgroupCountRates' => array(
                'showOverview' => false,
                'mode' => 'associate',
            ),
            'products' => array(
                'showOverview' => false,
                'showDetail' => false,
            ),
        );

        return $options;
    }

    protected function wrapLinkAroundElement($value, $rowData, $vgId)
    {
        $link = new \Cx\Core\Html\Model\Entity\HtmlElement('a');
        $data = new \Cx\Core\Html\Model\Entity\TextElement($value);

        $editLink = \Cx\Core\Html\Controller\ViewGenerator::getVgEditUrl(
            $vgId, $rowData['id']
        );
        $link->addChild($data);
        $link->setAttribute(
            'href', $editLink
        );

        return $link;
    }

    /**
     * Returns the HTML dropdown menu options with all of the
     * count type discount names plus a neutral option ("none")
     *
     * Backend use only.
     * @param   integer   $selectedId   The optional preselected ID
     * @return  string                  The HTML dropdown menu options
     *                                  on success, false otherwise
     * @static
     */
    static function getMenuOptionsGroupCount($selectedId=0)
    {
        global $_ARRAYLANG;

        $cx = \Cx\Core\Core\Controller\Cx::instanciate();
        $em =  $cx->getDb()->getEntityManager();

        $discountCountNames = $em->getRepository(
            'Cx\Modules\Shop\Model\Entity\DiscountgroupCountName'
        )->findAll();

        $arrName = array();
        foreach ($discountCountNames as $discountCountName) {
            $arrName[
                $discountCountName->getId()
            ] = $discountCountName->getName().' ('
                .$discountCountName->getUnit().')';
        }
        return \Html::getOptions(
            array(
                0 => $_ARRAYLANG['TXT_SHOP_DISCOUNT_GROUP_NONE']
            ) + $arrName,
            $selectedId
        );
    }
}