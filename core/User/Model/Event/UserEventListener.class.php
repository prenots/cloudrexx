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
 * Event listener for users
 *
 * @copyright   Cloudrexx AG
 * @author      Sam Hawkes <info@cloudrexx.com>
 * @package     cloudrexx
 * @subpackage  module_shop
 */
namespace Cx\Core\User\Model\Event;

/**
 * Event listener for users
 *
 * @copyright   Cloudrexx AG
 * @author      Sam Hawkes <info@cloudrexx.com>
 * @package     cloudrexx
 * @subpackage  module_shop
 */
class UserEventListener extends \Cx\Core\Event\Model\Entity\DefaultEventListener
{
    public function preUpdate(\Doctrine\ORM\Event\LifecycleEventArgs $eventArgs)
    {
        global $objInit, $_ARRAYLANG;

        //get the language interface text
        $langData   = $objInit->loadLanguageData('User');
        $_ARRAYLANG = array_merge($_ARRAYLANG, $langData);

        $entity = $eventArgs->getEntity();

        // Prevent the user from deactivating himself
        $user = \FWUser::getFWUserObject()->objUser;
        if (!$entity->getActive() && $user->getId() == $entity->getId()) {
            throw new \Cx\Core\Error\Model\Entity\ShinyException(
                $_ARRAYLANG['TXT_CORE_USER_NO_USER_WITH_SAME_ID']
            );
        }

        if (isset($eventArgs->getEntityChangeSet()['password'][1])) {
            $this->setHashPassword(
                $entity,
                $eventArgs->getEntityChangeSet()
            );
        }
    }

    public function prePersist(\Doctrine\ORM\Event\LifecycleEventArgs $eventArgs)
    {
        $entity = $eventArgs->getEntity();
        $this->setHashPassword(
            $entity,
            array()
        );
        // Set current date
        $date = new \DateTime();
        $eventArgs->getEntity()->setRegdate($date->getTimestamp());
    }

    /**
     * TODO: This should be moved to ViewGenerator and respective places
     */
    protected function setHashPassword($entity, $changeSet)
    {
        global $_CORELANG, $_CONFIG;

        $newPassword = $entity->getPassword();
        $confirmedPassword = $this->cx->getRequest()->getParam(
            'passwordConfirmed',
            false
        );

        if (!empty($newPassword)) {
            if (
                isset($confirmedPassword) &&
                !password_verify($confirmedPassword, $newPassword)
            ) {
                throw new \Cx\Core\Error\Model\Entity\ShinyException(
                    $_CORELANG['TXT_ACCESS_PASSWORD_NOT_CONFIRMED']
                );
            }
            return;
        }
    }
}
