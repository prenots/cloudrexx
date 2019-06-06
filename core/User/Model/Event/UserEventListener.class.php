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

        if (isset($eventArgs->getEntityChangeSet()['email'][1])) {
            $this->checkEmail($eventArgs->getEntity()->getEmail());
        }
        if (!empty($eventArgs->getEntityChangeSet()['username'][1])) {
            $this->checkUsername($entity->getUsername());
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
        $this->checkEmail($entity->getEmail());
        if (!empty($entity->getUsername())) {
            $this->checkUsername($entity->getUsername());
        }
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
        $oldPassword = isset($changeSet['password']) ?
            $changeSet['password'][0] : '';
        $confirmedPassword = $this->cx->getRequest()->getParam(
            'passwordConfirmed', false
        );

        if (empty($entity->getPassword()) ||
            !$this->cx->getRequest()->hasParam('passwordConfirmed', false) ) {
            if (empty($oldPassword)) {
                throw new \Cx\Core\Error\Model\Entity\ShinyException(
                    $_CORELANG['TXT_ACCESS_INVALID_PASSWORD']
                );
            }
            $entity->setPassword($this->hashPassword($oldPassword));

            return;
        }

        if (
            !empty($newPassword) ||
            self::isValidPassword($newPassword)
        ) {
            if (
                isset($confirmedPassword) &&
                $newPassword != $confirmedPassword
            ) {
                throw new \Cx\Core\Error\Model\Entity\ShinyException(
                    $_CORELANG['TXT_ACCESS_PASSWORD_NOT_CONFIRMED']
                );
            }
            $entity->setPassword($this->hashPassword($newPassword));
            return;
        }

        if (
            isset($_CONFIG['passwordComplexity']) &&
            $_CONFIG['passwordComplexity'] == 'on'
        ) {
            throw new \Cx\Core\Error\Model\Entity\ShinyException(
                $_CORELANG['TXT_ACCESS_INVALID_PASSWORD_WITH_COMPLEXITY']
            );
        } else {
            throw new \Cx\Core\Error\Model\Entity\ShinyException(
                $_CORELANG['TXT_ACCESS_INVALID_PASSWORD']
            );
        }
        if (empty($oldPassword)) {
            throw new \Cx\Core\Error\Model\Entity\ShinyException(
                $_CORELANG['TXT_ACCESS_INVALID_PASSWORD']
            );
        }
        $entity->setPassword($this->hashPassword($oldPassword));
    }

    /**
     * Returns true if the given $password is valid
     * @param   string    $password
     * @return  boolean
     */
    protected function isValidPassword($password)
    {
        global $_CONFIG;

        if (strlen($password) >= 6) {
            if (
                isset($_CONFIG['passwordComplexity']) &&
                $_CONFIG['passwordComplexity'] == 'on'
            ) {
                // Password must contain the following characters: upper, lower
                // case and numbers
                if (
                    !preg_match('/[A-Z]+/', $password) ||
                    !preg_match('/[a-z]+/', $password) ||
                    !preg_match('/[0-9]+/', $password)
                ) {
                    return false;
                }
            }
            return true;
        }
        return false;
    }

    /**
     * Generate hash of password with default hash algorithm
     *
     * @param string $password Password to be hashed
     *
     * @return string The generated hash of the supplied password
     * @throws  \Cx\Core\Error\Model\Entity\ShinyException In case the password
     *                                                    hash generation fails
     */
    protected function hashPassword($password)
    {
        $hash = password_hash($password, \PASSWORD_BCRYPT);
        if ($hash !== false) {
            return $hash;
        }

        throw new \Cx\Core\Error\Model\Entity\ShinyException(
            'Failed to generate a new password hash'
        );
    }

    protected function checkEmail($mail)
    {
        global $_CORELANG;

        if (!\FWValidator::isEmail($mail)) {
            throw new \Cx\Core\Error\Model\Entity\ShinyException(
                $_CORELANG['TXT_ACCESS_INVALID_EMAIL_ADDRESS']
            );
        }

        $em = $this->cx->getDb()->getEntityManager();
        $existingEntity = $em->getRepository(
            'Cx\Core\User\Model\Entity\User'
        )->findOneBy(array('email' => $mail));

        if (!empty($existingEntity)) {
            throw new \Cx\Core\Error\Model\Entity\ShinyException(
                $_CORELANG['TXT_ACCESS_EMAIL_ALREADY_USED']
            );
        }
    }

    protected function checkUsername($username)
    {
        global $_CORELANG;

        if (!$this->isValidUsername($username)) {
            throw new \Cx\Core\Error\Model\Entity\ShinyException(
                $_CORELANG['TXT_ACCESS_INVALID_USERNAME']
            );
        }

        $em = $this->cx->getDb()->getEntityManager();
        $existingEntity = $em->getRepository(
            'Cx\Core\User\Model\Entity\User'
        )->findOneBy(array('username' => $username));

        if (!empty($existingEntity)) {
            throw new \Cx\Core\Error\Model\Entity\ShinyException(
                $_CORELANG['TXT_ACCESS_USERNAME_ALREADY_USED']
            );
        }
    }

    /**
     * Returns true if the given $username is valid
     * @param   string    $username
     * @return  boolean
     * @static
     */
    protected function isValidUsername($username)
    {
        if (preg_match('/^[a-zA-Z0-9-_]*$/', $username)) {
            return true;
        }
        // For version 2.3, inspired by migrating Shop Customers to Users:
        // In addition to the above, also accept usernames that look like valid
        // e-mail addresses
        if (\FWValidator::isEmail($username)) {
            return true;
        }
        return false;
    }
}
