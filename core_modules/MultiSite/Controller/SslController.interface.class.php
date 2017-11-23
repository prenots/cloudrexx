<?php

/**
 * SSL interface for Multisite hosts
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
     * Wheter or not this SslController can generate self-signed certificates
     *
     * If this method returns true, activateSSLCertificate() can be called
     * without calling installSSLCertificate() first. If no certificate with
     * the supplied name exists, installSSLCertificate() will issue a self-
     * signed certificate.
     * The return value of this method changes the behavior of some methods of
     * other HostController interfaces: If this method returns true, whenever
     * a custom domain is mapped a certificate is generated which results in
     * the SslController and WebDistributionController methods being triggered.
     * The same thing happens when a custom domain gets unmapped.
     * If this method returns false, the methods mentioned above are only
     * triggered if a certificate is manually added.
     * @return boolean True if this controller can issue self-signed certificates
     */
    public function canGenerateCertificates();

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
