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
 * Class MediaDirEventListener
 *
 * @copyright   Cloudrexx AG
 * @author      Robin Glauser <robin.glauser@comvation.com>
 * @package     cloudrexx
 */

namespace Cx\Modules\MediaDir\Model\Event;


use Cx\Core\Core\Controller\Cx;
use Cx\Core\MediaSource\Model\Entity\MediaSourceManager;
use Cx\Core\MediaSource\Model\Entity\MediaSource;
use Cx\Core\Event\Model\Entity\DefaultEventListener;

/**
 * Class MediaDirEventListener
 *
 * @copyright   Cloudrexx AG
 * @author      Robin Glauser <robin.glauser@comvation.com>
 * @package     cloudrexx
 */
class MediaDirEventListener extends DefaultEventListener
{

    /**
     * Global search event listener
     * Appends the MediaDir search results to the search object
     *
     * @param array $search
     */
    public function SearchFindContent($search) {
        if (!$search->getAccessablePage('MediaDir')) {
            return;
        }
        $objEntry = new \Cx\Modules\MediaDir\Controller\MediaDirectoryEntry('MediaDir');
        $result   = new \Cx\Core_Modules\Listing\Model\Entity\DataSet($objEntry->searchResultsForSearchModule($search->getTerm()));
        $search->appendResult($result);
    }

    public function mediasourceLoad(
        MediaSourceManager $mediaBrowserConfiguration
    ) {
        global $_ARRAYLANG;
        \Env::get('init')->loadLanguageData('MediaDir');
        $mediaType = new MediaSource('mediadir',$_ARRAYLANG['TXT_FILEBROWSER_MEDIADIR'], array(
            $this->cx->getWebsiteImagesMediaDirPath(),
            $this->cx->getWebsiteImagesMediaDirWebPath(),
        ),array(153));
        $mediaBrowserConfiguration->addMediaType($mediaType);
    }

    /**
     * Clear all ESI cache
     *
     * @param string $eventArg event argument
     *
     * @return null
     */
    public function clearEsiCache($eventArg)
    {
        if (empty($eventArg) || $eventArg != 'MediaDir') {
            return;
        }

        $cache = $this->cx->getComponent('Cache');
        $forms =
            new \Cx\Modules\MediaDir\Controller\MediaDirectoryForm(
                null,
                'MediaDir'
            );
        $blockParams = array();
        $formParams  = array();
        $themeRepo   = new \Cx\Core\View\Model\Repository\ThemeRepository();
        foreach ($themeRepo->findAll() as $theme) {
            //Fetch possible cache clearing params for
            //showing latest entries by form block
            foreach ($forms->getForms() as $formId => $formCmd) {
                $block        = 'mediadirLatest_form_' . $formCmd;
                $formParams[] = $this->getCacheParamsByBlock(
                    $theme,
                    $block,
                    array('formId' => $formId, 'block' => 'mediadirLatest')
                );
            }
            //Fetch possible cache clearing params for
            //showing latest entries by block 'mediadirLatest'
            $formParams[] = $this->getCacheParamsByBlock(
                $theme,
                'mediadirLatest',
                array('block' => 'mediadirLatest')
            );
            //Fetch possible cache clearing params for
            //showing latest entries by block
            $combinations = $this->getPossibleOccuranceOfBlocks($theme);
            if (empty($combinations)) {
                continue;
            }
            foreach ($combinations as $combination) {
                foreach ($combination as $position => $block) {
                    $blockParams[] = array(
                        'template'    => $theme->getId(),
                        'file'        => $block['file'],
                        'blockId'     => $block['blockId'],
                        'position'    => $position + 1,
                        'totalBlocks' => count($combination)
                    );
                }
            }
        }

        //Fetch possible cache clearing params for
        //showing level/category navbar
        $mediaDirLvl = new \Cx\Modules\MediaDir\Controller\MediaDirectoryLevel(
            null,
            null,
            1,
            'MediaDir'
        );
        $levelIds    = $this->getLevelOrCategoryEntryIds($mediaDirLvl->arrLevels);
        $mediaDirCat = new \Cx\Modules\MediaDir\Controller\MediaDirectoryCategory(
            null,
            null,
            1,
            'MediaDir'
        );
        $catIds    = $this->getLevelOrCategoryEntryIds(
            $mediaDirCat->arrCategories,
            'category'
        );
        $navParams = array();
        foreach ($catIds as $catId) {
            $navParams[] = array('cid' => $catId);
            if (empty($mediaDirCat->arrSettings['settingsShowLevels'])) {
                continue;
            }
            foreach ($levelIds as $levelId) {
                $navParams[] = array('lid' => $levelId);
                $navParams[] = array('lid' => $levelId, 'cid' => $catId);
            }
        }
        //Clearing cache for mediadir level/category navigation bar,
        //Latest entries by placeholder and by block
        foreach (\FWLanguage::getActiveFrontendLanguages() as $lang) {
            //Clear level/category navbar cache
            foreach ($navParams as $navParam) {
                $navParam['lang'] = $lang['id'];
                $cache->clearSsiCachePage(
                    'MediaDir',
                    'getNavigationPlacholder',
                    $navParam
                );
            }
            //clear cache for latest entries by placeholder
            $cache->clearSsiCachePage(
                'MediaDir',
                'getLatestPlacholder',
                array('lang' => $lang['id'])
            );
            //clear cache for latest entries by block
            foreach ($blockParams as $blockParam) {
                $blockParam['lang'] = $lang['id'];
                $cache->clearSsiCachePage(
                    'MediaDir',
                    'getHeadlines',
                    $blockParam
                );
            }
            //clear cache for latest entries by form
            foreach ($formParams as $formParam) {
                if (empty($formParam)) {
                    continue;
                }
                $formParam['lang'] = $lang['id'];
                $cache->clearSsiCachePage(
                    'MediaDir',
                    'getLatestEntries',
                    $formParam
                );
            }
        }
    }

    /**
     * Get level/category entryids
     *
     * @param array  $arrEntries array of entries
     * @param string $type       if the type is level then
     *                           getting level entry ids
     *                           otherwise category entry ids
     * @param array  $entryIds   entry ids
     *
     * @return array array of entry id
     */
    protected function getLevelOrCategoryEntryIds(
        $arrEntries,
        $type = 'level',
        &$entryIds = array()
    ) {
        if (empty($arrEntries)) {
            return;
        }

        $idKey    = ($type == 'level') ? 'levelId' : 'catId';
        $childKey = ($type == 'level') ? 'levelChildren' : 'catChildren';
        foreach ($arrEntries as $arrEntry) {
            $entryIds[] = $arrEntry[$idKey];
            if (empty($arrEntry[$childKey])) {
                continue;
            }
            $this->getLevelOrCategoryEntryIds(
                $arrEntry[$childKey],
                $type,
                $entryIds
            );
        }

        return $entryIds;
    }

    /**
     * Get the cache params by checking the block exist in the theme template
     *
     * @param \Cx\Core\View\Model\Entity\Theme $theme       theme object
     * @param string                           $block       block name
     * @param array                            $extraParams extra params
     *
     * @return array list of params
     */
    protected function getCacheParamsByBlock(
        \Cx\Core\View\Model\Entity\Theme $theme,
        $block,
        $extraParams = array()
    ) {
        if (empty($block)) {
            return;
        }

        $templates = array_merge(
            array('index.html', 'home.html', 'content.html'),
            \Env::get('init')->getCustomContentTemplatesForTheme($theme)
        );
        foreach ($templates as $template) {
            if (!$theme->isBlockExistsInfile($template, $block)) {
                continue;
            }
            if (!is_array($extraParams) || empty($extraParams)) {
                $params[] = array(
                    'template' => $theme->getId(),
                    'file'     => $template
                );
                continue;
            }
            $params[] = array_merge(
                array(
                    'template' => $theme->getId(),
                    'file'     => $template
                ),
                $extraParams
            );
        }

        return $params;
    }

    /**
     * Get the possible occurance of latest blocks
     *
     * @param \Cx\Core\View\Model\Entity\Theme $theme theme object
     *
     * @return array list of possible occurance
     */
    protected function getPossibleOccuranceOfBlocks(
        \Cx\Core\View\Model\Entity\Theme $theme
    ) {
        $templateFiles    = \Env::get('init')
            ->getCustomContentTemplatesForTheme($theme);
        $homeContentFiles = array('home.html');
        $contentFiles     = array('content.html');
        if (!empty($templateFiles)) {
            foreach ($templateFiles as $templateFile) {
                if (preg_match('/^(content)_(.+).html$/', $templateFile)) {
                    $contentFiles[] = $templateFile;
                }
                if (preg_match('/^(home)_(.+).html$/', $templateFile)) {
                    $homeContentFiles[] = $templateFile;
                }
            }
        }

        $indexBlocks = array();
        for ($i = 1; $i <= 10; ++$i) {
            if (
                $theme->isBlockExistsInfile(
                    'index.html',
                    'mediadirLatest_row_' . $i
                )
            ) {
                $indexBlocks[] = $i;
            }
        }

        $combinationsFromContent = $this->checkAndGetExistsBlockList(
            $theme,
            $contentFiles,
            $indexBlocks
        );
        $combinationsFromHome    = $this->checkAndGetExistsBlockList(
            $theme,
            $homeContentFiles,
            $indexBlocks
        );

        return array_merge($combinationsFromContent, $combinationsFromHome);
    }

    /**
     * Check and get the existing blocks from the array of file
     *
     * @param \Cx\Core\View\Model\Entity\Theme $theme       theme object
     * @param array                            $files       list of home/content files
     * @param array                            $indexBlocks list of blocks available in index file
     *
     * @return array possible occurance of block list
     */
    protected function checkAndGetExistsBlockList(
        \Cx\Core\View\Model\Entity\Theme $theme,
        $files = array(),
        $indexBlocks = array()
    ) {
        if (empty($files)) {
            return;
        }

        $list = array();
        foreach ($files as $file) {
            $latestBlocks = array();
            for ($i = 1; $i <= 10; ++$i) {
                if (in_array($i, $indexBlocks)) {
                    $latestBlocks[] = array(
                        'blockId' => $i,
                        'file'    => 'index.html'
                    );
                    continue;
                }
                if (
                    !$theme->isBlockExistsInfile(
                        $file,
                        'mediadirLatest_row_' . $i
                    )
                ) {
                    continue;
                }
                $latestBlocks[] = array('blockId' => $i, 'file' => $file);
            }
            $list[] = $latestBlocks;
        }

        return $list;
    }
}
