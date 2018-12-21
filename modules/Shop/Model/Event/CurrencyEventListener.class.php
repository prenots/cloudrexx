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
 * Event listener for all currency events
 *
 * @copyright   Cloudrexx AG
 * @author      Sam Hawkes <info@cloudrexx.com>
 * @package     cloudrexx
 * @subpackage  module_shop
 */
namespace Cx\Modules\Shop\Model\Event;

/**
 * Event listener for all currency events
 *ö
 * @copyright   Cloudrexx AG
 * @author      Sam Hawkes <info@cloudrexx.com>
 * @package     cloudrexx
 * @subpackage  module_shop
 */
class CurrencyEventListener extends \Cx\Core\Event\Model\Entity\DefaultEventListener
{
    /**
     * @param \Doctrine\ORM\Event\LifecycleEventArgs $eventArgs
     * @throws \Doctrine\ORM\ORMException
     */
    public function postPersist(\Doctrine\ORM\Event\LifecycleEventArgs $eventArgs)
    {
        die();
        if (!empty($eventArgs->getEntity()->getDefault())) {
            $this->setDefaultEntity($eventArgs->getEntity()->getId(), $eventArgs->getEntityManager());
        }
    }

    /**
     * @param \Doctrine\ORM\Event\LifecycleEventArgs $eventArgs
     * @throws \Doctrine\ORM\ORMException
     */
    public function postUpdate(\Doctrine\ORM\Event\LifecycleEventArgs $eventArgs)
    {
        die();
        if (!empty($eventArgs->getEntity()->getDefault())) {
            $this->setDefaultEntity($eventArgs->getEntity()->getId(), $eventArgs->getEntityManager());
        }
    }

    protected function setDefaultEntity($persistedEntityId, $em)
    {
        $repo = $em->getRepository(
            '\Cx\Modules\Shop\Model\Entity\Currency'
        );

        foreach ($repo->findAll() as $entity) {
            $default = 0;
            if ($entity->getId() == $persistedEntityId) {
                $default = 1;
            }
            $entity->setDefault($default);
            $em->persist($entity);
        }
        $em->flush();
    }
}