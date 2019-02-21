<?php

/**
 * Cloudrexx App by Comvation AG
 *
 * @category  CloudrexxApp
 * @package   User
 * @author    Cloudrexx AG <dario.graf@comvation.com>
 * @copyright Cloudrexx AG
 * @link      https://www.cloudrexx.com
 *
 * Unauthorized copying, changing or deleting
 * of any file from this app is strictly prohibited
 *
 * Authorized copying, changing or deleting
 * can only be allowed by a separate contract
 **/

namespace Cx\Core_Modules\Login\Controller;

/**
 * Specific TwoFactorAuthentification class for this Component.
 *
 * Used as an abstraction of the 2fa library
 *
 * @category  CloudrexxApp
 * @package   User
 * @author    Cloudrexx AG <dario.graf@comvation.com>
 * @copyright Cloudrexx AG
 * @link      https://www.cloudrexx.com
 */

class TwoFactorAuthentication
{
    protected $tfa;
    protected $secret;
    protected $bits = 160;
    protected $size = 200;
    protected $label = 'QR Code';
    protected $requireCryptosecure = true;

    /**
     * TwoFactorAuthentication constructor.
     *
     * @param null $issuer
     * @param int $digits
     * @param int $period
     * @param string $algorithm
     * @param null $qrcodeprovider
     * @param null $rngprovider
     * @param null $timeprovider
     */
    public function __construct($issuer = null, $digits = 6, $period = 30, $algorithm = 'sha1', $qrcodeprovider = null, $rngprovider = null, $timeprovider = null)
    {
        $this->tfa = new \RobThree\Auth\TwoFactorAuth($issuer, $digits, $period, $algorithm, $qrcodeprovider, $rngprovider, $timeprovider);
    }

    /**
     * Create secret
     *
     * @return mixed
     */
    public function createSecret()
    {
        $this->secret = $this->tfa->createSecret($this->bits, $this->requireCryptosecure);

        return $this->secret;
    }

    /**
     * Get code corresponding to given secret
     *
     * @param $secret
     * @return string
     */
    public function getCode($secret)
    {
        $this->secret = $secret;

        return $this->tfa->getCode($this->secret);
    }

    /**
     * Check validity of code corresponding with secret
     *
     * @param $secret
     * @return bool
     */
    public function verifyCode($secret, $code)
    {
        $this->secret = $secret;

        $result = $this->tfa->verifyCode($this->secret, $code);

        return $result;
    }

    /**
     * Get qr code uri to display secret as qr code image
     *
     * @param $label
     * @return string
     */
    public function getQRCodeImageAsDataUri($label)
    {
        if (!is_string($label) && $label !== '') {
            $label = $this->label;
        }

        return $this->tfa->getQRCodeImageAsDataUri($label, $this->secret, $this->size);
    }

    /**
     * Get stored secret of user
     *
     * @param $userId
     * @return mixed
     */
    public function getSecretByUser($userId)
    {
        $em = \Cx\Core\Core\Controller\Cx::instanciate()->getDb()->getEntityManager();

        $repo = $em->getRepository(
            '\Cx\Core\User\Model\Entity\TwoFactorAuthentication'
        );

        $secret = $repo->findOneBy(array('userId' => $userId))->getData();

        return $secret;
    }
}