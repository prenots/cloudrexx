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
 * Discount
 *
 * Optional calculation of discounts in the Shop.
 * Note: This is to be customized for individual online shops.
 * @copyright   CLOUDREXX CMS - CLOUDREXX AG
 * @author      Reto Kohli <reto.kohli@comvation.ch>
 * @access      public
 * @version     3.0.0
 * @package     cloudrexx
 * @subpackage  module_shop
 */

namespace Cx\Modules\Shop\Controller;

/**
 * Discount
 *
 * Processes many kinds of discounts - as long as you can express the
 * rules in the terms used here.
 * @copyright   CLOUDREXX CMS - CLOUDREXX AG
 * @author      Reto Kohli <reto.kohli@comvation.ch>
 * @access      public
 * @version     3.0.0
 * @package     cloudrexx
 * @subpackage  module_shop
 */
class Discount
{
    /**
     * Text keys
     */
    const TEXT_NAME_GROUP_COUNT = 'discount_group_name';
    const TEXT_UNIT_GROUP_COUNT = 'discount_group_unit';
    const TEXT_NAME_GROUP_ARTICLE = 'discount_group_article';
    const TEXT_NAME_GROUP_CUSTOMER = 'discount_group_customer';

    /**
     * Tells for each group if different products using this discount group can
     * be summarized to apply the discount.
     * @var array
     */
    protected static $arrDiscountIsCumulative = null;

    /**
     * Array of count type discount group names
     * @var   array
     */
    protected static $arrDiscountCountName = null;

    /**
     * Array of count type discount group units
     * @var   array
     */
    protected static $arrDiscountCountRate = null;

    /**
     * Array of Customer groups
     * @var   array
     */
    protected static $arrCustomerGroup = null;

    /**
     * Array of Article groups
     * @var   array
     */
    protected static $arrArticleGroup = null;

    /**
     * Initializes all static Discount data
     * @return  boolean             True on success, false otherwise
     */
    static function init()
    {
        global $objDatabase;

        $arrSql = \Text::getSqlSnippets('`discount`.`id`',
            FRONTEND_LANG_ID, 'Shop', array(
                'name' => self::TEXT_NAME_GROUP_COUNT,
                'unit' => self::TEXT_UNIT_GROUP_COUNT));
        $query = "
            SELECT `discount`.`id`, `discount`.`cumulative`, ".$arrSql['field']."
              FROM `".DBPREFIX."module_shop".MODULE_INDEX."_discountgroup_count_name` AS `discount`
                   ".$arrSql['join']."
             ORDER BY `discount`.`id` ASC";
        $objResult = $objDatabase->Execute($query);
        if (!$objResult) return self::errorHandler();
        self::$arrDiscountCountName = array();
        static::$arrDiscountIsCumulative = array();
        while (!$objResult->EOF) {
            $group_id = $objResult->fields['id'];
            $strName = $objResult->fields['name'];
            if (is_null($strName)) {
                $strName = \Text::getById($group_id, 'Shop',
                    self::TEXT_NAME_GROUP_COUNT)->content();
            }
            $strUnit = $objResult->fields['unit'];
            if (is_null($strUnit)) {
                $strUnit = \Text::getById($group_id, 'Shop',
                    self::TEXT_UNIT_GROUP_COUNT)->content();
            }
            self::$arrDiscountCountName[$group_id] = array(
                'name' => $strName,
                'unit' => $strUnit,
            );
            $isCumulative = (bool) $objResult->fields['cumulative'];
            static::$arrDiscountIsCumulative[$group_id] = $isCumulative;
            $objResult->MoveNext();
        }

        // Note that the ordering is significant here.
        // Some methods rely on it to find the applicable rate.
        $query = "
            SELECT `group_id`, `count`, `rate`
              FROM `".DBPREFIX."module_shop".MODULE_INDEX."_discountgroup_count_rate`
             ORDER by `count` DESC";
        $objResult = $objDatabase->Execute($query);
        if (!$objResult) return self::errorHandler();
        self::$arrDiscountCountRate = array();
        while (!$objResult->EOF) {
            self::$arrDiscountCountRate[$objResult->fields['group_id']]
                [$objResult->fields['count']] = $objResult->fields['rate'];
            $objResult->MoveNext();
        }

        return true;
    }


    /**
     * Flushes all static Discount data
     * @return  void
     */
    static function flush()
    {
        self::$arrDiscountCountName = null;
        self::$arrDiscountCountRate = null;
        self::$arrCustomerGroup = null;
        self::$arrArticleGroup = null;
        self::$arrDiscountRateCustomer = null;
    }


    /**
     * Delete the count type discount group seleted by its ID from the database
     *
     * Backend use only.
     * @param   integer   $group_id     The discount group ID
     * @return  boolean                 True on success, false otherwise
     * @static
     * @author  Reto Kohli <reto.kohli@comvation.com>
     */
    static function deleteDiscountCount($group_id)
    {
        global $objDatabase, $_ARRAYLANG;

        if (empty($group_id)) return false;
        if (is_null(self::$arrDiscountCountName)) self::init();
        if (empty(self::$arrDiscountCountName[$group_id])) return true;
        // Remove counts and rates
        $query = "
            DELETE FROM `".DBPREFIX."module_shop".MODULE_INDEX."_discountgroup_count_rate`
             WHERE `group_id`=$group_id";
        $objResult = $objDatabase->Execute($query);
        if (!$objResult) return self::errorHandler();
        // Remove the group
        if (!\Text::deleteById($group_id, 'Shop', self::TEXT_NAME_GROUP_COUNT)) {
            return \Message::error($_ARRAYLANG['TXT_SHOP_DISCOUNT_COUNT_ERROR_DELETING']);
        }
        if (!\Text::deleteById($group_id, 'Shop', self::TEXT_UNIT_GROUP_COUNT)) {
            return \Message::error($_ARRAYLANG['TXT_SHOP_DISCOUNT_COUNT_ERROR_DELETING']);
        }
        $query = "
            DELETE FROM `".DBPREFIX."module_shop".MODULE_INDEX."_discountgroup_count_name`
             WHERE `id`=$group_id";
        $objResult = $objDatabase->Execute($query);
        if (!$objResult) {
            return \Message::error($_ARRAYLANG['TXT_SHOP_DISCOUNT_COUNT_ERROR_DELETING']);
        }
        return \Message::ok($_ARRAYLANG['TXT_SHOP_DISCOUNT_COUNT_DELETED_SUCCESSFULLY']);
    }


    /**
     * Returns the HTML dropdown menu options with all of the
     * customer group names
     *
     * Backend use only.
     * @param   integer   $selectedId   The optional preselected ID
     * @return  string                  The HTML dropdown menu options
     * @static
     * @author  Reto Kohli <reto.kohli@comvation.com>
     */
    static function getMenuOptionsGroupCustomer($selectedId=0)
    {
        global $_ARRAYLANG;

        return \Html::getOptions(
            array(
                0 => $_ARRAYLANG['TXT_SHOP_DISCOUNT_GROUP_NONE']
            ) + self::getCustomerGroupNameArray(), $selectedId);
    }


    /**
     * Returns the HTML dropdown menu options with all of the
     * article group names, plus a null option prepended
     *
     * Backend use only.
     * @param   integer   $selectedId   The optional preselected ID
     * @return  string                  The HTML dropdown menu options
     * @static
     * @author  Reto Kohli <reto.kohli@comvation.com>
     * @deprecated Use the ViewGenerator
     */
    static function getMenuOptionsGroupArticle($selectedId=0)
    {
        $articleGroups = static::getArticleGroupArray();

        global $_ARRAYLANG;
        static $arrArticleGroupName = null;

        if (is_null($arrArticleGroupName)) {
            $arrArticleGroupName = array();
            foreach ($articleGroups as $id => $articleGroup) {
                $arrArticleGroupName[
                    $id
                ] = $articleGroup['name'];
            }
        }
        return \Html::getOptions(
            array(0 => $_ARRAYLANG['TXT_SHOP_DISCOUNT_GROUP_NONE'], )
          + $arrArticleGroupName, $selectedId);
    }


    /**
     * Returns an array with all the customer group names
     * indexed by their ID
     *
     * Backend use only.
     * @return  array                 The group name array on success,
     *                                null otherwise
     * @static
     * @author  Reto Kohli <reto.kohli@comvation.com>
     */
    static function getCustomerGroupArray()
    {
        $cx = \Cx\Core\Core\Controller\Cx::instanciate();
        $customerGroups = $cx->getDb()->getEntityManager()->getRepository(
            'Cx\Modules\Shop\Model\Entity\CustomerGroup'
        )->findAll();

        self::$arrCustomerGroup = array();
        foreach ($customerGroups as $customerGroup) {
            $group_id = $customerGroup->getId();
            $strName = $customerGroup->getName();
            self::$arrCustomerGroup[$group_id] = array(
                'name' => $strName,
            );
        }
        return self::$arrCustomerGroup;
    }


    /**
     * Returns an array with all the article group names indexed by their ID
     *
     * Backend use only.
     * @return  array                 The group name array on success,
     *                                null otherwise
     * @static
     * @author  Reto Kohli <reto.kohli@comvation.com>
     */
    static function getArticleGroupArray()
    {
        $cx = \Cx\Core\Core\Controller\Cx::instanciate();
        $articleGroups = $cx->getDb()->getEntityManager()->getRepository(
            'Cx\Modules\Shop\Model\Entity\ArticleGroup'
        )->findAll();

        self::$arrArticleGroup = array();
        foreach ($articleGroups as $articleGroup) {
            $group_id = $articleGroup->getId();
            $strName = $articleGroup->getName();
            self::$arrArticleGroup[$group_id] = array(
                'name' => $strName,
            );
        }

        return self::$arrArticleGroup;
    }

    /**
     * Returns a string with the customer group name
     * for the given ID
     *
     * Backend use only.
     * @param   integer   $group_id     The Customer group ID
     * @return  string                  The group name on success,
     *                                  the string for "none" otherwise
     * @static
     * @author  Reto Kohli <reto.kohli@comvation.com>
     */
    static function getCustomerGroupName($group_id)
    {
        global $_ARRAYLANG;

        $customerGroups = static::getCustomerGroupArray();
        if (isset($customerGroups[$group_id])) {
            return $customerGroups[$group_id]['name'];
        }
        return $_ARRAYLANG['TXT_SHOP_DISCOUNT_GROUP_NONE'];
    }


    /**
     * Returns an array with the customer group names, indexed by ID
     *
     * Backend use only.
     * Note that the array returned may be empty.
     * @return  array                   The group name array on success,
     *                                  null otherwise
     * @static
     * @author  Reto Kohli <reto.kohli@comvation.com>
     */
    static function getCustomerGroupNameArray()
    {
        $customerGroups = static::getCustomerGroupArray();

        $arrGroupname = array();
        foreach ($customerGroups as $id => $arrGroup) {
            $arrGroupname[$id] = $arrGroup['name'];
        }
        return $arrGroupname;
    }

    /**
     * Tries to fix any database problems
     * @return  boolean           False.  Always.
     * @throws  Cx\Lib\Update_DatabaseException
     */
    static function errorHandler()
    {
//die("Discount::errorHandler(): Disabled!<br />");
// Discount
        \Text::errorHandler();

        $table_name = DBPREFIX.'module_shop_article_group';
        $table_structure = array(
            'id' => array('type' => 'INT(10)', 'unsigned' => true, 'notnull' => true, 'auto_increment' => true, 'primary' => true),
        );
        $table_index = array();
//\DBG::activate(DBG_DB);
        if (!\Cx\Lib\UpdateUtil::table_exist($table_name)) {
            \Cx\Lib\UpdateUtil::table($table_name, $table_structure, $table_index);
        }
        $default_lang_id = \FWLanguage::getDefaultLangId();
        if (\Cx\Lib\UpdateUtil::column_exist($table_name, 'name')) {
            \Text::deleteByKey('Shop', self::TEXT_NAME_GROUP_ARTICLE);
            $query = "
                SELECT `id`, `name`
                  FROM `$table_name`";
            $objResult = \Cx\Lib\UpdateUtil::sql($query);
            if (!$objResult) {
                throw new \Cx\Lib\Update_DatabaseException(
                   "Failed to query article group names", $query);
            }
            while (!$objResult->EOF) {
                $group_id = $objResult->fields['id'];
                $name = $objResult->fields['name'];
                if (!\Text::replace($group_id, $default_lang_id, 'Shop',
                    self::TEXT_NAME_GROUP_ARTICLE, $name)) {
                    throw new \Cx\Lib\Update_DatabaseException(
                       "Failed to migrate article group names");
                }
                $objResult->MoveNext();
            }
            \Cx\Lib\UpdateUtil::table($table_name, $table_structure, $table_index);
        }

        $table_name = DBPREFIX.'module_shop_customer_group';
        $table_structure = array(
            'id' => array('type' => 'INT(10)', 'unsigned' => true, 'notnull' => true, 'auto_increment' => true, 'primary' => true),
        );
        $table_index = array();
        if (!\Cx\Lib\UpdateUtil::table_exist($table_name)) {
            \Cx\Lib\UpdateUtil::table($table_name, $table_structure, $table_index);
        }
        if (\Cx\Lib\UpdateUtil::column_exist($table_name, 'name')) {
            \Text::deleteByKey('Shop', self::TEXT_NAME_GROUP_CUSTOMER);
            $query = "
                SELECT `id`, `name`
                  FROM `$table_name`";
            $objResult = \Cx\Lib\UpdateUtil::sql($query);
            if (!$objResult) {
                throw new \Cx\Lib\Update_DatabaseException(
                   "Failed to query customer group names", $query);
            }
            while (!$objResult->EOF) {
                $group_id = $objResult->fields['id'];
                $name = $objResult->fields['name'];
                if (!\Text::replace($group_id, $default_lang_id, 'Shop',
                    self::TEXT_NAME_GROUP_CUSTOMER, $name)) {
                throw new \Cx\Lib\Update_DatabaseException(
                   "Failed to migrate customer group names");
                }
                $objResult->MoveNext();
            }
            \Cx\Lib\UpdateUtil::table($table_name, $table_structure, $table_index);
        }

        $table_name = DBPREFIX.'module_shop_rel_discount_group';
        $table_structure = array(
            'customer_group_id' => array('type' => 'int(10)', 'unsigned' => true, 'notnull' => true, 'default' => 0, 'primary' => true),
            'article_group_id' => array('type' => 'int(10)', 'unsigned' => true, 'notnull' => true, 'default' => 0, 'primary' => true),
            'rate' => array('type' => 'decimal(9,2)', 'notnull' => true, 'default' => '0.00'),
        );
        $table_index = array();
        if (!\Cx\Lib\UpdateUtil::table_exist($table_name)) {
            \Cx\Lib\UpdateUtil::table($table_name, $table_structure, $table_index);
        }

        $table_name = DBPREFIX.'module_shop_discountgroup_count_name';
        $table_structure = array(
            'id' => array('type' => 'INT(10)', 'unsigned' => true, 'notnull' => true, 'auto_increment' => true, 'primary' => true),
        );
        $table_index = array();
        if (!\Cx\Lib\UpdateUtil::table_exist($table_name)) {
            \Cx\Lib\UpdateUtil::table($table_name, $table_structure, $table_index);
        }
        if (\Cx\Lib\UpdateUtil::column_exist($table_name, 'name')) {
            \Text::deleteByKey('Shop', self::TEXT_NAME_GROUP_COUNT);
            \Text::deleteByKey('Shop', self::TEXT_UNIT_GROUP_COUNT);
            $query = "
                SELECT `id`, `name`, `unit`
                  FROM `$table_name`";
            $objResult = \Cx\Lib\UpdateUtil::sql($query);
            if (!$objResult) {
                throw new \Cx\Lib\Update_DatabaseException(
                   "Failed to query count group names", $query);
            }
            while (!$objResult->EOF) {
                $group_id = $objResult->fields['id'];
                $name = $objResult->fields['name'];
                $unit = $objResult->fields['unit'];
                if (!\Text::replace($group_id, $default_lang_id, 'Shop',
                    self::TEXT_NAME_GROUP_COUNT, $name)) {
                    throw new \Cx\Lib\Update_DatabaseException(
                       "Failed to migrate count group names");
                }
                if (!\Text::replace($group_id, $default_lang_id, 'Shop',
                    self::TEXT_UNIT_GROUP_COUNT, $unit)) {
                    throw new \Cx\Lib\Update_DatabaseException(
                       "Failed to migrate count group units");
                }
                $objResult->MoveNext();
            }
            \Cx\Lib\UpdateUtil::table($table_name, $table_structure, $table_index);
        }

        $table_name = DBPREFIX.'module_shop_discountgroup_count_rate';
        $table_structure = array(
            'group_id' => array('type' => 'INT(10)', 'unsigned' => true, 'notnull' => true, 'default' => 0, 'primary' => true),
            'count' => array('type' => 'INT(10)', 'unsigned' => true, 'notnull' => true, 'default' => 1, 'primary' => true),
            'rate' => array('type' => 'DECIMAL(5,2)', 'unsigned' => true, 'notnull' => true, 'default' => '0.00'),
        );
        $table_index = array();
        \Cx\Lib\UpdateUtil::table($table_name, $table_structure, $table_index);

        // Always
        return false;
    }

}
