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
            'updateOrderStatus'
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
        if (empty($arguments['post']) ||
            empty($arguments['post']['orderId']) ||
            empty($arguments['post']['statusId'])
        ) {
            $this->messages[] = 'Zu wenige Argumente';
            return array('status' => 'error', 'message' => $this->messages);

        }
        $updateStock = false;
        if (!empty($arguments['post']['updateStock'])) {
            $updateStock = (bool)$arguments['post']['updateStock'];
        }
        $sendMail = false;
        if (!empty($arguments['post']['sendMail'])) {
            $sendMail = (bool)$arguments['post']['sendMail'];
        }
        $em = $this->cx->getDb()->getEntityManager();
        $orderRepo = $em->getRepository('Cx\Modules\Shop\Model\Entity\Order');
        $orderRepo->updateStatus(
            intval($arguments['post']['orderId']),
            intval($arguments['post']['statusId']),
            $updateStock
        );

        if ($sendMail) {
            $email = \Cx\Modules\Shop\Controller\ShopLibrary::sendConfirmationMail(
                $arguments['post']['orderId']
            );
            if ($email) {
                $this->messages[] = 'email versendet ' . $email;
            } else {
                $this->messages[] = 'email nicht versendet';
            }
        }

        return array('message' => $this->messages);
    }
}