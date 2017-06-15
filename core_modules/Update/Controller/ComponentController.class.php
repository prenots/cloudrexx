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
 * Class ComponentController
 *
 * @copyright   CLOUDREXX CMS - CLOUDREXX AG
 * @author      CLOUDREXX Development Team <info@cloudrexx.com>
 * @package     cloudrexx
 * @subpackage  module_update
 */

namespace Cx\Core_Modules\Update\Controller;

/**
 * Class ComponentController
 *
 * The main Update component
 *
 * @copyright   CLOUDREXX CMS - CLOUDREXX AG
 * @author      CLOUDREXX Development Team <info@cloudrexx.com>
 * @package     cloudrexx
 * @subpackage  module_update
 */
class ComponentController extends \Cx\Core\Core\Model\Entity\SystemComponentController {

    /**
     * Get the controller classes
     *
     * @return array array of the controller classes.
     */
    public function getControllerClasses() {
        return array('Update');
    }

    /**
     * {@inheritDoc}
     */
    public function getCommandsForCommandMode()
    {
        return array('update', 'up');
    }

    /**
     * {@inheritDoc}
     */
    public function getCommandDescription($command, $short = false)
    {
        if ($command == 'update') {
            return 'Update framework';
        }
        if ($command == 'up') {
            return 'Shortcut alias for `up`';
        }
    }

    /**
     * {@inheritDoc}
     */
    public function executeCommand($command, $arguments, $dataArguments = array())
    {
        if ($command == 'update' || $command == 'up') {
            if (count($arguments) !== 2) {
                echo "Command {$command} have invalid arguments.\r\n Use
                    {$command} [({component name} {version number})] \r\r\n
                    Eg:- {$command} Crm 5.0.0";
                return;
            }
            $this->triggerComponentUpdate(ucfirst($arguments[0]), $arguments[1]);
            return;
        }
    }

    /**
     * Trigger update for Component
     *
     * @param string $componentName   Name of the component
     * @param string $codeBaseVersion Version number
     */
    protected function triggerComponentUpdate($componentName, $codeBaseVersion)
    {
        //Check the component have a meta.yml if not exists, add as new
        $component = $this->getComponent($componentName);
        if (!$component) {
            echo "Your component is not valid. Please correct this in before you proceed. \r\n\r\n";
            return;
        }
        if (!file_exists($component->getDirectory() . '/meta.yml')) {
            $reflectionComponent =
                new \Cx\Core\Core\Model\Entity\ReflectionComponent(
                    $component->getSystemComponent()
                );
            $reflectionComponent->writeMetaDataToFile(
                $component->getDirectory() . '/meta.yml'
            );
        }

        $objYaml = new \Symfony\Component\Yaml\Yaml();
        $file    = new \Cx\Lib\FileSystem\File(
            $component->getDirectory() . '/meta.yml'
        );
        $content = $objYaml->parse($file->getData());
        if (
            isset($content['DlcInfo']) &&
            isset($content['DlcInfo']['version']) &&
            !empty($content['DlcInfo']['version'])
        ) {
            $oldCodeBaseVerison = $content['DlcInfo']['version'];
        }
        $params = array(
            'oldCodeBaseId'    => $oldCodeBaseVerison,
            'latestCodeBaseId' => $codeBaseVersion,
            'components'       => array($componentName)
        );
        try {
            $updateController = $this->getController('Update');
            $updateController->triggerUpdate($params);

            echo "Successfully triggered the update process. \r\n\r\n";
            return;
        } catch (\Exception $e) {
            \DBG::dump($e->getMessage());
            echo "Failed to trigger the update process. \r\n\r\n";
            return;
        }
    }

    /**
     * postInit
     *
     * @param \Cx\Core\Core\Controller\Cx $cx
     */
    public function postInit(\Cx\Core\Core\Controller\Cx $cx)
    {
        $updateFile = $cx->getWebsiteTempPath() . '/Update/' .
            \Cx\Core_Modules\Update\Model\Repository\DeltaRepository::PENDING_DB_UPDATES_YML;
        if (!file_exists($updateFile)) {
            return;
        }

        $updateController    = $this->getController('Update');
        $componentController = $this->getComponent('MultiSite');
        if ($componentController) {
            \Cx\Core\Setting\Controller\Setting::init(
                'MultiSite',
                'config',
                'FileSystem'
            );
	        $mode = \Cx\Core\Setting\Controller\Setting::getValue(
                'mode',
                'MultiSite'
            );
	        if ($mode != \Cx\Core_Modules\MultiSite\Controller\ComponentController::MODE_WEBSITE) {
	            return;
	        }
	        $componentController->setCustomerPanelDomainAsMainDomain();
            $updateController->setIsMultiSiteEnv(true);
        }

        $updateController->applyDelta();
    }

}
