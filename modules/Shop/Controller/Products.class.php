<?php

/**
 * Cloudrexx
 *
 * @link      http://www.cloudrexx.com
 * @copyright Cloudrexx AG 2007-2015
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
 * Products helper class
 *
 * @package     cloudrexx
 * @subpackage  module_shop
 * @copyright   CLOUDREXX CMS - CLOUDREXX AG
 * @author      Reto Kohli <reto.kohli@comvation.com>
 * @version     3.0.0
 */

namespace Cx\Modules\Shop\Controller;

/**
 * Product helper object
 *
 * Provides methods for accessing sets of Products, displaying menus
 * and the like.
 * @package     cloudrexx
 * @subpackage  module_shop
 * @copyright   CLOUDREXX CMS - CLOUDREXX AG
 * @author      Reto Kohli <reto.kohli@comvation.com>
 * @version     3.0.0
 */
class Products
{
    /**
     * Sorting order strings according to the corresponding setting
     *
     * Order 1: By order field value ascending, ID descending
     * Order 2: By name ascending, Product ID ascending
     * Order 3: By Product ID ascending, name ascending
     * @var     array
     * @see     Products::getByShopParam()
     * @author  Reto Kohli <reto.kohli@comvation.com>
     */
    public static $arrProductOrder = array(
        1 => '`product`.`ord` ASC, `id` DESC',
        2 => '`name` ASC, `code` ASC',
        3 => '`code` ASC, `name` ASC',
    );

    /**
     * Builds and returns a database query for Products
     * @param   integer|array   $product_id         The optional Product ID,
     *                                              an array of such, or null
     * @param   integer|string  $category_id        The optional Category ID,
     *                                              or comma separated list
     * @param   integer         $manufacturer_id    The optional manufacturer ID
     * @param   string          $pattern            The optional search term
     * @param   integer         $flagSpecialoffer   The optional special offer
     *                                              flag
     * @param   boolean         $flagLastFive       The optional "last five"
     *                                              flag
     * @param   integer|string  $orderSetting       The optional order setting,
     *                                              or SQL string
     * @param   boolean         $flagIsReseller     The optional reseller flag
     * @param   boolean         $flagShowInactive   The optional inactive flag
     * @return  string                              The SQL query
     */
    public static function getQueryParts(
        $product_id=null, $category_id=null,
        $manufacturer_id=null, $pattern=null,
        $flagSpecialoffer=false, $flagLastFive=false,
        $orderSetting='',
        $flagIsReseller=null,
        $flagShowInactive=false)
    {
        // The name and code fields may be used for sorting.
        // Include them in the field list in order to introduce the alias
        $arrSql = \Text::getSqlSnippets(
            '`product`.`id`', FRONTEND_LANG_ID, 'Shop',
            array(
                'name' => Product::TEXT_NAME,
                'code' => Product::TEXT_CODE,
            )
        );
        $querySelect = "
            SELECT `product`.`id`, ".$arrSql['field'];
        $queryCount = "SELECT COUNT(*) AS `numof_products`";
        $queryJoin = '
            FROM `'.DBPREFIX.'module_shop'.MODULE_INDEX.'_products` AS `product`
            LEFT JOIN `'.DBPREFIX.'module_shop'.MODULE_INDEX.'_rel_category_product` AS `category_product`
              ON `category_product`.`product_id`=`product`.`id` 
            LEFT JOIN `'.DBPREFIX.'module_shop'.MODULE_INDEX.'_categories` AS `category` 
              ON `category_product`.`category_id`=`category`.`id` '.
            $arrSql['join'];
        $queryWhere = ' WHERE 1'.
            // Limit Products to available and active in the frontend
            ($flagShowInactive
                ? ''
                : ' AND `product`.`active`=1
                    AND (`product`.`stock_visible`=0 OR `product`.`stock`>0)
                    '.($category_id ? '' : 'AND `category`.`active`=1' )/*only check if active when not in category view*/.'
                    AND (
                        `product`.`date_start` <= CURRENT_DATE()
                     OR `product`.`date_start` IS NULL
                    )
                    AND (
                        `product`.`date_end` >= CURRENT_DATE()
                     OR `product`.`date_end` IS NULL
                    )'
            ).
            // Limit Products visible to resellers or non-resellers
            ($flagIsReseller === true
              ? ' AND `b2b`=1'
              : ($flagIsReseller === false ? ' AND `b2c`=1' : ''));

//\DBG::log("Products::getByShopParams(): Ordersetting in: $orderSetting");
        // Legacy order presets: Use as default
        if (   is_numeric($orderSetting)
            && isset(self::$arrProductOrder[$orderSetting]))
            $orderSetting = self::$arrProductOrder[$orderSetting];
//\DBG::log("Products::getByShopParams(): Ordersetting #1: $orderSetting");
        if (empty($orderSetting))
            $orderSetting = self::$arrProductOrder[1];
//\DBG::log("Products::getByShopParams(): Ordersetting #2: $orderSetting");
        // Iff the order is specified as the nonexistent "price" field, substitute
        // that by the correct price field:
        //  - "discountprice" if "discount_active" != 0,
        //  - "resellerprice" if $flagIsReseller != 0, or
        //  - "normalprice" otherwise
        $match = null;
        // Note:  The regex /\\b<field name>(?:\\W+(asc|desc))?\\b/i handles
        // optional backticks and/or whitespace around the field name (\W).
        if (preg_match('/\\bprice(?i:\\W+(asc|desc))?\\b/',
                $orderSetting, $match)) {
            $orderField = ($flagIsReseller ? 'resellerprice' : 'normalprice');
            $orderSetting =
                'IF(`discount_active`=0,`'.$orderField.'`,`discountprice`)'.
                (isset($match[1]) ? ' '.$match[1] : '');
        }
//\DBG::log("Products::getByShopParams(): Ordersetting #3: $orderSetting");
        // The use of the Sorting class does not (yet) support ordering by
        // multiple fields.  In order to keep the ordering stable for
        // identical values, the unique criterion (id DESC) is added.
        $queryOrder = ' ORDER BY '.$orderSetting. ', `id` DESC';
//\DBG::log("Products::getByShopParams(): Ordersetting #4: $orderSetting");
        // Apply ordering by "bestseller".
        // Note that this overrides the previous order, and adds GROUP BY.
        // The additional alias "bestseller" queries the number of units sold.
        if (preg_match('/\\bbestseller\\b/',
                $orderSetting, $match)) {
            $querySelect .=
                ', COUNT(`item`.`id`) AS `bestseller`';
            $queryJoin .=
                ' LEFT JOIN `'.DBPREFIX.'module_shop_order_items` AS `item`
                    ON `product`.`id`=`item`.`product_id`';
            $queryOrder = '
                GROUP BY `product`.`id`
                ORDER BY '.$orderSetting.', `id` DESC';
        }
//\DBG::log("Products::getByShopParams(): Ordersetting #5: $orderSetting");

        $querySpecialOffer = '';
        if (   $flagLastFive
            || $flagSpecialoffer === \Cx\Modules\Shop\Controller\ProductController::DEFAULT_VIEW_LASTFIVE) {
            // Select last five (or so) products added to the database
// TODO: Extend for searching for most recently modified Products
            $limit = ($flagLastFive === true ? 5 : $flagLastFive);
            $queryOrder = ' ORDER BY `id` DESC';
            $queryCount = "SELECT $limit AS `numof_products`";
        } else {
            // Build standard full featured query
            $querySpecialOffer =
                (   $flagSpecialoffer === \Cx\Modules\Shop\Controller\ProductController::DEFAULT_VIEW_DISCOUNTS
                 || $flagSpecialoffer === true // Old behavior!
                  ? ' AND `product`.`discount_active`=1'
                  : ($flagSpecialoffer === \Cx\Modules\Shop\Controller\ProductController::DEFAULT_VIEW_MARKED
                      ? " AND `product`.`flags` LIKE '%__SHOWONSTARTPAGE__%'" : '')
                );
            // Limit by Product ID (unused by getByShopParameters()!)
            if (is_array($product_id)) {
                $queryWhere .=
                    ' AND `product`.`id` IN('.join(',', $product_id).')';
            } elseif ($product_id > 0) {
                $queryWhere .=
                    ' AND `product`.`id`='.$product_id;
            }
            // Limit Products by Manufacturer ID, if any
            if ($manufacturer_id > 0) {
                $queryJoin .= '
                    INNER JOIN `'.DBPREFIX.'module_shop'.MODULE_INDEX.'_manufacturer` AS `m`
                       ON `m`.`id`=`product`.`manufacturer_id`';
                $queryWhere .= ' AND `product`.`manufacturer_id`='.$manufacturer_id;
            }
            // Limit Products by ShopCategory ID or IDs, if any
            // (Pricelists use comma separated values, for example)
            if ($category_id) {
                $queryCategories = '`category_product`.`category_id` IN ('
                    .(is_array($category_id) ? implode(',',$category_id) : $category_id).')';
                $queryWhere .= ' AND ('.$queryCategories.')';
            }
            // Limit Products by search pattern, if any
            if ($pattern != '') {
                $arrSqlPattern = \Text::getSqlSnippets(
                    '`product`.`id`', FRONTEND_LANG_ID, 'Shop',
                    array(
                        'short' => Product::TEXT_SHORT,
                        'long' => Product::TEXT_LONG,
                        'keys' => Product::TEXT_KEYS,
                        'uri' => Product::TEXT_URI,
                    )
                );
                $pattern = contrexx_raw2db($pattern);
// TODO: This is prolly somewhat slow.  Could we use an "index" of sorts?
                $querySelect .=
                    ', '.$arrSqlPattern['field'].
                    ', MATCH ('.$arrSql['alias']['name'].')'.
                    " AGAINST ('%$pattern%') AS `score1`".
                    ', MATCH ('.$arrSqlPattern['alias']['short'].')'.
                    " AGAINST ('%$pattern%') AS `score2`".
                    ', MATCH ('.$arrSqlPattern['alias']['long'].')'.
                    " AGAINST ('%$pattern%') AS `score3`";
                $queryJoin .= $arrSqlPattern['join'];
                $queryWhere .= "
                    AND (   `product`.`id` LIKE '%$pattern%'
                         OR ".$arrSql['alias']['name']." LIKE '%$pattern%'
                         OR ".$arrSql['alias']['code']." LIKE '%$pattern%'
                         OR ".$arrSqlPattern['alias']['long']." LIKE '%$pattern%'
                         OR ".$arrSqlPattern['alias']['short']." LIKE '%$pattern%'
                         OR ".$arrSqlPattern['alias']['keys']." LIKE '%$pattern%')";
            }
        }
//\DBG::log("querySelect $querySelect");//\DBG::log("queryCount $queryCount");\DBG::log("queryJoin $queryJoin");\DBG::log("queryWhere $queryWhere");//\DBG::log("querySpecialOffer $querySpecialOffer");\DBG::log("queryOrder $queryOrder");
        $queryTail = $queryJoin.$queryWhere.$querySpecialOffer;
        return array($querySelect, $queryCount, $queryTail, $queryOrder);
    }
}
