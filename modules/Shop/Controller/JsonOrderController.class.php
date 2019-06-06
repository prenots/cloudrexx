<?php
/**
 * Cloudrexx
 *
 * @link      http://www.cloudrexx.com
 * @copyright Cloudrexx AG 2007-2019
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
 * JsonController for Order
 *
 * @copyright   Cloudrexx AG
 * @author      Sam Hawkes <info@cloudrexx.com>
 * @package     cloudrexx
 * @subpackage  module_shop
 * @version     5.0.0
 */

namespace Cx\Modules\Shop\Controller;

/**
 * JsonController for Order
 *
 * @copyright   Cloudrexx AG
 * @author      Sam Hawkes <info@cloudrexx.com>
 * @package     cloudrexx
 * @subpackage  module_shop
 * @version     5.0.0
 */
class JsonOrderController
    extends \Cx\Core\Core\Model\Entity\Controller
    implements \Cx\Core\Json\JsonAdapter
{
    /**
     * @var array messages from this controller
     */
    protected $messages;

    /**
     * Returns the internal name used as identifier for this adapter
     * @return String Name of this adapter
     */
    public function getName()
    {
        return 'Order';
    }

    /**
     * Returns an array of method names accessable from a JSON request
     *
     * @return array List of method names
     */
    public function getAccessableMethods()
    {
        return array(
            'updateOrderStatus',
            'deleteOrder',
            'setEmptyForOrderItem',
            'generateOrderItemShowView',
            'setEmptyForWeight',
            'setEmptyForPrice',
            'appendCurrency',
            'addCustomerLink',
            'getZipAndCity',
            'getStatus'
        );
    }

    /**
     * Returns all messages as string
     *
     * @return String HTML encoded error messages
     */
    public function getMessagesAsString()
    {
        return implode('<br />', $this->messages);
    }

    /**
     * Returns default permission as object
     *
     * @return \Cx\Core_Modules\Access\Model\Entity\Permission
     */
    public function getDefaultPermissions()
    {
        $permission = new \Cx\Core_Modules\Access\Model\Entity\Permission(
            array('http', 'https'),
            array('get', 'post'),
            true,
            array()
        );

        return $permission;
    }

    public function updateOrderStatus($arguments)
    {
        global $_ARRAYLANG, $objInit;

        $langData   = $objInit->getComponentSpecificLanguageData(
            'Shop',
            false
        );
        $_ARRAYLANG = array_merge($_ARRAYLANG, $langData);

        if (empty($arguments['post']) ||
            empty($arguments['post']['orderId']) ||
            !isset($arguments['post']['statusId']) ||
            !isset($arguments['post']['oldStatusId'])
        ) {
            $this->messages[] = 'Zu wenige Argumente';
            return array('status' => 'error', 'message' => $this->messages);

        }
        $updateStock = false;
        if (!empty($arguments['post']['updateStock'])) {
            $updateStock = filter_var(
                $arguments['post']['updateStock'],
                FILTER_VALIDATE_BOOLEAN
            );
        }
        $sendMail = false;
        if (!empty($arguments['post']['sendMailToCrm'])) {
            $sendMail = filter_var(
                $arguments['post']['sendMailToCrm'],
                FILTER_VALIDATE_BOOLEAN
            );
        }
        $em = $this->cx->getDb()->getEntityManager();
        $orderRepo = $em->getRepository('Cx\Modules\Shop\Model\Entity\Order');
        $orderRepo->updateStatus(
            intval($arguments['post']['orderId']),
            intval($arguments['post']['statusId']),
            intval($arguments['post']['oldStatusId']),
            $updateStock
        );

        if ($sendMail) {
            $email = false;
            switch ($arguments['post']['statusId']) {
                case \Cx\Modules\Shop\Model\Repository\OrderRepository::STATUS_CONFIRMED:
                    $email = \Cx\Modules\Shop\Controller\ShopLibrary::sendConfirmationMail($arguments['post']['orderId']);
                    break;
                case \Cx\Modules\Shop\Model\Repository\OrderRepository::STATUS_COMPLETED:
                    $email = \Cx\Modules\Shop\Controller\ShopManager::sendProcessedMail($arguments['post']['orderId']);
                    break;
            }
            if ($email) {
                $this->messages[] = sprintf(
                    $_ARRAYLANG['TXT_EMAIL_SEND_SUCCESSFULLY'],
                    $email
                );
            } else {
                $this->messages[] = $_ARRAYLANG['TXT_MESSAGE_SEND_ERROR'];
            }
        }

        $this->messages[] = $_ARRAYLANG['TXT_SHOP_ORDER_STATUS_CHANGED'];

        return array('message' => $this->messages);
    }

    public function deleteOrder($params)
    {
        global $_ARRAYLANG, $objInit;

        $langData   = $objInit->getComponentSpecificLanguageData(
            'Shop',
            false
        );
        $_ARRAYLANG = array_merge($_ARRAYLANG, $langData);

        $entityId = 0;
        $updateStock = false;

        $cx = \Cx\Core\Core\Controller\Cx::instanciate();
        if ($params['post'] && !empty($params['post']['orderId'])) {
            $entityId = $params['post']['orderId'];

            if (isset($params['post']['updateStock'])) {
                $updateStock = filter_var(
                    $params['post']['updateStock'],
                    FILTER_VALIDATE_BOOLEAN
                );
            }
        }

        if (empty($entityId)) {
            return;
        }

        $em = $cx->getDb()->getEntityManager();

        $em->getRepository(
            '\Cx\Modules\Shop\Model\Entity\Order'
        )->deleteById($entityId, $updateStock);

        $this->messages[] = $_ARRAYLANG['TXT_SHOP_DELETED_ORDER'];

        return array('message' => $this->messages);
    }

    public function setEmptyForOrderItem($params)
    {
        if (empty($params['data'])) {
            return ' ';
        }
        return $params['data'];
    }

    public function setEmptyForWeight($params)
    {
        if (empty($params['data']) && !is_numeric($params['data'])) {
            return '';
        }
        return $params['data'] .' g';
    }

    public function setEmptyForPrice($params)
    {
        if (empty($params['data']) && !is_numeric($params['data'])) {
            return '';
        }
        $currency = $this->cx->getDb()->getEntityManager()->getRepository(
            'Cx\Modules\Shop\Model\Entity\Currency'
        )->findOneBy(array('active' => 1));
        return $params['data'] .' ' . $currency->getCode();
    }

    public function generateOrderItemShowView($params)
    {
        global $_ARRAYLANG;

        if (!isset($params['entity'])) {
            throw new \Cx\Core\Error\Model\Entity\ShinyException(
                $_ARRAYLANG['TXT_SHOP_ORDER_COULD_NOT_BE_FOUND']
            );
        }

        $order = $this->cx->getDb()->getEntityManager()->getRepository(
            '\Cx\Modules\Shop\Model\Entity\Order'
        )->findOneBy(array('id' => $params['entity']['id']));

        if (empty($order)) {
            return null;
        }

        $tableContent = array();
        $netPrice = 0;
        $totalWeight = 0;
        $totalVat = $params['entity']['vatAmount'];

        foreach ($order->getOrderItems() as $item) {
            $price = $item->getPrice();
            if (count($item->getOrderAttributes()) > 0) {
                foreach ($item->getOrderAttributes() as $attribute) {
                    $price += $attribute->getPrice();
                }
            }
            $price = number_format($price, 2);
            $sum = number_format(
                $price * $item->getQuantity(),
                2
            );

            $totalWeight += $item->getWeight();
            $netPrice += $sum;

            $productCode = '';

            $tableContent[] = array(
                'quantity' => $item->getQuantity(),
                'productId' => $item->getProductId(),
                'productCode' => $productCode,
                'productName' => $item->getProductName(),
                'totalWeight' => '',
                'weight' => $item->getWeight(),
                'price' => $price,
                'vat' => $item->getVatRate(),
                'sum' => $sum,
                'orderAttributes' => $item->getOrderAttributes()
            );
        }

        foreach ($order->getRelCustomerCoupons() as $coupon) {
            $netPrice -= $coupon->getAmount();

            $tableContent[] = array(
                'quantity' => '',
                'productId' => '',
                'productCode' => $coupon->getCode(),
                'productName' => $_ARRAYLANG['TXT_SHOP_DISCOUNT_COUPON_CODE'],
                'totalWeight' => '',
                'weight' => '',
                'price' => '',
                'vat' => '',
                'sum' => '-' . $coupon->getAmount(),
                'orderAttributes' => ''
            );
        }
        $total = (float)$params['entity']['shipmentAmount']
            + (float)$params['entity']['paymentAmount']
            + (float)$netPrice;

        if (!Vat::isIncluded()) {
            $total += $totalVat;
        }

        $netPrice = number_format($netPrice, 2);
        $total = number_format($total, 2);

        $customAttrs = array(
            'TXT_SHOP_DETAIL_NETPRICE' => $netPrice,
            Vat::isIncluded() ? 'TXT_TAX_PREFIX_INCL' : 'TXT_TAX_PREFIX_EXCL' => $totalVat,
            'empty' => '',
            'shipmentAmount' => $params['entity']['shipmentAmount'],
            'paymentAmount' => $params['entity']['paymentAmount'],
            'sum' => $total
        );

        foreach ($customAttrs as $key=>$value) {
            $row = array(
                'quantity' => '',
                'productId' => '',
                'productCode' => '',
                'productName' => '',
                'totalWeight' => '',
                'weight' => '',
                'price' => '',
                'vat' => $_ARRAYLANG[$key],
                'sum' => $value
            );
            if ($key == 'shipmentAmount') {
                $row['totalWeight'] = $_ARRAYLANG['TXT_TOTAL_WEIGHT'];
                $row['weight'] = $totalWeight;
            }
            $tableContent[] = $row;
        }

        $options = array(
            'fields' => array(
                'orderAttributes' => array(
                    'showOverview' => false,
                ),
                'vat' => array(
                    'table' => array(
                        'parse' => function($vatRate, $rowData) {
                            if (!is_numeric($vatRate)) {
                                return '<b>'.$vatRate.'</b>';
                            }
                            $price = $rowData['price'];
                            $quantity = $rowData['quantity'];
                            $sum = $price * $quantity;
                            if (Vat::isIncluded()) {
                                $vatSum = $sum - ($sum / ($vatRate / 100 + 1));
                            } else {
                                $vatSum = $sum * $vatRate;
                            }

                            $vatSum = number_format($vatSum, 2);

                            $vatRateWrapper = new \Cx\Core\Html\Model\Entity\HtmlElement('span');
                            $vatRateWrapper->addChild(
                                new \Cx\Core\Html\Model\Entity\TextElement(
                                    $vatRate .'%'
                                )
                            );
                            $vatSumWrapper = new \Cx\Core\Html\Model\Entity\HtmlElement('span');
                            $vatSumWrapper->addChild(
                                new \Cx\Core\Html\Model\Entity\TextElement(
                                    $vatSum . ' CHF'
                                )
                            );
                            return $vatRateWrapper . $vatSumWrapper;
                        },
                        'attributes' => array(
                            'class' => 'vat-detail align-right'
                        )
                    ),
                ),
                'productName' => array(
                    'table' => array(
                        'parse' => function($value, $rowData) {
                            $productName = $value;
                            if (isset($rowData['orderAttributes'])) {
                                foreach($rowData['orderAttributes'] as $attribute) {
                                    $productName .= '<br/><i> - '
                                        . $attribute->getAttributeName() .': '
                                        . $attribute->getOptionName() . ' ('
                                        . $attribute->getPrice() . ')</i>';
                                }
                            }

                            return $productName;
                        },
                        'attributes' => array(
                            'class' => 'product-name-detail'
                        ),
                    )
                ),
                'productId' => array(
                    'table' => array(
                        'parse' => array(
                            'adapter' => 'Order',
                            'method' => 'setEmptyForOrderItem'
                        ),
                        'attributes' => array(
                            'class' => 'product-id-detail'
                        ),
                    )
                ),
                'productCode' => array(
                    'table' => array(
                        'parse' => array(
                            'adapter' => 'Order',
                            'method' => 'setEmptyForOrderItem'
                        ),
                        'attributes' => array(
                            'class' => 'product-code-detail'
                        ),
                    )
                ),
                'quantity' => array(
                    'table' => array(
                        'parse' => array(
                            'adapter' => 'Order',
                            'method' => 'setEmptyForOrderItem'
                        ),
                        'attributes' => array(
                            'class' => 'quantity-detail'
                        ),
                    )
                ),
                'totalWeight' => array(
                    'table' => array(
                        'parse' => array(
                            'adapter' => 'Order',
                            'method' => 'setEmptyForOrderItem'
                        ),
                        'attributes' => array(
                            'class' => 'strong-text align-right'
                        ),
                    )
                ),
                'weight' => array(
                    'table' => array(
                        'parse' => array(
                            'adapter' => 'Order',
                            'method' => 'setEmptyForWeight'
                        ),
                        'attributes' => array(
                            'class' => 'align-right weight-detail'
                        ),
                    )
                ),
                'price' => array(
                    'table' => array(
                        'parse' => array(
                            'adapter' => 'Order',
                            'method' => 'setEmptyForPrice'
                        ),
                        'attributes' => array(
                            'class' => 'align-right price-detail'
                        ),
                    )
                ),
                'sum' => array(
                    'table' => array(
                        'parse' => array(
                            'adapter' => 'Order',
                            'method' => 'setEmptyForPrice'
                        ),
                        'attributes' => array(
                            'class' => 'align-right sum-detail'
                        ),
                    ),
                    'header' => $_ARRAYLANG['TXT_SHOP_ORDER_SUM']
                ),
            )
        );

        $table = new \Cx\Core_Modules\Listing\Model\Entity\DataSet(
            $tableContent
        );

        return new \BackendTable($table, $options, '', null);
    }

    public function appendCurrency($params)
    {
        $currency = $params['entity']['currency'];
        return $params['value'] . ' ' . $currency->getCode();
    }

    public function addCustomerLink($params)
    {
        if (empty($params['value'])) {
            return ' ';
        }
        if (empty($params['entity']['customerId'])) {
            return $params['value'];
        }

        $link = new \Cx\Core\Html\Model\Entity\HtmlElement('a');
        $link->addChild(
            new \Cx\Core\Html\Model\Entity\TextElement(
                $params['value']
            )
        );

        $customerId = $params['entity']['customerId'];
        $linkUrl = \Cx\Core\Routing\Url::fromBackend('Shop', 'customerdetails');
        $linkUrl->setParam('customer_id', $customerId);

        $link->setAttribute('href', $linkUrl);

        return $link;
    }

    public function getZipAndCity($params)
    {
        return $params['value'] . ' ' . $params['entity']['city'];
    }

    public function getStatus($params)
    {
        $statusValues = $this->cx->getDb()
            ->getEntityManager()->getRepository(
                $this->getNamespace()
                . '\\Model\Entity\Order'
            )->getStatusValues();

        return $statusValues[$params['value']];
    }
}