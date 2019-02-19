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
 * Shop Order
 * @copyright   CLOUDREXX CMS - CLOUDREXX AG
 * @author      Reto Kohli <reto.kohli@comvation.com>
 * @version     3.0.0
 * @package     cloudrexx
 * @subpackage  module_shop
 * @todo        Test!
 */

namespace Cx\Modules\Shop\Controller;

/**
 * Shop Order
 * @copyright   CLOUDREXX CMS - CLOUDREXX AG
 * @author      Reto Kohli <reto.kohli@comvation.com>
 * @version     3.0.0
 * @package     cloudrexx
 * @subpackage  module_shop
 */
class Order
{
    /**
     * Order status constant values
     * @author  Reto Kohli <reto.kohli@comvation.com>
     */
    const STATUS_PENDING   = 0;
    const STATUS_CONFIRMED = 1;
    const STATUS_DELETED   = 2;
    const STATUS_CANCELLED = 3;
    const STATUS_COMPLETED = 4;
    const STATUS_PAID      = 5;
    const STATUS_SHIPPED   = 6;
    /**
     * Total number of states.
     * @internal Keep this up to date!
     * @author  Reto Kohli <reto.kohli@comvation.com>
     */
    const STATUS_MAX = 7;
    /**
     * Folder name for (image) file uploads in the Shop
     *
     * Note that this is prepended with the document root when necessary.
     */
    const UPLOAD_FOLDER = 'media/Shop/upload/';

    protected $id = null;
    protected $customer_id = null;
    protected $currency_id = null;
    protected $shipment_id = null;
    protected $payment_id = null;
    protected $lang_id = 0;
    protected $status = 0;
    protected $sum = 0.00;
    protected $vat_amount = 0.00;
    protected $shipment_amount = 0.00;
    protected $payment_amount = 0.00;

// 20111017 Added billing address
    protected $billing_gender = '';
    protected $billing_company = '';
    protected $billing_firstname = '';
    protected $billing_lastname = '';
    protected $billing_address = '';
    protected $billing_city = '';
    protected $billing_zip = '';
    protected $billing_country_id = 0;
    protected $billing_phone = '';
    protected $billing_fax = '';
    protected $billing_email = '';

    protected $gender = '';
    protected $company = '';
    protected $firstname = '';
    protected $lastname = '';
    protected $address = '';
    protected $city = '';
    protected $zip = '';
    protected $country_id = 0;
    protected $phone = '';
    protected $ip = '';
    protected $note = '';
    protected $date_time = '0000-00-00 00:00:00';
    protected $modified_on = '0000-00-00 00:00:00';
    protected $modified_by = '';
/*  OBSOLETE
    ccNumber
    ccDate
    ccName
    ccCode */


    /**
     * Returns the Order ID
     *
     * This value is null unless it has been stored before.
     * @return  integer         The Order ID
     */
    function id()
    {
        return $this->id;
    }

    /**
     * Set the Order id
     *
     * @param integer $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * Returns the Customer ID
     *
     * Optionally sets the value first if the parameter value is an integer
     * greater than zero
     * This value is null unless it has been set before.
     * @param   integer $customer_id    The optional Customer ID
     * @return  integer                 The Customer ID
     */
    function customer_id($customer_id=null)
    {
        if (isset($customer_id)) {
            $customer_id = intval($customer_id);
            if ($customer_id > 0) {
                $this->customer_id = $customer_id;
            }
        }
        return $this->customer_id;
    }

    /**
     * Returns the Currency ID
     *
     * Optionally sets the value first if the parameter value is an integer
     * greater than zero
     * This value is null unless it has been set before.
     * @param   integer $currency_id    The optional Currency ID
     * @return  integer                 The Currency ID
     */
    function currency_id($currency_id=null)
    {
        if (isset($currency_id)) {
            $currency_id = intval($currency_id);
            if ($currency_id > 0) {
                $this->currency_id = $currency_id;
            }
        }
        return $this->currency_id;
    }

    /**
     * Returns the Shipment ID
     *
     * Optionally sets the value first if the parameter value is an integer
     * greater than or equal to zero.
     * A zero Shipper ID represents "no shipment required".
     * This value is null unless it has been set before.
     * @param   integer $shipment_id    The optional Shipment ID
     * @return  integer                 The Shipment ID
     * @todo    Must be properly named "shipper_id"
     */
    function shipment_id($shipment_id=null)
    {
        if (isset($shipment_id)) {
            $shipment_id = intval($shipment_id);
            // May be empty (zero for no shipment)!
            if ($shipment_id >= 0) {
                $this->shipment_id = $shipment_id;
            }
        }
        return $this->shipment_id;
    }

    /**
     * Returns the Payment ID
     *
     * Optionally sets the value first if the parameter value is an integer
     * greater than zero
     * This value is null unless it has been set before.
     * @param   integer $payment_id     The optional Payment ID
     * @return  integer                 The Payment ID
     */
    function payment_id($payment_id=null)
    {
        if (isset($payment_id)) {
            $payment_id = intval($payment_id);
            if ($payment_id > 0) {
                $this->payment_id = $payment_id;
            }
        }
        return $this->payment_id;
    }

    /**
     * Returns the language ID
     *
     * Optionally sets the value first if the parameter value is an integer
     * greater than zero
     * This value is zero unless it has been set before.
     * @param   integer $lang_id    The optional language ID
     * @return  integer             The language ID
     */
    function lang_id($lang_id=null)
    {
        if (isset($lang_id)) {
            $lang_id = intval($lang_id);
            if ($lang_id > 0) {
                $this->lang_id = $lang_id;
            }
        }
        return $this->lang_id;
    }

    /**
     * Returns the status
     *
     * Optionally sets the value first if the parameter value is an integer
     * greater than or equal to zero.
     * Note that the value is not verified other than that.
     * This value is zero unless it has been set before.
     * @param   integer $status     The optional status
     * @return  integer             The status
     */
    function status($status=null)
    {
        if (isset($status)) {
            $status = intval($status);
            if ($status >= 0) {
                $this->status = $status;
            }
        }
        return $this->status;
    }

    /**
     * Returns the total sum, including fees and tax
     *
     * Optionally sets the value first if the parameter value is a float
     * greater than or equal to zero.
     * Note that the value is not verified other than that.
     * This value is zero unless it has been set before.
     * It is interpreted as an amount in the Currency specified by
     * the Currency ID.
     * @param   float   $sum    The optional sum
     * @return  float           The sum
     */
    function sum($sum=null)
    {
        if (isset($sum)) {
            $sum = floatval($sum);
            if ($sum >= 0) {
                $this->sum = number_format($sum, 2, '.', '');
            }
        }
        return $this->sum;
    }

    /**
     * Returns the VAT amount
     *
     * Optionally sets the value first if the parameter value is a float
     * greater than or equal to zero.
     * Note that the value is not verified other than that.
     * This value is zero unless it has been set before.
     * It is interpreted as an amount in the Currency specified by
     * the Currency ID.
     * @param   float   $vat_amount     The optional VAT amount
     * @return  float                   The VAT amount
     */
    function vat_amount($vat_amount=null)
    {
        if (isset($vat_amount)) {
            $vat_amount = floatval($vat_amount);
            if ($vat_amount >= 0) {
                $this->vat_amount = number_format($vat_amount, 2, '.', '');
            }
        }
        return $this->vat_amount;
    }

    /**
     * Returns the shipment fee
     *
     * Optionally sets the value first if the parameter value is a float
     * greater than or equal to zero.
     * Note that the value is not verified other than that.
     * This value is zero unless it has been set before.
     * It is interpreted as an amount in the Currency specified by
     * the Currency ID.
     * @param   float   $shipment_amount    The optional shipment fee
     * @return  float                       The shipment fee
     */
    function shipment_amount($shipment_amount=null)
    {
        if (isset($shipment_amount)) {
            $shipment_amount = floatval($shipment_amount);
            if ($shipment_amount >= 0) {
                $this->shipment_amount =
                    number_format($shipment_amount, 2, '.', '');
            }
        }
        return $this->shipment_amount;
    }

    /**
     * Returns the payment fee
     *
     * Optionally sets the value first if the parameter value is a float
     * greater than or equal to zero.
     * Note that the value is not verified other than that.
     * This value is zero unless it has been set before.
     * It is interpreted as an amount in the Currency specified by
     * the Currency ID.
     * @param   float   $payment_amount     The optional payment fee
     * @return  float                       The payment fee
     */
    function payment_amount($payment_amount=null)
    {
        if (isset($payment_amount)) {
            $payment_amount = floatval($payment_amount);
            if ($payment_amount >= 0) {
                $this->payment_amount =
                    number_format($payment_amount, 2, '.', '');
            }
        }
        return $this->payment_amount;
    }

    /**
     * Returns the gender (billing addres)
     *
     * Optionally sets the value first if the parameter value is a non-empty
     * string.
     * Note that the value is not verified other than that.
     * Valid values are defined by the User_Profile_Attribute class.
     * This value is the empty string unless it has been set before.
     * @param   string  $gender     The optional gender
     * @return  string              The gender
     */
    function billing_gender($billing_gender=null)
    {
        if (isset($billing_gender)) {
            $billing_gender = trim(strip_tags($billing_gender));
            if ($billing_gender != '') {
                $this->billing_gender = $billing_gender;
            }
        }
        return $this->billing_gender;
    }

    /**
     * Returns the company (billing address)
     *
     * Optionally sets the value first if the parameter value is a non-empty
     * string.
     * Note that the value is not verified other than that.
     * This value is the empty string unless it has been set before.
     * @param   string  $billing_company    The optional company
     * @return  string                      The company
     */
    function billing_company($billing_company=null)
    {
        if (isset($billing_company)) {
            $this->billing_company = trim(strip_tags($billing_company));
        }
        return $this->billing_company;
    }

    /**
     * Returns the first name (billing address)
     *
     * Optionally sets the value first if the parameter value is a non-empty
     * string.
     * Note that the value is not verified other than that.
     * This value is the empty string unless it has been set before.
     * @param   string  $billing_firstname  The optional first name
     * @return  string                      The first name
     */
    function billing_firstname($billing_firstname=null)
    {
        if (isset($billing_firstname)) {
            $billing_firstname = trim(strip_tags($billing_firstname));
            if ($billing_firstname != '') {
                $this->billing_firstname = $billing_firstname;
            }
        }
        return $this->billing_firstname;
    }

    /**
     * Returns the last name (billing address)
     *
     * Optionally sets the value first if the parameter value is a non-empty
     * string.
     * Note that the value is not verified other than that.
     * This value is the empty string unless it has been set before.
     * @param   string  $billing_lastname   The optional last name
     * @return  string                      The last name
     */
    function billing_lastname($billing_lastname=null)
    {
        if (isset($billing_lastname)) {
            $billing_lastname = trim(strip_tags($billing_lastname));
            if ($billing_lastname != '') {
                $this->billing_lastname = $billing_lastname;
            }
        }
        return $this->billing_lastname;
    }

    /**
     * Returns the address (billing address)
     *
     * Optionally sets the value first if the parameter value is a non-empty
     * string.
     * Note that the value is not verified other than that.
     * This value is the empty string unless it has been set before.
     * @param   string  $billing_address    The optional address
     * @return  string                      The address
     */
    function billing_address($billing_address=null)
    {
        if (isset($billing_address)) {
            $billing_address = trim(strip_tags($billing_address));
            if ($billing_address != '') {
                $this->billing_address = $billing_address;
            }
        }
        return $this->billing_address;
    }

    /**
     * Returns the city (billing address)
     *
     * Optionally sets the value first if the parameter value is a non-empty
     * string.
     * Note that the value is not verified other than that.
     * This value is the empty string unless it has been set before.
     * @param   string  $billing_city   The optional city
     * @return  string                  The city
     */
    function billing_city($billing_city=null)
    {
        if (isset($billing_city)) {
            $billing_city = trim(strip_tags($billing_city));
            if ($billing_city != '') {
                $this->billing_city = $billing_city;
            }
        }
        return $this->billing_city;
    }

    /**
     * Returns the zip (billing address)
     *
     * Optionally sets the value first if the parameter value is a non-empty
     * string.
     * Note that the value is not verified other than that.
     * This value is the empty string unless it has been set before.
     * @param   string  $billing_zip    The optional zip
     * @return  string                  The zip
     */
    function billing_zip($billing_zip=null)
    {
        if (isset($billing_zip)) {
            $billing_zip = trim(strip_tags($billing_zip));
            if ($billing_zip != '') {
                $this->billing_zip = $billing_zip;
            }
        }
        return $this->billing_zip;
    }

    /**
     * Returns the Country ID (billing address)
     *
     * Optionally sets the value first if the parameter value is an integer
     * greater than zero.
     * Note that the value is not verified other than that.
     * This value is zero unless it has been set before.
     * @param   integer $billing_country_id     The optional Country ID
     * @return  integer                         The Country ID
     */
    function billing_country_id($billing_country_id=null)
    {
        if (isset($billing_country_id)) {
            $billing_country_id = intval($billing_country_id);
            if ($billing_country_id > 0) {
                $this->billing_country_id = $billing_country_id;
            }
        }
        return $this->billing_country_id;
    }

    /**
     * Returns the phone number (billing address)
     *
     * Optionally sets the value first if the parameter value is a non-empty
     * string.
     * Note that the value is not verified other than that.
     * This value is the empty string unless it has been set before.
     * @param   string  $billing_phone  The optional phone number
     * @return  string                  The phone number
     */
    function billing_phone($billing_phone=null)
    {
        if (isset($billing_phone)) {
            $billing_phone = trim(strip_tags($billing_phone));
            if ($billing_phone != '') {
                $this->billing_phone = $billing_phone;
            }
        }
        return $this->billing_phone;
    }

    /**
     * Returns the fax number (billing address)
     *
     * Optionally sets the value first if the parameter value is a non-empty
     * string.
     * Note that the value is not verified other than that.
     * This value is the empty string unless it has been set before.
     * @param   string  $billing_fax    The optional fax number
     * @return  string                  The fax number
     */
    function billing_fax($billing_fax=null)
    {
        if (isset($billing_fax)) {
            $billing_fax = trim(strip_tags($billing_fax));
            if ($billing_fax != '') {
                $this->billing_fax = $billing_fax;
            }
        }
        return $this->billing_fax;
    }

    /**
     * Returns the e-mail address (customer)
     *
     * Optionally sets the value first if the parameter value is a non-empty
     * string.
     * Note that the value is not verified other than that.
     * This value is the empty string unless it has been set before.
     * @param   string  $billing_email  The optional e-mail address
     * @return  string                  The e-mail address
     */
    function billing_email($billing_email=null)
    {
        if (isset($billing_email)) {
            $billing_email = trim(strip_tags($billing_email));
            if ($billing_email != '') {
                $this->billing_email = $billing_email;
            }
        }
        return $this->billing_email;
    }

    /**
     * Returns the gender (shipment address)
     *
     * Optionally sets the value first if the parameter value is a non-empty
     * string.
     * Note that the value is not verified other than that.
     * Valid values are defined by the User_Profile_Attribute class.
     * This value is the empty string unless it has been set before.
     * @param   string  $gender     The optional gender
     * @return  string              The gender
     */
    function gender($gender=null)
    {
        if (isset($gender)) {
            $gender = trim(strip_tags($gender));
            if ($gender != '') {
                $this->gender = $gender;
            }
        }
        return $this->gender;
    }

    /**
     * Returns the company (shipment address)
     *
     * Optionally sets the value first if the parameter value is a non-empty
     * string.
     * Note that the value is not verified other than that.
     * This value is the empty string unless it has been set before.
     * @param   string  $company    The optional company
     * @return  string              The company
     */
    function company($company=null)
    {
        if (isset($company)) {
            $this->company = trim(strip_tags($company));
        }
        return $this->company;
    }

    /**
     * Returns the first name (shipment address)
     *
     * Optionally sets the value first if the parameter value is a non-empty
     * string.
     * Note that the value is not verified other than that.
     * This value is the empty string unless it has been set before.
     * @param   string  $firstname  The optional first name
     * @return  string              The first name
     */
    function firstname($firstname=null)
    {
        if (isset($firstname)) {
            $firstname = trim(strip_tags($firstname));
            if ($firstname != '') {
                $this->firstname = $firstname;
            }
        }
        return $this->firstname;
    }

    /**
     * Returns the last name (shipment address)
     *
     * Optionally sets the value first if the parameter value is a non-empty
     * string.
     * Note that the value is not verified other than that.
     * This value is the empty string unless it has been set before.
     * @param   string  $lastname   The optional last name
     * @return  string              The last name
     */
    function lastname($lastname=null)
    {
        if (isset($lastname)) {
            $lastname = trim(strip_tags($lastname));
            if ($lastname != '') {
                $this->lastname = $lastname;
            }
        }
        return $this->lastname;
    }

    /**
     * Returns the address (shipment address)
     *
     * Optionally sets the value first if the parameter value is a non-empty
     * string.
     * Note that the value is not verified other than that.
     * This value is the empty string unless it has been set before.
     * @param   string  $address    The optional address
     * @return  string              The address
     */
    function address($address=null)
    {
        if (isset($address)) {
            $address = trim(strip_tags($address));
            if ($address != '') {
                $this->address = $address;
            }
        }
        return $this->address;
    }

    /**
     * Returns the city (shipment address)
     *
     * Optionally sets the value first if the parameter value is a non-empty
     * string.
     * Note that the value is not verified other than that.
     * This value is the empty string unless it has been set before.
     * @param   string  $city   The optional city
     * @return  string          The city
     */
    function city($city=null)
    {
        if (isset($city)) {
            $city = trim(strip_tags($city));
            if ($city != '') {
                $this->city = $city;
            }
        }
        return $this->city;
    }

    /**
     * Returns the zip (shipment address)
     *
     * Optionally sets the value first if the parameter value is a non-empty
     * string.
     * Note that the value is not verified other than that.
     * This value is the empty string unless it has been set before.
     * @param   string  $zip    The optional zip
     * @return  string          The zip
     */
    function zip($zip=null)
    {
        if (isset($zip)) {
            $zip = trim(strip_tags($zip));
            if ($zip != '') {
                $this->zip = $zip;
            }
        }
        return $this->zip;
    }

    /**
     * Returns the Country ID (shipment address)
     *
     * Optionally sets the value first if the parameter value is an integer
     * greater than zero.
     * Note that the value is not verified other than that.
     * This value is zero unless it has been set before.
     * @param   integer country_id  The optional Country ID
     * @return  integer             The Country ID
     */
    function country_id($country_id=null)
    {
        if (isset($country_id)) {
            $country_id = intval($country_id);
            if ($country_id > 0) {
                $this->country_id = $country_id;
            }
        }
        return $this->country_id;
    }

    /**
     * Returns the phone number (shipment address)
     *
     * Optionally sets the value first if the parameter value is a non-empty
     * string.
     * Note that the value is not verified other than that.
     * This value is the empty string unless it has been set before.
     * @param   string  $phone  The optional phone number
     * @return  string          The phone number
     */
    function phone($phone=null)
    {
        if (isset($phone)) {
            $phone = trim(strip_tags($phone));
            if ($phone != '') {
                $this->phone = $phone;
            }
        }
        return $this->phone;
    }

    /**
     * Returns the IP address
     *
     * Optionally sets the value first if the parameter value is a non-empty
     * string.
     * Note that the value is not verified other than that.
     * This value is the empty string unless it has been set before.
     * @param   string  $ip     The optional IP address
     * @return  string          The IP address
     */
    function ip($ip=null)
    {
        if (isset($ip)) {
            $ip = trim(strip_tags($ip));
            if ($ip != '') {
                $this->ip = $ip;
            }
        }
        return $this->ip;
    }

    /**
     * Returns the order note
     *
     * Optionally sets the value first if the parameter value is a non-empty
     * string.
     * Note that the value is not verified other than that.
     * This value is the empty string unless it has been set before.
     * @param   string  $note   The optional order note
     * @return  string          The order note
     */
    function note($note=null)
    {
        if (isset($note)) {
            $note = trim(strip_tags($note));
            if ($note != '') {
                $this->note = $note;
            }
        }
        return $this->note;
    }

    /**
     * Returns the date and time the Order was placed
     *
     * Optionally sets the value first if the parameter value is a non-empty
     * string.
     * Note that the value is verified and interpreted using strtotime().
     * If the resulting time is non-zero, it is accepted and converted
     * to DATETIME format.
     * This value is '0000-00-00 00:00:00' unless it has been set before.
     * @param   string  $date_time  The optional order date and time
     * @return  string              The order date and time, in DATETIME format
     */
    function date_time($date_time=null)
    {
        if (isset($date_time)) {
            $date_time = strtotime(trim(strip_tags($date_time)));
            if ($date_time > 0) {
                $this->date_time =
                    date(ASCMS_DATE_FORMAT_INTERNATIONAL_DATETIME, $date_time);
            }
        }
        return $this->date_time;
    }

    /**
     * Returns the date and time the Order was last edited
     *
     * Optionally sets the value first if the parameter value is a non-empty
     * string.
     * Note that the value is verified and interpreted using strtotime().
     * If the resulting time is non-zero, it is accepted and converted
     * to DATETIME format.
     * This value is '0000-00-00 00:00:00' unless it has been set before.
     * @param   string  $modified_on    The optional edit date and time
     * @return  string                  The edit date and time,
     *                                  in DATETIME format
     */
    function modified_on($modified_on=null)
    {
        if (isset($modified_on)) {
            $modified_on = strtotime(trim(strip_tags($modified_on)));
            if ($modified_on > 0) {
                $this->modified_on =
                    date(ASCMS_DATE_FORMAT_INTERNATIONAL_DATETIME, $modified_on);
            }
        }
        return $this->modified_on;
    }

    /**
     * Returns the user name of the User that last edited this Order
     *
     * Optionally sets the value first if the parameter value is a non-empty
     * string.
     * Note that the value is not verified other than that.
     * This value is the empty string unless it has been set before.
     * @param   string  $modified_by    The optional user name
     * @return  string                  The user name
     */
    function modified_by($modified_by=null)
    {
        if (isset($modified_by)) {
            $modified_by = trim(strip_tags($modified_by));
            if ($modified_by != '') {
                $this->modified_by = $modified_by;
            }
        }
        return $this->modified_by;
    }


    /**
     * Returns the Order for the ID given
     *
     * If the ID is invalid or no record is found for it, returns null.
     * @param   integer   $id       The Order ID
     * @return  Order               The object on success, null otherwise
     */
    static function getById($id)
    {
        global $objDatabase;

//DBG::activate(DBG_PHP|DBG_ADODB|DBG_LOG_FIREPHP);

        $query = "
            SELECT `id`, `customer_id`, `lang_id`, `currency_id`,
                   `shipment_id`, `payment_id`,
                   `status`,
                   `sum`,
                   `vat_amount`, `shipment_amount`, `payment_amount`,".
// 20111017 Added billing address
            "
                   `billing_gender`, `billing_company`,
                   `billing_firstname`, `billing_lastname`,
                   `billing_address`, `billing_city`, `billing_zip`,
                   `billing_country_id`,
                   `billing_phone`, `billing_fax`,
                   `billing_email`,
                   `gender`, `company`, `firstname`, `lastname`,
                   `address`, `city`, `zip`, `country_id`, `phone`,
                   `ip`,
                   `note`,
                   `date_time`, `modified_on`, `modified_by`
              FROM `".DBPREFIX."module_shop".MODULE_INDEX."_orders`
             WHERE `id`=".intval($id);
//DBG::activate(DBG_ADODB);
        $objResult = $objDatabase->Execute($query);
//DBG::deactivate(DBG_ADODB);
        if (!$objResult) return self::errorHandler();
        if ($objResult->EOF) {
//DBG::log("Order::getById(): Failed to get Order ID $id");
            return null;
        }
        $objOrder = new Order();
        $objOrder->id = $objResult->fields['id'];
        $objOrder->customer_id($objResult->fields['customer_id']);
        $objOrder->currency_id($objResult->fields['currency_id']);
        $objOrder->shipment_id($objResult->fields['shipment_id']);
        $objOrder->payment_id($objResult->fields['payment_id']);
        $objOrder->lang_id($objResult->fields['lang_id']);
        $objOrder->status($objResult->fields['status']);
        $objOrder->sum($objResult->fields['sum']);
        $objOrder->vat_amount($objResult->fields['vat_amount']);
        $objOrder->shipment_amount($objResult->fields['shipment_amount']);
        $objOrder->payment_amount($objResult->fields['payment_amount']);
        $objOrder->gender($objResult->fields['gender']);
        $objOrder->company($objResult->fields['company']);
        $objOrder->firstname($objResult->fields['firstname']);
        $objOrder->lastname($objResult->fields['lastname']);
        $objOrder->address($objResult->fields['address']);
        $objOrder->city($objResult->fields['city']);
        $objOrder->zip($objResult->fields['zip']);
        $objOrder->country_id($objResult->fields['country_id']);
        $objOrder->phone($objResult->fields['phone']);
// 20111017 Added billing address
        $objOrder->billing_gender($objResult->fields['billing_gender']);
        $objOrder->billing_company($objResult->fields['billing_company']);
        $objOrder->billing_firstname($objResult->fields['billing_firstname']);
        $objOrder->billing_lastname($objResult->fields['billing_lastname']);
        $objOrder->billing_address($objResult->fields['billing_address']);
        $objOrder->billing_city($objResult->fields['billing_city']);
        $objOrder->billing_zip($objResult->fields['billing_zip']);
        $objOrder->billing_country_id($objResult->fields['billing_country_id']);
        $objOrder->billing_phone($objResult->fields['billing_phone']);
        $objOrder->billing_fax($objResult->fields['billing_fax']);
        $objOrder->billing_email($objResult->fields['billing_email']);
        $objOrder->ip($objResult->fields['ip']);
        $objOrder->note($objResult->fields['note']);
        $objOrder->date_time($objResult->fields['date_time']);
        $objOrder->modified_on($objResult->fields['modified_on']);
        $objOrder->modified_by($objResult->fields['modified_by']);
        return $objOrder;
    }


    /**
     * Inserts a new Order into the database table
     *
     * Does not handle items nor attributes, see {@see insertItem()} and
     * {@see insertAttribute()} for that.
     * Fails if the ID is non-empty, or if the record cannot be inserted
     * for any reason.
     * Does not insert the shipment related properties if the shipment ID
     * is empty.  Those fields *SHOULD* default to NULL.
     * @return  integer             The ID of the record inserted on success,
     *                              false otherwise
     */
    function insert()
    {
        global $objDatabase, $_ARRAYLANG;

        if ($this->id) {
            return false;
        }
        // Ignores the shipment if not applicable
        $query = "
            INSERT INTO `".DBPREFIX."module_shop".MODULE_INDEX."_orders` (
                `customer_id`, `currency_id`, `sum`,
                `date_time`, `status`,
                `payment_id`, `payment_amount`,
                `vat_amount`,
                `ip`, `lang_id`,
                `note`,".
// 20111017 Added billing address
                "
                `billing_gender`,
                `billing_company`,
                `billing_firstname`,
                `billing_lastname`,
                `billing_address`,
                `billing_city`,
                `billing_zip`,
                `billing_country_id`,
                `billing_phone`,
                `billing_fax`,
                `billing_email`".
            ($this->shipment_id ? ',
                `company`, `gender`,
                `firstname`, `lastname`,
                `address`, `city`,
                `zip`, `country_id`, `phone`,
                `shipment_id`, `shipment_amount`' : '')."
            ) VALUES (
                $this->customer_id, $this->currency_id, $this->sum,
                ".($this->date_time ? "'$this->date_time'" : "'".date('Y-m-d H:i:s')."'").",
                $this->status,
                $this->payment_id, $this->payment_amount,
                $this->vat_amount,
                '".addslashes($this->ip)."',
                $this->lang_id,
                '".addslashes($this->note)."',".
// 20111017 Added billing address
                "
                '".addslashes($this->billing_gender)."',
                '".addslashes($this->billing_company)."',
                '".addslashes($this->billing_firstname)."',
                '".addslashes($this->billing_lastname)."',
                '".addslashes($this->billing_address)."',
                '".addslashes($this->billing_city)."',
                '".addslashes($this->billing_zip)."',
                '".$this->billing_country_id."',
                '".addslashes($this->billing_phone)."',
                '".addslashes($this->billing_fax)."',
                '".addslashes($this->billing_email)."'".
            ($this->shipment_id ? ",
                '".addslashes($this->company)."',
                '".addslashes($this->gender)."',
                '".addslashes($this->firstname)."',
                '".addslashes($this->lastname)."',
                '".addslashes($this->address)."',
                '".addslashes($this->city)."',
                '".addslashes($this->zip)."',
                $this->country_id,
                '".addslashes($this->phone)."',
                $this->shipment_id,
                $this->shipment_amount" : '')."
            )";
        $objResult = $objDatabase->Execute($query);
        if (!$objResult) {
            \Message::error($_ARRAYLANG['TXT_SHOP_ERROR_STORING_ORDER']);
            return false;
        }
        $this->id = $objDatabase->Insert_ID();
        return $this->id;
    }


    /**
     * Returns an array of Attributes and chosen options for this Order
     *
     * Options for uploads are linked to their respective files
     * The array looks like this:
     *  array(
     *    item ID => array(
     *      "Attribute name" => array(
     *        Attribute ID => array
     *          'name' => "option name",
     *          'price' => "price",
     *         ),
     *       [... more ...]
     *      ),
     *    ),
     *    [... more ...]
     *  )
     * Note that the array may be empty.
     * @return  array           The Attribute/option array on success,
     *                          null otherwise
     */
    function getOptionArray($withHtmlNotation = true)
    {
        global $objDatabase;

        $query = "
            SELECT `attribute`.`id`, `attribute`.`item_id`, `attribute`.`attribute_name`,
                   `attribute`.`option_name`, `attribute`.`price`
              FROM `".DBPREFIX."module_shop".MODULE_INDEX."_order_attributes` AS `attribute`
              JOIN `".DBPREFIX."module_shop".MODULE_INDEX."_order_items` AS `item`
                ON `attribute`.`item_id`=`item`.`id`
             WHERE `item`.`order_id`=".$this->id()."
             ORDER BY `attribute`.`attribute_name` ASC, `attribute`.`option_name` ASC";
        $objResult = $objDatabase->Execute($query);
        if (!$objResult) return self::errorHandler();
        $arrProductOptions = array();
        while (!$objResult->EOF) {
            $option_full = $objResult->fields['option_name'];
            $option = ShopLibrary::stripUniqidFromFilename($option_full);
            $path = Order::UPLOAD_FOLDER.$option_full;
            // Link option names to uploaded files
            if (   $option != $option_full
                && \File::exists($path)) {
                if ($withHtmlNotation) {
                    $option =
                        '<a href="'.$path.'" target="uploadFile">'.$option.'</a>';
                }
            }
            $id = $objResult->fields['id'];
            $price = $objResult->fields['price'];
            $arrProductOptions[$objResult->fields['item_id']]
                    [$objResult->fields['attribute_name']][$id] = array(
                'name' => $option,
                'price' => $price,
            );
            $objResult->MoveNext();
        }
        return $arrProductOptions;
    }


    /**
     * Stores the Order
     *
     * Takes all values as found in the POST array
     * @global  array             $_ARRAYLANG   Language array
     * @global  ADONewConnection  $objDatabase  Database connection object
     * @return  boolean                         True on success, false otherwise
     * @static
     */
    static function storeFromPost()
    {
        global $objDatabase, $_ARRAYLANG;

        $order_id = (isset($_POST['order_id'])
            ? intval($_POST['order_id']) : null);
        if (empty($order_id)) return null;
        // calculate the total order sum in the selected currency of the customer
        $totalOrderSum =
            floatval($_POST['shippingPrice'])
          + floatval($_POST['paymentPrice']);
        // the tax amount will be set, even if it's included in the price already.
        // thus, we have to check the setting.
        if (!Vat::isIncluded()) {
            $totalOrderSum += floatval($_POST['taxPrice']);
        }
        // store the product details and add the price of each product
        // to the total order sum $totalOrderSum
        $order = self::getById($order_id);
        $orderOptions = $order->getOptionArray();
        foreach ($_REQUEST['product_list'] as $orderItemId => $product_id) {
            if ($orderItemId != 0 && $product_id == 0) {
                // delete the product from the list
                $query = "
                    DELETE FROM ".DBPREFIX."module_shop".MODULE_INDEX."_order_items
                     WHERE id=$orderItemId";
                $objResult = $objDatabase->Execute($query);
                if (!$objResult) {
                    return self::errorHandler();
                }
                $query = "
                    DELETE FROM ".DBPREFIX."module_shop".MODULE_INDEX."_order_attributes
                     WHERE id=$orderItemId";
                $objResult = $objDatabase->Execute($query);
                if (!$objResult) {
                    return self::errorHandler();
                }
            } elseif ($product_id != 0) {
                $objProduct = Product::getById($product_id);
                if (!$objProduct) {
                    \Message::error(sprintf(
                        $_ARRAYLANG['TXT_SHOP_PRODUCT_NOT_FOUND'],
                        $product_id));
                    continue;
                }
                $product_name = $objProduct->name();
                $productPrice = $price = $_REQUEST['productPrice'][$orderItemId];
                if (isset($orderOptions[$orderItemId])) {
                    foreach ($orderOptions[$orderItemId] as $optionValues) {
                        foreach ($optionValues as $value) {
                            $price += $value['price'];
                        }
                    }
                }
                $price = \Cx\Modules\Shop\Controller\CurrencyController::formatPrice($price);
                $productPrice = \Cx\Modules\Shop\Controller\CurrencyController::formatPrice($productPrice);
                $quantity = max(1,
                    intval($_REQUEST['productQuantity'][$orderItemId]));
                $totalOrderSum += $price * $quantity;
                $vat_rate = Vat::format(
                    $_REQUEST['productTaxPercent'][$orderItemId]);
                $weight = Weight::getWeight(
                    $_REQUEST['productWeight'][$orderItemId]);
                if ($orderItemId == 0) {
                    // Add a new product to the list
                    if (!self::insertItem($order_id, $product_id, $product_name,
                        $productPrice, $quantity, $vat_rate, $weight, array())) {
                        return false;
                    }
                } else {
                    // Update the order item
                    if (!self::updateItem($orderItemId, $product_id,
                        $product_name, $productPrice, $quantity, $vat_rate, $weight, array())) {
                        return false;
                    }
                }
            }
        }
        $objUser = \FWUser::getFWUserObject()->objUser;
        // Store the order details
// TODO: Should add verification for POSTed fields and ignore unset values!
        $query = "
            UPDATE ".DBPREFIX."module_shop".MODULE_INDEX."_orders
               SET `sum`=".floatval($totalOrderSum).",
                   `shipment_amount`=".floatval($_POST['shippingPrice']).",
                   `payment_amount`=".floatval($_POST['paymentPrice']).",
                   `status`='".intval($_POST['order_status'])."',
                   `billing_gender`='".contrexx_input2db($_POST['billing_gender'])."',
                   `billing_company`='".contrexx_input2db($_POST['billing_company'])."',
                   `billing_firstname`='".contrexx_input2db($_POST['billing_firstname'])."',
                   `billing_lastname`='".contrexx_input2db($_POST['billing_lastname'])."',
                   `billing_address`='".contrexx_input2db($_POST['billing_address'])."',
                   `billing_city`='".contrexx_input2db($_POST['billing_city'])."',
                   `billing_zip`='".contrexx_input2db($_POST['billing_zip'])."',
                   `billing_country_id`='".intval($_POST['billing_country_id'])."',
                   `billing_phone`='".contrexx_input2db($_POST['billing_phone'])."',
                   `billing_fax`='".contrexx_input2db($_POST['billing_fax'])."',
                   `billing_email`='".contrexx_input2db($_POST['billing_email'])."',
                   `gender`='".contrexx_input2db($_POST['shipPrefix'])."',
                   `company`='".contrexx_input2db($_POST['shipCompany'])."',
                   `firstname`='".contrexx_input2db($_POST['shipFirstname'])."',
                   `lastname`='".contrexx_input2db($_POST['shipLastname'])."',
                   `address`='".contrexx_input2db($_POST['shipAddress'])."',
                   `city`='".contrexx_input2db($_POST['shipCity'])."',
                   `zip`='".contrexx_input2db($_POST['shipZip'])."',
                   `country_id`=".intval($_POST['shipCountry']).",
                   `phone`='".contrexx_input2db($_POST['shipPhone'])."',
                   `vat_amount`=".floatval($_POST['taxPrice']).",
                   `shipment_id`=".intval($_POST['shipperId']).",
                   `modified_by`='".$objUser->getUsername()."',
                   `modified_on`='".date('Y-m-d H:i:s')."'
             WHERE `id`=$order_id";
        // should not be changed, see above
        // ", payment_id = ".intval($_POST['paymentId']).
        if (!$objDatabase->Execute($query)) {
            \Message::error($_ARRAYLANG['TXT_SHOP_ORDER_ERROR_STORING']);
            return self::errorHandler();
        }
        \Message::ok($_ARRAYLANG['TXT_DATA_RECORD_UPDATED_SUCCESSFUL']);
        // Send an email to the customer, if requested
        if (!empty($_POST['sendMail'])) {
            $result = ShopLibrary::sendConfirmationMail($order_id);
            if (!$result) {
                return \Message::error($_ARRAYLANG['TXT_MESSAGE_SEND_ERROR']);
            }
            \Message::ok(sprintf($_ARRAYLANG['TXT_EMAIL_SEND_SUCCESSFULLY'], $result));
        }
        return true;
    }


    /**
     * Clear all shipment related properties
     *
     * Called by insert() when there is no shipment ID
     */
    function clearShipment()
    {
        $this->address = null;
        $this->city = null;
        $this->company = null;
        $this->country_id = null;
        $this->firstname = null;
        $this->lastname = null;
        $this->phone = null;
        $this->gender = null;
        $this->shipment_amount = 0;
        $this->shipment_id = null;
        $this->zip = null;
    }


    /**
     * Deletes this Order
     * @return  boolean                 True on success, false otherwise
     */
    function delete()
    {
        return self::deleteById($this->id);
    }


    /**
     * Deletes the Order with the given ID
     *
     * @param   integer   $order_id     The Order ID
     * @param   boolean   $stockUpdate  True to update stock of the product
     *
     * @return  boolean                 True on success, false otherwise
     */
    static function deleteById($order_id, $stockUpdate = false)
    {
        global $objDatabase, $_ARRAYLANG;

        $order_id = contrexx_input2int($order_id);
        if (empty($order_id)) {
            return false;
        }

        // Deactivate accounts autocreated for downloads
        $objOrder = self::getById($order_id);
        if (   $objOrder
            && $objCustomer = Customer::getById($objOrder->customer_id())
        ) {
            $customer_email =
                Orders::usernamePrefix."_${order_id}_%-" .
                $objCustomer->email();
            $objUser = \FWUser::getFWUserObject()->objUser->getUsers(
                array('email' => $customer_email)
            );
            if ($objUser) {
                while (!$objUser->EOF) {
                    $objUser->setActiveStatus(false);
                    if (!$objUser->store()) {
                        return false;
                    }
                    $objUser->next();
                }
            }
        }

        $objUser = \FWUser::getFWUserObject()->objUser;
        $query = '
            UPDATE
                `' . DBPREFIX . 'module_shop' . MODULE_INDEX . '_orders`
            SET
                `status` = '. Order::STATUS_DELETED .',
                `modified_by` = "' . contrexx_raw2db($objUser->getUsername()) . '",
                `modified_on` = "' . date('Y-m-d H:i:s') . '"
            WHERE
                `id`='. $order_id;
        if (!$objDatabase->Execute($query)) {
            return \Message::error($_ARRAYLANG['TXT_SHOP_ERROR_DELETING_ORDER']);
        }
        if ($stockUpdate) {
            $order = new static();
            $order->setId($order_id);
            $order->updateStock();
        }
        return true;
    }

    /**
     * Update related product stock
     *
     * @param boolean $increaseStock True to increase stock, false to decrease
     */
    public function updateStock($increaseStock = true)
    {
        global $_ARRAYLANG;

        $arrItems  = $this->getItems();
        foreach ($arrItems as $item) {

            $product = Product::getById($item['product_id']);
            if (!$product) {
                \DBG::log(sprintf(
                    $_ARRAYLANG['TXT_SHOP_PRODUCT_NOT_FOUND'],
                    $item['product_id']
                ));
                continue;
            }
            $stock = $product->stock();
            if ($increaseStock) {
                $stock += $item['quantity'];
            } else {
                $stock -= $item['quantity'];
            }

            $product->stock($stock);
            $product->store();
        }
    }

    /**
     * Returns an array of item IDs for the given Order ID
     *
     * Mind that the returned array may be empty.
     * On failure, returns null.
     * @param   integer   $order_id   The Order ID
     * @return  array                 The array of item IDs on success,
     *                                null otherwise
     */
    static function getItemIdArray($order_id)
    {
        global $objDatabase, $_ARRAYLANG;

        $order_id = intval($order_id);
        $query = "
            SELECT `id`
              FROM `".DBPREFIX."module_shop".MODULE_INDEX."_order_items`
             WHERE `order_id`=$order_id";
        $objResult = $objDatabase->Execute($query);
        if (!$objResult) {
            \Message::error(
                $_ARRAYLANG['TXT_SHOP_ERROR_QUERYING_ORDER_ITEMS']);
            return null;
        }
        $arrItemId = array();
        while (!$objResult->EOF) {
            $arrItemId[] = $objResult->fields['id'];
            $objResult->MoveNext();
        }
        return $arrItemId;
    }


    /**
     * Inserts a single item into the database
     *
     * Note that all parameters are mandatory.
     * All of $order_id, $product_id, and $quantity must be greater than zero.
     * The $weight must not be negative.
     * If there are no options, set $arrOptions to the empty array.
     * Sets an error Message in case there is anything wrong.
     * @global  ADONewConnection    $objDatabase
     * @global  array   $_ARRAYLANG
     * @param   integer $order_id       The Order ID
     * @param   integer $product_id     The Product ID
     * @param   string  $name           The item name
     * @param   float   $price          The item price (one unit)
     * @param   integer $quantity       The quantity (in units)
     * @param   float   $vat_rate       The applicable VAT rate
     * @param   integer $weight         The item weight (in grams, one unit)
     * @param   array   $arrOptions     The array of selected options
     * @return  boolean                 True on success, false otherwise
     * @static
     */
    static function insertItem($order_id, $product_id, $name, $price, $quantity,
        $vat_rate, $weight, $arrOptions
    ) {
        global $objDatabase, $_ARRAYLANG;

        $product_id = intval($product_id);
        if ($product_id <= 0) {
            return \Message::error($_ARRAYLANG['TXT_SHOP_ORDER_ITEM_ERROR_INVALID_PRODUCT_ID']);
        }
        $quantity = intval($quantity);
        if ($quantity <= 0) {
            return \Message::error($_ARRAYLANG['TXT_SHOP_ORDER_ITEM_ERROR_INVALID_QUANTITY']);
        }
        $weight = intval($weight);
        if ($weight < 0) {
            return \Message::error($_ARRAYLANG['TXT_SHOP_ORDER_ITEM_ERROR_INVALID_WEIGHT']);
        }
        $query = "
            INSERT INTO ".DBPREFIX."module_shop".MODULE_INDEX."_order_items (
                order_id, product_id, product_name,
                price, quantity, vat_rate, weight
            ) VALUES (
                $order_id, $product_id, '".addslashes($name)."',
                '".\Cx\Modules\Shop\Controller\CurrencyController::formatPrice($price)."', $quantity,
                '".Vat::format($vat_rate)."', $weight
            )";
        $objResult = $objDatabase->Execute($query);
        if (!$objResult) {
            return \Message::error($_ARRAYLANG['TXT_SHOP_ORDER_ITEM_ERROR_INSERTING']);
        }
        $item_id = $objDatabase->Insert_ID();
        foreach ($arrOptions as $attribute_id => $arrOptionIds) {
            if (!self::insertAttribute($item_id, $attribute_id, $arrOptionIds)) {
                return false;
            }
        }
        return true;
    }


    /**
     * Updates a single item in the database
     *
     * Note that all parameters are mandatory.
     * All of $item_id, $product_id, and $quantity must be greater than zero.
     * The $weight must not be negative.
     * If there are no options, set $arrOptions to the empty array.
     * Sets an error Message in case there is anything wrong.
     * @global  ADONewConnection    $objDatabase
     * @global  array   $_ARRAYLANG
     * @param   integer $item_id        The item ID
     * @param   integer $product_id     The Product ID
     * @param   string  $name           The item name
     * @param   float   $price          The item price (one unit)
     * @param   integer $quantity       The quantity (in units)
     * @param   float   $vat_rate       The applicable VAT rate
     * @param   integer $weight         The item weight (in grams, one unit)
     * @param   array   $arrOptions     The array of selected options
     * @return  boolean                 True on success, false otherwise
     * @static
     */
    static function updateItem($item_id, $product_id, $name, $price, $quantity,
        $vat_rate, $weight, $arrOptions
    ) {
        global $objDatabase, $_ARRAYLANG;

        $item_id = intval($item_id);
        if ($item_id <= 0) {
            return \Message::error($_ARRAYLANG['TXT_SHOP_ORDER_ITEM_ERROR_INVALID_ITEM_ID']);
        }
        $product_id = intval($product_id);
        if ($product_id <= 0) {
            return \Message::error($_ARRAYLANG['TXT_SHOP_ORDER_ITEM_ERROR_INVALID_PRODUCT_ID']);
        }
        $quantity = intval($quantity);
        if ($quantity <= 0) {
            return \Message::error($_ARRAYLANG['TXT_SHOP_ORDER_ITEM_ERROR_INVALID_QUANTITY']);
        }
        $weight = intval($weight);
        if ($weight < 0) {
            return \Message::error($_ARRAYLANG['TXT_SHOP_ORDER_ITEM_ERROR_INVALID_WEIGHT']);
        }
        $query = "
            UPDATE ".DBPREFIX."module_shop".MODULE_INDEX."_order_items
               SET `product_id`=$product_id,
                   `product_name`='".addslashes($name)."',
                   `price`='".\Cx\Modules\Shop\Controller\CurrencyController::formatPrice($price)."',
                   `quantity`=$quantity,
                   `vat_rate`='".Vat::format($vat_rate)."',
                   `weight`=$weight
             WHERE `id`=$item_id";
        $objResult = $objDatabase->Execute($query);
        if (!$objResult) {
            return \Message::error($_ARRAYLANG['TXT_SHOP_ORDER_ITEM_ERROR_UPDATING']);
        }

        // don't save options if there is none
        if (empty($arrOptions)) return true;

        if (!self::deleteOptions($item_id)) return false;
        foreach ($arrOptions as $attribute_id => $arrOptionIds) {
            if (!self::insertAttribute($item_id, $attribute_id, $arrOptionIds)) {
                return false;
            }
        }
        return true;
    }


    /**
     * Add the option IDs of the given Attribute ID to the Order item
     *
     * Will add error messages using {@see Message::error()}, if any.
     * The $arrOptionIds array must have the form
     *  array(attribute_id => array(option_id, ...))
     * @param   integer   $item_id        The Order item ID
     * @param   integer   $attribute_id   The Attribute ID
     * @param   array     $arrOptionIds   The array of option IDs
     * @return  boolean                   True on success, false otherwise
     * @static
     */
    static function insertAttribute($item_id, $attribute_id, $arrOptionIds)
    {
        global $objDatabase, $_ARRAYLANG;

        $objAttribute = Attribute::getById($attribute_id);
        if (!$objAttribute) {
            return \Message::error($_ARRAYLANG['TXT_SHOP_ERROR_INVALID_ATTRIBUTE_ID']);
        }
        $name = $objAttribute->getName();
        $_arrOptions = Attributes::getOptionArrayByAttributeId($attribute_id);
        foreach ($arrOptionIds as $option_id) {
            $arrOption = null;
            if ($objAttribute->getType() >= Attribute::TYPE_TEXT_OPTIONAL) {
                // There is exactly one option record for these
                // types.  Use that and overwrite the empty name with
                // the text or file name.
                $arrOption = current($_arrOptions);
                $arrOption['value'] = $option_id;
            } else {
                // Use the option record for the option ID given
                $arrOption = $_arrOptions[$option_id];
            }
            if (!is_array($arrOption)) {
                \Message::error($_ARRAYLANG['TXT_SHOP_ERROR_INVALID_OPTION_ID']);
                continue;
            }
            $query = "
                INSERT INTO `".DBPREFIX."module_shop".MODULE_INDEX."_order_attributes`
                   SET `item_id`=$item_id,
                       `attribute_name`='".addslashes($name)."',
                       `option_name`='".addslashes($arrOption['value'])."',
                       `price`='".$arrOption['price']."'";
            $objResult = $objDatabase->Execute($query);
            if (!$objResult) {
                return \Message::error($_ARRAYLANG['TXT_ERROR_INSERTING_ORDER_ITEM_ATTRIBUTE']);
            }
        }
        return true;
    }


    /**
     * Delete the options associated with the given item ID
     *
     * Will add error messages using {@see Message::error()}, if any.
     * @param   integer   $item_id        The Order item ID
     * @return  boolean                   True on success, false otherwise
     */
    static function deleteOptions($item_id)
    {
        global $objDatabase, $_ARRAYLANG;

        $item_id = intval($item_id);
        if ($item_id > 0) {
            $query = "
                DELETE FROM `".DBPREFIX."module_shop".MODULE_INDEX."_order_attributes`
                 WHERE `item_id`=$item_id";
            if ($objDatabase->Execute($query)) {
                return true;
            }
        }
        return \Message::error(
            $_ARRAYLANG['TXT_SHOP_ORDER_ITEM_ERROR_DELETING_ATTRIBUTES']);
    }


    /**
     * Returns the most recently used language ID found in the order table
     * for the given Customer ID
     *
     * Note that this method must be used for migrating old Shop Customers ONLY.
     * It returns null if no order is found, or on error.
     * @param   integer   $customer_id      The Customer ID
     * @return  integer                     The language ID on success,
     *                                      null otherwise
     */
    static function getLanguageIdByCustomerId($customer_id)
    {
        global $objDatabase;

        $query = "
            SELECT `lang_id`
              FROM `".DBPREFIX."module_shop".MODULE_INDEX."_orders`
             WHERE `customer_id`=$customer_id
             ORDER BY `id` DESC";
        $objResult = $objDatabase->Execute($query);
        if (!$objResult || $objResult->EOF) return null;
        return $objResult->fields['lang_id'];
    }


    /**
     * Handles database errors
     *
     * Also migrates the old database structure to the new one
     * @return  boolean             False.  Always.
     */
    static function errorHandler()
    {
// Order
        ShopSettings::errorHandler();
        \Cx\Core\Country\Controller\Country::errorHandler();

        $table_name = DBPREFIX.'module_shop_order_items';
        $table_structure = array(
            'id' => array('type' => 'INT(10)', 'unsigned' => true, 'auto_increment' => true, 'primary' => true, 'renamefrom' => 'order_items_id'),
            'order_id' => array('type' => 'INT(10)', 'unsigned' => true, 'default' => '0', 'renamefrom' => 'orderid'),
            'product_id' => array('type' => 'INT(10)', 'unsigned' => true, 'default' => '0', 'renamefrom' => 'productid'),
            'product_name' => array('type' => 'VARCHAR(255)', 'default' => ''),
            'price' => array('type' => 'DECIMAL(9,2)', 'unsigned' => true, 'default' => '0.00'),
            'quantity' => array('type' => 'INT(10)', 'unsigned' => true, 'default' => '0'),
            'vat_rate' => array('type' => 'DECIMAL(5,2)', 'unsigned' => true, 'notnull' => false, 'default' => null, 'renamefrom' => 'vat_percent'),
            'weight' => array('type' => 'INT(10)', 'unsigned' => true, 'default' => '0'),
        );
        $table_index = array(
            'order' => array('fields' => array('order_id')));
        \Cx\Lib\UpdateUtil::table($table_name, $table_structure, $table_index);

        $table_name = DBPREFIX.'module_shop_order_attributes';
        if (!\Cx\Lib\UpdateUtil::table_exist($table_name)) {
            $table_name_old = DBPREFIX.'module_shop_order_items_attributes';
            $table_structure = array(
                'id' => array('type' => 'INT(10)', 'unsigned' => true, 'auto_increment' => true, 'primary' => true, 'renamefrom' => 'orders_items_attributes_id'),
                'item_id' => array('type' => 'INT(10)', 'unsigned' => true, 'default' => '0', 'renamefrom' => 'order_items_id'),
                'attribute_name' => array('type' => 'VARCHAR(255)', 'default' => '', 'renamefrom' => 'product_option_name'),
                'option_name' => array('type' => 'VARCHAR(255)', 'default' => '', 'renamefrom' => 'product_option_value'),
                'price' => array('type' => 'DECIMAL(9,2)', 'unsigned' => false, 'default' => '0.00', 'renamefrom' => 'product_option_values_price'),
            );
            $table_index = array(
                'item_id' => array('fields' => array('item_id')));
            \Cx\Lib\UpdateUtil::table($table_name_old, $table_structure, $table_index);
            \Cx\Lib\UpdateUtil::table_rename($table_name_old, $table_name);
        }

        // LSV
        $table_name = DBPREFIX.'module_shop_lsv';
        $table_structure = array(
            'order_id' => array('type' => 'INT(10)', 'unsigned' => true, 'primary' => true, 'renamefrom' => 'id'),
            'holder' => array('type' => 'tinytext', 'default' => ''),
            'bank' => array('type' => 'tinytext', 'default' => ''),
            'blz' => array('type' => 'tinytext', 'default' => ''),
        );
        $table_index = array();
        \Cx\Lib\UpdateUtil::table($table_name, $table_structure, $table_index);

        $table_name = DBPREFIX.'module_shop_orders';
        $table_structure = array(
            'id' => array('type' => 'INT(10)', 'unsigned' => true, 'auto_increment' => true, 'primary' => true, 'renamefrom' => 'orderid'),
            'customer_id' => array('type' => 'INT(10)', 'unsigned' => true, 'default' => '0', 'renamefrom' => 'customerid'),
            'currency_id' => array('type' => 'INT(10)', 'unsigned' => true, 'default' => '0', 'renamefrom' => 'selected_currency_id'),
            'shipment_id' => array('type' => 'INT(10)', 'unsigned' => true, 'notnull' => false, 'default' => null, 'renamefrom' => 'shipping_id'),
            'payment_id' => array('type' => 'INT(10)', 'unsigned' => true, 'default' => '0'),
            'lang_id' => array('type' => 'INT(10)', 'unsigned' => true, 'default' => '0', 'renamefrom' => 'customer_lang'),
            'status' => array('type' => 'TINYINT(1)', 'unsigned' => true, 'default' => '0', 'renamefrom' => 'order_status'),
            'sum' => array('type' => 'DECIMAL(9,2)', 'unsigned' => true, 'default' => '0.00', 'renamefrom' => 'currency_order_sum'),
            'vat_amount' => array('type' => 'DECIMAL(9,2)', 'unsigned' => true, 'default' => '0.00', 'renamefrom' => 'tax_price'),
            'shipment_amount' => array('type' => 'DECIMAL(9,2)', 'unsigned' => true, 'default' => '0.00', 'renamefrom' => 'currency_ship_price'),
            'payment_amount' => array('type' => 'DECIMAL(9,2)', 'unsigned' => true, 'default' => '0.00', 'renamefrom' => 'currency_payment_price'),
// 20111017 Added billing address
            'billing_gender' => array('type' => 'VARCHAR(50)', 'notnull' => false, 'default' => null),
            'billing_company' => array('type' => 'VARCHAR(100)', 'notnull' => false, 'default' => null),
            'billing_firstname' => array('type' => 'VARCHAR(40)', 'notnull' => false, 'default' => null),
            'billing_lastname' => array('type' => 'VARCHAR(100)', 'notnull' => false, 'default' => null),
            'billing_address' => array('type' => 'VARCHAR(40)', 'notnull' => false, 'default' => null),
            'billing_city' => array('type' => 'VARCHAR(50)', 'notnull' => false, 'default' => null),
            'billing_zip' => array('type' => 'VARCHAR(10)', 'notnull' => false, 'default' => null),
            'billing_country_id' => array('type' => 'INT(10)', 'unsigned' => true, 'notnull' => false, 'default' => null),
            'billing_phone' => array('type' => 'VARCHAR(20)', 'notnull' => false, 'default' => null),
            'billing_fax' => array('type' => 'VARCHAR(20)', 'notnull' => false, 'default' => null),
            'billing_email' => array('type' => 'VARCHAR(255)', 'notnull' => false, 'default' => null),
            'gender' => array('type' => 'VARCHAR(50)', 'notnull' => false, 'default' => null, 'renamefrom' => 'ship_prefix'),
            'company' => array('type' => 'VARCHAR(100)', 'notnull' => false, 'default' => null, 'renamefrom' => 'ship_company'),
            'firstname' => array('type' => 'VARCHAR(40)', 'notnull' => false, 'default' => null, 'renamefrom' => 'ship_firstname'),
            'lastname' => array('type' => 'VARCHAR(100)', 'notnull' => false, 'default' => null, 'renamefrom' => 'ship_lastname'),
            'address' => array('type' => 'VARCHAR(40)', 'notnull' => false, 'default' => null, 'renamefrom' => 'ship_address'),
            'city' => array('type' => 'VARCHAR(50)', 'notnull' => false, 'default' => null, 'renamefrom' => 'ship_city'),
            'zip' => array('type' => 'VARCHAR(10)', 'notnull' => false, 'default' => null, 'renamefrom' => 'ship_zip'),
            'country_id' => array('type' => 'INT(10)', 'unsigned' => true, 'notnull' => false, 'default' => null, 'renamefrom' => 'ship_country_id'),
            'phone' => array('type' => 'VARCHAR(20)', 'notnull' => false, 'default' => null, 'renamefrom' => 'ship_phone'),
            'ip' => array('type' => 'VARCHAR(50)', 'default' => '', 'renamefrom' => 'customer_ip'),
            'note' => array('type' => 'TEXT', 'default' => '', 'renamefrom' => 'customer_note'),
            'date_time' => array('type' => 'TIMESTAMP', 'default' => '0000-00-00 00:00:00', 'renamefrom' => 'order_date'),
            'modified_on' => array('type' => 'TIMESTAMP', 'default' => null, 'notnull' => false, 'renamefrom' => 'last_modified'),
            'modified_by' => array('type' => 'VARCHAR(50)', 'notnull' => false, 'default' => null),
        );
        $table_index = array(
            'status' => array('fields' => array('status')));
        \Cx\Lib\UpdateUtil::table($table_name, $table_structure, $table_index);

// TODO: TEST
// Migrate present Customer addresses to the new billing address fields.
// Note that this method is also called in Customer::errorHandler() *before*
// any Customer is modified.  Thus, we can safely depend on the old
// Customer table in one way -- if it doesn't exist, all Orders and Customers
// have been successfully migrated already.
        $table_name_customer = DBPREFIX."module_shop_customers";
        if (\Cx\Lib\UpdateUtil::table_exist($table_name_customer)) {
// On the other hand, there may have been an error somewhere in between
// altering the Orders table and moving Customers to the Users table.
// So, to be on the safe side, we will only update Orders where the billing
// address fields are all NULL, as is the case just after the alteration
// of the Orders table above.
// Also note that any inconsistencies involving missing Customer records will
// be left over as-is and may later be handled in the backend.
            $objResult = \Cx\Lib\UpdateUtil::sql("
                SELECT DISTINCT `customer_id`,
                       `customer`.`prefix`,
                       `customer`.`firstname`, `customer`.`lastname`,
                       `customer`.`company`, `customer`.`address`,
                       `customer`.`city`, `customer`.`zip`,
                       `customer`.`country_id`,
                       `customer`.`phone`, `customer`.`fax`,
                       `customer`.`email`
                  FROM `$table_name`
                  JOIN `$table_name_customer` AS `customer`
                    ON `customerid`=`customer_id`
                 WHERE `billing_gender` IS NULL
                   AND `billing_company` IS NULL
                   AND `billing_firstname` IS NULL
                   AND `billing_lastname` IS NULL
                   AND `billing_address` IS NULL
                   AND `billing_city` IS NULL
                   AND `billing_zip` IS NULL
                   AND `billing_country_id` IS NULL
                   AND `billing_phone` IS NULL
                   AND `billing_fax` IS NULL
                   AND `billing_email` IS NULL");
            while ($objResult && !$objResult->EOF) {
                $customer_id = $objResult->fields['customer_id'];
                $gender = 'gender_unknown';
                if (preg_match('/^(?:frau|mad|mme|signora|miss)/i',
                    $objResult->fields['prefix'])) {
                    $gender = 'gender_female';
                } elseif (preg_match('/^(?:herr|mon|signore|mister|mr)/i',
                    $objResult->fields['prefix'])) {
                    $gender = 'gender_male';
                }
                \Cx\Lib\UpdateUtil::sql("
                    UPDATE `$table_name`
                       SET `billing_gender`='".addslashes($gender)."',
                           `billing_company`='".addslashes($objResult->fields['company'])."',
                           `billing_firstname`='".addslashes($objResult->fields['firstname'])."',
                           `billing_lastname`='".addslashes($objResult->fields['lastname'])."',
                           `billing_address`='".addslashes($objResult->fields['address'])."',
                           `billing_city`='".addslashes($objResult->fields['city'])."',
                           `billing_zip`='".addslashes($objResult->fields['zip'])."',
                           `billing_country_id`=".intval($objResult->fields['country_id']).",
                           `billing_phone`='".addslashes($objResult->fields['phone'])."',
                           `billing_fax`='".addslashes($objResult->fields['fax'])."',
                           `billing_email`='".addslashes($objResult->fields['email'])."'
                     WHERE `customer_id`=$customer_id
                       AND `billing_gender` IS NULL
                       AND `billing_company` IS NULL
                       AND `billing_firstname` IS NULL
                       AND `billing_lastname` IS NULL
                       AND `billing_address` IS NULL
                       AND `billing_city` IS NULL
                       AND `billing_zip` IS NULL
                       AND `billing_country_id` IS NULL
                       AND `billing_phone` IS NULL
                       AND `billing_fax` IS NULL
                       AND `billing_email` IS NULL");
                $objResult->MoveNext();
            }
        }

        // Finally, update the migrated Order records with the proper gender
        // strings as used in the User class hierarchy as well
        $objResult = \Cx\Lib\UpdateUtil::sql("
            SELECT `id`, `gender`
              FROM `$table_name`
             WHERE `gender` NOT IN
                   ('gender_male', 'gender_female', 'gender_undefined')");
        while ($objResult && !$objResult->EOF) {
            $gender = 'gender_unknown';
            if (preg_match('/^(?:frau|mad|mme|signora|miss)/i',
                $objResult->fields['gender'])) {
                $gender = 'gender_female';
            } elseif (preg_match('/^(?:herr|mon|signore|mister|mr)/i',
                $objResult->fields['gender'])) {
                $gender = 'gender_male';
            }
            \Cx\Lib\UpdateUtil::sql("
                UPDATE `$table_name`
                   SET `gender`='".addslashes($gender)."'
                 WHERE `id`=".$objResult->fields['id']);
            $objResult->MoveNext();
        }

        // Always
        return false;
    }


    /**
     * Returns an array of items contained in this Order
     * @global  ADONewConnection    $objDatabase
     * @global  array               $_ARRAYLANG
     * @return  array                               The items array on success,
     *                                              false otherwise
     * @todo    Let items be handled by their own class
     */
    function getItems($withHtmlNotation = true)
    {
        global $objDatabase, $_ARRAYLANG;

        $query = "
            SELECT `id`, `product_id`, `product_name`,
                   `price`, `quantity`, `vat_rate`, `weight`
              FROM `".DBPREFIX."module_shop".MODULE_INDEX."_order_items`
             WHERE `order_id`=?";
        $objResult = $objDatabase->Execute($query, array($this->id));
        if (!$objResult) {
            return self::errorHandler();
        }
        $arrProductOptions = $this->getOptionArray($withHtmlNotation);
        $items = array();
        while (!$objResult->EOF) {
            $item_id = $objResult->fields['id'];
            $product_id = $objResult->fields['product_id'];
            $name = $objResult->fields['product_name'];
            $price = $objResult->fields['price'];
            $quantity = $objResult->fields['quantity'];
            $vat_rate = $objResult->fields['vat_rate'];
            // Get missing product details
            $objProduct = Product::getById($product_id);
            if (!$objProduct) {
                \Message::warning(sprintf(
                    $_ARRAYLANG['TXT_SHOP_PRODUCT_NOT_FOUND'], $product_id));
                $objProduct = new Product('', 0, $name, '', $price,
                    0, 0, 0, $product_id);
            }
            $code = $objProduct->code();
            $distribution = $objProduct->distribution();
            $vat_id = $objProduct->vat_id();
            $weight = '0';
            if ($distribution != 'download') {
                $weight = $objResult->fields['weight'];
            }
            $item = array(
                'product_id' => $product_id,
                'quantity' => $quantity,
                'name' => $name,
                'price' => $price,
                'item_id' => $item_id,
                'code' => $code,
                'vat_id' => $vat_id,
                'vat_rate' => $vat_rate,
                'weight' => $weight,
                'attributes' => array(),
            );
            if (isset($arrProductOptions[$item_id])) {
                $item['attributes'] = $arrProductOptions[$item_id];
            }
            $items[] = $item;
            $objResult->MoveNext();
        }
        return $items;
    }
}
