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
 * This listener ensures slug consistency on Page objects.
 * On Flushing, all entities are scanned and changed where needed.
 * After persist, the XMLSitemap is rewritten
 *
 * @copyright   CLOUDREXX CMS - CLOUDREXX AG
 * @author      CLOUDREXX Development Team <info@cloudrexx.com>
 * @package     cloudrexx
 * @subpackage  core_contentmanager
 */

namespace Cx\Core\ContentManager\Model\Event;

use \Cx\Core\ContentManager\Model\Entity\Page as Page;
use Doctrine\Common\Util\Debug as DoctrineDebug;

/**
 * PageEventListenerException
 *
 * @copyright   CLOUDREXX CMS - CLOUDREXX AG
 * @author      CLOUDREXX Development Team <info@cloudrexx.com>
 * @package     cloudrexx
 * @subpackage  core_contentmanager
 */
class PageEventListenerException extends \Exception {}

/**
 * PageEventListener
 *
 * @copyright   CLOUDREXX CMS - CLOUDREXX AG
 * @author      CLOUDREXX Development Team <info@cloudrexx.com>
 * @package     cloudrexx
 * @subpackage  core_contentmanager
 */
class PageEventListener implements \Cx\Core\Event\Model\Entity\EventListener {

    /**
     * lastPreUpdateChangeset
     *
     * @var array Entity changeset
     */
    protected $lastPreUpdateChangeset;

    /**
     * lastUpdatedEntity
     *
     * @var \Cx\Core\ContentManager\Model\Entity\Page
     */
    protected $lastUpdatedEntity;

    /**
     * entity's OldEditingStatus
     *
     * @var string
     */
    protected $entitysOldEditingStatus;

    /**
     * updatePageLog
     *
     * @var boolean
     */
    protected $updatePageLog = true;

    public function prePersist($eventArgs) {
        $this->setUpdatedByCurrentlyLoggedInUser($eventArgs);
        $this->fixAutoIncrement();
    }

    /**
     *
     * @param \Doctrine\ORM\Event\PreUpdateEventArgs $eventArgs
     */
    public function preUpdate($eventArgs) {
        $this->setUpdatedByCurrentlyLoggedInUser($eventArgs);
        $this->lastPreUpdateChangeset = $eventArgs->getEntityChangeSet();
    }

    /**
     * The page is updated by currently logged user
     *
     * @param \Doctrine\ORM\Event\PreUpdateEventArgs $eventArgs
     *
     * @return null
     */
    protected function setUpdatedByCurrentlyLoggedInUser($eventArgs) {
        $entity = $eventArgs->getEntity();

        if ($entity instanceof \Cx\Core\ContentManager\Model\Entity\Page) {
            $entity->setUpdatedBy(
                \FWUser::getFWUserObject()->objUser->getUsername()
            );
        }
    }

    public function preRemove($eventArgs) {
        $em      = $eventArgs->getEntityManager();
        $uow     = $em->getUnitOfWork();
        $entity  = $eventArgs->getEntity();

        // remove aliases of page
        $aliases = $entity->getAliases();
        if (!empty($aliases)) {
            foreach ($aliases as $alias) {
                $node = $alias->getNode();
                $em->remove($node);
                $uow->computeChangeSet(
                    $em->getClassMetadata('Cx\Core\ContentManager\Model\Entity\Node'),
                    $node
                );
            }
        }
    }

    public function postPersist($eventArgs) {
        $this->writeXmlSitemap($eventArgs);
        // drop complete cache on page creation:
        $cx = \Cx\Core\Core\Controller\Cx::instanciate();
        $cx->getComponent('Cache')->clearCache();
    }

    public function postUpdate($eventArgs) {
        $this->writeXmlSitemap($eventArgs);
        // drop complete cache if active or visible flag changed
        // or if navigation title, navigation CSS name or slug changed:
        if (
            $this->lastPreUpdateChangeset &&
            (
                isset($this->lastPreUpdateChangeset['active']) ||
                isset($this->lastPreUpdateChangeset['display']) ||
                isset($this->lastPreUpdateChangeset['slug']) ||
                isset($this->lastPreUpdateChangeset['cssNavName']) ||
                isset($this->lastPreUpdateChangeset['title'])
            )
        ) {
            $cx = \Cx\Core\Core\Controller\Cx::instanciate();
            $cx->getComponent('Cache')->clearCache();
        }
    }

    public function postRemove($eventArgs) {
        $this->writeXmlSitemap($eventArgs);
    }

    protected function writeXmlSitemap($eventArgs) {
        global $_CONFIG;

        $entity = $eventArgs->getEntity();
        if (($entity instanceof \Cx\Core\ContentManager\Model\Entity\Page)
            && ($entity->getType() != \Cx\Core\ContentManager\Model\Entity\Page::TYPE_ALIAS)
            && ($_CONFIG['xmlSitemapStatus'] == 'on')
        ) {
            \Cx\Core\PageTree\XmlSitemapPageTree::write();
        }
    }

    /**
     * Event postFlush
     *
     * @param \Doctrine\ORM\Event\PostFlushEventArgs $eventArgs
     */
    public function postFlush($eventArgs)
    {
        if (!$this->updatePageLog || !$this->lastUpdatedEntity) {
            return;
        }

        $this->updatePageLogs(
            $eventArgs->getEntityManager(),
            $this->lastUpdatedEntity
        );
    }

    /**
     * Event OnFlush
     *
     * @param \Doctrine\ORM\Event\OnFlushEventArgs $eventArgs
     */
    public function onFlush($eventArgs) {
        $em = $eventArgs->getEntityManager();

        $uow = $em->getUnitOfWork();

        $pageRepo = $em->getRepository('Cx\Core\ContentManager\Model\Entity\Page');

        /*
         * Really unneccessary, because we do not use resultcache
         * @see http://bugs.contrexx.com/contrexx/ticket/2339
         */
        //\Cx\Core\Core\Controller\Cx::instanciate()->getComponent('Cache')->clearCache();

        foreach ($uow->getScheduledEntityUpdates() AS $entity) {
            $this->checkValidPersistingOperation($pageRepo, $entity);
            if (!$this->updatePageLog || !($entity instanceof Page)) {
                continue;
            }
            $this->lastUpdatedEntity = $entity;
            $changeSet = $uow->getEntityChangeSet($entity);
            if (isset($changeSet['editingStatus'])) {
                $this->entitysOldEditingStatus = $changeSet['editingStatus'][0];
            } else  {
                $this->entitysOldEditingStatus =
                    $this->lastUpdatedEntity->getEditingStatus();
            }
        }
    }

    /**
     * Update page logs while add/edit a page as draft
     *
     * @param Cx\Core\Model\Controller\EntityManager   $em   Entity Manager object
     * @param Cx\Core\ContentManager\Model\Entity\Page $page Page object
     *
     * @return boolean
     */
    protected function updatePageLogs($em, $page)
    {
        if (!$page) {
            return false;
        }

        $this->updatePageLog = false;
        $updatingDraft       = false;
        $logRepo             = $em->getRepository(
            'Cx\Core\ContentManager\Model\Entity\LogEntry'
        );
        $action = '';
        if (isset($_POST['action'])) {
            $action = contrexx_input2raw($_POST['action']);
        }

        if (
            !empty($action) &&
            ($action != 'publish') &&
            !($this->entitysOldEditingStatus != '')
        ) {
            $action = 'publish';
        }

        if (
            !\FWValidator::isEmpty($page->getEditingStatus()) &&
            in_array(
                $page->getEditingStatus(),
                array('hasDraftWaiting', 'hasDraft')
            )
        ) {
            $updatingDraft = false;
            if ($this->entitysOldEditingStatus != '') {
                $updatingDraft = true;
            }
            // Gedmo-loggable generates a LogEntry (i.e. revision) on persist, so we'll have to
            // store the draft first, then revert the current version to what it previously was.
            // In the end, we'll have the current [published] version properly stored as a page
            // and the draft version stored as a gedmo LogEntry.
            $logEntries  = $logRepo->getLogEntries($page, false, 2);
            //Revert the page to published version
            $cachedEditingStatus = $page->getEditingStatus();
            $logRepo->revert($page, $logEntries[1]->getVersion());
            //Update page editing status and action(publish, activate,
            //deactivate, show, hide, protect, unprotect, lock, unlock) based values
            $page->setEditingStatus($cachedEditingStatus);
            switch($action) {
                case 'activate':
                case 'publish':
                    $page->setActive(true);
                    break;
                case 'deactivate':
                    $page->setActive(false);
                    break;
                case 'show':
                    $page->setDisplay(true);
                    break;
                case 'hide':
                    $page->setDisplay(false);
                    break;
                case 'protect':
                    $page->setFrontendProtection(true);
                    break;
                case 'unprotect':
                    $page->setFrontendProtection(false);
                    break;
                case 'lock':
                    $page->setBackendProtection(true);
                    break;
                case 'unlock':
                    $page->setBackendProtection(false);
                    break;
            }
            $em->getUnitOfWork()->computeChangeSet(
                $em->getClassMetadata('Cx\Core\ContentManager\Model\Entity\Page'),
                $page
            );
            //Gedmo auto-logs slightly too much data. clean up unnecessary revisions:
            if ($updatingDraft) {
                $em->flush();
                $logEntries = $logRepo->getLogEntries($page, true, 3);
                $currentLog = $logEntries[1];
                $currentLogData = $currentLog->getData();
                $currentLogData['editingStatus'] = $page->getEditingStatus();
                $currentLog->setData($currentLogData);
                $em->getUnitOfWork()->computeChangeSet(
                    $em->getClassMetadata('Cx\Core\ContentManager\Model\Entity\LogEntry'),
                    $currentLog
                );

                $liveUpdateLog = $logEntries[2];
                $em->remove($logEntries[2]);
            }
            $em->flush();
        }
        //this fixes log version number skipping
        $em->clear();
        $logs = $logRepo->getLogEntries($page, true, 2);
        $em->getUnitOfWork()->computeChangeSet(
            $em->getClassMetadata('Cx\Core\ContentManager\Model\Entity\LogEntry'),
            $logs[0]
        );

        if (!$updatingDraft) {
            $em->flush();
            $this->updatePageLog     = true;
            $this->lastUpdatedEntity = null;
            return;
        }
        $data = $logs[1]->getData();
        $data['editingStatus'] = 'hasDraft';
        if (
            $action == 'publish' &&
            !\Permission::checkAccess(78, 'static', true)
        ) {
            $data['editingStatus'] = 'hasDraftWaiting';
        }
        $this->updateLogByAction($data, $action);
        $logs[1]->setData($data);
        $em->getUnitOfWork()->computeChangeSet(
            $em->getClassMetadata('Cx\Core\ContentManager\Model\Entity\LogEntry'),
            $logs[1]
        );
        if (!empty($action) && $action != 'publish') {
            $data = $logs[0]->getData();
            if ($liveUpdateLog) {
                $data = $liveUpdateLog->getData();
            }
            $this->updateLogByAction($data, $action);
            $logs[0]->setData($data);
            $em->getUnitOfWork()->computeChangeSet(
                 $em->getClassMetadata('Cx\Core\ContentManager\Model\Entity\LogEntry'),
                $logs[0]
            );
        }
        $em->flush();
        $this->updatePageLog     = true;
        $this->lastUpdatedEntity = null;
    }

    /**
     * Update log by action
     *
     * @param array  $data   log data
     * @param string $action action value
     */
    protected function updateLogByAction(&$data, $action)
    {
        if (empty($action)) {
            return;
        }

        switch($action) {
            case 'activate':
            case 'publish':
                $data['active'] = true;
                break;
            case 'deactivate':
                $data['active'] = false;
                break;
            case 'show':
                $data['display'] = true;
                break;
            case 'hide':
                $data['display'] = false;
                break;
            case 'protect':
                $data['protection'] = $data['protection'] | FRONTEND_PROTECTION;
                break;
            case 'unprotect':
                $data['protection'] = $data['protection'] & ~FRONTEND_PROTECTION;
                break;
            case 'lock':
                $data['protection'] = $data['protection'] | BACKEND_PROTECTION;
                break;
            case 'unlock':
                $data['protection'] = $data['protection'] & ~BACKEND_PROTECTION;
                break;
        }
    }

    /**
     * Sanity test for Pages. Prevents user from persisting bogus Pages.
     * This is the case if
     *  - the Page has fallback content. In this case, the Page's content was overwritten with
     *    other data that is not meant to be persisted.
     *  - more than one page has module home without cmd
     * @throws PageEventListenerException
     */
    protected function checkValidPersistingOperation($pageRepo, $page) {
        global $_CORELANG;

        if ($page instanceof Page) {
            if ($page->isVirtual()) {
                throw new PageEventListenerException('Tried to persist Page "'.$page->getTitle().'" with id "'.$page->getId().'". This Page is virtual and cannot be stored in the DB.');
            }
            if ($page->getModule() == 'Home'
                    && $page->getCmd() == ''
                    && $page->getType() == \Cx\Core\ContentManager\Model\Entity\Page::TYPE_APPLICATION
            ) {
                $home = $pageRepo->findBy(array(
                    'module' => 'Home',
                    'cmd' => '',
                    'lang' => $page->getLang(),
                    'type' => \Cx\Core\ContentManager\Model\Entity\Page::TYPE_APPLICATION,
                ));
                reset($home);
                if (   count($home) > 1
                    || (   count($home) == 1
                        && current($home)->getId() != $page->getId())
                ) {
                    throw new PageEventListenerException('Tried to persist Page "'.$page->getTitle().'" with id "'.$page->getId().'". Only one page with module "home" and no cmd is allowed.');

                    // the following is not necessary, since a nice error message
                    // is display by javascript.
                    // find the other page to display a better error message:
                    if (current($home)->getId() == $page->getId()) {
                        $home = end($home);
                    } else {
                        $home = current($home);
                    }
                    throw new PageEventListenerException(sprintf($_CORELANG['TXT_CORE_CM_HOME_FAIL'], $home->getId(), $home->getPath()));

                    //SQLSTATE[23000]: Integrity constraint violation: 1062 Duplicate entry '1-110-Cx\Model\ContentManager\Page' for key 'log_class_unique_version_idx'

                    //'Tried to persist Page "'.$page->getTitle().'" with id "'.$page->getId().'". Only one page with module "home" and no cmd is allowed.');
                }
            }
        }
    }

    /**
     * Fix the auto increment for the content_page table
     * Ticket #1070 in bug tracker
     *
     * The last content page have been deleted and the website was moved to another server, in this case
     * the auto increment does not match the log's last object_id. This will cause a duplicate primary key.
     */
    private function fixAutoIncrement() {
        $database = \Env::get('db');
        $result = $database->Execute("SELECT MAX(CONVERT(`object_id`, UNSIGNED)) AS `oldAutoIncrement`
                                        FROM `" . DBPREFIX . "log_entry`
                                        WHERE `object_class` = 'Cx\\\\Core\\\\ContentManager\\\\Model\\\\Entity\\\\Page'");
        if ($result === false) return;
        $oldAutoIncrement = $result->fields['oldAutoIncrement'] + 1;
        $result = $database->Execute("SHOW TABLE STATUS LIKE '" . DBPREFIX . "content_page'");
        if ($result !== false && $result->fields['Auto_increment'] < $oldAutoIncrement) {
            $result = $database->Execute("ALTER TABLE `" . DBPREFIX . "content_page` AUTO_INCREMENT = " . contrexx_raw2db($oldAutoIncrement));
        }
    }

    public function onEvent($eventName, array $eventArgs) {
        $this->$eventName(current($eventArgs));
    }

     public static function SearchFindContent($search) {
        $pageRepo = \Env::get('em')->getRepository('Cx\Core\ContentManager\Model\Entity\Page');
        $result = new \Cx\Core_Modules\Listing\Model\Entity\DataSet(
            $pageRepo->searchResultsForSearchModule(
                $search->getTerm(),
                \Env::get('cx')->getLicense(),
                $search->getRootPage()
            )
        );
        $search->appendResult($result);
    }
}
