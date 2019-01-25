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
 * ProductController to handle products
 *
 * @copyright   Cloudrexx AG
 * @author      Sam Hawkes <info@cloudrexx.com>
 * @package     cloudrexx
 * @subpackage  module_shop
 */
namespace Cx\Modules\Shop\Controller;

/**
 * ProductController to handle products
 *
 * @copyright   Cloudrexx AG
 * @author      Sam Hawkes <info@cloudrexx.com>
 * @package     cloudrexx
 * @subpackage  module_shop
 */
class ProductController extends \Cx\Core\Core\Model\Entity\Controller
{
    /**
     * Get ViewGenerator options for entity
     *
     * @param $options array predefined ViewGenerator options
     * @return array includes ViewGenerator options for entity
     */
    public function getViewGeneratorOptions($options)
    {
        global $_ARRAYLANG;

        $options['functions']['copy'] = true;

        $options['functions']['sortBy'] = array(
            'field' => array('ord' => SORT_ASC)
        );

        $options['order']['overview'] = array(
            'id',
            'flags',
            'name',
            'code',
            'discountActive',
            'discountprice',
            'normalprice',
            'resellerprice',
            'vat',
            'distribution',
            'stock',
        );
        $options['order']['form'] = array(
            'id',
            'name',
            'categories',
            'code',
            'normalprice',
            'resellerprice',
            'discountprice',
            'discountActive',
            'articleGroup',
            'discountgroupCountName',
            'vat',
            'distribution',
            'short',
            'long',
            'keys',
            'manufacturer',
            'uri',
            'stock',
            'stockVisible',
            'minimumOrderQuantity',
            'relProductAttributes',
            'picture',
            'active',
            'b2b',
            'b2c',
            'dateStart',
            'dateEnd'
        );

        $options['fields'] = array(
            'flags' => array(
                'header' => $this->getFlagHeader(),
                'editable' => true,
                'type' => 'checkboxes',
                'sorting' => false,
                'showDetail' => false,
            ),
            'name' => array(
                'header' => $_ARRAYLANG['TXT_PRODUCT_NAME'],
            ),
            'discountActive' => array(
                'editable' => true,
            ),
            'discountprice' => array(
                'editable' => true,
                'sorting' => false,
            ),
            'normalprice' => array(
                'editable' => true,
            ),
            'resellerprice' => array(
                'editable' => true,
            ),
            'vat' => array(
                'editable' => true,
                'sorting' => false,
            ),
            'stock' => array(
                'editable' => true,
            ),
            'distribution' => array(
            ),
            'code' => array(
                'editable' => true,
                'header' => $_ARRAYLANG['TXT_SHOP_PRODUCT_CODE'],
            ),
            'picture' => array(
                'showOverview' => false,
                'type' => 'image'
            ),
            'groupId' => array(
                'showOverview' => false,
                'showDetail' => false,
            ),
            'stockVisible' => array(
                'showOverview' => false,
            ),
            'active' => array(
                'showOverview' => false,
            ),
            'b2b' => array(
                'showOverview' => false,
            ),
            'b2c' => array(
                'showOverview' => false,
            ),
            'categories' => array(
                'showOverview' => false,
            ),
            'dateStart' => array(
                'showOverview' => false,
            ),
            'dateEnd' => array(
                'showOverview' => false,
            ),
            'manufacturerId' => array(
                'showOverview' => false,
                'showDetail' => false,
            ),
            'ord' => array(
                'showOverview' => false,
                'showDetail' => false,
            ),
            'vatId' => array(
                'showOverview' => false,
                'showDetail' => false,
            ),
            'weight' => array(
                'showOverview' => false,
            ),
            'articleId' => array(
                'showOverview' => false,
                'showDetail' => false,
            ),
            'minimumOrderQuantity' => array(
                'showOverview' => false,
            ),
            'uri' => array(
                'showOverview' => false,
            ),
            'short' => array(
                'showOverview' => false,
                'type' => 'sourcecode'
            ),
            'long' => array(
                'showOverview' => false,
                'type' => 'sourcecode'
            ),
            'keys' => array(
                'showOverview' => false,
                'tooltip' => $_ARRAYLANG['TXT_SHOP_KEYWORDS_TOOLTIP'],
            ),
            'discountCoupons' => array(
                'showOverview' => false,
                'showDetail' => false,
            ),
            'orderItems' => array(
                'showOverview' => false,
                'showDetail' => false,
            ),
            'relProductAttributes' => array(
                'showOverview' => false,
                'mode' => 'associate'
            ),
            'manufacturer' => array(
                'showOverview' => false,
            ),
            'discountgroupCountName' => array(
                'showOverview' => false,
            ),
            'articleGroup' => array(
                'showOverview' => false,
            ),
            'userGroups' => array(
                'showOverview' => false,
            ),
        );

        return $options;
    }

    /**
     * Return a div with an icon and tooltip for the flag attribute.
     *
     * @return \Cx\Core\Html\Model\Entity\HtmlElement
     */
    protected function getFlagHeader()
    {
        global $_ARRAYLANG;
        $wrapper = new \Cx\Core\Html\Model\Entity\HtmlElement('div');
        $img = new \Cx\Core\Html\Model\Entity\HtmlElement('img');
        $tooltipTrigger = new \Cx\Core\Html\Model\Entity\HtmlElement('span');
        $empty = new \Cx\Core\Html\Model\Entity\TextElement('');
        $tooltipMessage = new \Cx\Core\Html\Model\Entity\HtmlElement('span');
        $message = new \Cx\Core\Html\Model\Entity\TextElement(
            $_ARRAYLANG['TXT_SHOP_SHOW_PRODUCT_ON_START_PAGE_TIP']
        );

        $img->setAttributes(
            array(
                'style' => 'display:block',
                'src' => $this->cx->getCodeBaseCoreWebPath()
                    . '/Core/View/Media/icons/home.gif'
            )
        );

        $tooltipTrigger->addClass('tooltip-trigger icon-info');
        $tooltipMessage->addClass('tooltip-message');
        $tooltipTrigger->addChild($empty);
        $tooltipMessage->addChild($message);
        $wrapper->addChildren(array($img, $tooltipTrigger, $tooltipMessage));

        return $wrapper;
    }
}