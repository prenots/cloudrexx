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
 * @subpackage  coremodule_login
 */

namespace Cx\Core_Modules\Login\Testing\UnitTest;

/**
 * LoginTest
 *
 * @copyright   Cloudrexx AG
 * @author      Dario Graf <dario.graf@comvation.com>
 * @package     cloudrexx
 * @subpackage  coremodule_login
 */

class LoginTest extends \Cx\Core\Test\Model\Entity\ContrexxTestCase
{
    /**
     * Existing secret needed for testing
     */
    const EXISTING_SECRET = 'NSJV QFNK XGS3 MYYZ SWYH 5CNO P47C KDCX';

    /**
     * Validates correct return of getCode
     *
     * @covers \Cx\Core_Modules\Login\Controller\TwoFactorAuthentication::getCode;
     */
    public function testGetCode()
    {
        $tfa = new \Cx\Core_Modules\Login\Controller\TwoFactorAuthentication();

        $secret = $this::EXISTING_SECRET;

        $code = $tfa->getCode($secret);

        $this->assertIsString($code);
    }

    /**
     * Validates correct return of verifyCode
     *
     * @covers \Cx\Core_Modules\Login\Controller\TwoFactorAuthentication::verifyCode;
     * @depends testGetCode
     */
    public function testVerifyCode()
    {
        $tfa = new \Cx\Core_Modules\Login\Controller\TwoFactorAuthentication();

        $secret = $this::EXISTING_SECRET;

        $code = $tfa->getCode($secret);

        $result = $tfa->verifyCode($secret, $code);

        $this->assertTrue($result);
    }

    /*
     * Validates correct return of secret
     *
     * @covers \Cx\Core_Modules\Login\Controller\TwoFactorAuthentication::createSecret;
     */
    public function testCreateSecret()
    {
        $tfa = new \Cx\Core_Modules\Login\Controller\TwoFactorAuthentication();

        $secret = $tfa->createSecret();

        $this->assertIsString($secret);
    }

    /*
     * Validates correct return of QR Code
     *
     * @covers \Cx\Core_Modules\Login\Controller\TwoFactorAuthentication::getQRCodeImageAsDataUri;
     */
    public function testGetQRCodeImageAsDataUri()
    {
        $tfa = new \Cx\Core_Modules\Login\Controller\TwoFactorAuthentication();

        $qrUri = $tfa->getQRCodeImageAsDataUri($this::EXISTING_SECRET);

        $this->assertIsString($qrUri);
    }
}
