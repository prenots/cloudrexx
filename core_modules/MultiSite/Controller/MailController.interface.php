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
     */
    public function enableMailService();
    
    /**
     * Disable the new mail service
     * 
     */
    public function disableMailService();
    
}