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
 * Class MigrationsVersion
 *
 * @copyright   CONTREXX CMS - COMVATION AG
 * @author      Project Team SS4U <info@comvation.com>
 * @package     contrexx
 * @subpackage  coremodule_update
 */

namespace Cx\Core_Modules\Update\Model\Entity;

use Doctrine\DBAL\Migrations\Configuration\Configuration;

/**
 * Class MigrationsVersion
 *
 * @copyright   CONTREXX CMS - COMVATION AG
 * @author      Project Team SS4U <info@comvation.com>
 * @package     contrexx
 * @subpackage  coremodule_update
 */
class MigrationsVersion extends \Doctrine\DBAL\Migrations\Version {
    
    /**
     * @var Connection
     */
    private $connection;
    
    /**
     * @var Component
     */
    private $component;
    
    public function __construct(Configuration $configuration, $version, $class, $component)
    {
        parent::__construct($configuration, $version, $class);
        $this->connection = $configuration->getConnection();
        $this->component  = $component;
    }
    
    public function markMigrated()
    {
        $this->getConfiguration()->createMigrationTable();
        $this->connection->executeQuery("INSERT INTO " . $this->getConfiguration()->getMigrationsTableName() . " (version, component) VALUES (?, ?)", array($this->getVersion(), $this->component));
    }

    public function markNotMigrated()
    {
        $this->getConfiguration()->createMigrationTable();
        $this->connection->executeQuery("DELETE FROM " . $this->getConfiguration()->getMigrationsTableName() . " WHERE version = ? AND component = ?", array($this->getVersion(), $this->component));
    }
}
