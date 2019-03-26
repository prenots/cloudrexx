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
    const DEFAULT_VIEW_NONE = 0;
    const DEFAULT_VIEW_MARKED = 1;
    const DEFAULT_VIEW_DISCOUNTS = 2;
    const DEFAULT_VIEW_LASTFIVE = 3;
    const DEFAULT_VIEW_COUNT = 4;

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
        $options['functions']['editable'] = true;
        $options['functions']['searching'] = true;
        $options['functions']['filtering'] = true;
        $options['functions']['sortBy'] = array(
            'field' => array('ord' => SORT_ASC)
        );
        $options['functions']['status'] = array(
            'field' => 'discountActive'
        );

        $options['tabs']['product-option'] = array(
            'header' => $_ARRAYLANG['TXT_PRODUCT_OPTIONS'],
            'fields' => array(
                'relProductAttributes'
            )
        );
        $options['tabs']['product-picture'] = array(
            'header' => $_ARRAYLANG['TXT_PRODUCT_IMAGE'],
            'fields' => array(
                'picture'
            )
        );
        $options['tabs']['product-status'] = array(
            'header' => $_ARRAYLANG['TXT_PRODUCT_STATUS'],
            'fields' => array(
                'active',
                'b2b',
                'b2c',
                'dateStart',
                'dateEnd'
            )
        );

        $options['order']['overview'] = array(
            'id',
            'discountActive',
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
            'id' => array(
                'allowFiltering' => false,
            ),
            'flags' => array(
                'header' => $this->getFlagHeader(),
                'editable' => true,
                'sorting' => false,
                'allowFiltering' => false,
                'type' => 'checkboxes',
                'validValues' => '__SHOWONSTARTPAGE__',
            ),
            'name' => array(
                'header' => $_ARRAYLANG['TXT_PRODUCT_NAME'],
                'allowFiltering' => false,
                'table' => array(
                    'attributes' => array(
                        'class' => 'product-name',
                    ),
                ),
            ),
            'discountActive' => array(
                'editable' => true,
                'allowFiltering' => false,
                'table' => array(
                    'attributes' => array(
                        'class' => 'small'
                    ),
                ),
            ),
            'discountprice' => array(
                'editable' => true,
                'sorting' => false,
                'allowFiltering' => false,
                'attributes' => array(
                    'class' => 'small',
                ),
            ),
            'normalprice' => array(
                'formtext' => $_ARRAYLANG['normalprice-detail'],
                'editable' => true,
                'allowFiltering' => false,
                'attributes' => array(
                    'class' => 'small',
                ),
            ),
            'resellerprice' => array(
                'formtext' => $_ARRAYLANG['resellerprice-detail'],
                'editable' => true,
                'allowFiltering' => false,
                'attributes' => array(
                    'class' => 'small',
                ),
            ),
            'vat' => array(
                'formtext' => $_ARRAYLANG['vat-detail'],
                'editable' => true,
                'sorting' => false,
                'allowFiltering' => false,
            ),
            'stock' => array(
                'editable' => true,
                'allowFiltering' => false,
                'attributes' => array(
                    'class' => 'small',
                ),
            ),
            'distribution' => array(
                'type' => 'select',
                'valueCallback' => function($val) {
                    global $_ARRAYLANG;
                    return $_ARRAYLANG[$val];
                },
                'validValues' => implode(
                    ',',
                    \Cx\Modules\Shop\Controller\Distribution::getArrDistributionTypes()
                ),
                'storecallback' => function() {

                },
                'allowFiltering' => false,
            ),
            'code' => array(
                'editable' => true,
                'header' => $_ARRAYLANG['TXT_SHOP_PRODUCT_CODE'],
                'allowFiltering' => false,
                'attributes' => array(
                    'size' => '10'
                ),
            ),
            'picture' => array(
                'showOverview' => false,
                'type' => 'image',
                'allowFiltering' => false,
            ),
            'groupId' => array(
                'showOverview' => false,
                'showDetail' => false,
                'allowFiltering' => false,
            ),
            'stockVisible' => array(
                'showOverview' => false,
                'allowFiltering' => false,
            ),
            'active' => array(
                'showOverview' => false,
                'allowFiltering' => false,
            ),
            'b2b' => array(
                'showOverview' => false,
                'allowFiltering' => false,
            ),
            'b2c' => array(
                'showOverview' => false,
                'allowFiltering' => false,
            ),
            'categories' => array(
                'showOverview' => false,
                'mode' => 'associate',
            ),
            'dateStart' => array(
                'showOverview' => false,
                'allowFiltering' => false,
            ),
            'dateEnd' => array(
                'showOverview' => false,
                'allowFiltering' => false,
            ),
            'manufacturerId' => array(
                'showOverview' => false,
                'showDetail' => false,
                'allowFiltering' => false,
            ),
            'ord' => array(
                'showOverview' => false,
                'showDetail' => false,
                'allowFiltering' => false,
            ),
            'vatId' => array(
                'showOverview' => false,
                'showDetail' => false,
                'allowFiltering' => false,
            ),
            'weight' => array(
                'showOverview' => false,
                'allowFiltering' => false,
            ),
            'articleId' => array(
                'showOverview' => false,
                'showDetail' => false,
                'allowFiltering' => false,
            ),
            'minimumOrderQuantity' => array(
                'showOverview' => false,
                'allowFiltering' => false,
            ),
            'uri' => array(
                'showOverview' => false,
                'allowFiltering' => false,
            ),
            'short' => array(
                'showOverview' => false,
                'type' => 'wysiwyg',
                'allowFiltering' => false,
            ),
            'long' => array(
                'showOverview' => false,
                'type' => 'wysiwyg',
                'allowFiltering' => false,
            ),
            'keys' => array(
                'showOverview' => false,
                'tooltip' => $_ARRAYLANG['TXT_SHOP_KEYWORDS_TOOLTIP'],
                'allowFiltering' => false,
            ),
            'discountCoupons' => array(
                'showOverview' => false,
                'showDetail' => false,
                'allowFiltering' => false,
            ),
            'orderItems' => array(
                'showOverview' => false,
                'showDetail' => false,
                'allowFiltering' => false,
            ),
            'relProductAttributes' => array(
                'showOverview' => false,
                'mode' => 'associate',
                'allowFiltering' => false,
            ),
            'manufacturer' => array(
                'showOverview' => false,
                'allowFiltering' => false,
            ),
            'discountgroupCountName' => array(
                'showOverview' => false,
                'mode' => 'associate',
                'allowFiltering' => false,
            ),
            'articleGroup' => array(
                'showOverview' => false,
                'mode' => 'associate',
                'allowFiltering' => false,
            ),
            'userGroups' => array(
                'showOverview' => false,
                'mode' => 'associate',
                'allowFiltering' => false,
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

    /**
     * Returns an array of image names, widths and heights from
     * the base64 encoded string taken from the database
     *
     * The array returned looks like
     *  array(
     *    1 => array(
     *      'img' => <image1>,
     *      'width' => <image1.width>,
     *      'height' => <image1.height>
     *    ),
     *    2 => array( ... ), // The same as above, three times in total
     *    3 => array( ... ),
     * )
     * @param   string  $base64Str  The base64 encoded image string
     * @return  array               The decoded image array
     * @static
     * @author  Reto Kohli <reto.kohli@comvation.com>
     */
    static function get_image_array_from_base64($base64Str)
    {
        // Pre-init array to avoid "undefined index" notices
        $arrPictures = array(
            1 => array('img' => '', 'width' => 0, 'height' => 0),
            2 => array('img' => '', 'width' => 0, 'height' => 0),
            3 => array('img' => '', 'width' => 0, 'height' => 0)
        );
        if (strpos($base64Str, ':') === false)
            // have to return an array with the desired number of elements
            // and an empty file name in order to show the "dummy" picture(s)
            return $arrPictures;
        $i = 0;
        foreach (explode(':', $base64Str) as $imageData) {
            $shopImage = $shopImage_width = $shopImage_height = null;
            list($shopImage, $shopImage_width, $shopImage_height) = explode('?', $imageData);
            $shopImage        = base64_decode($shopImage);
            $shopImage_width  = base64_decode($shopImage_width);
            $shopImage_height = base64_decode($shopImage_height);
            $arrPictures[++$i] = array(
                'img' => $shopImage,
                'width' => $shopImage_width,
                'height' => $shopImage_height,
            );
        }
        return $arrPictures;
    }

    /**
     * Returns HTML code for dropdown menu options to choose the default
     * view on the Shop starting page.
     *
     * Possible choices are defined by global constants
     * self::DEFAULT_VIEW_* and corresponding language variables.
     * @static
     * @param   integer   $selected     The optional preselected view index
     * @return  string                  The HTML menu options
     */
    static function getDefaultViewMenuoptions($selected='')
    {
        global $_ARRAYLANG;

        $strMenuoptions = '';
        for ($i = 0; $i < self::DEFAULT_VIEW_COUNT; ++$i) {
            $strMenuoptions .=
                "<option value='$i'".
                ($selected == $i ? ' selected="selected"' : '').'>'.
                $_ARRAYLANG['TXT_SHOP_PRODUCT_DEFAULT_VIEW_'.$i].
                "</option>\n";
        }
        return $strMenuoptions;
    }

    /**
     * Returns a string with HTML options for any menu
     *
     * Includes Products with the given active status only if $active is
     * not null.  The options' values are the Product IDs.
     * The sprintf() format for the options defaults to "%2$s", possible
     * values are:
     *  - %1$u: The Product ID
     *  - %2$s: The Product name
     * @static
     * @param   integer   $selected     The optional preselected Product ID
     * @param   boolean   $active       Optional.  Include active (true) or
     *                                  inactive (false) Products only.
     *                                  Ignored if null.  Defaults to null
     * @param   string    $format       The optional sprintf() format
     * @param   boolean   $showAllOptions Show all options and not only the selected
     * @return  array                   The HTML options string on success,
     *                                  null otherwise
     * @global  ADONewConnection
     * @author  Reto Kohli <reto.kohli@comvation.com>
     */
    static function getMenuoptions($selected=null, $active=null, $format='%2$s', $showAllOptions = true)
    {
        global $_ARRAYLANG;

        $arrName =
            array(0 => $_ARRAYLANG['TXT_SHOP_PRODUCT_NONE']) +
            self::getNameArray($active, $format);
        if ($arrName === false) return null;

        if ($selected && !$showAllOptions) {
            $arrName = array();
            $product = Product::getById($selected);
            if ($product) {
                $arrName[$product->id()] = $product->name();
            }
        }
        return \Html::getOptions($arrName, $selected);
    }

    /**
     * Returns an array of Products selected by parameters as available in
     * the Shop.
     *
     * The $count parameter is set to the number of records found.
     * After it returns, it contains the actual number of matching Products.
     * @param   integer     $count          The desired number of Products,
     *                                      by reference.  Set to the actual
     *                                      total matching number.
     * @param   integer     $offset         The Product offset
     * @param   integer|array   $product_id     The optional Product ID,
     *                                      or an array of such
     * @param   integer     $category_id    The ShopCategory ID
     * @param   integer     $manufacturer_id  The Manufacturer ID
     * @param   string      $pattern        A search pattern
     * @param   boolean     $flagSpecialoffer Limit results to special offers
     *                                      if true.  Disabled if either
     *                                      the Product ID, Category ID,
     *                                      Manufacturer ID, or the search
     *                                      pattern is non-empty.
     * @param   boolean     $flagLastFive   Limit results to the last five
     *                                      Products added to the Shop if true.
     *                                      Note: You may specify an integer
     *                                      count as well, this will set the
     *                                      limit accordingly.
     * @param   integer     $orderSetting   The sorting order setting, defaults
     *                                      to the order field value ascending,
     *                                      Product ID descending
     * @param   boolean     $flagIsReseller The reseller status of the
     *                                      current customer, ignored if
     *                                      it's the empty string
     * @param   boolean     $flagShowInactive   Include inactive Products
     *                                      if true.  Backend use only!
     * @return  array                       Array of Product objects,
     *                                      or false if none were found
     * @static
     * @author      Reto Kohli <reto.kohli@comvation.com>
     */
    static function getByShopParams(
        &$count, $offset=0,
        $product_id=null, $category_id=null,
        $manufacturer_id=null, $pattern=null,
        $flagSpecialoffer=false, $flagLastFive=false,
        $orderSetting='',
        $flagIsReseller=null,
        $flagShowInactive=false
    ) {
        global $objDatabase, $_CONFIG;

//\DBG::activate(DBG_ADODB_ERROR|DBG_LOG_FIREPHP);

        // Do not show any Products if no selection is made at all
        if (   empty($product_id)
            && empty($category_id)
            && empty($manufacturer_id)
            && empty($pattern)
            && empty($flagSpecialoffer)
            && empty($flagLastFive)
            && empty($flagShowInactive) // Backend only!
        ) {
            $count = 0;
            return array();
        }
// NOTE:
// This was an optimization, but does not (yet) consider the other parameters.
//        if ($product_id) {
//            // Select single Product by ID
//            $objProduct = Product::getById($product_id);
//            // Inactive Products MUST NOT be shown in the frontend
//            if (   $objProduct
//                && ($flagShowInactive || $objProduct->active())) {
//                $count = 1;
//                return array($objProduct);
//            }
//            $count = 0;
//            return false;
//        }
        list($querySelect, $queryCount, $queryTail, $queryOrder) =
            self::getQueryParts(
                $product_id, $category_id, $manufacturer_id, $pattern,
                $flagSpecialoffer, $flagLastFive, $orderSetting,
                $flagIsReseller, $flagShowInactive);
        $limit = ($count > 0
            ? $count
            : (!empty($_CONFIG['corePagingLimit'])
                ? $_CONFIG['corePagingLimit'] : 10));
        $count = 0;
//\DBG::activate(DBG_ADODB);
        $objResult = $objDatabase->SelectLimit(
            $querySelect.$queryTail.$queryOrder, $limit, $offset);
        if (!$objResult) return Product::errorHandler();
//\DBG::deactivate(DBG_ADODB);
        $arrProduct = array();
        while (!$objResult->EOF) {
            $product_id = $objResult->fields['id'];
            $objProduct = Product::getById($product_id);
            if ($objProduct)
                $arrProduct[$product_id] = $objProduct;
            $objResult->MoveNext();
        }
        $objResult = $objDatabase->Execute($queryCount.$queryTail);
        if (!$objResult) return false;
        $count = $objResult->fields['numof_products'];
//\DBG::log("Products::getByShopParams(): Set count to $count");
        return $arrProduct;
    }
}