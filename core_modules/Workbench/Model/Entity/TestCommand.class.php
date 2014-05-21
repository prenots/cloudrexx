<?php
/**
 * Command to access behat command line tools
 * @author Michael Ritter <michael.ritter@comvation.com>
 */

namespace Cx\Core_Modules\Workbench\Model\Entity;

/**
 * Command to access behat command line tools
 * @author Michael Ritter <michael.ritter@comvation.com>
 */
class TestCommand extends Command {
    
    /**
     * Command name
     * @var string
     */
    protected $name = 'test';
    
    /**
     * Command description
     * @var string
     */
    protected $description = 'Wrapper for behat command line tools';
    
    /**
     * Command synopsis
     * @var string
     */
    protected $synopsis = 'workbench(.bat) test ([{component_name}|{component_type}]) ({some_crazy_arguments_for_phpunit})';
    
    /**
     * Command help text
     * @var string
     */
    protected $help = 'To be defined';
    
    /**
     * Execute this command
     * @param array $arguments Array of commandline arguments
     */
    public function execute(array $arguments) {
        /*
         * When creating a new component
         *  - cd to component's testing folder
         *  - behat --init
         *  - sample feature file (behat story-syntax)
         *  - behat --snippets
         * 
         * To execute tests
         *  - cd to component's testing folder
         *  - behat
         * 
         * Create test code
         *  - behat --snippets
         */
        /*global $argv;

        // php phpunit.php --bootstrap ../cx_bootstrap.php --testdox ../test/core/
        //\DBG::activate(DBG_PHP);
        $argv = array(
            'phpunit.php',
            //'--bootstrap',
            //'../cx_bootstrap.php',
            '--testdox',
            ASCMS_DOCUMENT_ROOT.'/testing/tests/core/',
        );*/
        
        $systemConfig      = \Env::get('config');
        $useCustomizing    = isset($systemConfig['useCustomizings']) && $systemConfig['useCustomizings'] == 'on';
        
        $arrComponentTypes = array('core', 'core_module', 'module');        
        $testingFolders    = array();
        
        // check for the component type
        if (isset($arguments[2]) && in_array($arguments[2], $arrComponentTypes)) {
            $testingFolders = $this->getTestingFoldersByType($arguments[2], $useCustomizing);
        } elseif (!empty ($arguments[2])) {
            // check whether it a valid component
            $componentName = $arguments[2];
            $componentType = '';
            
            foreach ($arrComponentTypes as $cType) {
                $componentFolder        = $this->getModuleFolder($componentName, $cType, $useCustomizing);
                $componentTestingFolder = $componentFolder . ASCMS_TESTING_FOLDER;
                if (!empty($componentFolder) && file_exists($componentFolder) && file_exists($componentTestingFolder)) {
                    $componentType = $cType;
                    $testingFolders[$componentName] = $componentTestingFolder;
                    break;
                }                
            }
        }
        
        // get all testing folder when component type or name not specificed
        if (empty($testingFolders)) {
            foreach ($arrComponentTypes as $cType) {
                $testingFolders = array_merge($testingFolders, $this->getTestingFoldersByType($cType, $useCustomizing));
            }
        }
             
        //chdir(ASCMS_DOCUMENT_ROOT.'/testing/PHPUnit/');
        //echo shell_exec('php phpunit.php --bootstrap ../cx_bootstrap.php --testdox ../tests/core/');
        
        $this->interface->show('Done');
    }
    
    /**
     * Return the testing folders by component type
     * 
     * @param  string $componentType Component type
     * 
     * @return array Testing folders by given component type
     */
    function getTestingFoldersByType($componentType, $useCustomizing) {
        
        $cx = \Env::get('cx');
        $em = $cx->getDb()->getEntityManager();        
        
        $testingFolders = array();
        
        $systemComponentRepo = $em->getRepository('Cx\\Core\\Core\\Model\\Entity\\SystemComponent');
        $systemComponents = $systemComponentRepo->findBy(array('type'=>$componentType));
        
        if (!empty($systemComponents)) {
            foreach ($systemComponents as $systemComponent) {
                $componentFolder        = $systemComponent->getDirectory($useCustomizing);
                $componentTestingFolder = $componentFolder . ASCMS_TESTING_FOLDER;
                
                if (file_exists($componentFolder) && file_exists($componentTestingFolder)) {
                    $testingFolders[$systemComponent->getName()] = $componentTestingFolder;
                }                
            }
        }
        
        // load the old legacy components. assume core_module, module can only possible
        if (in_array($componentType, array('core_module', 'module'))) {
            static $objModuleChecker = NULL;

            if (!isset($objModuleChecker)) {
                $objModuleChecker = \Cx\Core\ModuleChecker::getInstance(\Env::get('em'), \Env::get('db'), \Env::get('ClassLoader'));
            }
            
            $arrModules = array();
            switch ($componentType) {
                case 'core_module':
                    $arrModules = $objModuleChecker->getCoreModules();
                    break;
                case 'module':
                    $arrModules = $objModuleChecker->getModules();
                    break;
                default:
                    break;
            }
            
            foreach ($arrModules as $component) {
                if (!array_key_exists($component, $testingFolders)) {
                    $componentFolder        = $this->getModuleFolder($component, $componentType, $useCustomizing);
                    $componentTestingFolder = $componentFolder . ASCMS_TESTING_FOLDER;
                    if (!empty($componentFolder) && file_exists($componentFolder) && file_exists($componentTestingFolder)) {
                        $testingFolders[$component] = $componentTestingFolder;
                    }
                }
            }
        }

        return $testingFolders;
    }
    
    /**
     * Returns module folder name
     * 
     * @param string  $componentName     Component Name
     * @param string  $componentType     Component Type
     * @param boolean $allowCustomizing  Check for the customizing folder
     * 
     * @return string module folder name
     */
    function getModuleFolder($componentName, $componentType, $allowCustomizing = true)
    {
        $basepath      = ASCMS_DOCUMENT_ROOT . \Cx\Core\Core\Model\Entity\SystemComponent::getPathForType($componentType);
        $componentPath = $basepath . '/' . $componentName;
        
        if (!$allowCustomizing) {
            return $componentPath;
        }
        
        return \Env::get('ClassLoader')->getFilePath($componentPath);
    }
}
