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
 * EventListener for News
 *
 * @copyright   Cloudrexx AG
 * @author      Project Team SS4U <info@cloudrexx.com>
 * @package     cloudrexx
 * @subpackage  coremodule_news
 */

namespace Cx\Core_Modules\News\Model\Event;

/**
 * NewsInternalException
 *
 * @copyright   Cloudrexx AG
 * @author      Project Team SS4U <info@cloudrexx.com>
 * @package     cloudrexx
 * @subpackage  coremodule_news
 */
class NewsInternalException extends \Exception {}

/**
 * EventListener for News
 *
 * @copyright   Cloudrexx AG
 * @author      Project Team SS4U <info@cloudrexx.com>
 * @package     cloudrexx
 * @subpackage  coremodule_news
 */
class NewsEventListener extends \Cx\Core\Event\Model\Entity\DefaultEventListener {
    /**
     * Global search event listener
     * Appends the News search results to the search object
     *
     * @param object $search \Cx\Core_Modules\Search\Controller\Search
     */
    public function SearchFindContent($search)
    {
        $result = new \Cx\Core_Modules\Listing\Model\Entity\DataSet(
            $this->getNewsForSearchComponent(contrexx_raw2db($search->getTerm()))
        );
        $search->appendResult($result);
    }

    /**
     * Find News by keyword $searchTerm and return them in a
     * two-dimensional array compatible to be used by Search component.
     *
     * @param string $searchTerm The keyword to search by
     * @return array Two-dimensional array of News found by keyword $searchTerm.
     *               If integration into search component is disabled or
     *               no News matched the giving keyword, then an
     *               empty array is returned.
     */
    protected function getNewsForSearchComponent($searchTerm)
    {
        try {
            $arrCategoryIds = $this->getCategoryFilterForSearchComponent();
        } catch (NewsInternalException $e) {
            return array();
        }

        $newsLib   = new \Cx\Core_Modules\News\Controller\NewsLibrary();
        $objResult = $newsLib->getNewsForSearchTerm($arrCategoryIds, $searchTerm);
        if (!$objResult) {
            return array();
        }

        \Cx\Core\Setting\Controller\Setting::init('Config', 'site','Yaml');
        $maxLength = \Cx\Core\Setting\Controller\Setting::getValue(
            'searchDescriptionLength',
            'Config'
        );
        $arrayOfSearchResult = array();
        while (!$objResult->EOF) {
            $score        = $objResult->fields['score'];
            $scorePercent = ($score >= 1 ? 100 : intval($score * 100));
            $date         = !empty($objResult->fields['date'])
                ? $objResult->fields['date'] : null;
            $content = !empty($objResult->fields['content'])
                ? \Cx\Core_Modules\Search\Controller\Search::shortenSearchContent(
                    $objResult->fields['content'],
                    $maxLength
                )
                : '';

            $arrayOfSearchResult[] = array(
                'Score'     => ($score == 0) ? 25 : $scorePercent,
                'Title'     => $objResult->fields['title'],
                'Content'   => $content,
                'Link'      => $newsLib->getApplicationUrl($objResult->fields),
                'Date'      => $date,
                'Component' => 'News',
            );
            $objResult->MoveNext();
        }

        return $arrayOfSearchResult;
    }

    /**
     * Get category Ids from the published application pages of this component
     *
     * @return array List of published category IDs.
     *               An empty array is returned, in case an application
     *               page is published that has no category restriction set
     *               through its CMD.
     * @throws NewsInternalException In case no application page of this
     *                               component is published
     */
    protected function getCategoryFilterForSearchComponent()
    {
        \Cx\Core\Setting\Controller\Setting::init('Config', 'site','Yaml');
        $coreListProtectedPages   = \Cx\Core\Setting\Controller\Setting::getValue(
            'coreListProtectedPages',
            'Config'
        );
        $searchVisibleContentOnly = \Cx\Core\Setting\Controller\Setting::getValue(
            'searchVisibleContentOnly',
            'Config'
        );

        // fetch data about existing application pages of this component
        $cmds     = array();
        $em       = $this->cx->getDb()->getEntityManager();
        $pageRepo = $em->getRepository('Cx\Core\ContentManager\Model\Entity\Page');
        $pages    = $pageRepo->getAllFromModuleCmdByLang('News');
        foreach ($pages as $pagesOfLang) {
            foreach ($pagesOfLang as $page) {
                $cmds[] = $page->getCmd();
            }
        }

        // check if an application page is published
        $arrCategoryIds = array();
        foreach (array_unique($cmds) as $cmd) {
            // fetch application page with specific CMD from current locale
            $page = $pageRepo->findOneByModuleCmdLang('News', $cmd, FRONTEND_LANG_ID);

            // skip if the page does not exist in current locale or
            // has not been published
            if (
                !$page ||
                !$page->isActive()
            ) {
                continue;
            }

            // skip invisible page (if excluded from search)
            if (
                $searchVisibleContentOnly == 'on' &&
                !$page->isVisible()
            ) {
                continue;
            }

            // skip protected page (if excluded from search)
            if (
                $coreListProtectedPages == 'off' &&
                $page->isFrontendProtected() &&
                $this->getComponent('Session')->getSession() &&
                !\Permission::checkAccess($page->getFrontendAccessId(), 'dynamic', true)
            ) {
                continue;
            }

            // in case the CMD is an integer, then
            // the integer does represent an ID of category which has to be
            // applied to the search filter
            if (preg_match('/^\d+$/', $cmd)) {
                $arrCategoryIds[] = $cmd;
                continue;
            }

            // in case the CMD is hypen '-' seperated integer(eg: '2-3'),
            // then the integer values does represent an ID of category
            // which has to be applied to the search filter
            if (strpos($cmd, '-') !== false) {
                $arrCategoryIds = array_merge($arrCategoryIds, array_filter(explode('-', $cmd)));
                continue;
            }

            // in case an application exists that has not set a category-ID as
            // its CMD, then we do not have to restrict the search by one or
            // more specific categories
            return array();
        }

        // if we reached this point and no category-IDs have been fetched
        // then this means that no application is published
        if (empty($arrCategoryIds)) {
            throw new NewsInternalException('Application is not published');
        }

        return $arrCategoryIds;
    }
}
