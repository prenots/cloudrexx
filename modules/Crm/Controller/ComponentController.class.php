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
 * Main controller for Crm
 *
 * @copyright   Cloudrexx AG
 * @author      Project Team SS4U <info@cloudrexx.com>
 * @package     cloudrexx
 * @subpackage  module_crm
 */

namespace Cx\Modules\Crm\Controller;

/**
 * Main controller for Crm
 *
 * @copyright   Cloudrexx AG
 * @author      Project Team SS4U <info@cloudrexx.com>
 * @package     cloudrexx
 * @subpackage  module_crm
 */
class ComponentController extends \Cx\Core\Core\Model\Entity\SystemComponentController {
    public function getControllerClasses() {
        // Return an empty array here to let the component handler know that there
        // does not exist a backend, nor a frontend controller of this component.
        return array();
    }

     /**
     * Load your component.
     *
     * @param \Cx\Core\ContentManager\Model\Entity\Page $page       The resolved page
     */
    public function load(\Cx\Core\ContentManager\Model\Entity\Page $page) {
        global $objTemplate, $_CORELANG, $subMenuTitle;
        switch ($this->cx->getMode()) {
            case \Cx\Core\Core\Controller\Cx::MODE_BACKEND:
                $this->cx->getTemplate()->addBlockfile('CONTENT_OUTPUT', 'content_master', 'LegacyContentMaster.html');
                $objTemplate = $this->cx->getTemplate();

                \Permission::checkAccess(194, 'static');
                $subMenuTitle = $_CORELANG['TXT_CRM'];
                $objCrmModule = new CrmManager($this->getName());
                $objCrmModule->getPage();
                break;
        }
    }

    public function postResolve(\Cx\Core\ContentManager\Model\Entity\Page $page) {
        $evm = \Env::get('cx')->getEvents();

        $userEventListener    = new \Cx\Modules\Crm\Model\Event\UserEventListener();
        $evm->addModelListener(\Doctrine\ORM\Events::postUpdate, 'User', $userEventListener);
    }

    /**
     * Returns a list of command mode commands provided by this component
     *
     * @return array List of command names
     */
    public function getCommandsForCommandMode()
    {
        return array('Crm');
    }

    /**
     * Execute api command
     *
     * @param string $command       Name of command to execute
     * @param array  $arguments     List of arguments for the command
     * @param array  $dataArguments (optional) List of data arguments for the command
     */
    public function executeCommand($command, $arguments, $dataArguments = array())
    {
        global $_ARRAYLANG;

        $subcommand = null;
        if (!empty($arguments[0])) {
            $subcommand = $arguments[0];
        }

        $_ARRAYLANG = \Env::get('init')->getComponentSpecificLanguageData(
            'Crm',
            false,
            LANG_ID
        );

        $crmManager = new CrmManager($this->getName());
        switch ($command) {
            case 'Crm':
                switch ($subcommand) {
                    case 'Exportvcf':
                        $crmManager->exportVcf();
                        break;
                    case 'CustomerToolTipDetail':
                        $crmManager->customerTooltipDetail();
                        break;
                    case 'AddContact':
                        $crmManager->addContact();
                        break;
                    case 'DownloadDocument':
                        $fileName = $crmManager->getContactFileNameById(
                            contrexx_input2int($arguments['id']),
                            contrexx_input2int($arguments['customer'])
                        );
                        $crmManager->download($fileName);
                        break;
                    case 'Exportcsv':
                        $crmInterface = new CrmInterface(
                            $crmManager->_objTpl,
                            $this->getName()
                        );
                        $crmInterface->csvExport();
                        break;
                }
                break;
            default:
                break;
        }
    }
}
