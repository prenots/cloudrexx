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
 * Class UpdateController
 *
 * @copyright   CLOUDREXX CMS - CLOUDREXX AG
 * @author      Project Team SS4U <info@cloudrexx.com>
 * @package     cloudrexx
 * @subpackage  coremodule_update
 */

namespace Cx\Core_Modules\Update\Controller;

/**
 * Class UpdateController
 *
 * The main Update component
 *
 * @copyright   CLOUDREXX CMS - CLOUDREXX AG
 * @author      Project Team SS4U <info@cloudrexx.com>
 * @package     cloudrexx
 * @subpackage  coremodule_update
 */
class UpdateController extends \Cx\Core\Core\Model\Entity\Controller {

    
    /**
     * pending codeBase changes yml
     * @var string $pendingCodeBaseChangesYml
     */
    protected $pendingCodeBaseChangesYml = 'PendingCodeBaseChanges.yml';

    /**
     * Migration folder path
     * @var string $migrationFolderPath
     */
    protected $migrationFolderPath = '/Data/Migrations';
    
    /**
     * Command Line Interface
     * @var \Symfony\Component\Console\Application | array()
     */
    protected $cli = array();
    
    /**
     * Calculate database delta
     *
     * It will calculate which codeBase database update scripts need to be executed.
     * If the original version number is smaller than new version number,
     * Add each version between those two versions to the delta as non-rollback updates otherwise delta as rollback updates
     * 
     * @param array $params array of values for delta calculation
     * (components, codeBasePath, oldCodeBaseId, latestCodeBaseId)
     */
    public function calculateDbDelta($params)
    {
        //get the component list for update
        $qb = $this->cx->getDb()->getEntityManager()->createQueryBuilder();
        $qb ->select('c')
            ->from('Cx\Core\Core\Model\Entity\SystemComponent', 'c');
        !empty($params['components']) ? 
            $qb->where($qb->expr()->in('c.name', $params['components'])) : '';
        $componentList = $qb->getQuery()->getResult();
        if (\FWValidator::isEmpty($componentList)) {
            throw new \Exception('UpdateController::calculateDbDelta(): Invalid component list.');
        }

        $params['oldCodeBaseId']    = str_replace('.', '', $params['oldCodeBaseId']);
        $params['latestCodeBaseId'] = str_replace('.', '', $params['latestCodeBaseId']);
        foreach ($componentList as $component) {
            $this->calculateComponentDbDelta($params, $component);
        }
    }

    /**
     * Calculate component's database delta
     * 
     * @param array                                      $params    array of values for delta calculation
     * @param \Cx\Core\Core\Model\Entity\SystemComponent $component component entity
     * 
     * @return boolean
     */
    protected function calculateComponentDbDelta($params, \Cx\Core\Core\Model\Entity\SystemComponent $component)
    {
        static $objYaml = null;
        
        $componentName = $component->getName();
        $basePath      = \Cx\Core\Core\Model\Entity\SystemComponent::getPathForType($component->getType());
        $componentPath = $this->cx->getCodeBasePath() .  $basePath .'/' . $componentName;
        
        //Get the current codebase value from the component meta.yml
        if (file_exists($componentPath . '/meta.yml')) {
            if (!isset($objYaml)) {
                $objYaml = new \Symfony\Component\Yaml\Yaml();
            }
            $file    = new \Cx\Lib\FileSystem\File($componentPath . '/meta.yml');
            $content = $objYaml->load($file->getData());
            if (    isset($content['DlcInfo']) 
                &&  isset($content['DlcInfo']['version'])
                &&  !empty($content['DlcInfo']['version'])
            ) {
                $params['oldCodeBaseId'] = str_replace('.', '', $content['DlcInfo']['version']);
            }
        }
        
        //Check the migration folder exists in the corresponding component 
        //If not, proceed with next component
        //If exists, get version number from the version filename
        $versionClassPath = $params['codeBasePath'] .  $basePath .'/' . $componentName . $this->migrationFolderPath;
        if (!file_exists($versionClassPath)) {
            return false;
        }
        
        $versionFiles = array_diff(scandir($versionClassPath), array('..', '.'));
        $versions     = array();
        foreach ($versionFiles as $versionFile) {
            $versions[] = substr(str_replace('.php', '', $versionFile), 7);
        }

        //If version files not exists in migration folder, 
        //proceed with next component
        if (empty($versions)) {
            return false;
        }

        //compare version number to calculate Delta
        $i = 1;
        $isHigherVersion = $params['oldCodeBaseId'] < $params['latestCodeBaseId'];
        if (!$isHigherVersion) {
            rsort($versions);
        }
        foreach ($versions as $version) {
            if (    (   $isHigherVersion 
                    &&  (   $version > $params['oldCodeBaseId'] 
                        &&  $version <= $params['latestCodeBaseId']
                        )
                    )
                ||
                    (   !$isHigherVersion 
                    &&  (   $version <= $params['oldCodeBaseId'] 
                        &&  $version > $params['latestCodeBaseId']
                        )
                    )
            ) {
                $delta = new \Cx\Core_Modules\Update\Model\Entity\Delta();
                $rollBack = !$isHigherVersion ? true : false;
                $delta->addCodeBase($componentName, $version, $rollBack, $i);
                $this->registerDbUpdateHooks($delta);
                $i++;
            }
        }
        
        return true;
    }

    /**
     * Register DB Update hooks
     * 
     * This saves the calculated delta to /tmp/Update/$pendingCodeBaseChangesYml. 
     * It contains a serialized Delta.
     * 
     * @staticvar object $deltaRepo
     * @param \Cx\Core_Modules\Update\Model\Entity\Delta $delta
     */
    public function registerDbUpdateHooks(\Cx\Core_Modules\Update\Model\Entity\Delta $delta) {
        static $deltaRepo = null;
        if (!isset($deltaRepo)) {
            $deltaRepo = new \Cx\Core_Modules\Update\Model\Repository\DeltaRepository();
        }

        $deltaRepo->add($delta);
        $deltaRepo->flush();
    }

    /**
     * Get the serialized Delta
     * 
     * This loads the serialized Delta and calls applyNext() on it 
     * until returns false.
     * 
     * @return null
     */
    public function applyDelta() 
    {
        //set the website as Offline mode
        \Cx\Core\Setting\Controller\Setting::init('MultiSite', '', 'FileSystem');
        \Cx\Core\Setting\Controller\Setting::set('websiteState', \Cx\Core_Modules\MultiSite\Model\Entity\Website::STATE_OFFLINE);
        \Cx\Core\Setting\Controller\Setting::update('websiteState');

        //Read the current and new CodeBase versions and component list from the yml file
        $yamlFile = $this->cx->getWebsiteTempPath() . '/Update/'. $this->pendingCodeBaseChangesYml;
        if (\Cx\Lib\FileSystem\FileSystem::exists($yamlFile)) {
            $pendingCodeBaseChanges = $this->getUpdateWebsiteDetailsFromYml($yamlFile);
            $latestCodeBase         = $pendingCodeBaseChanges['PendingCodeBaseChanges']['latestCodeBaseId'];
            $components             = $pendingCodeBaseChanges['PendingCodeBaseChanges']['components'];
        }
        $isWebsiteUpdate = empty($components) ? true : false;
        $components      = empty($components) ? $this->getAllComponentList() : $components;
        
        //Run the DB migration process
        $return = $this->dbMigrationProcess($components);
        
        //Run the Rollback process if the update process is interrupted
        if (!$return['status'] && !empty($pendingCodeBaseChanges)) {
            $params = array(
                'components'      => $return['updatedComponents'],
                'oldCodeBase'     => $pendingCodeBaseChanges['PendingCodeBaseChanges']['oldCodeBaseId'],
                'latestCodeBase'  => $latestCodeBase,
                'isWebsiteUpdate' => $isWebsiteUpdate,
                
            );
            $this->rollBackProcess($params);
        }
        
        //If all the components are updated successfully 
        //then update all the component version in the corresponding meta.yml
        if ($return['status']) {
            $em = $this->cx->getDb()->getEntityManager();
            $componentRepo = $em->getRepository('Cx\Core\Core\Model\Entity\SystemComponent');
            foreach ($components as $componentName) {
                $component = $componentRepo->findOneBy(array('name' => $componentName));
                if (!$component) {
                    continue;
                }
                $reflectionComponent = new \Cx\Core\Core\Model\Entity\ReflectionComponent($component->getSystemComponent());
                $reflectionComponent->updateMetaData($component->getDirectory() . '/meta.yml', array('version' => $latestCodeBase));
            }
        }
        
        //Remove the folder '/tmp/Update', After the completion of rollback or Non-rollback process
        $tmpUpdateFolderPath = \Cx\Core\Core\Controller\Cx::instanciate()->getWebsiteTempPath() . '/Update';
        if (\Cx\Lib\FileSystem\FileSystem::exists($tmpUpdateFolderPath)) {
            \Cx\Lib\FileSystem\FileSystem::delete_folder($tmpUpdateFolderPath, true);
        }
        
        //set the website back to Online mode
        \Cx\Core\Setting\Controller\Setting::set('websiteState', \Cx\Core_Modules\MultiSite\Model\Entity\Website::STATE_ONLINE);
        \Cx\Core\Setting\Controller\Setting::update('websiteState');
    }

    /**
     * Run the DB migration process
     * 
     * @param array $components list of components for migration process
     * 
     * @return array migration process status return as array
     */
    protected function dbMigrationProcess($components) 
    {
        $updatedComponentList = array();
        foreach ($components as $component) {
            $deltaRepository = new \Cx\Core_Modules\Update\Model\Repository\DeltaRepository();
            $deltas = $deltaRepository->findBy(array('component' => $component));
            if (empty($deltas)) {
                continue;
            }

            asort($deltas);
            $updatedComponentList[] = $component;
            foreach ($deltas as $delta) {
                $status = $delta->applyNext();
                $delta->setRollback($delta->getRollback() ? false : true);
                $deltaRepository->flush();
                //Check if any of the update process is interrupt state, then stop the migration process
                if (!$status) {
                    return array('status' => false, 'updatedComponents' => $updatedComponentList);
                }
            }
        }
        return array('status' => true);
    }

    /**
     * Run the rollback process to rollback the delta and update website codeBase
     * 
     * @param array $params
     */
    protected function rollBackProcess($params)
    {
        //DB rollback process
        $this->rollBackDelta($params['components']);
        
        //If it is website update process, then rollback the codebase changes
        //(settings.php, configuration.php and website codebase in manager and service)
        if (    $params['isWebsiteUpdate'] 
            &&  !empty($params['oldCodeBase']) 
            &&  !empty($params['latestCodeBase'])
        ) {
            //Register YamlSettingEventListener
            \Cx\Core\Config\Controller\ComponentController::registerYamlSettingEventListener();

            //Update codeBase in website
            $this->updateCodeBase($params['latestCodeBase'], null, $params['oldCodeBase']);

            //Update website codebase in manager and service
            \Cx\Core\Setting\Controller\Setting::init('MultiSite', '', 'FileSystem');
            $websiteName = \Cx\Core\Setting\Controller\Setting::getValue('websiteName', 'MultiSite');
            $params = array('websiteName' => $websiteName, 'codeBase' => $params['oldCodeBase']);
            \Cx\Core_Modules\MultiSite\Controller\JsonMultiSiteController::executeCommandOnMyServiceServer('updateWebsiteCodeBase', $params);
        }
    }

    /**
     * Rollback the delta based on the component to reverse the website to old state
     * 
     * @param array $components
     */
    protected function rollBackDelta($components) 
    {
        if (empty($components)) {
            return;
        }
        
        //Run the DB rollback process
        foreach ($components as $component) {
            $deltaRepository = new \Cx\Core_Modules\Update\Model\Repository\DeltaRepository();
            $rollBackDeltas = $deltaRepository->findBy(array('component' => $component, 'rollback' => true));
            if (!$rollBackDeltas) {
                continue;
            }
            
            rsort($rollBackDeltas);
            foreach ($rollBackDeltas as $rollBackDelta) {
                if (!$rollBackDelta->applyNext()) {
                    $websiteName = \Cx\Core\Setting\Controller\Setting::getValue('websiteName', 'MultiSite');
                    $params = array('websiteName' => $websiteName, 'emailTemplateKey' => 'notification_update_error_email');
                    \Cx\Core_Modules\MultiSite\Controller\JsonMultiSiteController::executeCommandOnMyServiceServer('sendUpdateNotification', $params);
                    break 2;
                }
            }
        }
    }

    /**
     * Update CodeBase
     * 
     * @param string $newCodeBaseVersion   latest codeBase version
     * @param string $installationRootPath installationRoot path
     * @param string $oldCodeBaseVersion   old codeBase version
     */
    public function updateCodeBase($newCodeBaseVersion, $installationRootPath, $oldCodeBaseVersion = '') 
    {
        //change installation root
        $objConfigData = new \Cx\Lib\FileSystem\File(\Cx\Core\Core\Controller\Cx::instanciate()->getWebsiteConfigPath() . '/configuration.php');
        $configData    = $objConfigData->getData();
        if (!\FWValidator::isEmpty($oldCodeBaseVersion)) {
            $matches = array();
            preg_match('/\\$_PATHCONFIG\\[\'ascms_installation_root\'\\] = \'(.*?)\';/', $configData , $matches);
            $installationRootPath = str_replace($newCodeBaseVersion, $oldCodeBaseVersion, $matches[1] );
            $newCodeBaseVersion   = $oldCodeBaseVersion;
        }
        
        $newConfigData = preg_replace('/\\$_PATHCONFIG\\[\'ascms_installation_root\'\\] = \'.*?\';/', '$_PATHCONFIG[\'ascms_installation_root\'] = \'' . $installationRootPath . '\';', $configData);

        $objConfigData->write($newConfigData);
        
        //change code base
        \Cx\Core\Setting\Controller\Setting::init('Config', '', 'Yaml');
        \Cx\Core\Setting\Controller\Setting::set('coreCmsVersion', $newCodeBaseVersion);
        \Cx\Core\Setting\Controller\Setting::update('coreCmsVersion');
    }
    
    /**
     * Get Doctrine Migration Command Line Interface
     * 
     * @param string $component migration component name
     * 
     * @return \Symfony\Component\Console\Application
     */
    public function getDoctrineMigrationCli($component)
    {
        if (empty($component)) {
            throw new \Exception('UpdateController::getDoctrineMigrationCli(): Invalid component migration.');
        }
        
        if (isset($this->cli[$component])) {
            return $this->cli[$component];
        }
        
        $em = $this->cx->getDb()->getEntityManager();
        $conn = $em->getConnection();

        $this->cli[$component] = new \Symfony\Component\Console\Application('Doctrine Migration Command Line Interface', \Doctrine\Common\Version::VERSION);
        $this->cli[$component]->setCatchExceptions(true);
        $helperSet = $this->cli[$component]->getHelperSet();
        $helpers = array(
            'db' => new \Doctrine\DBAL\Tools\Console\Helper\ConnectionHelper($conn),
            'em' => new \Doctrine\ORM\Tools\Console\Helper\EntityManagerHelper($em),
        );
        foreach ($helpers as $name => $helper) {
            $helperSet->set($helper, $name);
        }

        $componentRepo   = $em->getRepository('Cx\Core\Core\Model\Entity\SystemComponent');
        $objComponent    = $componentRepo->findOneBy(array('name' => $component));
        $entityNameSpace = $objComponent->getNamespace() . '\\Data\\Migrations';
        //custom configuration
        $configuration = new \Cx\Core_Modules\Update\Model\Entity\MigrationsConfiguration($conn);
        $configuration->setName('Doctrine Migration');
        $configuration->setMigrationComponent($component);
        $configuration->setMigrationsNamespace($entityNameSpace);
        $configuration->setMigrationsTableName(DBPREFIX . 'migration_versions');
        $configuration->setMigrationsDirectory($objComponent->getDirectory() . $this->migrationFolderPath);
        $configuration->registerMigrationsFromDirectory($objComponent->getDirectory() . $this->migrationFolderPath);

        $this->cli[$component]->addCommands(array(
            // Migrations Commands
            $this->getDoctrineMigrationCommand('\Cx\Core_Modules\Update\Model\Entity\MigrationsDiffDoctrineCommand', $configuration),
            $this->getDoctrineMigrationCommand('\Doctrine\DBAL\Migrations\Tools\Console\Command\ExecuteCommand', $configuration),
            $this->getDoctrineMigrationCommand('\Doctrine\DBAL\Migrations\Tools\Console\Command\GenerateCommand', $configuration),
            $this->getDoctrineMigrationCommand('\Doctrine\DBAL\Migrations\Tools\Console\Command\MigrateCommand', $configuration),
            $this->getDoctrineMigrationCommand('\Doctrine\DBAL\Migrations\Tools\Console\Command\StatusCommand', $configuration),
            $this->getDoctrineMigrationCommand('\Doctrine\DBAL\Migrations\Tools\Console\Command\VersionCommand', $configuration),
        ));
        $this->cli[$component]->setAutoExit(false);
        
        return $this->cli[$component];
    }
    
    
    /**
     * Get the doctrine migrations command as object
     * 
     * @param string $migrationCommandNameSpace
     * @param object $configuration
     * 
     * @return object doctrine migration command
     */
    protected function getDoctrineMigrationCommand($migrationCommandNameSpace, $configuration) {
        $migrationCommand = new $migrationCommandNameSpace();
        $migrationCommand->setMigrationConfiguration($configuration);
        return $migrationCommand;
    }
    
    /**
     * Store the website details into the YML file
     * 
     * @param string $folderPath
     * @param string $filePath
     * @param array  $ymlContent
     * 
     * @return null
     */
    public function storeUpdateWebsiteDetailsToYml($folderPath, $filePath, $ymlContent)
    {
        if (empty($folderPath) || empty($filePath)) {
            return;
        }

        try {
            if (!file_exists($folderPath)) {
                \Cx\Lib\FileSystem\FileSystem::make_folder($folderPath);
            }

            $file = new \Cx\Lib\FileSystem\File($filePath);
            $file->touch();

            $yaml = new \Symfony\Component\Yaml\Yaml();
            $file->write(
                $yaml->dump(
                        array('PendingCodeBaseChanges' => $ymlContent )
                )
            );
        } catch (\Exception $e) {
            \DBG::log($e->getMessage());
        }
    }
    
    /**
     * Get update websiteDetailsFromYml
     * 
     * @param string $file yml file name
     * 
     * @return array website details
     */
    public function getUpdateWebsiteDetailsFromYml($file) {
        if (!file_exists($file)) {
            return;
        }
        $objFile = new \Cx\Lib\FileSystem\File($file);
        $yaml = new \Symfony\Component\Yaml\Yaml();
        return $yaml->load($objFile->getData());
    }
    
    /**
     * getAllCodeBaseVersions
     * 
     * @param string $codeBasePath codeBase path
     * 
     * @return array codeBase versions
     */
    public function getAllCodeBaseVersions($codeBasePath) {
        //codebase
        $codeBaseVersions   = array();
        $codebaseScannedDir = array_values(array_diff(scandir($codeBasePath), array('..', '.')));
        foreach ($codebaseScannedDir as $value) {
            $configFile = $codeBasePath . '/' . $value . '/installer/config/config.php';
            if (file_exists($configFile)) {
                $configContents = file_get_contents($configFile);
                if (preg_match_all('/\\$_CONFIG\\[\'(.*?)\'\\]\s+\=\s+\'(.*?)\';/s', $configContents, $matches)) {
                    $configValues       = array_combine($matches[1], $matches[2]);
                    $codeBaseVersions[] = $configValues['coreCmsVersion'];
                }
            }
        }
        return $codeBaseVersions;
    }
    
    /**
     * Get codeBase Changes file
     * 
     * @return string $pendingCodeBaseChangesYml
     */
    public function getPendingCodeBaseChangesFile() {
        return $this->pendingCodeBaseChangesYml;
    }
    
    /**
     * Get all the component list
     * 
     * @return array $componentList component list return as array
     */
    public function getAllComponentList()
    {
        $em = $this->cx->getDb()->getEntityManager();
        $componentRepo = $em->getRepository('Cx\Core\Core\Model\Entity\SystemComponent');
        $components    = $componentRepo->findAll();
        if ($components) {
            $componentList = array();
            foreach ($components as $component) {
                //This condition for avoiding the components Media1, Media2, Media3 and Media4
                if (!file_exists($component->getDirectory())) {
                    continue;
                }
                $componentList[] = $component->getName();
            }
        }
        sort($componentList);
        return $componentList;
    }
}