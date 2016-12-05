<?php

namespace Cx\Modules\FavoriteList\Model\Event;

/**
 * CatalogSaveEventListenerException
 *
 * @copyright   Comvation AG
 * @author      Manuel Schenk <manuel.scenk@comvation.com>
 * @package     cloudrexx
 * @subpackage  module_favoritelist
 */
class CatalogSaveEventListenerException extends \Exception
{
}

/**
 * CatalogSaveEventListener
 *
 * @copyright   Comvation AG
 * @author      Manuel Schenk <manuel.scenk@comvation.com>
 * @package     cloudrexx
 * @subpackage  module_favoritelist
 */
class CatalogSaveEventListener extends \Cx\Core\Event\Model\Entity\DefaultEventListener
{

    public function prePersist($eventArgs)
    {
        $entity = $eventArgs->getEntity();
        $entity->setDate(new \DateTime());
        $entity->setSessionId($this->getComponent('Session')->getSession()->sessionid);
    }
}
