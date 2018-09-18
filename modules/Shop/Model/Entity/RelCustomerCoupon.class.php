<?php
/**
 * Cloudrexx
 *
 * @link      http://www.cloudrexx.com
 * @copyright Cloudrexx AG 2007-2018
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
 * Class RelCustomerCoupon
 *
 * @copyright   CLOUDREXX CMS - Cloudrexx AG Thun
 * @author      Sam Hawkes <info@cloudrexx.com>
 * @package     cloudrexx
 * @subpackage  module_shop
 * @version     5.0.0
 */
namespace Cx\Modules\Shop\Model\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * A Coupon which was used by a customer in an order.
 *
 * @copyright   CLOUDREXX CMS - Cloudrexx AG Thun
 * @author      Sam Hawkes <info@cloudrexx.com>
 * @package     cloudrexx
 * @subpackage  module_shop
 * @version     5.0.0
 */
class RelCustomerCoupon extends \Cx\Model\Base\EntityBase {
    /**
     * @var string
     */
    protected $code;

    /**
     * @var integer
     */
    protected $customerId;

    /**
     * @var integer
     */
    protected $orderId;

    /**
     * @var integer
     */
    protected $count;

    /**
     * @var string
     */
    protected $amount;

    /**
     * @var \Cx\Modules\Shop\Model\Entity\Orders
     */
    protected $orders;


    /**
     * Set code
     *
     * @param string $code
     * @return RelCustomerCoupon
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code
     *
     * @return string 
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set customerId
     *
     * @param integer $customerId
     * @return RelCustomerCoupon
     */
    public function setCustomerId($customerId)
    {
        $this->customerId = $customerId;

        return $this;
    }

    /**
     * Get customerId
     *
     * @return integer 
     */
    public function getCustomerId()
    {
        return $this->customerId;
    }

    /**
     * Set orderId
     *
     * @param integer $orderId
     * @return RelCustomerCoupon
     */
    public function setOrderId($orderId)
    {
        $this->orderId = $orderId;

        return $this;
    }

    /**
     * Get orderId
     *
     * @return integer 
     */
    public function getOrderId()
    {
        return $this->orderId;
    }

    /**
     * Set count
     *
     * @param integer $count
     * @return RelCustomerCoupon
     */
    public function setCount($count)
    {
        $this->count = $count;

        return $this;
    }

    /**
     * Get count
     *
     * @return integer 
     */
    public function getCount()
    {
        return $this->count;
    }

    /**
     * Set amount
     *
     * @param string $amount
     * @return RelCustomerCoupon
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * Get amount
     *
     * @return string 
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * Set orders
     *
     * @param \Cx\Modules\Shop\Model\Entity\Orders $orders
     * @return RelCustomerCoupon
     */
    public function setOrders(\Cx\Modules\Shop\Model\Entity\Orders $orders = null)
    {
        $this->orders = $orders;

        return $this;
    }

    /**
     * Get orders
     *
     * @return \Cx\Modules\Shop\Model\Entity\Orders 
     */
    public function getOrders()
    {
        return $this->orders;
    }
}
