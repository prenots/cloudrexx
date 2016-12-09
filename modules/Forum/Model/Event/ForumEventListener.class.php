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
 * EventListener for Forum
 *
 * @copyright   Cloudrexx AG
 * @author      Project Team SS4U <info@cloudrexx.com>
 * @package     cloudrexx
 * @subpackage  module_forum
 */

namespace Cx\Modules\Forum\Model\Event;

/**
 * EventListener for Forum
 *
 * @copyright   Cloudrexx AG
 * @author      Project Team SS4U <info@cloudrexx.com>
 * @package     cloudrexx
 * @subpackage  module_forum
 */
class ForumEventListener implements \Cx\Core\Event\Model\Entity\EventListener {

    public function onEvent($eventName, array $eventArgs) {
        $this->$eventName(current($eventArgs));
    }

    public static function SearchFindContent($search) {
        $term_db = $search->getTerm();

        $query = "SELECT `thread_id` AS `id`, `subject` AS `title`, `content`,
                           MATCH (`subject`, `content`, `keywords`) AGAINST ('%$term_db%') AS score
                      FROM " . DBPREFIX . "module_forum_postings
                     WHERE (   subject LIKE ('%$term_db%')
                            OR content LIKE ('%$term_db%')
                            OR keywords LIKE ('%$term_db%'))";
        $result = new \Cx\Core_Modules\Listing\Model\Entity\DataSet($search->getResultArray($query, 'Forum', 'thread', 'id=', $search->getTerm()));
        $search->appendResult($result);
    }

    /**
     * Clear all Ssi cache
     */
    public function clearEsiCache(array $eventArgs)
    {
        global $objCache;
        if (empty($eventArgs) || $eventArgs != 'Forum') {
            return;
        }

        $cache   = $this->cx->getComponent('Cache');
        // clear ssi cache
        foreach ($themeRepo->findAll() as $theme) {
            foreach (\FWLanguage::getActiveFrontendLanguages() as $lang) {
                $objCache->clearSsiCachePage(
                    'Forum',
                    'getForumContent',
                    array(
                        'theme' => $theme->getId(),
                        'lang'  => $lang
                    )
                );
            }
        }
        //clear Forum-TagCloud
        $themeRepo = new \Cx\Core\View\Model\Repository\ThemeRepository();
        foreach ($themeRepo->findAll() as $theme) {
            $searchTemplateFiles = $theme->getTemplateFileNames();
            if (!$searchTemplateFiles) {
                continue;
            }
            $arrDetails = array();
            foreach ($searchTemplateFiles as $file) {
                $content = $theme->getContentFromFile($file);
                if (!$content) {
                    continue;
                }
                $matches = null;
                if (
                    preg_match_all(
                        '/\{FORUM_TAG_CLOUD\}/mi',
                        $content,
                        $matches
                    ) == 0
                ) {
                    continue;
                }
                $arrDetails[$file] = $matches[0];
            }

            if (!$arrDetails) {
                continue;
            }

            foreach (\FWLanguage::getActiveFrontendLanguages() as $lang) {
                $cache->clearSsiCachePage(
                    'Forum',
                    'getForumContent',
                    array(
                        'theme' => $theme->getId(),
                        'file'  => $file,
                        'lang'  => $lang
                    )
                );
            }
        }
    }
}
