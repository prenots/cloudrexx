<?php
/**
 * Abstract class HostController
 *
 * This is the main class for all host controllers
 *
 * @copyright   CLOUDREXX - Cloudrexx AG Thun
 * @author      Michael Ritter <michael.ritter@cloudrexx.com>
 * @package     cloudrexx
 * @subpackage  coremodule_MultiSite
 * @version     1.0.0
 */

namespace Cx\Core_Modules\MultiSite\Controller;

/**
 * Abstract class HostController
 *
 * This is the main class for all host controllers
 *
 * @todo This class should inherit from EntityBase
 *
 * @copyright   CLOUDREXX - Cloudrexx AG Thun
 * @author      Michael Ritter <michael.ritter@cloudrexx.com>
 * @package     cloudrexx
 * @subpackage  coremodule_MultiSite
 * @version     1.0.0
 */
abstract class HostController implements DbController, WebDistributionController, UserStorageController, DnsController, MailController {

    /**
     * Initializes the settings for this HostController
     * This is only triggered once (on setup) of the environment
     */
    public static abstract function initSettings();

    /**
     * Initializes the HostController from its local configuration
     * @return HostController Newly instanciated host controller
     */
    public static abstract function fromConfig();
}
