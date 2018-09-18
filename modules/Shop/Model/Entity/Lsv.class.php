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
 * Class Lsv
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
 * Lsv consists of an order id, holder, bank of the customer and the blz of
 * the bank.
 *
 * @copyright   CLOUDREXX CMS - Cloudrexx AG Thun
 * @author      Sam Hawkes <info@cloudrexx.com>
 * @package     cloudrexx
 * @subpackage  module_shop
 * @version     5.0.0
 */
class Lsv extends \Cx\Model\Base\EntityBase {
    /**
     * @var integer
     */
    protected $orderId;

    /**
     * @var string
     */
    protected $holder;

    /**
     * @var string
     */
    protected $bank;

    /**
     * @var string
     */
    protected $blz;

    /**
     * @var \Cx\Modules\Shop\Model\Entity\Orders
     */
    protected $orders;


    /**
     * Set orderId
     *
     * @param integer $orderId
     * @return Lsv
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
     * Set holder
     *
     * @param string $holder
     * @return Lsv
     */
    public function setHolder($holder)
    {
        $this->holder = $holder;

        return $this;
    }

    /**
     * Get holder
     *
     * @return string 
     */
    public function getHolder()
    {
        return $this->holder;
    }

    /**
     * Set bank
     *
     * @param string $bank
     * @return Lsv
     */
    public function setBank($bank)
    {
        $this->bank = $bank;

        return $this;
    }

    /**
     * Get bank
     *
     * @return string 
     */
    public function getBank()
    {
        return $this->bank;
    }

    /**
     * Set blz
     *
     * @param string $blz
     * @return Lsv
     */
    public function setBlz($blz)
    {
        $this->blz = $blz;

        return $this;
    }

    /**
     * Get blz
     *
     * @return string 
     */
    public function getBlz()
    {
        return $this->blz;
    }

    /**
     * Set orders
     *
     * @param \Cx\Modules\Shop\Model\Entity\Orders $orders
     * @return Lsv
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
