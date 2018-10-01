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


use \Cx\Update\UpdatePageEventListener as PageEventListener;

require_once(UPDATE_PATH . '/core/UpdatePageEventListener.class.php');
require_once(UPDATE_PATH . '/lib/Gedmo/Loggable/Entity/MappedSuperclass/AbstractLogEntry.php');
require_once(UPDATE_PATH . '/core/Model/Controller/EntityManager.class.php');

$_DBCONFIG   = \Env::get('dbconfig');
$doctrineDir = ASCMS_LIBRARY_PATH . '/doctrine/';
require_once(UPDATE_PATH . '/lib/FRAMEWORK/DBG/DoctrineSQLLogger.class.php');

$config = new \Doctrine\ORM\Configuration();

$cache = new \Doctrine\Common\Cache\ArrayCache();
$config->setResultCacheImpl($cache);
$config->setMetadataCacheImpl($cache);
$config->setQueryCacheImpl($cache);

$config->setProxyDir(ASCMS_MODEL_PROXIES_PATH);
$config->setProxyNamespace('Cx\Model\Proxies');
$config->setAutoGenerateProxyClasses(false);

global $pdoConnectionUpdate;
$pdoConnectionUpdate->setAttribute(PDO::ATTR_STATEMENT_CLASS, array('Doctrine\DBAL\Driver\PDOStatement', array()));
$connectionOptions = array(
    'pdo' => $pdoConnectionUpdate,
    'dbname'    => $_DBCONFIG['database'],
);

$evm = new \Doctrine\Common\EventManager();

$chainDriverImpl = new \Doctrine\ORM\Mapping\Driver\DriverChain();
$driverImpl = new \Doctrine\ORM\Mapping\Driver\YamlDriver(ASCMS_MODEL_PATH.'/yml');
$chainDriverImpl->addDriver($driverImpl, 'Cx');
$chainDriverImpl->getDrivers()['Cx']->getLocator()->addPaths(array(
    ASCMS_CORE_PATH.'/ContentManager/Model/Yaml',
    ASCMS_CORE_PATH.'/Core/Model/Yaml',
    ASCMS_CORE_PATH.'/View/Model/Yaml',
    ASCMS_CORE_PATH.'/Locale/Model/Yaml',
    ASCMS_CORE_PATH.'/Net/Model/Yaml',
));

//loggable stuff
$loggableDriverImpl = $config->newDefaultAnnotationDriver(array(
    UPDATE_CORE,
    $doctrineDir.'Gedmo/Loggable/Entity', // Document for ODM
));
$chainDriverImpl->addDriver($loggableDriverImpl, 'Gedmo\Loggable');

$loggableListener = new \Cx\Update\core\LoggableListener();
$evm->addEventSubscriber($loggableListener);
\Env::set('loggableListener', $loggableListener);

//tree stuff
$treeListener = new \Gedmo\Tree\TreeListener();
$evm->addEventSubscriber($treeListener);
$config->setMetadataDriverImpl($chainDriverImpl);

//table prefix
$prefixListener = new \DoctrineExtension\TablePrefixListener($_DBCONFIG['tablePrefix']);
$evm->addEventListener(\Doctrine\ORM\Events::loadClassMetadata, $prefixListener);

//page listener for unique slugs
$pageListener = new PageEventListener();
$evm->addEventListener(\Doctrine\ORM\Events::preUpdate, $pageListener);
$evm->addEventListener(\Doctrine\ORM\Events::onFlush, $pageListener);
$evm->addEventListener(\Doctrine\ORM\Events::preRemove, $pageListener);

$config->setSqlLogger(new \Cx\Lib\DBG\DoctrineSQLLogger());

$em = \Cx\Update\EntityManager::create($connectionOptions, $config, $evm);

//resolve enum, set errors
$conn = $em->getConnection();
foreach (array('enum', 'timestamp') as $type) {
    \Doctrine\DBAL\Types\Type::addType(
        $type,
        'Cx\Core\Model\Model\Entity\\' . ucfirst($type) . 'Type'
    );
    $conn->getDatabasePlatform()->registerDoctrineTypeMapping(
        $type,
        $type
    );
}
$conn->getDatabasePlatform()->registerDoctrineTypeMapping(
    'set',
    'string'
);
\Cx\Core\Model\Controller\YamlDriver::registerKnownEnumTypes($conn);

Env::setEm($em);
