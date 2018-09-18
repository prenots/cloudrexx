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
 * Class DiscountCoupon
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
 * With the discount coupon, an order can be discounted. This coupon can be
 * restricted to a product, payment, customer or minimum amount.  The code must
 * have at least 6 digits and be unique.
 *
 * @copyright   CLOUDREXX CMS - Cloudrexx AG Thun
 * @author      Sam Hawkes <info@cloudrexx.com>
 * @package     cloudrexx
 * @subpackage  module_shop
 * @version     5.0.0
 */
class DiscountCoupon extends \Cx\Model\Base\EntityBase {
    /**
     * @var integer
     */
    protected $id;
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
    protected $paymentId;

    /**
     * @var integer
     */
    protected $productId;

    /**
     * @var integer
     */
    protected $startTime;

    /**
     * @var integer
     */
    protected $endTime;

    /**
     * @var integer
     */
    protected $uses;

    /**
     * @var boolean
     */
    protected $global;

    /**
     * @var string
     */
    protected $minimumAmount;

    /**
     * @var string
     */
    protected $discountAmount;

    /**
     * @var string
     */
    protected $discountRate;

    /**
     * @var \Cx\Modules\Shop\Model\Entity\Payment
     */
    protected $payment;

    /**
     * @var \Cx\Modules\Shop\Model\Entity\Products
     */
    protected $products;


    /**
     * Set id
     *
     * @param integer $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set code
     *
     * @param string $code
     * @return DiscountCoupon
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
     * @return DiscountCoupon
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
     * Set paymentId
     *
     * @param integer $paymentId
     * @return DiscountCoupon
     */
    public function setPaymentId($paymentId)
    {
        $this->paymentId = $paymentId;

        return $this;
    }

    /**
     * Get paymentId
     *
     * @return integer 
     */
    public function getPaymentId()
    {
        return $this->paymentId;
    }

    /**
     * Set productId
     *
     * @param integer $productId
     * @return DiscountCoupon
     */
    public function setProductId($productId)
    {
        $this->productId = $productId;

        return $this;
    }

    /**
     * Get productId
     *
     * @return integer 
     */
    public function getProductId()
    {
        return $this->productId;
    }

    /**
     * Set startTime
     *
     * @param integer $startTime
     * @return DiscountCoupon
     */
    public function setStartTime($startTime)
    {
        $this->startTime = $startTime;

        return $this;
    }

    /**
     * Get startTime
     *
     * @return integer 
     */
    public function getStartTime()
    {
        return $this->startTime;
    }

    /**
     * Set endTime
     *
     * @param integer $endTime
     * @return DiscountCoupon
     */
    public function setEndTime($endTime)
    {
        $this->endTime = $endTime;

        return $this;
    }

    /**
     * Get endTime
     *
     * @return integer 
     */
    public function getEndTime()
    {
        return $this->endTime;
    }

    /**
     * Set uses
     *
     * @param integer $uses
     * @return DiscountCoupon
     */
    public function setUses($uses)
    {
        $this->uses = $uses;

        return $this;
    }

    /**
     * Get uses
     *
     * @return integer 
     */
    public function getUses()
    {
        return $this->uses;
    }

    /**
     * Set global
     *
     * @param boolean $global
     * @return DiscountCoupon
     */
    public function setGlobal($global)
    {
        $this->global = $global;

        return $this;
    }

    /**
     * Get global
     *
     * @return boolean 
     */
    public function getGlobal()
    {
        return $this->global;
    }

    /**
     * Set minimumAmount
     *
     * @param string $minimumAmount
     * @return DiscountCoupon
     */
    public function setMinimumAmount($minimumAmount)
    {
        $this->minimumAmount = $minimumAmount;

        return $this;
    }

    /**
     * Get minimumAmount
     *
     * @return string 
     */
    public function getMinimumAmount()
    {
        return $this->minimumAmount;
    }

    /**
     * Set discountAmount
     *
     * @param string $discountAmount
     * @return DiscountCoupon
     */
    public function setDiscountAmount($discountAmount)
    {
        $this->discountAmount = $discountAmount;

        return $this;
    }

    /**
     * Get discountAmount
     *
     * @return string 
     */
    public function getDiscountAmount()
    {
        return $this->discountAmount;
    }

    /**
     * Set discountRate
     *
     * @param string $discountRate
     * @return DiscountCoupon
     */
    public function setDiscountRate($discountRate)
    {
        $this->discountRate = $discountRate;

        return $this;
    }

    /**
     * Get discountRate
     *
     * @return string 
     */
    public function getDiscountRate()
    {
        return $this->discountRate;
    }

    /**
     * Set payment
     *
     * @param \Cx\Modules\Shop\Model\Entity\Payment $payment
     * @return DiscountCoupon
     */
    public function setPayment(\Cx\Modules\Shop\Model\Entity\Payment $payment = null)
    {
        $this->payment = $payment;

        return $this;
    }

    /**
     * Get payment
     *
     * @return \Cx\Modules\Shop\Model\Entity\Payment 
     */
    public function getPayment()
    {
        return $this->payment;
    }

    /**
     * Set products
     *
     * @param \Cx\Modules\Shop\Model\Entity\Products $products
     * @return DiscountCoupon
     */
    public function setProducts(\Cx\Modules\Shop\Model\Entity\Products $products = null)
    {
        $this->products = $products;

        return $this;
    }

    /**
     * Get products
     *
     * @return \Cx\Modules\Shop\Model\Entity\Products 
     */
    public function getProducts()
    {
        return $this->products;
    }
}
