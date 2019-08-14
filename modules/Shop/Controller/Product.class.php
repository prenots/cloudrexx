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
 * Shop Product class
 * @version     3.0.0
 * @package     cloudrexx
 * @subpackage  module_shop
 * @copyright   CLOUDREXX CMS - CLOUDREXX AG
 * @author      Reto Kohli <reto.kohli@comvation.com>
 */

namespace Cx\Modules\Shop\Controller;

/**
 * Product as available in the Shop.
 *
 * Includes access methods and data layer.
 * Do not, I repeat, do not access private fields, or even try
 * to access the database directly!
 * @version     3.0.0
 * @package     cloudrexx
 * @subpackage  module_shop
 * @copyright   CLOUDREXX CMS - CLOUDREXX AG
 * @author      Reto Kohli <reto.kohli@comvation.com>
 */
class Product
{
    /**
     * Text keys
     */
    const TEXT_NAME  = 'product_name';
    const TEXT_SHORT = 'product_short';
    const TEXT_LONG  = 'product_long';
    const TEXT_CODE  = 'product_code';
    const TEXT_URI   = 'product_uri';
    const TEXT_KEYS  = 'product_keys';

    /**
     * Handles database errors
     *
     * Also migrates text fields to the new structure
     * @return  boolean         False.  Always.
     * @static
     * @throws  Cx\Lib\Update_DatabaseException
     */
    static function errorHandler()
    {
// Product
        // Fix the Text, Discount, and Manufacturer tables first
        \Text::errorHandler();
//        Discount::errorHandler(); // Called by Customer::errorHandler();
        $cx = \Cx\Core\Core\Controller\Cx::instanciate();
        $manufacturer = $cx->getDb()->getEntityManager()->getRepository(
            '\Cx\Modules\Shop\Model\Entity\Manufacturer'
        );
        $manufacturer->errorHandler();

        $table_name = DBPREFIX.'module_shop_products';
        $table_structure = array(
            'id' => array('type' => 'INT(10)', 'unsigned' => true, 'auto_increment' => true, 'primary' => true),
            'normalprice' => array('type' => 'DECIMAL(9,2)', 'default' => '0.00'),
            'resellerprice' => array('type' => 'DECIMAL(9,2)', 'default' => '0.00'),
            'discountprice' => array('type' => 'DECIMAL(9,2)', 'default' => '0.00'),
            'discount_active' => array('type' => 'TINYINT(1)', 'unsigned' => true, 'default' => '0', 'renamefrom' => 'is_special_offer'),
            'stock' => array('type' => 'INT(10)', 'default' => '10'),
            'stock_visible' => array('type' => 'TINYINT(1)', 'unsigned' => true, 'default' => '1', 'renamefrom' => 'stock_visibility'),
            'active' => array('type' => 'TINYINT(1)', 'unsigned' => true, 'default' => '1', 'renamefrom' => 'status'),
            'b2b' => array('type' => 'TINYINT(1)', 'unsigned' => true, 'default' => '1'),
            'b2c' => array('type' => 'TINYINT(1)', 'unsigned' => true, 'default' => '1'),
            'date_start' => array('type' => 'TIMESTAMP', 'default' => '0000-00-00 00:00:00', 'renamefrom' => 'startdate'),
            'date_end' => array('type' => 'TIMESTAMP', 'default' => '0000-00-00 00:00:00', 'renamefrom' => 'enddate'),
            'weight' => array('type' => 'INT(10)', 'unsigned' => true, 'notnull' => false, 'default' => null),
            'vat_id' => array('type' => 'INT(10)', 'unsigned' => true, 'notnull' => false, 'default' => null),
            'manufacturer_id' => array('type' => 'INT(10)', 'unsigned' => true, 'notnull' => false, 'default' => null, 'renamefrom' => 'manufacturer'),
            'group_id' => array('type' => 'INT(10)', 'unsigned' => true, 'notnull' => false, 'default' => null),
            'article_id' => array('type' => 'INT(10)', 'unsigned' => true, 'notnull' => false, 'default' => null),
            'ord' => array('type' => 'INT(10)', 'default' => '0', 'renamefrom' => 'sort_order'),
            'distribution' => array('type' => 'VARCHAR(16)', 'default' => '', 'renamefrom' => 'handler'),
            'picture' => array('type' => 'VARCHAR(4096)', 'notnull' => false, 'default' => null),
            'flags' => array('type' => 'VARCHAR(4096)', 'notnull' => false, 'default' => null),
            'minimum_order_quantity' => array('type' => 'INT(10)', 'unsigned' => false, 'default' => '0'),
// Obsolete:
//`property1` varchar(100) COLLATE utf8_unicode_ci DEFAULT '',
//`property2` varchar(100) COLLATE utf8_unicode_ci DEFAULT '',
//`manufacturer_url` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
        );
        $table_index =  array(
            'group_id' => array('fields' => array('group_id')),
            'article_id' => array('fields' => array('article_id')),
            'flags' => array('fields' => array('flags'), 'type' => 'FULLTEXT', ),
        );
        $default_lang_id = \FWLanguage::getDefaultLangId();
        if (\Cx\Lib\UpdateUtil::table_exist($table_name)) {
            if (\Cx\Lib\UpdateUtil::column_exist($table_name, 'title')) {
                // Migrate all Product strings to the Text table first
                \Text::deleteByKey('Shop', self::TEXT_NAME);
                \Text::deleteByKey('Shop', self::TEXT_SHORT);
                \Text::deleteByKey('Shop', self::TEXT_LONG);
                \Text::deleteByKey('Shop', self::TEXT_CODE);
                \Text::deleteByKey('Shop', self::TEXT_URI);
                \Text::deleteByKey('Shop', self::TEXT_KEYS);
                $query = "
                    SELECT `id`, `title`, `shortdesc`, `description`,
                           `product_id`, `external_link`, `keywords`
                      FROM `$table_name`";
                $objResult = \Cx\Lib\UpdateUtil::sql($query);
                if (!$objResult) {
                    throw new \Cx\Lib\Update_DatabaseException(
                        "Failed to query Product strings", $query);
                }
                while (!$objResult->EOF) {
                    $id = $objResult->fields['id'];
                    $name = $objResult->fields['title'];
                    if (!\Text::replace($id, $default_lang_id, 'Shop',
                        self::TEXT_NAME, $name)) {
                        throw new \Cx\Lib\Update_DatabaseException(
                            "Failed to migrate Product name '$name'");
                    }
                    $short = $objResult->fields['shortdesc'];
                    if (!\Text::replace($id, $default_lang_id, 'Shop',
                        self::TEXT_SHORT, $short)) {
                        throw new \Cx\Lib\Update_DatabaseException(
                            "Failed to migrate Product short '$short'");
                    }
                    $long = $objResult->fields['description'];
                    if (!\Text::replace($id, $default_lang_id, 'Shop',
                        self::TEXT_LONG, $long)) {
                        throw new \Cx\Lib\Update_DatabaseException(
                            "Failed to migrate Product long '$long'");
                    }
                    $code = $objResult->fields['product_id'];
                    if (!\Text::replace($id, $default_lang_id, 'Shop',
                        self::TEXT_CODE, $code)) {
                        throw new \Cx\Lib\Update_DatabaseException(
                            "Failed to migrate Product code '$code'");
                    }
                    $uri = $objResult->fields['external_link'];
                    if (!\Text::replace($id, $default_lang_id, 'Shop',
                        self::TEXT_URI, $uri)) {
                        throw new \Cx\Lib\Update_DatabaseException(
                            "Failed to migrate Product uri '$uri'");
                    }
                    $keys = $objResult->fields['keywords'];
                    if (!\Text::replace($id, $default_lang_id, 'Shop',
                        self::TEXT_KEYS, $keys)) {
                        throw new \Cx\Lib\Update_DatabaseException(
                            "Failed to migrate Product keys '$keys'");
                    }
                    $objResult->MoveNext();
                }
            }
        }
        \Cx\Lib\UpdateUtil::table($table_name, $table_structure, $table_index);

        // Also fix Customer and some related tables
        Customer::errorHandler();

        // Always
        return false;
    }

}
