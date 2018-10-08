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
 * @copyright   Cloudrexx AG
 * @author      Sam Hawkes <info@cloudrexx.com>
 * @package     cloudrexx
 */

namespace Cx\Core\MediaSource\Model\Entity;

/**
 * Handling Indexer Exception
 *
 * @copyright   Cloudrexx AG
 * @author      Sam Hawkes <info@cloudrexx.com>
 * @package     cloudrexx
 * @subpackage  core_mediasource
 */
class IndexerException extends \Exception {}

/**
 * Abstract class for Indexer
 *
 * Add, remove or search index entries. With this class you get the possibility
 * to write an indexer for searching other files
 *
 * @copyright   Cloudrexx AG
 * @author      Sam Hawkes <info@cloudrexx.com>
 * @package     cloudrexx
 * @subpackage  core_mediasource
 */
abstract class Indexer extends \Cx\Model\Base\EntityBase
{
    /**
     * @var $extensions array extension array
     */
    protected $extensions;

    /**
     * Set extensions of indexer
     *
     * @param $extensions array all extensions of indexer
     *
     * @return void
     */
    public function setExtensions($extensions)
    {
        $this->extensions = $extensions;
    }

    /**
     * Get extensions of indexer
     *
     * @return array
     */
    public function getExtensions()
    {
        return $this->extensions;
    }

    /**
     * Index all files which match the indexer type
     *
     * @param $path    string path to indexing file
     * @param $oldPath string (optional) path of the previous location, to get
     *                        the right database entry.
     *                        example use-case: used if an entry is moved.
     * @param $flush   bool   if you want to flush or not
     *
     * @throws \Doctrine\ORM\OptimisticLockException
     * @return void
     */
    public function index($path, $oldPath = '', $flush = true)
    {
        $pathToText = $path;
        $em = $this->cx->getDb()->getEntityManager();
        $repo = $em->getRepository(
            'Cx\Core\MediaSource\Model\Entity\IndexerEntry'
        );

        if (!empty($oldPath)) {
            $indexerEntry = $repo->findOneBy(
                array('path' => $oldPath, 'indexer' => get_class($this))
            );
            $pathToText = $oldPath;
        } else {
            $indexerEntry = $repo->findOneBy(
                array('path' => $path, 'indexer' => get_class($this))
            );
        }

        if (!$indexerEntry) {
            $indexerEntry = new \Cx\Core\MediaSource\Model\Entity\IndexerEntry();
        }
        $indexerEntry->setPath($path);
        $indexerEntry->setIndexer(get_class($this));
        $content = $this->getText($pathToText);
        $indexerEntry->setContent(
            $content
        );
        $indexerEntry->setLastUpdate(new \DateTime('now'));

        $em->persist($indexerEntry);

        if ($flush) {
            $em->flush();
        }
    }

    /**
     * Delete entries to clear the index
     *
     * @param $path string path to string
     */
    public function clearIndex($path = '')
    {
        $em = $this->cx->getDb()->getEntityManager();
        $indexerEntryRepo = $em->getRepository(
            '\Cx\Core\MediaSource\Model\Entity\IndexerEntry'
        );
        if (!empty($path)) {
            $indexerEntries = $indexerEntryRepo->findBy(
                array('path' => $path, 'indexer' => get_class($this))
            );
        } else {
            $indexerEntries = $indexerEntryRepo->findBy(
                array('indexer' => get_class($this))
            );
        }

        foreach ($indexerEntries as $indexerEntry) {
            $em->remove($indexerEntry);
        }
        $em->flush();
    }

    /**
     * Return all index entries that match
     *
     * @param $searchterm string term to search
     * @param $path       string path to search in
     *
     * @return array
     */
    public function getMatches($searchterm, $path)
    {
        $em = $this->cx->getDb()->getEntityManager();

        $qb = $em->createQueryBuilder();
        $query = $qb->select(array('ie'))->from(
            'Cx\Core\MediaSource\Model\Entity\IndexerEntry', 'ie'
        )->where(
            $qb->expr()->like('ie.path', ':path')
        )->andWhere(
            $qb->expr()->like(
                'ie.content', ':searchterm'
            )
        )->andWhere(
            $qb->expr()->eq(
                'ie.indexer', ':indexer'
            )
        )->orWhere(
            $qb->expr()->like(
                'ie.path', ':searchtermAndPath'
            )
        )->orWhere(
            $qb->expr()->eq(
                'ie.indexer', ':indexer'
            )
        )->setParameters(
            array(
                'path' => $path.'%',
                'searchterm' => '%'.$searchterm.'%',
                'indexer' => get_class($this),
                'searchtermAndPath' => $path.'%'.$searchterm.'%'
            )
        )->getQuery();

        return $query->getResult();
    }

    /**
     * Get text from an indexed file
     *
     * @param $filepath string path to file
     *
     * @return string
     */
    abstract protected function getText($filepath);

}