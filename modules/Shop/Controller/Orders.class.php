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
 * Shop Order Helpers
 * @copyright   CLOUDREXX CMS - CLOUDREXX AG
 * @author      Reto Kohli <reto.kohli@comvation.com>
 * @version     3.0.0
 * @package     cloudrexx
 * @subpackage  module_shop
 */

namespace Cx\Modules\Shop\Controller;

/**
 * Shop Order Helpers
 * @copyright   CLOUDREXX CMS - CLOUDREXX AG
 * @author      Reto Kohli <reto.kohli@comvation.com>
 * @version     3.0.0
 * @package     cloudrexx
 * @subpackage  module_shop
 */
class Orders
{
    const usernamePrefix = 'shop_customer';

    /**
     * Returns an array of Order IDs for the given parameters
     *
     * The $filter array may include zero or more of the following field
     * names as indices, plus some value or array of values that will be tested:
     * - id             An Order ID or array of IDs
     * - customer_id    A Customer ID or array of IDs
     * - status         An Order status or array of status
     * - term           An arbitrary search term.  Matched against the fields
     *                  company, firstname, lastname, address, city,
     *                  phone, and email (shipping address).
     * - letter         A letter (or string) that will be matched at the
     *                  beginning of the fields company, firstname, or lastname.
     * Add more fields when needed.
     *
     * The $order parameter value may be one of the table field names plus
     * an optional SQL order direction.
     * Add Backticks to the table and field names as required.
     * $limit defaults to 1000 if it is empty or greater.
     * Note that the array returned is empty if no matching Order is found.
     * @param   integer   $count      The actual number of records returned,
     *                                by reference
     * @param   string    $order      The optional sorting order field,
     *                                SQL syntax. Defaults to 'id ASC'
     * @param   array     $filter     The optional array of filter values
     * @param   integer   $offset     The optional zero based offset for the
     *                                results returned.
     *                                Defaults to 0 (zero)
     * @param   integer   $limit      The optional maximum number of results
     *                                to be returned.
     *                                Defaults to 1000
     * @return  array                 The array of Order IDs on success,
     *                                false otherwise
     */
    static function getIdArray(
        &$count, $order=null, $filter=null, $offset=0, $limit=0
    ) {
        global $objDatabase;
//DBG::activate(DBG_ADODB);
//DBG::log("Order::getIdArray(): Order $order");

        $query_id = "SELECT `order`.`id`";
        $query_count = "SELECT COUNT(*) AS `numof_orders`";
        $query_from = "
              FROM `".DBPREFIX."module_shop_orders` AS `order`";
        $query_where = "
             WHERE 1".
              (empty($filter['id'])
                  ? ''
                  : (is_array($filter['id'])
                      ? " AND `order`.`id` IN (".join(',', $filter['id']).")"
                      : " AND `order`.`id`=".intval($filter['id']))).
              (empty($filter['id>'])
                  ? ''
                  : " AND `order`.`id`>".intval($filter['id>'])).
              (empty($filter['customer_id'])
                  ? ''
                  : (is_array($filter['customer_id'])
                      ? " AND `order`.`customer_id` IN (".
                        join(',', $filter['customer_id']).")"
                      : " AND `order`.`customer_id`=".
                        intval($filter['customer_id']))).
              (empty($filter['status'])
                  ? ''
                  // Include status
                  : (is_array($filter['status'])
                      ? " AND `order`.`status` IN (".join(',', $filter['status']).")"
                      : " AND `order`.`status`=".intval($filter['status']))).
              (empty($filter['!status'])
                  ? ''
                  // Exclude status
                  : (is_array($filter['!status'])
                      ? " AND `order`.`status` NOT IN (".join(',', $filter['!status']).")"
                      : " AND `order`.`status`!=".intval($filter['!status']))).
              (empty($filter['date>='])
                  ? ''
                  : " AND `order`.`date_time`>='".
                    addslashes($filter['date>='])."'");
              (empty($filter['date<'])
                  ? ''
                  : " AND `order`.`date_time`<'".
                    addslashes($filter['date<'])."'");

        if (isset($filter['letter'])) {
            $term = addslashes($filter['letter']).'%';
            $query_where .= "
                AND (   `profile`.`company` LIKE '$term'
                     OR `profile`.`firstname` LIKE '$term'
                     OR `profile`.`lastname` LIKE '$term')";
        }
        if (isset($filter['term'])) {
            $term = '%'.addslashes($filter['term']).'%';
            $query_where .= "
                AND (   `user`.`username` LIKE '$term'
                     OR `user`.`email` LIKE '$term'
                     OR `profile`.`company` LIKE '$term'
                     OR `profile`.`firstname` LIKE '$term'
                     OR `profile`.`lastname` LIKE '$term'
                     OR `profile`.`address` LIKE '$term'
                     OR `profile`.`city` LIKE '$term'
                     OR `profile`.`phone_private` LIKE '$term'
                     OR `profile`.`phone_fax` LIKE '$term'
                     OR `order`.`company` LIKE '$term'
                     OR `order`.`firstname` LIKE '$term'
                     OR `order`.`lastname` LIKE '$term'
                     OR `order`.`address` LIKE '$term'
                     OR `order`.`city` LIKE '$term'
                     OR `order`.`phone` LIKE '$term'
                     OR `order`.`note` LIKE '$term')";
        }

// NOTE: For customized Order IDs
        // Check if the user wants to search the pseudo "account names".
        // These may be customized with pre- or postfixes.
        // Adapt the regex as needed.
//        $arrMatch = array();
//        $searchAccount = '';
//            (preg_match('/^A-(\d{1,2})-?8?(\d{0,2})?/i', $term, $arrMatch)
//                ? "OR (    `order`.`date_time` LIKE '__".$arrMatch[1]."%'
//                       AND `order`.`id` LIKE '%".$arrMatch[2]."')"
//                : ''
//            );

        // Need to join the User for filter and sorting.
        // Note: This might be optimized, so the join only occurs when
        // searching or sorting by Customer name.
        $query_join = "
            LEFT JOIN `".DBPREFIX."access_users` AS `user`
              ON `order`.`customer_id`=`user`.`id`
            LEFT JOIN `".DBPREFIX."access_user_profile` AS `profile`
              ON `user`.`id`=`profile`.`user_id`";
        // The order *SHOULD* contain the direction.  Defaults to DESC here!
        $direction = (preg_match('/\sASC$/i', $order) ? 'ASC' : 'DESC');
        if (preg_match('/customer_name/', $order)) {
            $order =
                "`profile`.`lastname` $direction, ".
                "`profile`.`firstname` $direction";
        }
        $query_order = ($order ? " ORDER BY $order" : '');
        $count = 0;
        // Some sensible hardcoded limit to prevent memory problems
        $limit = intval($limit);
        if ($limit < 0 || $limit > 1000) $limit = 1000;
        // Get the IDs of the Orders according to the offset and limit
//DBG::activate(DBG_ADODB);
        $objResult = $objDatabase->SelectLimit(
            $query_id.$query_from.$query_join.$query_where.$query_order,
            $limit, intval($offset));
//DBG::deactivate(DBG_ADODB);
        if (!$objResult) return Order::errorHandler();
        $arrId = array();
        while (!$objResult->EOF) {
            $arrId[] = $objResult->fields['id'];
            $objResult->MoveNext();
        }
//DBG::log("Order::getIdArray(): limit $limit, count $count, got ".count($arrId)." IDs: ".var_export($arrId, true));
//DBG::deactivate(DBG_ADODB);
        // Get the total count of matching Orders, set $count
        $objResult = $objDatabase->Execute(
            $query_count.$query_from.$query_join.$query_where);
        if (!$objResult) return Order::errorHandler();
        $count = $objResult->fields['numof_orders'];
//DBG::log("Count: $count");
        // Return the array of IDs
        return $arrId;
    }
}
