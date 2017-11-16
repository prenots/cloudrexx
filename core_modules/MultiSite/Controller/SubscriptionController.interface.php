<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Cx\Core_Modules\MultiSite\Controller;


/**
 * Description of newPHPClass
 *
 * @author ritt0r
 */
interface SubscriptionController {

    /**
     * Creaye a Customer
     * @param \Cx\Core\Model\Model\Entity\Subscription
     * @throws MultiSiteDbException On error
     */
    public function createCustomer(\Cx\Core_Modules\MultiSite\Model\Entity\Customer $customer);
    
    /**
     * Get the all available service plans of mail service server
     */
    public function getAvailableServicePlansOfMailServer();
    
    /**
     * Create new site/domain
     * 
     * @param string  $domain         Name of the site/domain to create
     * @param integer $subscriptionId Id of the Subscription assigned for the new site/domain
     * @param string  $documentRoot   Document root to create the site/domain
     */
    public function createSite($domain, $subscriptionId, $documentRoot = 'httpdocs');
    
    /**
     * Renaming the site/domain
     * 
     * @param string $oldDomainName old domain name
     * @param string $newDomainName new domain name
     */
    public function renameSite($oldDomainName, $newDomainName);
    
    /**
     * Remove the site by the domain name.
     * 
     * @param string $domain Domain name to remove
     */
    public function deleteSite($domain);
    
    /**
     * Get all the sites under the existing subscription
     */
    public function getAllSites();

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
