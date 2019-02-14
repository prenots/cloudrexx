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
