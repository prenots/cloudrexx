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
 * EventListener for Directory
 *
 * @copyright   Cloudrexx AG
 * @author      Project Team SS4U <info@cloudrexx.com>
 * @package     cloudrexx
 * @subpackage  module_data
 */

namespace Cx\Modules\Data\Model\Event;

use Cx\Core\Event\Model\Entity\DefaultEventListener;

/**
 * EventListener for Directory
 *
 * @copyright   Cloudrexx AG
 * @author      Project Team SS4U <info@cloudrexx.com>
 * @package     cloudrexx
 * @subpackage  module_data
 */
class DataEventListener extends DefaultEventListener {
    /**
      * Clear all Ssi cache
      *
      * @param array $eventArgs Event args
      */
     public function clearEsiCache(array $eventArgs)
     {
        if (empty($eventArgs) || $eventArgs != 'Data') {
            return;
        }

        $cache   = $this->cx->getComponent('Cache');
        $em      = $this->cx->getDb()->getEntityManager();
        $typeApp = \Cx\Core\ContentManager\Model\Entity\Page::TYPE_APPLICATION;
        //clear ssi cache for data content present in content page
        foreach (\FWLanguage::getActiveFrontendLanguages() as $lang) {
            $qb = $em->createQueryBuilder();
            $qb->select('p')
               ->from('Cx\Core\ContentManager\Model\Entity\Page', 'p')
               ->where('p.lang = ' . $lang['id'])
               ->andWhere($qb->expr()->in(
                    'p.type',
                    array(
                        \Cx\Core\ContentManager\Model\Entity\Page::TYPE_CONTENT,
                        $typeApp
                    )
                ));
            $contentPages = $qb->getQuery()->getResult();
            if (!$contentPages) {
                continue;
            }
            foreach($contentPages as $page) {
                if ($page->getType() == $typeApp) {
                    $content = $this->cx->getContentTemplateOfPage($page);
                } else {
                    $content = $page->getContent();
                }
                $matches = null;
                if (
                    preg_match_all(
                        '/\{DATA_[A-Z_0-9]+\}/',
                        $content,
                        $matches
                    ) == 0
                ) {
                    continue;
                }
                foreach ($matches[0] as $match) {
                    $cache->clearSsiCachePage(
                        'Data',
                        'getDataContent',
                        array(
                            'page'        => $page->getContent(),
                            'lang'        => $lang['id'],
                            'placeholder' => $match
                        )
                    );
                }
            }
        }

        //clear ssi cache for data content present in themes files
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
                        '/\{DATA_[A-Z_0-9]+\}/',
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
                foreach ($arrDetails as $file => $placeholders) {
                    foreach ($placeholders as $placeholder) {
                        $cache->clearSsiCachePage(
                            'Data',
                            'getDataContent',
                            array(
                                'template'    => $theme->getId(),
                                'file'        => $file,
                                'lang'        => $lang['id'],
                                'placeholder' => $placeholder
                            )
                        );
                    }
                }
            }
                
        }
    }
}