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
    const USES_UNLIMITED = 1e10;

    /**
     * This ensures that only one error message per type is shown
     * @var array
     */
    protected static $hasMessage = array();

    /**
     * The Coupon code
     * @var   string
     */
    protected $code = '';
    /**
     * Get or set the Coupon code
     * @param   string    $code       The optional new Coupon code
     * @return  string                The Coupon code
     */
    function code($code=null)
    {
        if (isset($code)) $this->code = $code;
        return $this->code;
    }

    /**
     * The minimum amount for which this Coupon is applicable
     *
     * Includes the Product prices only, possibly already discounted
     * @var   double
     */
    protected $minimum_amount = 0;
    /**
     * Get or set the minimum amount
     * @param   double    $minimum_amount The optional minimum amount
     * @return  double                    The minimum amount
     */
    function minimum_amount($minimum_amount=null)
    {
        if (isset($minimum_amount)) $this->minimum_amount = $minimum_amount;
        return $this->minimum_amount;
    }

    /**
     * The discount rate
     *
     * If this is non-empty, the discount amount *MUST* be zero.
     * @var   double
     */
    protected $discount_rate = 0;
    /**
     * Get or set the discount rate
     * @param   double    $discount_rate  The optional discount rate
     * @return  double                    The discount rate
     */
    function discount_rate($discount_rate=null)
    {
        if (isset($discount_rate)) $this->discount_rate = $discount_rate;
        return $this->discount_rate;
    }

    /**
     * The discount amount
     *
     * If this is non-empty, the discount rate *MUST* be zero.
     * @var   double
     */
    protected $discount_amount = 0;
    /**
     * Get or set the discount amount
     * @param   double    $discount_amount  The optional discount amount
     * @return  double                      The discount amount
     */
    function discount_amount($discount_amount=null)
    {
        if (isset($discount_amount)) $this->discount_amount = $discount_amount;
        return $this->discount_amount;
    }

    /**
     * The validity period start time in time() format
     * @var   integer
     */
    protected $start_time = 0;
    /**
     * Get or set the start time
     * @param   integer   $start_time   The optional start time
     * @return  integer                 The start time
     */
    function start_time($start_time=null)
    {
        if (isset($start_time)) $this->start_time = $start_time;
        return $this->start_time;
    }

    /**
     * The validity period end time in time() format
     * @var   integer
     */
    protected $end_time = 0;
    /**
     * Get or set the end time
     * @param   integer   $end_time     The optional end time
     * @return  integer                 The end time
     */
    function end_time($end_time=null)
    {
        if (isset($end_time)) $this->end_time = $end_time;
        return $this->end_time;
    }

    /**
     * The number of uses available
     *
     * This is always initialized to the correct value for any customer,
     * if applicable.
     * Notes:
     *  - For general per-customer Coupons, this value never
     *    changes.  You have to subtract the number of times
     *    it has been used by each Customer.
     *  - For personal Customer Coupons and global Coupons,
     *    it is decremented on each use.
     * @var   integer
     */
    protected $uses = 1;
    /**
     * Get or set the uses available
     * @param   integer   $uses   The optional uses available
     * @return  integer                     The uses available
     */
    function uses($uses=null)
    {
        if (isset($uses)) $this->uses = $uses;
        return $this->uses;
    }

    /**
     * If true, the Coupon is globally valid for any registered or
     * non-registered Customer
     * @var   boolean
     */
    protected $global = true;
    /**
     * Get or set the global flag
     * @param   boolean   $global           The optional global flag
     * @return  boolean                     The global flag
     */
    function is_global($global=null)
    {
        if (isset($global)) $this->global = (boolean)$global;
        return $this->global;
    }

    /**
     * The Customer ID for which the Coupon is valid
     *
     * If empty, it is valid for all Customers
     * @var   integer
     */
    protected $customer_id = 0;
    /**
     * Get or set the Customer ID
     * @param   integer   $customer_id      The optional Customer ID
     * @return  integer                     The Customer ID
     */
    function customer_id($customer_id=null)
    {
        if (isset($customer_id)) $this->customer_id = intval($customer_id);
        return $this->customer_id;
    }

    /**
     * The Product ID to which this Coupon applies
     *
     * If empty, it does not apply to any Product in particular, but
     * to any Product in the order.
     * @var   integer
     */
    protected $product_id = 0;
    /**
     * Get or set the Product ID
     * @param   integer   $product_id       The optional Product ID
     * @return  integer                     The Product ID
     */
    function product_id($product_id=null)
    {
        if (isset($product_id)) $this->product_id = $product_id;
        return $this->product_id;
    }

    /**
     * The Payment ID to which this Coupon applies
     *
     * If non-empty, it only applies when the corresponding Payment is selected
     * @var   integer
     */
    protected $payment_id = 0;
    /**
     * Get or set the Payment ID
     * @param   integer   $payment_id       The optional Payment ID
     * @return  integer                     The Payment ID
     */
    function payment_id($payment_id=null)
    {
        if (isset($payment_id)) $this->payment_id = $payment_id;
        return $this->payment_id;
    }

    /**
     * The number of times the code has been used with an Order
     *
     * This is always initialized to the correct value for the customer,
     * if applicable.
     * @var   integer
     */
    protected $used = null;
    /**
     * Get the number of times the code has been used
     * @return  integer                     The number of uses made
     */
    function used()
    {
        return $this->used;
    }


    /**
     * Returns the index for this Coupon
     *
     * Returns NULL iff the code is empty
     * @return  string              The Coupon index
     */
    function getIndex()
    {
        if (empty($this->code)) return NULL;
        return $this->code.'-'.$this->customer_id;
    }


    /**
     * Returns the Coupon for the given code
     *
     * If the code is unknown, returns null.
     * On failure returns false.
     * Does not deduct any counts or amounts already used.
     * Use {@see available()} to see whether a Customer may still use
     * a given code.
     * Use {@see getByOrderId()} to get a Coupon that was used in
     * conjunction with any particular Order ID.
     * @param   string    $code           The coupon code
     * @return  Coupon                    The matching Coupon on success,
     *                                    false on error, or null otherwise
     * @static
     */
    static function get($code)
    {
        global $objDatabase;

        // See if the code exists and is still valid
        $query = "
            SELECT `code`, `payment_id`, `start_time`, `end_time`,
                   `minimum_amount`, `discount_rate`, `discount_amount`,
                   `uses`, `global`, `customer_id`, `product_id`
              FROM `".DBPREFIX."module_shop".MODULE_INDEX."_discount_coupon`
             WHERE `code`='".addslashes($code)."'";
        $objResult = $objDatabase->Execute($query);
        // Failure
        if (!$objResult) {
\DBG::log("Coupon::get($code): ERROR: Query failed");
            return self::errorHandler();
        }
        // Not found
        if ($objResult->EOF) {
//DBG::log("Coupon::get($code): Note: None found");
            return null;
        }
        $objCoupon = new Coupon();
        $objCoupon->code($objResult->fields['code']);
        $objCoupon->payment_id($objResult->fields['payment_id']);
        $objCoupon->start_time($objResult->fields['start_time']);
        $objCoupon->end_time($objResult->fields['end_time']);
        $objCoupon->minimum_amount($objResult->fields['minimum_amount']);
        $objCoupon->discount_rate($objResult->fields['discount_rate']);
        $objCoupon->discount_amount($objResult->fields['discount_amount']);
        $objCoupon->uses($objResult->fields['uses']);
        $objCoupon->is_global($objResult->fields['global']);
        $objCoupon->customer_id($objResult->fields['customer_id']);
        $objCoupon->product_id($objResult->fields['product_id']);
        $objCoupon->used = $objCoupon->getUsedCount();
        return $objCoupon;
    }

    /**
     * This ensures that for every message type, only the first one is shown
     * @todo Move this behavior to Message class
     * @param string $type Type name
     * @return boolean Whether we already had such a message or not
     */
    protected static function hasMessage($type) {
        $hasMessage = (isset(self::$hasMessage[$type]) && self::$hasMessage[$type]);
        self::$hasMessage[$type] = true;
        return $hasMessage;
    }


    /**
     * Verifies the coupon code and returns the first matching one
     *
     * If the code is valid, returns the Coupon.
     * If the code is unknown, or limited and already exhausted, returns false.
     * Also note that no counter is changed upon verification; to update
     * a coupon after use see {@see redeem()}.
     * Use {@see getByOrderId()} to get a (used) Coupon that was used in
     * conjunction with any partivcular Order, without any verification.
     * @param   string    $code           The coupon code
     * @param   double    $order_amount   The order amount
     * @param   integer   $customer_id    The Customer ID
     * @param   integer   $product_id     The Product ID
     * @param   integer   $payment_id     The Payment ID
     * @return  Coupon                    The matching Coupon on success,
     *                                    false on error, or null otherwise
     * @static
     */
    static function available($code, $order_amount,
        $customer_id=null, $product_id=null, $payment_id=null
    ) {
        global $_ARRAYLANG;

        // See if the code exists and is still valid
        $objCoupon = self::get($code);
        if ($objCoupon === false) {
//DBG::log("Coupon::available($code, $order_amount, $customer_id, $product_id, $payment_id): ERROR getting the Coupon");
            return false;
        }
        if (!$objCoupon) return null;
        // Verify "ownership" first.  No point in setting status messages
        // that are inappropriate for other users.
        if ($objCoupon->customer_id
         && $objCoupon->customer_id != intval($customer_id)) {
//DBG::log("Coupon::available($code, $order_amount, $customer_id, $product_id, $payment_id): Wrong Customer ID");
            return null;
        }
        if ($objCoupon->product_id != intval($product_id)) {
//DBG::log("Coupon::available($code, $order_amount, $customer_id, $product_id, $payment_id): Wrong Product ID, need ".$objCoupon->product_id);
            if ($objCoupon->product_id) {
                if (!self::hasMessage('TXT_SHOP_COUPON_UNAVAILABLE_FOR_THIS_PRODUCT')) {
                    \Message::information($_ARRAYLANG['TXT_SHOP_COUPON_UNAVAILABLE_FOR_THIS_PRODUCT']);
                }
            }
            return null;
        }
        if ($objCoupon->payment_id
         && $objCoupon->payment_id != intval($payment_id)) {
//DBG::log("Coupon::available($code, $order_amount, $customer_id, $product_id, $payment_id): Wrong Payment ID");
            if (!self::hasMessage('TXT_SHOP_COUPON_UNAVAILABLE_FOR_THIS_PAYMENT')) {
                \Message::information($_ARRAYLANG['TXT_SHOP_COUPON_UNAVAILABLE_FOR_THIS_PAYMENT']);
            }
            return null;
        }
        if ($objCoupon->start_time
         && $objCoupon->start_time > time()) {
//DBG::log("Coupon::available($code, $order_amount, $customer_id, $product_id, $payment_id): Not valid yet");
            if (!self::hasMessage('TXT_SHOP_COUPON_UNAVAILABLE_YET')) {
                \Message::information($_ARRAYLANG['TXT_SHOP_COUPON_UNAVAILABLE_YET']);
            }
            return null;
        }
        if ($objCoupon->end_time
         && $objCoupon->end_time < time()) {
//DBG::log("Coupon::available($code, $order_amount, $customer_id, $product_id, $payment_id): No longer valid");
            if (!self::hasMessage('TXT_SHOP_COUPON_UNAVAILABLE_ALREADY')) {
                \Message::information($_ARRAYLANG['TXT_SHOP_COUPON_UNAVAILABLE_ALREADY']);
            }
            return null;
        }
        // Deduct amounts already redeemed
        if (   floatval($objCoupon->discount_amount) > 0
            && $objCoupon->getUsedAmount($customer_id) >= $objCoupon->discount_amount) {
//DBG::log("Coupon::available($code, $order_amount, $customer_id, $product_id, $payment_id): Deduct amounts redeemed");
            return null;
        }
        if ($objCoupon->minimum_amount > floatval($order_amount)) {
//DBG::log("Coupon::available($code, $order_amount, $customer_id, $product_id, $payment_id): Order amount too low");
            if (!self::hasMessage('TXT_SHOP_COUPON_UNAVAILABLE_FOR_AMOUNT')) {
                \Message::information(sprintf(
                    $_ARRAYLANG['TXT_SHOP_COUPON_UNAVAILABLE_FOR_AMOUNT'],
                    $objCoupon->minimum_amount, \Cx\Modules\Shop\Controller\CurrencyController::getActiveCurrencyCode()));
            }
            return null;
        }
//DBG::log("Coupon::available($code, $order_amount, $customer_id, $product_id, $payment_id): Found ".(var_export($objCoupon, true)));
        // Unlimited uses
        if ($objCoupon->uses > 1e9) return $objCoupon;

        // Deduct the number of times the Coupon has been redeemed already:
        // - If the Coupon's customer_id is empty, subtract all uses
        // - Otherwise, subtract the current customer's uses only
        $objCoupon->uses(
            $objCoupon->uses
          - $objCoupon->getUsedCount(
              ($objCoupon->customer_id
                ? $customer_id : null)));
        if ($objCoupon->uses <= 0) {
//DBG::log("Coupon::available($code, $order_amount, $customer_id, $product_id, $payment_id): Fully redeemed");
            if (!self::hasMessage('TXT_SHOP_COUPON_UNAVAILABLE_CAUSE_USED_UP')) {
                \Message::information($_ARRAYLANG['TXT_SHOP_COUPON_UNAVAILABLE_CAUSE_USED_UP']);
            }
            return null;
        }
        return $objCoupon;
    }

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
        $amount = $this->getDiscountAmount($amount);
        $uses = intval((boolean)$uses);
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
     * Returns the count of the uses for the given code
     *
     * The optional $customer_id limits the result to the uses of that
     * Customer.
     * Returns 0 (zero) for codes not present in the relation (yet).
     * @param   integer   $customer_id    The optional Customer ID
     * @return  mixed                     The number of uses of the code
     *                                    on success, false otherwise
     */
    private function getUsedCount($customer_id=0)
    {
        global $objDatabase;

        $query = "
            SELECT SUM(`count`) AS `numof_uses`
              FROM `".DBPREFIX."module_shop".MODULE_INDEX."_rel_customer_coupon`
             WHERE `code`='".addslashes($this->code)."'
               AND `count`!=0
             ".($customer_id ? " AND `customer_id`=$customer_id" : '');
        $objResult = $objDatabase->Execute($query);
        // Failure or none found
        if (!$objResult) return self::errorHandler();
        if ($objResult->EOF || empty ($objResult->fields['numof_uses'])) {
            return 0;
        }
        // The Coupon has been used so many times already
        return $objResult->fields['numof_uses'];
    }


    /**
     * Returns the discount amount used with this Coupon
     *
     * The optional $customer_id and $order_id limit the result to the uses
     * of that Customer and Order.
     * Returns 0 (zero) for Coupons that have not been used with the given
     * parameters, and thus are not present in the relation.
     * @param   integer   $customer_id    The optional Customer ID
     * @param   integer   $order_id       The optional Order ID
     * @return  mixed                     The amount used with this Coupon
     *                                    on success, false otherwise
     */
    public function getUsedAmount($customer_id=NULL, $order_id=NULL)
    {
        global $objDatabase;

        $query = "
            SELECT SUM(`amount`) AS `amount`
              FROM `".DBPREFIX."module_shop".MODULE_INDEX."_rel_customer_coupon`
             WHERE `code`='".addslashes($this->code)."'
               AND `count`!=0".
            // Customers with ID 0 are customers without an account
            // A coupon limited to one use per customer can only be used once
            // by no-account users (see changeset 26506)
            ($customer_id !== null ? " AND `customer_id`=$customer_id" : '').
            ($order_id ? " AND `order_id`=$order_id" : '');
        $objResult = $objDatabase->Execute($query);
        // Failure or none found
        if (!$objResult) return self::errorHandler();
        if ($objResult->EOF) return 0;
        // The Coupon has been used for so much already
        return $objResult->fields['amount'];
    }

    /**
     * Get coupon information from the database
     *
     * The array returned looks like
     *  array(
     *    'code-payment_id' => array(
     *      code => The Coupon code,
     *      minimum_amount => The minimum amount for which the coupon is valid,
     *      discount_rate => The discount rate,
     *      discount_amount => The discount amount,
     *      start_time => The validity period start time,
     *      end_time => The validity period end time,
     *      uses => The available number of uses,
     *      global => Flag for globally available coupons,
     *      customer_id => The Customer ID,
     *      product_id => The Product ID,
     *      payment_id => The Payment ID,
     *    ),
     *    ... more ...
     *  )
     * @param   integer   $offset           The offset.  Defaults to zero
     * @param   integer   $limit            The limit.  Defaults to 30.
     * @param   integer   $count            By reference.  Contains the actual
     *                                      number of total records on
     *                                      successful return
     * @param   string    $order            The sorting order.  Defaults to
     *                                      '`end_time` DESC'
     * @return  array                       The array of coupon data
     * @static
     */
    static function getArray($offset=0, $limit=0, &$count=0, $order='')
    {
        global $objDatabase;

        $offset = max(0, intval($offset));
        $limit = min(0, intval($limit));
        if (empty($limit)) $limit = 30;
        // The count is zero if an error occurs.
        // Shuts up the code analyzer warning.
        $count -= $count;
        if (empty($order)) $order='`end_time` DESC';
        $query = "
            SELECT `code`, `payment_id`, `start_time`, `end_time`,
                   `minimum_amount`, `discount_rate`, `discount_amount`,
                   `uses`, `global`, `customer_id`, `product_id`
              FROM `".DBPREFIX."module_shop".MODULE_INDEX."_discount_coupon`
             ORDER BY $order";
        $objResult = $objDatabase->SelectLimit($query, $limit, $offset);
        if (!$objResult) return self::errorHandler();
        $arrCoupons = array();
        while (!$objResult->EOF) {
//echo("Fields: ".var_export($objResult->fields, true));
            $objCoupon = new Coupon();
            $code = $objResult->fields['code'];
            $payment_id = $objResult->fields['payment_id'];
            $objCoupon->code($code);
            $objCoupon->payment_id($payment_id);
            $objCoupon->start_time($objResult->fields['start_time']);
            $objCoupon->end_time($objResult->fields['end_time']);
            $objCoupon->minimum_amount($objResult->fields['minimum_amount']);
            $objCoupon->discount_rate($objResult->fields['discount_rate']);
            $objCoupon->discount_amount($objResult->fields['discount_amount']);
            $objCoupon->uses($objResult->fields['uses']);
            $objCoupon->is_global($objResult->fields['global']);
            $objCoupon->customer_id($objResult->fields['customer_id']);
            $objCoupon->product_id($objResult->fields['product_id']);
            $objCoupon->used = $objCoupon->getUsedCount();
            $arrCoupons[$code.'-'.$objCoupon->customer_id] = $objCoupon;
            $objResult->MoveNext();
        }
        $query = "
            SELECT COUNT(*) AS `numof_records`
              FROM `".DBPREFIX."module_shop".MODULE_INDEX."_discount_coupon`";
        $objResult = $objDatabase->Execute($query);
        if (!$objResult) return false;
        $count = $objResult->fields['numof_records'];
        return $arrCoupons;
    }


    /**
     * Returns true if the given code does not exist in the database
     *
     * Upon failure, true is returned in order to prevent overwriting the
     * potentially existing record.
     * @param   string    $code     The code
     * @return  boolean             True if the record exists or on failure,
     *                              false otherwise
     */
    static function codeExists($code)
    {
        global $objDatabase;

        $query = "
            SELECT 1
              FROM `".DBPREFIX."module_shop".MODULE_INDEX."_discount_coupon`
             WHERE `code`='".addslashes($code)."'";
        $objResult = $objDatabase->Execute($query);
        // Failure or none found
        if (!$objResult) {
            // Failure!  Assume that the code exists.
            return true;
        }
        if ($objResult->EOF) {
            return false;
        }
        return true;
    }


    /**
     * Returns true if the Coupon with the given index does not exist in
     * the database
     *
     * Upon failure, true is returned in order to prevent overwriting the
     * potentially existing record.
     * @param   string    $index    The Coupon index
     * @return  boolean             True if the record exists or on failure,
     *                              false otherwise
     */
    static function recordExists($index)
    {
        global $objDatabase;

        if (empty($index)) return false;
        list($code, $customer_id) = explode('-', $index);
        if (empty($code)) return false;
        $query = "
            SELECT 1
              FROM `".DBPREFIX."module_shop".MODULE_INDEX."_discount_coupon`
             WHERE `code`='".addslashes($code)."'
               AND `customer_id`=".intval($customer_id);
        $objResult = $objDatabase->Execute($query);
        if (!$objResult) {
            // Failure!  Assume that the Coupon exists.
            return true;
        }
        if ($objResult->EOF) {
            // None found
            return false;
        }
        // Exists
        return true;
    }


    /**
     * Returns the number of Coupons defined
     *
     * @todo    If the $active parameter value is set, limit the number to
     * Coupons of the given status (not implemented yet)
     * @return  integer               The number of Coupons
     */
    static function count_available()//$active=true)
    {
        global $objDatabase;

        $query = "
            SELECT COUNT(*) AS `numof_records`
              FROM `".DBPREFIX."module_shop".MODULE_INDEX."_discount_coupon`";
        $objResult = $objDatabase->Execute($query);
        if (!$objResult) return self::errorHandler();
        if ($objResult->EOF) return 0;
        return $objResult->fields['numof_records'];
    }


    /**
     * Returns the discount amount resulting from applying this Coupon
     *
     * This Coupon may contain either a discount rate or amount.
     * The rate has precedence and is thus applied in case both are set
     * (although this should never happen).
     * If neither is greater than zero, returns 0 (zero).  This also should
     * never happen.
     * If the Coupon has an amount, the sum of all previous redemptions
     * is subtracted first, and the remainder is returned.
     * Note that the value returned is never greater than $amount.
     * @param   float   $amount         The amount
     * @param   integer $customer_id    The Customer ID
     * @return  string                  The applicable discount amount
     */
    function getDiscountAmount($amount, $customer_id=NULL)
    {
        if ($this->discount_rate)
            return \Cx\Modules\Shop\Controller\CurrencyController::formatPrice($amount * $this->discount_rate / 100);
        $amount_available = max(0,
            $this->discount_amount - $this->getUsedAmount($customer_id));
        return \Cx\Modules\Shop\Controller\CurrencyController::formatPrice(
            min($amount, $amount_available));
    }


    /**
     * Returns a unique Coupon code with eight characters
     * @return    string            The Coupon code
     * @see       User::make_password()
     */
    static function getNewCode()
    {
        $code = null;
        while (true) {
            $code = \User::make_password(8, false);
            if (!self::codeExists($code)) break;
        }
        return $code;
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
