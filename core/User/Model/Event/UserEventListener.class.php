<?php

/**
 * Cloudrexx
 *
 * @link      http://www.cloudrexx.com
 * @copyright Cloudrexx AG 2007-2015
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
 * UserEventListener for User
 *
 * @copyright   Cloudrexx AG
 * @author      Dario Graf <dario.graf@comvation.com>
 * @package     cloudrexx
 * @subpackage  core_user
 */

namespace Cx\Core\User\Model\Event;

/**
 * Class to handle user events
 *
 * This class handles the events when a user is persisted
 *
 * @copyright   Cloudrexx AG
 * @author      Dario Graf <dario.graf@comvation.com>
 * @package     cloudrexx
 * @subpackage  core_user
 */

class UserEventListener extends \Cx\Core\Event\Model\Entity\DefaultEventListener
{
    /**
     * When the database entry is updated, set the correct data for 2fa entity
     * using the flush method
     *
     */
    public function postUpdate(\Doctrine\ORM\Event\LifecycleEventArgs $args)
    {
        $em = $args->getEntityManager();
        $user = $args->getEntity();

        $active = $user->getTwoFaActive();

        $email = $user->getEmail();

        if ($active == "0") {
            return true;
        }

        $secret = $this->cx->getRequest()->getParam('secret', false);

        $tfa = new \Cx\Core\User\Model\Entity\TwoFactorAuthentication();

        $tfa->setUser($user);
        $tfa->setName($email);
        $tfa->setData($secret);

        $em->persist($tfa);
        $em->flush();
    }

    /**
     * If a user is persisted, the entries for the 2fa table are created in
     * this function, because the ID of the user is required to save the user
     * properly. However, the ID does not yet exist before persisting.
     *
     * @param \Doctrine\ORM\Event\LifecycleEventArgs $args args of Lifecycle Event
     *
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function postPersist(\Doctrine\ORM\Event\LifecycleEventArgs $args)
    {
        $em = $args->getEntityManager();
        $user = $args->getEntity();

        $active = $user->getTwoFaActive();

        $email = $user->getEmail();

        if ($active == "0") {
            return true;
        }

        $secret = $this->cx->getRequest()->getParam('secret', false);

        $tfa = new \Cx\Core\User\Model\Entity\TwoFactorAuthentication();

        $tfa->setUser($user);
        $tfa->setName($email);
        $tfa->setData($secret);

        $em->persist($tfa);
        $em->flush();
    }
}