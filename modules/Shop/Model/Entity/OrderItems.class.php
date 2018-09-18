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
 * Class OrderItems
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
 * Product of an order, contains the total weight, quantity, price and the
 * product name. This allows the product to be changed, but the price and
 * name are the same as when the order was made.
 *
 * @copyright   CLOUDREXX CMS - Cloudrexx AG Thun
 * @author      Sam Hawkes <info@cloudrexx.com>
 * @package     cloudrexx
 * @subpackage  module_shop
 * @version     5.0.0
 */
class OrderItems extends \Cx\Model\Base\EntityBase {
    /**
     * @var integer
     */
    protected $id;

    /**
     * @var integer
     */
    protected $orderId;

    /**
     * @var integer
     */
    protected $productId;

    /**
     * @var string
     */
    protected $productName;

    /**
     * @var string
     */
    protected $price;

    /**
     * @var integer
     */
    protected $quantity;

    /**
     * @var string
     */
    protected $vatRate;

    /**
     * @var integer
     */
    protected $weight;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    protected $orderAttributes;

    /**
     * @var \Cx\Modules\Shop\Model\Entity\Orders
     */
    protected $orders;

    /**
     * @var \Cx\Modules\Shop\Model\Entity\Products
     */
    protected $products;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->orderAttributes = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set orderId
     *
     * @param integer $orderId
     * @return OrderItems
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
     * Set productId
     *
     * @param integer $productId
     * @return OrderItems
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
     * Set productName
     *
     * @param string $productName
     * @return OrderItems
     */
    public function setProductName($productName)
    {
        $this->productName = $productName;

        return $this;
    }

    /**
     * Get productName
     *
     * @return string 
     */
    public function getProductName()
    {
        return $this->productName;
    }

    /**
     * Set price
     *
     * @param string $price
     * @return OrderItems
     */
    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Get price
     *
     * @return string 
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Set quantity
     *
     * @param integer $quantity
     * @return OrderItems
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * Get quantity
     *
     * @return integer 
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * Set vatRate
     *
     * @param string $vatRate
     * @return OrderItems
     */
    public function setVatRate($vatRate)
    {
        $this->vatRate = $vatRate;

        return $this;
    }

    /**
     * Get vatRate
     *
     * @return string 
     */
    public function getVatRate()
    {
        return $this->vatRate;
    }

    /**
     * Set weight
     *
     * @param integer $weight
     * @return OrderItems
     */
    public function setWeight($weight)
    {
        $this->weight = $weight;

        return $this;
    }

    /**
     * Get weight
     *
     * @return integer 
     */
    public function getWeight()
    {
        return $this->weight;
    }

    /**
     * Add orderAttributes
     *
     * @param \Cx\Modules\Shop\Model\Entity\OrderAttributes $orderAttributes
     * @return OrderItems
     */
    public function addOrderAttribute(\Cx\Modules\Shop\Model\Entity\OrderAttributes $orderAttributes)
    {
        $this->orderAttributes[] = $orderAttributes;

        return $this;
    }

    /**
     * Remove orderAttributes
     *
     * @param \Cx\Modules\Shop\Model\Entity\OrderAttributes $orderAttributes
     */
    public function removeOrderAttribute(\Cx\Modules\Shop\Model\Entity\OrderAttributes $orderAttributes)
    {
        $this->orderAttributes->removeElement($orderAttributes);
    }

    /**
     * Get orderAttributes
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getOrderAttributes()
    {
        return $this->orderAttributes;
    }

    /**
     * Set orders
     *
     * @param \Cx\Modules\Shop\Model\Entity\Orders $orders
     * @return OrderItems
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

    /**
     * Set products
     *
     * @param \Cx\Modules\Shop\Model\Entity\Products $products
     * @return OrderItems
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
