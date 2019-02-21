<?php
/**
 * Created by PhpStorm.
 * User: sam
 * Date: 15.10.18
 * Time: 16:40
 */

namespace Cx\Modules\Shop\Model\Event;


class OrderEventListener extends \Cx\Core\Event\Model\Entity\DefaultEventListener
{
    public function preRemove() : void
    {
        $entityId = 0;
        $updateStock = false;

        $cx = \Cx\Core\Core\Controller\Cx::instanciate();
        if ($cx->getRequest()->hasParam('deleteid')) {
            $entityId = $cx->getRequest()->getParam('deleteid');

            if ($cx->getRequest()->hasParam('update_stock')) {
                $updateStock = $cx->getRequest()->getParam('update_stock');
            }
        }

        if (empty($entityId)) {
            return;
        }

        $em = $cx->getDb()->getEntityManager();

        $em->getRepository(
            '\Cx\Modules\Shop\Model\Entity\Order'
        )->deleteById($entityId, $updateStock);

        $url = \Cx\Core\Routing\Url::fromRequest();
        $url->removeAllParams();
        \Cx\Core\Csrf\Controller\Csrf::redirect($url->__toString());
    }
}