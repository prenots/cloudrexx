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

    /**
     * Deletes all Orders with the given Customer ID
     * @param   integer   $customer_id    The Customer ID
     * @return  boolean                   True on success, false otherwise
     */
    static function deleteByCustomerId($customer_id)
    {
        global $_ARRAYLANG;

        $count = 0;
        $arrOrderId = Orders::getIdArray(
            $count, null, array('customer_id' => $customer_id));
        if ($arrOrderId === false) {
            return \Message::error($_ARRAYLANG['TXT_SHOP_ERROR_CUSTOMER_QUERYING_ORDERS']);
        }
        foreach ($arrOrderId as $order_id) {
            if (!Order::deleteById($order_id)) {
                return \Message::error($_ARRAYLANG['TXT_SHOP_ERROR_CUSTOMER_DELETING_ORDERS']);
            }
        }
        return true;
    }

    /**
     * Returns an array with all placeholders and their values to be
     * replaced in any shop mailtemplate for the given order ID.
     *
     * You only have to set the 'substitution' index value of your MailTemplate
     * array to the array returned.
     * Customer data is not included here.  See {@see Customer::getSubstitutionArray()}.
     * Note that this method is now mostly independent of the current session.
     * The language of the mail template is determined by the browser
     * language range stored with the order.
     * @access  private
     * @static
     * @param   integer $order_id     The order ID
     * @param   boolean $create_accounts  If true, creates User accounts
     *                                    and Coupon codes.  Defaults to true
     * @return  array                 The array with placeholders as keys
     *                                and values from the order on success,
     *                                false otherwise
     */
    static function getSubstitutionArray($order_id, $create_accounts=true)
    {
        global $_ARRAYLANG;
/*
            $_ARRAYLANG['TXT_SHOP_URI_FOR_DOWNLOAD'].":\r\n".
            'http://'.$_SERVER['SERVER_NAME'].
            "/index.php?section=download\r\n";
*/
        $objOrder = Order::getById($order_id);
        if (!$objOrder) {
            // Order not found
            return false;
        }
        $cx = \Cx\Core\Core\Controller\Cx::instanciate();
        $currency = $cx->getDb()->getEntityManager()->getRepository(
            '\Cx\Modules\Shop\Model\Entity\Currency'
        )->find($objOrder->currency_id());

        $lang_id = $objOrder->lang_id();
        $status = $objOrder->status();
        $customer_id = $objOrder->customer_id();
        $customer = Customer::getById($customer_id);
        $payment_id = $objOrder->payment_id();
        $shipment_id = $objOrder->shipment_id();
        $arrSubstitution = array (
            'CUSTOMER_COUNTRY_ID' => $objOrder->billing_country_id(),
            'LANG_ID' => $lang_id,
            'NOW' => date(ASCMS_DATE_FORMAT_DATETIME),
            'TODAY' => date(ASCMS_DATE_FORMAT_DATE),
//            'DATE' => date(ASCMS_DATE_FORMAT_DATE, strtotime($objOrder->date_time())),
            'ORDER_ID' => $order_id,
            'ORDER_ID_CUSTOM' => ShopLibrary::getCustomOrderId($order_id),
// TODO: Use proper localized date formats
            'ORDER_DATE' =>
                date(ASCMS_DATE_FORMAT_DATE,
                    strtotime($objOrder->date_time())),
            'ORDER_TIME' =>
                date(ASCMS_DATE_FORMAT_TIME,
                    strtotime($objOrder->date_time())),
            'ORDER_STATUS_ID' => $status,
            'ORDER_STATUS' => $_ARRAYLANG['TXT_SHOP_ORDER_STATUS_'.$status],
            'MODIFIED' =>
                date(ASCMS_DATE_FORMAT_DATETIME,
                    strtotime($objOrder->modified_on())),
            'REMARKS' => $objOrder->note(),
            'ORDER_SUM' => sprintf('% 9.2f', $objOrder->sum()),
            'CURRENCY' => $currency->getCode(),
        );
        $arrSubstitution += $customer->getSubstitutionArray();
        if ($shipment_id) {
            $arrSubstitution += array (
                'SHIPMENT' => array(0 => array(
                    'SHIPMENT_NAME' => sprintf('%-40s', Shipment::getShipperName($shipment_id)),
                    'SHIPMENT_PRICE' => sprintf('% 9.2f', $objOrder->shipment_amount()),
                )),
// Unused
//                'SHIPMENT_ID' => $objOrder->shipment_id(),
                'SHIPPING_ADDRESS' => array(0 => array(
                    'SHIPPING_COMPANY' => $objOrder->company(),
                    'SHIPPING_TITLE' =>
                        $_ARRAYLANG['TXT_SHOP_'.strtoupper($objOrder->gender())],
                    'SHIPPING_FIRSTNAME' => $objOrder->firstname(),
                    'SHIPPING_LASTNAME' => $objOrder->lastname(),
                    'SHIPPING_ADDRESS' => $objOrder->address(),
                    'SHIPPING_ZIP' => $objOrder->zip(),
                    'SHIPPING_CITY' => $objOrder->city(),
                    'SHIPPING_COUNTRY_ID' => $objOrder->country_id(),
                    'SHIPPING_COUNTRY' => \Cx\Core\Country\Controller\Country::getNameById(
                        $objOrder->country_id()),
                    'SHIPPING_PHONE' => $objOrder->phone(),
                )),
            );
        }
        if ($payment_id) {
            $arrSubstitution += array (
                'PAYMENT' => array(0 => array(
                    'PAYMENT_NAME' => sprintf('%-40s', Payment::getNameById($payment_id)),
                    'PAYMENT_PRICE' => sprintf('% 9.2f', $objOrder->payment_amount()),
                )),
            );
        }
        $arrItems = $objOrder->getItems(false);
        if (!$arrItems) {
            \Message::warning($_ARRAYLANG['TXT_SHOP_ORDER_WARNING_NO_ITEM']);
        }
        // Deduct Coupon discounts, either from each Product price, or
        // from the items total.  Mind that the Coupon has already been
        // stored with the Order, but not redeemed yet.  This is done
        // in this method, but only if $create_accounts is true.
        $coupon_code = NULL;
        $coupon_amount = 0;
        $customerCouponRepo = $cx->getDb()->getEntityManager()->getRepository(
            'Cx\Modules\Shop\Model\Entity\RelCustomerCoupon'
        );
        $couponRepo = $cx->getDb()->getEntityManager()->getRepository(
            'Cx\Modules\Shop\Model\Entity\DiscountCoupon'
        );
        $objCustomerCoupon = $customerCouponRepo->findOneBy(array('orderId' => $order_id));

        if ($objCustomerCoupon) {
            $coupon_code = $objCustomerCoupon->getCode();
        }
        $orderItemCount = 0;
        $total_item_price = 0;
        // Suppress Coupon messages (see Coupon::available())
        \Message::save();
        foreach ($arrItems as $item) {
            $product_id = $item['product_id'];
            $objProduct = Product::getById($product_id);
            if (!$objProduct) {
//die("Product ID $product_id not found");
                continue;
            }
//DBG::log("Orders::getSubstitutionArray(): Item: Product ID $product_id");
            $product_name = substr($item['name'], 0, 40);
            $item_price = $item['price'];
            $quantity = $item['quantity'];
// TODO: Add individual VAT rates for Products
//            $orderItemVatPercent = $objResultItem->fields['vat_percent'];
            // Decrease the Product stock count,
            // applies to "real", shipped goods only
            $objProduct->decreaseStock($quantity);
            $product_code = $objProduct->code();
            // Pick the order items attributes
            $str_options = '';
            $optionList = array();
            // Any attributes?
            if ($item['attributes']) {
                $str_options = '  '; // '[';
                $attribute_name_previous = '';
                foreach ($item['attributes'] as $attribute_name => $arrAttribute) {
                    $optionValues = array();
//DBG::log("Attribute /$attribute_name/ => ".var_export($arrAttribute, true));
// NOTE: The option price is optional and may be left out
                    foreach ($arrAttribute as $arrOption) {
                        $option = array();
                        $option_name = $arrOption['name'];
                        $option_price = $arrOption['price'];
                        $item_price += $option_price;
                        // Recognize the names of uploaded files,
                        // verify their presence and use the original name
                        $option_name_stripped = ShopLibrary::stripUniqidFromFilename($option_name);
                        $path = Order::UPLOAD_FOLDER.$option_name;
                        if (   $option_name != $option_name_stripped
                            && \File::exists($path)) {
                            $option_name = $option_name_stripped;
                        }
                        if ($attribute_name != $attribute_name_previous) {
                            if ($attribute_name_previous) {
                                $str_options .= '; ';
                            }
                            $str_options .= $attribute_name.': '.$option_name;
                            $attribute_name_previous = $attribute_name;
                        } else {
                            $str_options .= ', '.$option_name;
                        }
                        $option['PRODUCT_OPTIONS_VALUE'] = $option_name;
// TODO: Add proper formatting with sprintf() and language entries
                        if ($option_price != 0) {
                            $str_options .=
                                ' './/' ('.
                                \Cx\Modules\Shop\Controller\CurrencyController::formatPrice($option_price).
                                ' '.\Cx\Modules\Shop\Controller\CurrencyController::getActiveCurrencyCode()
//                                .')'
                                ;
                            $option['PRODUCT_OPTIONS_PRICE'] = \Cx\Modules\Shop\Controller\CurrencyController::formatPrice($option_price);
                            $option['PRODUCT_OPTIONS_CURRENCY'] = \Cx\Modules\Shop\Controller\CurrencyController::getActiveCurrencyCode();
                        }
                        $optionValues[] = $option;
                    }
                    $optionList[] = array(
                        'PRODUCT_OPTIONS_NAME' => $attribute_name,
                        'PRODUCT_OPTIONS_VALUES' => $optionValues,
                    );
                }
//                $str_options .= ']';
            }
            // Product details
            $arrProduct = array(
                'PRODUCT_ID' => $product_id,
                'PRODUCT_CODE' => $product_code,
                'PRODUCT_QUANTITY' => $quantity,
                'PRODUCT_TITLE' => $product_name,
                'PRODUCT_OPTIONS' => $str_options,
                'PRODUCT_OPTION_LIST' => $optionList,
                'PRODUCT_ITEM_PRICE' => sprintf('% 9.2f', $item_price),
                'PRODUCT_TOTAL_PRICE' => sprintf('% 9.2f', $item_price*$quantity),
            );
//DBG::log("Orders::getSubstitutionArray($order_id, $create_accounts): Adding article: ".var_export($arrProduct, true));
            $orderItemCount += $quantity;
            $total_item_price += $item_price*$quantity;
            if ($create_accounts) {
                // Add an account for every single instance of every Product
                for ($instance = 1; $instance <= $quantity; ++$instance) {
                    $validity = 0; // Default to unlimited validity
                    // In case there are protected downloads in the cart,
                    // collect the group IDs
                    $arrUsergroupId = array();
                    if ($objProduct->distribution() == 'download') {
                        $usergroupIds = $objProduct->usergroup_ids();
                        if ($usergroupIds != '') {
                            $arrUsergroupId = explode(',', $usergroupIds);
                            $validity = $objProduct->weight();
                        }
                    }
                    // create an account that belongs to all collected
                    // user groups, if any.
                    if (count($arrUsergroupId) > 0) {
                        // The login names are created separately for
                        // each product instance
                        $username =
                            self::usernamePrefix.
                            "_${order_id}_${product_id}_${instance}";
                        $userEmail =
                            $username.'-'.$arrSubstitution['CUSTOMER_EMAIL'];
                        $userpass = \User::make_password();
                        $objUser = new \User();
                        $objUser->setUsername($username);
                        $objUser->setPassword($userpass);
                        $objUser->setEmail($userEmail);
                        $objUser->setAdminStatus(false);
                        $objUser->setActiveStatus(true);
                        $objUser->setGroups($arrUsergroupId);
                        $objUser->setValidityTimePeriod($validity);
                        $objUser->setFrontendLanguage(FRONTEND_LANG_ID);
                        $objUser->setBackendLanguage(FRONTEND_LANG_ID);
                        $objUser->setProfile(array(
                            'firstname' => array(0 => $arrSubstitution['CUSTOMER_FIRSTNAME']),
                            'lastname' => array(0 => $arrSubstitution['CUSTOMER_LASTNAME']),
                            'company' => array(0 => $arrSubstitution['CUSTOMER_COMPANY']),
                            'address' => array(0 => $arrSubstitution['CUSTOMER_ADDRESS']),
                            'zip' => array(0 => $arrSubstitution['CUSTOMER_ZIP']),
                            'city' => array(0 => $arrSubstitution['CUSTOMER_CITY']),
                            'country' => array(0 => $arrSubstitution['CUSTOMER_COUNTRY_ID']),
                            'phone_office' => array(0 => $arrSubstitution['CUSTOMER_PHONE']),
                            'phone_fax' => array(0 => $arrSubstitution['CUSTOMER_FAX']),
                        ));
                        if (!$objUser->store()) {
                            \Message::error(implode(
                                '<br />', $objUser->getErrorMsg()));
                            return false;
                        }
                        if (empty($arrProduct['USER_DATA']))
                            $arrProduct['USER_DATA'] = array();
                        $arrProduct['USER_DATA'][] = array(
                            'USER_NAME' => $username,
                            'USER_PASS' => $userpass,
                        );
                    }
//echo("Instance $instance");
                    if ($objProduct->distribution() == 'coupon') {
                        if (empty($arrProduct['COUPON_DATA']))
                            $arrProduct['COUPON_DATA'] = array();
//DBG::log("Orders::getSubstitutionArray(): Getting code");
                        $code = \Cx\Modules\Shop\Controller\DiscountCouponController::getNewCode();

                        $newCoupon = new \Cx\Modules\Shop\Model\Entity\DiscountCoupon();
                        $newCoupon->setCode($code);
                        $newCoupon->setDiscountAmount($item_price);
                        $newCoupon->setGlobal(true);
                        $newCoupon->setUses(1e10);

                        $em = $cx->getDb()->getEntityManager();
                        $em->persist($newCoupon);
                        $em->flush();

                        $arrProduct['COUPON_DATA'][] = array(
                            'COUPON_CODE' => $code
                        );
                    }
                }
                // Redeem the *product* Coupon, if possible for the Product
                if ($coupon_code) {
                    $objCoupon = $couponRepo->available($coupon_code,
                        $item_price*$quantity, $customer_id, $product_id,
                        $payment_id);
                    if ($objCoupon) {
                        $coupon_code = NULL;
                        $coupon_amount = $objCoupon->getDiscountAmountOrRate(
                            $item_price, $customer_id);
                        if ($create_accounts) {
                            $objCoupon->redeem($order_id, $customer_id,
                                $item_price*$quantity);
                        }
                    }
//\DBG::log("Orders::getSubstitutionArray(): Got Product Coupon $coupon_code");
                }
            }
            if (empty($arrSubstitution['ORDER_ITEM']))
                $arrSubstitution['ORDER_ITEM'] = array();
            $arrSubstitution['ORDER_ITEM'][] = $arrProduct;
        }
        $arrSubstitution['ORDER_ITEM_SUM'] =
            sprintf('% 9.2f', $total_item_price);
        $arrSubstitution['ORDER_ITEM_COUNT'] = sprintf('% 4u', $orderItemCount);
        // Redeem the *global* Coupon, if possible for the Order
        if ($coupon_code) {
            $objCoupon = $couponRepo->available($coupon_code,
                $total_item_price, $customer_id, null, $payment_id);
            if ($objCoupon) {
                $coupon_amount = $objCoupon->getDiscountAmountOrRate(
                    $total_item_price, $customer_id);
                if ($create_accounts) {
                    $objCoupon->redeem($order_id, $customer_id, $total_item_price);
                }
            }
        }
        \Message::restore();
        // Fill in the Coupon block with proper discount and amount
        if ($objCoupon) {
            $coupon_code = $objCoupon->getCode();
//\DBG::log("Orders::getSubstitutionArray(): Coupon $coupon_code, amount $coupon_amount");
        }
        if ($coupon_amount) {
//\DBG::log("Orders::getSubstitutionArray(): Got Order Coupon $coupon_code");
            $arrSubstitution['DISCOUNT_COUPON'][] = array(
                'DISCOUNT_COUPON_CODE' => sprintf('%-40s', $coupon_code),
                'DISCOUNT_COUPON_AMOUNT' => sprintf('% 9.2f', -$coupon_amount),
            );
        } else {
//\DBG::log("Orders::getSubstitutionArray(): No Coupon for Order ID $order_id");
        }
        if (Vat::isEnabled()) {
//DBG::log("Orders::getSubstitutionArray(): VAT amount: ".$objOrder->vat_amount());
            $arrSubstitution['VAT'] = array(0 => array(
                'VAT_TEXT' => sprintf('%-40s',
                    (Vat::isIncluded()
                        ? $_ARRAYLANG['TXT_SHOP_VAT_PREFIX_INCL']
                        : $_ARRAYLANG['TXT_SHOP_VAT_PREFIX_EXCL']
                    )),
                'VAT_PRICE' => $objOrder->vat_amount(),
            ));
        }
        return $arrSubstitution;
    }

}
