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
 * Coupon
 *
 * Manages and processes coupon codes for various kinds of discounts
 * @copyright   CLOUDREXX CMS - CLOUDREXX AG
 * @author      Reto Kohli <reto.kohli@comvation.ch>
 * @access      public
 * @version     3.0.0
 * @since       3.0.0
 * @package     cloudrexx
 * @subpackage  module_shop
 */

namespace Cx\Modules\Shop\Controller;

/**
 * Coupon
 *
 * Manages and processes coupon codes for various kinds of discounts
 * @copyright   CLOUDREXX CMS - CLOUDREXX AG
 * @author      Reto Kohli <reto.kohli@comvation.ch>
 * @access      public
 * @version     3.0.0
 * @since       3.0.0
 * @package     cloudrexx
 * @subpackage  module_shop
 */
class Coupon
{

    /**
     * Redeem the given coupon code
     *
     * Updates the database, if applicable.
     * Mind that you *MUST* decide which amount (Order or Product) to provide:
     *  - the Product amount if the Coupon has a non-empty Product ID, or
     *  - the Order amount otherwise
     * Provide a zero $uses count (but not null!) when you are storing the
     * Order.  Omit it, or set it to 1 (one) when the Order is complete.
     * The latter is usually the case on the success page, after the Customer
     * has returned to the Shop after paying.
     * Mind that the amount cannot be changed once the record has been
     * created, so only the use count will ever be updated.
     * $uses is never interpreted as anything other than 0 or 1!
     * @param   integer   $order_id         The Order ID
     * @param   integer   $customer_id      The Customer ID
     * @param   double    $amount           The Order- or the Product amount
     *                                      (if $this->product_id is non-empty)
     * @param   integer   $uses             The redeem count.  Set to 0 (zero)
     *                                      when storing the Order, omit or
     *                                      set to 1 (one) when redeeming
     *                                      Defaults to 1.
     * @return  Coupon                      The Coupon on success,
     *                                      false otherwise
     */
    function redeem($order_id, $customer_id, $amount, $uses=1)
    {
        global $objDatabase;

        // Applicable discount amount
        $amount = $this->getDiscountAmountOrRate($amount);
        $uses = intval((boolean)$uses);

        //Todo Call Redeem in Repo
        // Insert that use
        $query = "
            INSERT `".DBPREFIX."module_shop".MODULE_INDEX."_rel_customer_coupon` (
              `code`, `customer_id`, `order_id`, `count`, `amount`
            ) VALUES (
              '".addslashes($this->code)."', ".intval($customer_id).",
              ".intval($order_id).", $uses, $amount
            )
            ON DUPLICATE KEY UPDATE `count`=$uses";
        if (!$objDatabase->Execute($query)) {
//DBG::log("redeem($order_id, $customer_id, $amount, $uses): ERROR: Failed to add use $uses, amount $amount!");
            return false;
        }
//DBG::log("redeem($order_id, $customer_id, $amount, $uses): Used $uses, amount $amount<br />");
        return $this;
    }

    /**
     * Tries to fix any database related problems
     * @return  boolean     false     Always!
     * @throws  Cx\Lib\Update_DatabaseException
     */
    static function errorHandler()
    {
//die("Coupon::errorHandler(): Disabled");
// Coupon
        // Fix settings first
        ShopSettings::errorHandler();

        $table_name = DBPREFIX.'module_shop_discount_coupon';
        $table_structure = array(
            'id' => array('type' => 'INT(10)', 'auto_increment' => true, 'primary' => true),
            'code' => array('type' => 'varchar(20)', 'default' => '', 'unique' => true),
            'customer_id' => array('type' => 'INT(10)', 'unsigned' => true, 'default' => '0', 'unique' => true),
            'payment_id' => array('type' => 'INT(10)', 'unsigned' => true, 'default' => '0'),
            'product_id' => array('type' => 'INT(10)', 'unsigned' => true, 'default' => '0'),
            'start_time' => array('type' => 'INT(10)', 'unsigned' => true, 'default' => '0'),
            'end_time' => array('type' => 'INT(10)', 'unsigned' => true, 'default' => '0'),
            'uses' => array('type' => 'INT(10)', 'unsigned' => true, 'default' => '0', 'renamefrom' => 'uses_available'),
            'global' => array('type' => 'TINYINT(1)', 'unsigned' => true, 'default' => '0'),
            'minimum_amount' => array('type' => 'decimal(9,2)', 'unsigned' => true, 'default' => '0.00'),
            'discount_amount' => array('type' => 'decimal(9,2)', 'unsigned' => true, 'default' => '0.00'),
            'discount_rate' => array('type' => 'decimal(3,0)', 'unsigned' => true, 'default' => '0'),
        );
        $table_index = array();
        \Cx\Lib\UpdateUtil::table($table_name, $table_structure, $table_index);

        $table_name = DBPREFIX.'module_shop_rel_customer_coupon';
        $table_structure = array(
            'code' => array('type' => 'varchar(20)', 'default' => '', 'primary' => true),
            'customer_id' => array('type' => 'INT(10)', 'unsigned' => true, 'default' => '0', 'primary' => true),
            'order_id' => array('type' => 'INT(10)', 'unsigned' => true, 'default' => '0', 'primary' => true),
            'count' => array('type' => 'INT(10)', 'unsigned' => true, 'default' => '0'),
            'amount' => array('type' => 'DECIMAL(9,2)', 'unsigned' => true, 'default' => '0.00'),
        );
        $table_index = array();
        \Cx\Lib\UpdateUtil::table($table_name, $table_structure, $table_index);

        // Always
        return false;
    }

}
