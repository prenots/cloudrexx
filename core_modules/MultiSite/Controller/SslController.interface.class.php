<?php

/**
 * SSL interface Multisite hosts
 * 
 * @copyright   Cloudrexx AG
 * @author      Michael Ritter <michael.ritter@cloudrexx.com>
 * @package     cloudrexx
 * @subpackage  coremodule_multisite
 */

namespace Cx\Core_Modules\MultiSite\Controller;

class SslControllerException extends \Exception {}

/**
 * Manage SSL certificates
 * 
 * @copyright   Cloudrexx AG
 * @author      Michael Ritter <michael.ritter@cloudrexx.com>
 * @package     cloudrexx
 * @subpackage  coremodule_multisite
 */
interface SslController {

    /**
     * Install the SSL Certificate for the domain
     * 
     * @param string $name                      Certificate name
     * @param string $domain                    Domain name
     * @param string $certificatePrivateKey     certificate private key
     * @param string $certificateBody           certificate body
     * @param string $certificateAuthority      certificate authority
     */
    public function installSSLCertificate($name, $domain, $certificatePrivateKey, $certificateBody = null, $certificateAuthority = null);
    
    /**
     * Fetch the SSL Certificate details
     * 
     * @param string $domain domain name
     */
    public function getSSLCertificates($domain);
    
    /**
     * Remove the SSL Certificates
     * 
     * @param string $domain domain name
     * @param array  $names  certificate names
     */
    public function removeSSLCertificates($domain, $names = array());

    /**
     * Activate the SSL Certificate
     *
     * @param string $certificateName certificate name
     * @param string $domain          domain name
     */
    public function activateSSLCertificate($certificateName, $domain);
}
