<?php
/**
 * Created by PhpStorm.
 * User: sam
 * Date: 13.12.18
 * Time: 11:11
 */

namespace Cx\Modules\Shop\Model\Event;


class PricelistEventListener extends \Cx\Core\Event\Model\Entity\DefaultEventListener
{
    /**
     * If a pricelist is persisted, the relationship between pricelist and
     * category must be created, for which the ID is required. However, the ID
     * does not exist before it is persisted.
     *
     * @param \Doctrine\ORM\Event\LifecycleEventArgs $args args of Lifecycle Event
     *
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function postPersist(\Doctrine\ORM\Event\LifecycleEventArgs $args)
    {
    }
}