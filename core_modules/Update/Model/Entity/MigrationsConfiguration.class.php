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
 * Class MigrationsConfiguration
 *
 * @copyright   CONTREXX CMS - COMVATION AG
 * @author      Project Team SS4U <info@comvation.com>
 * @package     contrexx
 * @subpackage  coremodule_update
 */

namespace Cx\Core_Modules\Update\Model\Entity;

use Doctrine\DBAL\Connection,
    Doctrine\DBAL\Migrations\MigrationException,
    Doctrine\DBAL\Migrations\Version,
    Cx\Core_Modules\Update\Model\Entity\MigrationsVersion,
    Doctrine\DBAL\Migrations\OutputWriter,
    Doctrine\DBAL\Schema\Table,
    Doctrine\DBAL\Schema\Column,
    Doctrine\DBAL\Types\Type;

/**
 * Class MigrationsConfiguration
 *
 * @copyright   CONTREXX CMS - COMVATION AG
 * @author      Project Team SS4U <info@comvation.com>
 * @package     contrexx
 * @subpackage  coremodule_update
 */
class MigrationsConfiguration extends \Doctrine\DBAL\Migrations\Configuration\Configuration
{
    /**
     * Flag for whether or not the migration table has been created
     *
     * @var bool
     */
    private $migrationTableCreated = false;

    /**
     * Connection instance to use for migrations
     *
     * @var Connection
     */
    private $connection;

    /**
     * The migration table name to track versions in
     *
     * @var string
     */
    private $migrationsTableName = 'doctrine_migration_versions';

    /**
     * Array of the registered migrations
     *
     * @var array
     */
    private $migrations = array();

    /**
     * Component name
     * 
     * @var string $component
     */
    private $component;
    
    /**
     * Construct a migration configuration object.
     *
     * @param Connection $connection      A Connection instance
     * @param OutputWriter $outputWriter  A OutputWriter instance
     */
    public function __construct(Connection $connection, OutputWriter $outputWriter = null)
    {
        parent::__construct($connection, $outputWriter);
        $this->connection = $connection;
    }

    /**
     * Set the migration table name
     *
     * @param string $tableName The migration table name
     */
    public function setMigrationsTableName($tableName)
    {
        $this->migrationsTableName = $tableName;
    }

    /**
     * Returns the migration table name
     *
     * @return string $migrationsTableName The migration table name
     */
    public function getMigrationsTableName()
    {
        return $this->migrationsTableName;
    }

    /**
     * set the migration component
     * 
     * @param string $migrationComponent
     */
    public function setMigrationComponent($migrationComponent)
    {
        $this->component = $migrationComponent;
    }

    /**
     * get the migration component
     * 
     * @return string $component
     */
    public function getMigrationComponent()
    {
        return $this->component;
    }
    
    /**
     * Register a single migration version to be executed by a AbstractMigration
     * class.
     *
     * @param string $version  The version of the migration in the format YYYYMMDDHHMMSS.
     * @param string $class    The migration class to execute for the version.
     */
    public function registerMigration($version, $class)
    {
        $version = (string) $version;
        $class = (string) $class;
        if (isset($this->migrations[$this->component][$version])) {
            throw MigrationException::duplicateMigrationVersion($version, get_class($this->migrations[$version]));
        }
        $version = new MigrationsVersion($this, $version, $class, $this->component);
        $this->migrations[$this->component][$version->getVersion()] = $version;
        ksort($this->migrations[$this->component]);
        return $version;
    }

    /**
     * Get the array of registered migration versions.
     *
     * @return array $migrations
     */
    public function getMigrations()
    {
        return $this->migrations[$this->component];
    }

    /**
     * Returns the Version instance for a given version in the format YYYYMMDDHHMMSS.
     *
     * @param string $version   The version string in the format YYYYMMDDHHMMSS.
     * @return Version $version
     * @throws MigrationException $exception Throws exception if migration version does not exist.
     */
    public function getVersion($version)
    {
        if ( ! isset($this->migrations[$this->component][$version])) {
            throw MigrationException::unknownMigrationVersion($version);
        }
        return $this->migrations[$this->component][$version];
    }

    /**
     * Check if a version exists.
     *
     * @param string $version
     * @return bool $exists
     */
    public function hasVersion($version)
    {
        return isset($this->migrations[$this->component][$version]) ? true : false;
    }

    /**
     * Check if a version has been migrated or not yet
     *
     * @param Version $version
     * @return bool $migrated
     */
    public function hasVersionMigrated(Version $version)
    {
        $this->createMigrationTable();

        $version = $this->connection->fetchColumn("SELECT version FROM " . $this->migrationsTableName . " WHERE version = ? AND component = ?", array($version->getVersion(), $this->getMigrationComponent()));
        return $version !== false ? true : false;
    }

    /**
     * Returns all migrated versions from the versions table, in an array.
     *
     * @return array $migrated
     */
    public function getMigratedVersions()
    {
        $this->createMigrationTable();

        $ret = $this->connection->fetchAll("SELECT version FROM " . $this->migrationsTableName . " WHERE component = ?", array($this->getMigrationComponent()));
        $versions = array();
        foreach ($ret as $version) {
            $versions[] = current($version);
        }

        return $versions;
    }

    /**
     * Returns an array of available migration version numbers.
     *
     * @return array $availableVersions
     */
    public function getAvailableVersions()
    {
        $availableVersions = array();
        foreach ($this->migrations[$this->component] as $migration) {
            $availableVersions[] = $migration->getVersion();
        }
        return $availableVersions;
    }

    /**
     * Returns the current migrated version from the versions table.
     *
     * @return bool $currentVersion
     */
    public function getCurrentVersion()
    {
        $this->createMigrationTable();

        $where = null;
        if ($this->migrations[$this->component]) {
            $migratedVersions = array();
            foreach ($this->migrations[$this->component] as $migration) {
                $migratedVersions[] = sprintf("'%s'", $migration->getVersion());
            }
            $where = " WHERE version IN (" . implode(', ', $migratedVersions) . ") AND component = '" . $this->component . "'";
        }

        $sql = sprintf("SELECT version FROM %s%s ORDER BY version DESC",
            $this->migrationsTableName, $where
        );

        $sql = $this->connection->getDatabasePlatform()->modifyLimitQuery($sql, 1);
        $result = $this->connection->fetchColumn($sql);
        return $result !== false ? (string) $result : '0';
    }

    /**
     * Returns the total number of executed migration versions
     *
     * @return integer $count
     */
    public function getNumberOfExecutedMigrations()
    {
        $this->createMigrationTable();

        $result = $this->connection->fetchColumn("SELECT COUNT(version) FROM " . $this->migrationsTableName . " WHERE component = ?", array($this->getMigrationComponent()));
        return $result !== false ? $result : 0;
    }

    /**
     * Returns the total number of available migration versions
     *
     * @return integer $count
     */
    public function getNumberOfAvailableMigrations()
    {
        return count($this->migrations[$this->component]);
    }

    /**
     * Returns the latest available migration version.
     *
     * @return string $version  The version string in the format YYYYMMDDHHMMSS.
     */
    public function getLatestVersion()
    {
        $versions = array_keys($this->migrations[$this->component]);
        $latest = end($versions);
        return $latest !== false ? (string) $latest : '0';
    }

    /**
     * Create the migration table to track migrations with.
     *
     * @return bool $created  Whether or not the table was created.
     */
    public function createMigrationTable()
    {
        $this->validate();

        if ($this->migrationTableCreated) {
            return false;
        }

        if ( ! $this->connection->getSchemaManager()->tablesExist(array($this->migrationsTableName))) {
            $columns = array(
                'version' => new Column('version', Type::getType('string'), array('length' => 255)),
                'component' => new Column('component', Type::getType('string'), array('length' => 255)),
            );
            $table = new Table($this->migrationsTableName, $columns);
            $table->setPrimaryKey(array('version', 'component'));
            $this->connection->getSchemaManager()->createTable($table);

            $this->migrationTableCreated = true;

            return true;
        }
        return false;
    }

    /**
     * Returns the array of migrations to executed based on the given direction
     * and target version number.
     *
     * @param string $direction    The direction we are migrating.
     * @param string $to           The version to migrate to.
     * @return array $migrations   The array of migrations we can execute.
     */
    public function getMigrationsToExecute($direction, $to)
    {
        if ($direction === 'down') {
            if (count($this->migrations[$this->component])) {
                $allVersions = array_reverse(array_keys($this->migrations[$this->component]));
                $classes = array_reverse(array_values($this->migrations[$this->component]));
                $allVersions = array_combine($allVersions, $classes);
            } else {
                $allVersions = array();
            }
        } else {
            $allVersions = $this->migrations[$this->component];
        }
        $versions = array();
        $migrated = $this->getMigratedVersions();
        foreach ($allVersions as $version) {
            if ($this->shouldExecuteMigration($direction, $version, $to, $migrated)) {
                $versions[$version->getVersion()] = $version;
            }
        }
        return $versions;
    }
}