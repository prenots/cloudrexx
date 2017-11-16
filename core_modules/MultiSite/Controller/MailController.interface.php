<?php

/**
 * Mail controller for Multisite
 * 
 * @copyright   Comvation AG
 * @author      Project Team SS4U <info@comvation.com>
 * @package     contrexx
 * @subpackage  coremodule_multisite
 */

namespace Cx\Core_Modules\MultiSite\Controller;

class MailControllerException extends \Exception {}

/**
 * manage the mail service
 * 
 * @copyright   Comvation AG
 * @author      Project Team SS4U <info@comvation.com>
 * @package     contrexx
 * @subpackage  coremodule_multisite
 */
interface MailController {

    /**
     * Enable the new mail service
     * 
     * @param integer $subscriptionId
     */
    public function enableMailService($subscriptionId);
    
    /**
     * Disable the new mail service
     * 
     * @param integer $subscriptionId
     */
    public function disableMailService($subscriptionId);

    /**
     * Creates a subscription
     * 
     * @param string  $domain
     * @param integer $ipAddress
     * @param integer $subscriptionStatus
     * @param integer $customerId default null
     * @param integer $planId default null
     * 
     * @return subcription id
     */
    public function createSubscription($domain, $ipAddress, $subscriptionStatus = 0, $customerId = null, $planId = null);
    
    /**
     * Removes a subscription
     * 
     * @param int $subscriptionId
     * 
     * @throws MultiSiteDbException On error
     */
    public function removeSubscription($subscriptionId);
        
    /**
     * Rename a subscription
     * 
     * @param string $domain domain name
     * 
     * @return subscription id
     */
    public function renameSubscriptionName($domain);
    
    /**
     * Change the plan of the subscription
     * 
     * @param id     $subscriptionId  subcription id
     * @param string $planGuid        planGuid
     */
    public function changePlanOfSubscription($subscriptionId, $planGuid);
    
    /**
     * Creates a user account
     * 
     * @param string  $domain
     * @param string  $password
     * @param string  $role
     * @param integer $accountId
     * 
     * @return id
     */
    public function createUserAccount($domain, $password, $role, $accountId = null);
    
    /**
     * Delete a user account
     * 
     * @param $userAccountId
     * 
     * @return id 
     */
    public function deleteUserAccount($userAccountId);
    
    /**
     * Change the password from a user account
     * 
     * @param int $userAccountId user id
     * @param string $password
     * 
     * @return id 
     */
    public function changeUserAccountPassword($userAccountId, $password);

    /**
     * Create new domain alias
     * @param string $aliasName alias name
     */
    public function createDomainAlias($aliasName);
    
    /**
     * Rename the domain alias
     * @param string $oldAliasName old alias name
     * @param string $newAliasName new alias name
     */
    public function renameDomainAlias($oldAliasName, $newAliasName);
    
    /**
     * Remove the domain alias by name
     * @param string $aliasName alias name to delete
     */
    public function deleteDomainAlias($aliasName);
    
    /**
     * Create a new auto-login url for Panel.
     * 
     * @param integer $subscriptionId subscription id
     * @param string  $ipAddress      ip address
     * @param string  $sourceAddress  source address
     */
    public function getPanelAutoLoginUrl($subscriptionId, $ipAddress, $sourceAddress, $role);  
    
}
