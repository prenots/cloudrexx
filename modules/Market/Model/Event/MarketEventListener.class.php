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
 * EventListener for Market
 *
 * @copyright   Cloudrexx AG
 * @author      Project Team SS4U <info@cloudrexx.com>
 * @package     cloudrexx
 * @subpackage  module_market
 */

namespace Cx\Modules\Market\Model\Event;

use Cx\Core\Event\Model\Entity\DefaultEventListener;

/**
 * EventListener for Market
 *
 * @copyright   Cloudrexx AG
 * @author      Project Team SS4U <info@cloudrexx.com>
 * @package     cloudrexx
 * @subpackage  module_market
 */
class MarketEventListener extends DefaultEventListener {

    /**
     * Clear all Ssi cache
     */
    public function clearEsiCache(array $eventArgs) {

        if (empty($eventArgs) || $eventArgs != 'Market') {
            return;
        }

        // clear ssi cache
        $cache = $this->cx->getComponent('Cache');
        $em = $this->cx->getDb()->getEntityManager();
        $typeApp = \Cx\Core\ContentManager\Model\Entity\Page::TYPE_APPLICATION;
        $pattern = '/<!--\s+BEGIN\s+(marketLatest)\s+-->(.*)<!--\s+END\s+\1\s+-->/s';
        
        //clear ssi cache for market content present in content page
        $qb = $em->createQueryBuilder();
        $qb->select('p')
            ->from('Cx\Core\ContentManager\Model\Entity\Page', 'p')
            ->where($qb->expr()->in(
                    'p.type',
                    array(
                        \Cx\Core\ContentManager\Model\Entity\Page::TYPE_CONTENT,
                        $typeApp
                    )
            ));
        $contentPages = $qb->getQuery()->getResult();
        if (!$contentPages) {
            goto clearTemplateCache;
        }
        foreach ($contentPages as $page) {
            if (!preg_match($pattern, $page->getContent())) {
                continue;
            }
            $cache->clearSsiCachePage(
                'Market',
                'getMarketLatest',
                array(
                    'page' => $page->getId()
                )
            );
        }

        clearTemplateCache:
        //clear ssi cache for Market content present in themes files
        $themeRepo = new \Cx\Core\View\Model\Repository\ThemeRepository();
        foreach ($themeRepo->findAll() as $theme) {
            $searchTemplateFiles = $theme->getTemplateFileNames();
            if (!$searchTemplateFiles) {
                continue;
            }
            foreach ($searchTemplateFiles as $file) {
                if (!$theme->isBlockExistsInfile($file, 'marketLatest')) {
                    continue;
                }
                $cache->clearSsiCachePage(
                    'Market',
                    'getMarketLatest',
                    array(
                        'theme' => $theme->getId(),
                        'file' => $file
                    )
                );
            }
        }
    }
}
