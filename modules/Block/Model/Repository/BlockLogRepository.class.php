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
     * Returns logs for block
     *
     * @param $block Cx\Modules\Block\Model\Entity\Block
     * @param $limit integer
     * @param $offset integer
     * @return $logs Doctrine\Common\Collections\Collection
     */
    public function getLogs($block, $limit = null, $offset = null)
    {
        $em = \Cx\Core\Core\Controller\Cx::instanciate()->getDb()->getEntityManager();
        $logEntryRepo = $em->getRepository('\Cx\Modules\Block\Model\Entity\LogEntry');
        // finds logs by given parameters
        $logs = $logEntryRepo->findBy(
            array(
                'objectClass' => 'Cx\Modules\Block\Model\Entity\Block',
                'objectId' => $block->getId(),
            ),
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
     * Returns row count for block
     *
     * @param $block Cx\Modules\Block\Model\Entity\Block
     * @return $count integer
     */
    public function getLogCount($block)
    {
        // gets row count for given block
        $em = \Cx\Core\Core\Controller\Cx::instanciate()->getDb()->getEntityManager();
        $qb = $em->createQueryBuilder();
        $count = $qb->select('count(le.id)')
            ->from('\Cx\Modules\Block\Model\Entity\LogEntry', 'le')
            ->where('le.objectClass = \'Cx\Modules\Block\Model\Entity\Block\'')
            ->andWhere('le.objectId = :bId')
            ->setParameter('bId', $block->getId())
            ->getQuery()
            ->getSingleScalarResult();

        // returns row count
        return intval($count);
    }

    /**
     * Returns specific version of a block
     *
     * @param $block Cx\Modules\Block\Model\Entity\Block
     * @param $version integer
     * @return $revertedBlock Cx\Modules\Block\Model\Entity\Block
     */
    public function getBlockVersion($block, $version)
    {
        $em = \Cx\Core\Core\Controller\Cx::instanciate()->getDb()->getEntityManager();
        // reverts entity by version
        $this->revert($block, $version);
        $revertedBlock = $block;
        // clears the identity map of the EntityManager to enforce block reloading
        $em->clear($block);
        // returns reverted block
        return $revertedBlock;
    }
}
