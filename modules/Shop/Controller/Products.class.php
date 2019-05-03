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
    const DEFAULT_VIEW_NONE = 0;
    const DEFAULT_VIEW_MARKED = 1;
    const DEFAULT_VIEW_DISCOUNTS = 2;
    const DEFAULT_VIEW_LASTFIVE = 3;
    const DEFAULT_VIEW_COUNT = 4;

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
     * Returns an array of Product objects sharing the same Product code.
     * @param   string      $customId   The Product code
     * @return  mixed                   The array of matching Product objects
     *                                  on success, false otherwise.
     * @static
     * @author      Reto Kohli <reto.kohli@comvation.com>
     */
    static function getByCustomId($customId)
    {
        global $objDatabase;

        if (empty($customId)) return false;
        $query = "
            SELECT `id`
              FROM `".DBPREFIX."module_shop".MODULE_INDEX."_products`
             WHERE `product_id`='$customId'
             ORDER BY `id` ASC";
        $objResult = $objDatabase->Execute($query);
        if (!$objResult) return false;
        $arrProduct = array();
        while (!$objResult->EOF) {
            $arrProduct[] = Product::getById($objResult->Fields('id'));
            $objResult->MoveNext();
        }
        return $arrProduct;
    }


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
            || $flagSpecialoffer === self::DEFAULT_VIEW_LASTFIVE) {
            // Select last five (or so) products added to the database
// TODO: Extend for searching for most recently modified Products
            $limit = ($flagLastFive === true ? 5 : $flagLastFive);
            $queryOrder = ' ORDER BY `id` DESC';
            $queryCount = "SELECT $limit AS `numof_products`";
        } else {
            // Build standard full featured query
            $querySpecialOffer =
                (   $flagSpecialoffer === self::DEFAULT_VIEW_DISCOUNTS
                 || $flagSpecialoffer === true // Old behavior!
                  ? ' AND `product`.`discount_active`=1'
                  : ($flagSpecialoffer === self::DEFAULT_VIEW_MARKED
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

    /**
     * Returns an array of Product IDs contained by the given
     * ShopCategory ID.
     *
     * Orders the array by ascending ordinal field value
     * @param   integer   $category_id  The ShopCategory ID
     * @return  mixed                   The array of Product IDs on success,
     *                                  false otherwise.
     * @static
     * @author      Reto Kohli <reto.kohli@comvation.com>
     */
    static function getIdArrayByShopCategory($category_id)
    {
        global $objDatabase;

        $category_id = intval($category_id);
        $query = "
            SELECT `product_id`
              FROM `".DBPREFIX."module_shop".MODULE_INDEX."_rel_category_product`
             WHERE  `category_id` = $category_id
        ";
        $objResult = $objDatabase->Execute($query);
        if (!$objResult) return false;
        $arrProductId = array();
        while (!$objResult->EOF) {
            $arrProductId[] = $objResult->fields['product_id'];
            $objResult->MoveNext();
        }
        return $arrProductId;
    }


    /**
     * Returns the first matching picture name found in the Products
     * within the Shop Category given by its ID.
     * @global  ADONewConnection  $objDatabase    Database connection object
     * @param type $category_id
     * @return  string                      The image name, or the
     *                                      empty string.
     * @static
     * @author  Reto Kohli <reto.kohli@comvation.com>
     */
    static function getPictureByCategoryId($category_id)
    {
        global $objDatabase;

        $category_id = intval($category_id);
        $query = "
            SELECT `picture`
              FROM `".DBPREFIX."module_shop".MODULE_INDEX."_products` AS `p`
              LEFT JOIN `".DBPREFIX."module_shop".MODULE_INDEX."_rel_category_product` 
              AS `cp` ON `cp`.`product_id` = `p`.`id`
             WHERE FIND_IN_SET($category_id, `cp`.`category_id`)
               AND `picture`!=''
             ORDER BY `ord` ASC";
        $objResult = $objDatabase->SelectLimit($query, 1);
        if ($objResult && $objResult->RecordCount() > 0) {
            // Got a picture
            $arrImages = ProductController::get_image_array_from_base64(
                $objResult->fields['picture']);
            $imageName = $arrImages[1]['img'];
            return $imageName;
        }
        // No picture found here
        return '';
    }


    /**
     * Returns an array of ShopCategory IDs containing Products with
     * their flags containing the given string.
     * @param   string  $strName    The name of the flag to match
     * @return  mixed               The array of ShopCategory IDs on success,
     *                              false otherwise.
     * @static
     * @author      Reto Kohli <reto.kohli@comvation.com>
     */
    static function getShopCategoryIdArrayByFlag($strName)
    {
        global $objDatabase;

        $query = "
            SELECT DISTINCT category_id
              FROM ".DBPREFIX."module_shop".MODULE_INDEX."_products
             WHERE flags LIKE '%$strName%'
          ORDER BY category_id ASC";
        $objResult = $objDatabase->Execute($query);
        if (!$objResult) return false;
        $arrShopCategoryId = array();
        while (!$objResult->EOF) {
            $arrCategoryId = preg_split('/\s*,\s*/',
                $objResult->Fields['catid'], null, PREG_SPLIT_NO_EMPTY);
            foreach ($arrCategoryId as $category_id) {
                $arrShopCategoryId[$category_id] = null;
            }
            $objResult->MoveNext();
        }
        return array_flip($arrShopCategoryId);
    }

    /**
     * Apply the flags to all Products matching the given Product code
     *
     * Any Product and ShopCategory carrying one or more of the names
     * of any ShopCategory marked as "__VIRTUAL__" is cloned and added
     * to that category.  Those having any such flags removed are deleted
     * from the respective category.  Identical copies of the same Products
     * are recognized by their "product_id" (the Product code).
     *
     * Note that in this current version, only the flags of Products are
     * tested and applied.  Products are cloned and added together with
     * their immediate parent ShopCategories (aka "Article").
     *
     * Thus, all Products within the same "Article" ShopCategory carry the
     * same flags, as does the containing ShopCategory itself.
     * @param   integer     $productCode  The Product code (*NOT* the ID).
     *                                    This must be non-empty!
     * @param   string      $strNewFlags  The new flags for the Product
     * @static
     * @author      Reto Kohli <reto.kohli@comvation.com>
     */
    static function changeFlagsByProductCode($productCode, $strNewFlags)
    {
        if (empty($productCode)) return false;
        // Get all available flags.  These are represented by the names
        // of virtual root ShopCategories.
        $arrVirtual = ShopCategories::getVirtualCategoryNameArray();

        // Get the affected identical Products
        $arrProduct = Products::getByCustomId($productCode);
        // No way we can do anything useful without them.
        if (count($arrProduct) == 0) return false;

        // Get the Product flags.  As they're all the same, we'll use the
        // first one here.
        // Note that this object is used for reference only and is never stored.
        // Its database entry will be updated along the way, however.
        $_objProduct = $arrProduct[0];
        $strOldFlags = $_objProduct->getFlags();
        // Flag indicating whether the article has been cloned already
        // for all new flags set.
        $flagCloned = false;

        // Now apply the changes to all those identical Products, their parent
        // ShopCategories, and all sibling Products within them.
        foreach ($arrProduct as $objProduct) {
            // Get the containing article ShopCategory.
            $category_id = $objProduct->category_id();
            $objArticleCategory = ShopCategory::getById($category_id);
            if (!$objArticleCategory) continue;

            // Get parent (subgroup)
            $objSubGroupCategory =
                ShopCategory::getById($objArticleCategory->parent_id());
            // This should not happen!
            if (!$objSubGroupCategory) continue;
            $subgroupName = $objSubGroupCategory->name();

            // Get grandparent (group, root ShopCategory)
            $objRootCategory =
                ShopCategory::getById($objSubGroupCategory->parent_id());
            if (!$objRootCategory) continue;

            // Apply the new flags to all Products and Article ShopCategories.
            // Update the flags of the original Article ShopCategory first
            $objArticleCategory->flags($strNewFlags);
            $objArticleCategory->store();

            // Get all sibling Products affected by the same flags
            $arrSiblingProducts = Products::getByShopCategory(
                $objArticleCategory->id()
            );

            // Set the new flag set for all Products within the Article
            // ShopCategory.
            foreach ($arrSiblingProducts as $objProduct) {
                $objProduct->flags($strNewFlags);
                $objProduct->store();
            }

            // Check whether this group is affected by the changes.
            // If its name matches one of the flags, the Article and subgroup
            // may have to be removed.
            $strFlag = $objRootCategory->name();
            if (preg_match("/$strFlag/", $strNewFlags))
                // The flag is still there, don't bother.
                continue;

            // Also check whether this is a virtual root ShopCategory.
            if (in_array($strFlag, $arrVirtual)) {
                // It is one of the virtual roots, and the flag is missing.
                // So the Article has to be removed from this group.
                $objArticleCategory->delete();
                $objArticleCategory = false;
                // And if the subgroup happens to contain no more
                // "Article", delete it as well.
                $arrChildren = $objSubGroupCategory->getChildrenIdArray();
                if (count($arrChildren) == 0)
                    $objSubGroupCategory->delete();
                continue;
            }

            // Here, the virtual ShopCategory groups have been processed,
            // the only ones left are the "normal" ShopCategories.
            // Clone one of the Article ShopCategories for each of the
            // new flags set.
            // Already did that?
            if ($flagCloned) continue;

            // Find out what flags have been added.
            foreach ($arrVirtual as $strFlag) {
                // That flag is not present in the new flag set.
                if (!preg_match("/$strFlag/", $strNewFlags)) continue;
                // But it has been before.  The respective branch has
                // been truncated above already.
                if (preg_match("/$strFlag/", $strOldFlags)) continue;

                // That is a new flag for which we have to clone the Article.
                // Get the affected grandparent (group, root ShopCategory)
                $objTargetRootCategory =
                    ShopCategories::getChildNamed($strFlag, 0, false);
                if (!$objTargetRootCategory) continue;
                // Check whether the subgroup exists already
                $objTargetSubGroupCategory =
                    ShopCategories::getChildNamed(
                        $subgroupName, $objTargetRootCategory->id(), false);
                if (!$objTargetSubGroupCategory) {
                    // Nope, add the subgroup.
                    $objSubGroupCategory->makeClone();
                    $objSubGroupCategory->parent_id($objTargetRootCategory->id());
                    $objSubGroupCategory->store();
                    $objTargetSubGroupCategory = $objSubGroupCategory;
                }

                // Check whether the Article ShopCategory exists already
                $objTargetArticleCategory =
                    ShopCategories::getChildNamed(
                        $objArticleCategory->name(),
                        $objTargetSubGroupCategory->id(),
                        false
                    );
                if ($objTargetArticleCategory) {
                    // The Article Category already exists.
                } else {
                    // Nope, clone the "Article" ShopCategory and add it to the
                    // subgroup.  Note that the flags have been set already
                    // and don't need to be changed again here.
                    // Also note that the cloning process includes all content
                    // of the Article ShopCategory, but the flags will remain
                    // unchanged. That's why the flags have already been
                    // changed right at the beginning of the process.
                    $objArticleCategory->makeClone(true, true);
                    $objArticleCategory->parent_id($objTargetSubGroupCategory->id());
                    $objArticleCategory->store();
                    $objTargetArticleCategory = $objArticleCategory;
                }
            } // foreach $arrVirtual
        } // foreach $arrProduct
        // And we're done!
        return true;
    }
}
