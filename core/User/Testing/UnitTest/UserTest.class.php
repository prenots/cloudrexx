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
 * LoginTest
 *
 * @copyright   Cloudrexx AG
 * @author      Dario Graf <dario.graf@comvation.com>
 * @package     cloudrexx
 * @subpackage  core_user
 */

namespace Cx\Core\User\Testing\UnitTest;

/**
 * LoginTest
 *
 * @copyright   Cloudrexx AG
 * @author      Dario Graf <dario.graf@comvation.com>
 * @package     cloudrexx
 * @subpackage  core_user
 */

class UserTest extends \Cx\Core\Test\Model\Entity\DoctrineTestCase
{
    /**
     * Existing secret needed for testing
     */
    const EXISTING_SECRET = 'NSJV QFNK XGS3 MYYZ SWYH 5CNO P47C KDCX';

    /**
     * Existing UserId needed for testing
     */
    const EXISTING_USER_ID = 1;

    /**
     * Existing UserEmail needed for testing
     */
    const EXISTING_USER_EMAIL = 'info@example.org';

    /**
     * Validates the storage of a secret in the database
     *
     * @coversNothing
     */
    public function testStoreTwoFactorSettings()
    {
        $em = \Cx\Core\Core\Controller\Cx::instanciate()->getDb()->getEntityManager();

        $userRepo = $em->getRepository(
            '\Cx\Core\User\Model\Entity\User'
        );

        $user = $userRepo->findOneBy(array('id' => $this::EXISTING_USER_ID));

        $twoFactorAuth = new \Cx\Core\User\Model\Entity\TwoFactorAuthentication();

        $twoFactorAuth->setUser($user);
        $twoFactorAuth->setName($this::EXISTING_USER_EMAIL);
        $twoFactorAuth->setData($this::EXISTING_SECRET);

        $em->persist($twoFactorAuth);
        $em->flush();

        $repo = $em->getRepository(
            '\Cx\Core\User\Model\Entity\TwoFactorAuthentication'
        );

        $result = $repo->findOneBy(array('user' => $user));

        $this->assertNotNull($result);
    }

    /**
     * Validates the deletion of a secret in the database
     *
     * @coversNothing
     */
    public function testDeleteTwoFactorSettings()
    {
        $em = \Cx\Core\Core\Controller\Cx::instanciate()->getDb()->getEntityManager();

        $userRepo = $em->getRepository(
            '\Cx\Core\User\Model\Entity\User'
        );

        $user = $userRepo->findOneBy(array('id' => $this::EXISTING_USER_ID));

        $repo = $em->getRepository(
            '\Cx\Core\User\Model\Entity\TwoFactorAuthentication'
        );

        $twoFactorEntry = $repo->findOneBy(array('user' => $user));

        $user->setTwoFaActive(0);

        $em->remove($twoFactorEntry, $user);
        $em->flush();

        $result = $repo->findOneBy(array('user' => $user));

        $this->assertNull($result);
    }
}
