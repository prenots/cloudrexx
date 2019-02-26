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

}
