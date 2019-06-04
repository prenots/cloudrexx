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
    public function postUpdate(\Doctrine\ORM\Event\LifecycleEventArgs $args)
    {
        if (
            $this->cx->getRequest()->hasParam('sendMail', false) &&
            filter_var(
                $this->cx->getRequest()->getParam('sendMail', false),
                FILTER_VALIDATE_BOOLEAN
            )
        ) {
            \Cx\Modules\Shop\Controller\ShopManager::sendProcessedMail(
                $args->getEntity()->getId()
            );
        }
    }
}