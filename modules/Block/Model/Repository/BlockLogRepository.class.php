<?php

/**
 * Cloudrexx
 *
 * @link      https://www.cloudrexx.com
 * @copyright Cloudrexx AG 2007-2017
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
 * Cx\Modules\Block
 *
 * @copyright   CLOUDREXX CMS - CLOUDREXX AG
 * @author      Cloudrexx Development Team <info@cloudrexx.com>
 * @version     1.0.0
 * @package     cloudrexx
 * @subpackage  module_block
 */

namespace Cx\Modules\Block\Model\Repository;

use Gedmo\Loggable\Entity\Repository\LogEntryRepository;

/**
 * Cx\Modules\Block\Model\Repository\BlockLogRepository
 *
 * @copyright   CLOUDREXX CMS - CLOUDREXX AG
 * @author      Manuel Schenk <manuel.schenk@comvation.com>
 * @version     1.0.0
 * @package     cloudrexx
 * @subpackage  module_block
 */
class BlockLogRepository extends LogEntryRepository
{
    /**
     * Returns logs
     *
     * @param $entityClass string
     * @param $entityId integer
     * @param $action string
     * @param $limit integer
     * @param $offset integer
     * @return $logs array
     */
    public function getLogs($entityClass, $entityId, $action, $limit = null, $offset = null)
    {
        $em = \Cx\Core\Core\Controller\Cx::instanciate()->getDb()->getEntityManager();
        $logEntryRepo = $em->getRepository('\Cx\Modules\Block\Model\Entity\LogEntry');
        // sets findBy criteria
        $criteria = array(
            'objectClass' => $entityClass,
            'objectId' => $entityId,
        );
        // sets requested log action
        if ($action) {
            $criteria['action'] = $action;
        }
        // finds logs by given parameters
        $logs = $logEntryRepo->findBy(
            $criteria,
            array(
                'version' => 'DESC'
            ),
            $limit,
            $offset
        );
        // returns found logs
        return $logs;
    }

    /**
     * Returns row count for given entity
     *
     * @param $entity \Cx\Model\Base\EntityBase
     * @param $action string
     * @return $count integer
     */
    public function getLogCount($entity, $action)
    {
        // gets row count for given block
        $em = \Cx\Core\Core\Controller\Cx::instanciate()->getDb()->getEntityManager();
        $qb = $em->createQueryBuilder();
        $query = $qb->select('count(le.id)')
            ->from('\Cx\Modules\Block\Model\Entity\LogEntry', 'le')
            ->where('le.objectClass = \'' . get_class($entity) . '\'')
            ->andWhere('le.objectId = :eId')
            ->setParameter('eId', $entity->getId());

        // sets action if provided
        if ($action) {
            $query->andWhere('le.action = \'' . $action . '\'');
        }

        // gets result
        $count = $query->getQuery()->getSingleScalarResult();

        // returns row count
        return intval($count);
    }

    /**
     * Reverts given entity
     *
     * @param $entity \Cx\Model\Base\EntityBase
     * @param $version integer
     * @return $revertedEntity \Cx\Model\Base\EntityBase reverted doctrine entity
     */
    public function revertEntity($entity, $version)
    {
        $em = \Cx\Core\Core\Controller\Cx::instanciate()->getDb()->getEntityManager();
        // reverts entity by version
        $this->revert($entity, $version);
        $revertedEntity = $entity;
        // clears the identity map of the EntityManager to enforce entity reloading
        $em->clear($entity);
        // returns reverted entity
        return $revertedEntity;
    }
}
