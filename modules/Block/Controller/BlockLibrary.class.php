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

namespace Cx\Modules\Block\Controller;

/**
 * Cx\Modules\Block\Controller\BlockLibraryException
 *
 * @copyright   CLOUDREXX CMS - CLOUDREXX AG
 * @author      Manuel Schenk <manuel.schenk@comvation.com>
 * @version     1.0.0
 * @package     cloudrexx
 * @subpackage  module_block
 */
class BlockLibraryException extends \Exception
{
}

/**
 * Cx\Modules\Block\Controller\NoCategoryFoundException
 *
 * @copyright   CLOUDREXX CMS - CLOUDREXX AG
 * @author      Manuel Schenk <manuel.schenk@comvation.com>
 * @version     1.0.0
 * @package     cloudrexx
 * @subpackage  module_block
 */
class NoCategoryFoundException extends BlockLibraryException
{
}

/**
 * Cx\Modules\Block\Controller\BlockLibrary
 *
 * Block library class
 *
 * @copyright   CLOUDREXX CMS - CLOUDREXX AG
 * @author      Cloudrexx Development Team <info@cloudrexx.com>
 * @author      Manuel Schenk <manuel.schenk@comvation.com>
 * @access      private
 * @version     1.0.0
 * @package     cloudrexx
 * @subpackage  module_block
 * @todo        Edit PHP DocBlocks!
 */
class BlockLibrary
{
    /**
     * Block name prefix
     *
     * @access public
     * @var string
     */
    var $blockNamePrefix = 'BLOCK_';

    /**
     * Block ids
     *
     * @access private
     * @var array
     */
    var $_arrBlocks;

    /**
     * Array of categories
     *
     * @var array
     */
    var $_categories = array();

    /**
     * holds the category dropdown select options
     *
     * @var array of strings: HTML <options>
     */
    var $_categoryOptions = array();

    /**
     * array containing the category names
     *
     * @var array catId => name
     */
    var $_categoryNames = array();

    protected $availableTargeting = array(
        'country',
    );

    /**
     * Constructor
     */
    function __construct()
    {
        if (\Cx\Core\Core\Controller\Cx::instanciate()->getMode() != \Cx\Core\Core\Controller\Cx::MODE_COMMAND) {
            return;
        }
    }


    /**
     * Get blocks
     *
     * Get all blocks
     *
     * @access private
     * @global ADONewConnection
     * @see array blockLibrary::_arrBlocks
     * @return array Array with block ids
     */
    public function getBlocks($catId = 0)
    {
        if (!is_array($this->_arrBlocks)) {
            $em = \Cx\Core\Core\Controller\Cx::instanciate()->getDb()->getEntityManager();
            $blockRepo = $em->getRepository('\Cx\Modules\Block\Model\Entity\Block');

            if ($catId != 0) {
                $blocks = $blockRepo->findBy(array('category' => $catId));
            } else {
                $blocks = $blockRepo->findAll();
            }

            $this->_arrBlocks = array();

            foreach ($blocks as $block) {
                $langArr = array();
                $contents = $block->getRelLangContents();
                foreach ($contents as $content) {
                    if ($content->getActive() == 1) {
                        array_push($langArr, $content->getLocale()->getId());
                    }
                }

                $catId = 0;
                $cat = $block->getCategory();
                if ($cat) {
                    $catId = $block->getCategory()->getId();
                }

                $this->_arrBlocks[$block->getId()] = array(
                    'cat' => $catId,
                    'start' => $block->getStart(),
                    'end' => $block->getEnd(),
                    'order' => $block->getOrder(),
                    'random' => $block->getRandom(),
                    'random2' => $block->getRandom2(),
                    'random3' => $block->getRandom3(),
                    'random4' => $block->getRandom4(),
                    'global' => $block->getShowInGlobal(),
                    'active' => $block->getActive(),
                    'direct' => $block->getShowInDirect(),
                    'name' => $block->getName(),
                    'lang' => array_unique($langArr),
                );
            }
        }

        return $this->_arrBlocks;
    }

    /**
     * Add a new block to database
     *
     * @param int $cat
     * @param array $arrContent
     * @param string $name
     * @param int $start
     * @param int $end
     * @param int $blockRandom
     * @param int $blockRandom2
     * @param int $blockRandom3
     * @param int $blockRandom4
     * @param int $blockWysiwygEditor
     * @param array $arrLangActive
     * @return bool|int the block's id
     */
    public function _addBlock($cat, $arrContent, $name, $start, $end, $blockRandom, $blockRandom2, $blockRandom3, $blockRandom4, $blockWysiwygEditor, $arrLangActive)
    {
        $em = \Cx\Core\Core\Controller\Cx::instanciate()->getDb()->getEntityManager();
        $categoryRepo = $em->getRepository('\Cx\Modules\Block\Model\Entity\Category');
        $category = $categoryRepo->findOneBy(array('id' => $cat));

        $qb = $em->createQueryBuilder();
        $order = $qb->select('MAX(b.order)')
            ->from('\Cx\Modules\Block\Model\Entity\Block', 'b')
            ->getQuery()
            ->getSingleResult();

        $block = new \Cx\Modules\Block\Model\Entity\Block();
        $block->setCategory($category);
        $block->setStart($start);
        $block->setEnd($end);
        $block->setName($name);
        $block->setRandom($blockRandom);
        $block->setRandom2($blockRandom2);
        $block->setRandom3($blockRandom3);
        $block->setRandom4($blockRandom4);
        $block->setShowInGlobal(0);
        $block->setShowInCategory(0);
        $block->setShowInDirect(0);
        $block->setActive(1);
        $block->setOrder($order[1] + 1);
        $block->setWysiwygEditor($blockWysiwygEditor);

        $em->persist($block);
        $em->flush();
        $em->refresh($block);

        $id = $block->getId();

        $this->storeBlockContent($id, $arrContent, $arrLangActive);

        return $id;
    }


    /**
     * Update an existing block
     *
     * @param int $id
     * @param int $cat
     * @param array $arrContent
     * @param string $name
     * @param int $start
     * @param int $end
     * @param int $blockRandom
     * @param int $blockRandom2
     * @param int $blockRandom3
     * @param int $blockRandom4
     * @param int $blockWysiwygEditor
     * @param array $arrLangActive
     * @return bool|int the id of the block
     */
    public function _updateBlock($id, $cat, $arrContent, $name, $start, $end, $blockRandom, $blockRandom2, $blockRandom3, $blockRandom4, $blockWysiwygEditor, $arrLangActive)
    {
        $em = \Cx\Core\Core\Controller\Cx::instanciate()->getDb()->getEntityManager();
        $blockRepo = $em->getRepository('\Cx\Modules\Block\Model\Entity\Block');
        $categoryRepo = $em->getRepository('\Cx\Modules\Block\Model\Entity\Category');
        $block = $blockRepo->findOneBy(array('id' => $id));
        $category = $categoryRepo->findOneBy(array('id' => $cat));

        $block->setName($name);
        $block->setCategory($category);
        $block->setStart($start);
        $block->setEnd($end);
        $block->setRandom($blockRandom);
        $block->setRandom2($blockRandom2);
        $block->setRandom3($blockRandom3);
        $block->setRandom4($blockRandom4);
        $block->setWysiwygEditor($blockWysiwygEditor);

        $em->flush();

        $this->storeBlockContent($id, $arrContent, $arrLangActive);

        return $id;
    }

    /**
     * Store the placeholder settings for a block
     *
     * @param int $blockId
     * @param int $global
     * @param int $direct
     * @param int $category
     * @param array $globalAssociatedPages
     * @param array $directAssociatedPages
     * @param array $categoryAssociatedPages
     * @return bool it was successfully saved
     */
    protected function storePlaceholderSettings($blockId, $global, $direct, $category, $globalAssociatedPages, $directAssociatedPages, $categoryAssociatedPages)
    {
        $em = \Cx\Core\Core\Controller\Cx::instanciate()->getDb()->getEntityManager();
        $blockRepo = $em->getRepository('\Cx\Modules\Block\Model\Entity\Block');
        $block = $blockRepo->findOneBy(array('id' => $blockId));

        $block->setShowInGlobal($global);
        $block->setShowInDirect($direct);
        $block->setShowInCategory($category);

        $relPageRepo = $em->getRepository('\Cx\Modules\Block\Model\Entity\RelPage');
        $relPages = $relPageRepo->findBy(array('block' => $block));

        foreach ($relPages as $relPage) {
            $em->remove($relPage);
        }
        $em->flush();

        if ($global == 2) {
            $this->storePageAssociations($blockId, $globalAssociatedPages, 'global');
        }
        if ($direct == 1) {
            $this->storePageAssociations($blockId, $directAssociatedPages, 'direct');
        }
        if ($category == 1) {
            $this->storePageAssociations($blockId, $categoryAssociatedPages, 'category');
        }
        return true;
    }

    /**
     * Store the page associations
     *
     * @param int $blockId the block id
     * @param array $blockAssociatedPageIds the page ids
     * @param string $placeholder the placeholder
     */
    private function storePageAssociations($blockId, $blockAssociatedPageIds, $placeholder)
    {
        $em = \Cx\Core\Core\Controller\Cx::instanciate()->getDb()->getEntityManager();
        $blockRepo = $em->getRepository('\Cx\Modules\Block\Model\Entity\Block');
        $block = $blockRepo->findOneBy(array('id' => $blockId));
        $pageRepo = $em->getRepository('\Cx\Core\ContentManager\Model\Entity\Page');

        foreach ($blockAssociatedPageIds as $pageId) {
            if ($pageId > 0) {
                $page = $pageRepo->findOneBy(array('id' => $pageId));
                $relPage = new \Cx\Modules\Block\Model\Entity\RelPage();
                $relPage->setBlock($block);
                $relPage->setPage($page);
                $relPage->setPlaceholder($placeholder);

                $em->persist($relPage);
            }
        }

        $em->flush();
    }

    private function storeBlockContent($blockId, $arrContent, $arrLangActive)
    {
        $em = \Cx\Core\Core\Controller\Cx::instanciate()->getDb()->getEntityManager();
        $blockRepo = $em->getRepository('\Cx\Modules\Block\Model\Entity\Block');
        $relLangContentRepo = $em->getRepository('\Cx\Modules\Block\Model\Entity\RelLangContent');
        $localeRepo = $em->getRepository('\Cx\Core\Locale\Model\Entity\Locale');
        $block = $blockRepo->findOneBy(array('id' => $blockId));
        $relLangContents = $block->getRelLangContents();

        $arrPresentLang = array();
        foreach ($relLangContents as $relLangContent) {
            $arrPresentLang[] = $relLangContent->getLocale()->getId();
        }

        foreach ($arrContent as $langId => $content) {
            $content = preg_replace('/\[\[([A-Z0-9_-]+)\]\]/', '{\\1}', $content);
            $locale = $localeRepo->findOneBy(array('id' => $langId));
            if (in_array($langId, $arrPresentLang)) {
                $relLangContent = $relLangContentRepo->findOneBy(array('block' => $block, 'locale' => $locale));
                $relLangContent->setContent($content);
                $relLangContent->setActive(isset($arrLangActive[$langId]) ? $arrLangActive[$langId] : 0);
            } else {
                $relLangContent = new \Cx\Modules\Block\Model\Entity\RelLangContent();
                $relLangContent->setContent($content);
                $relLangContent->setActive(isset($arrLangActive[$langId]) ? $arrLangActive[$langId] : 0);
                $relLangContent->setBlock($block);
                $relLangContent->setLocale($locale);
                $em->persist($relLangContent);
            }
        }

        \Cx\Core\Core\Controller\Cx::instanciate()->getComponent('Cache')->clearSsiCachePage(
            'Block',
            'getBlockContent',
            array(
                'block' => $blockId,
            )
        );

        $qb = $em->createQueryBuilder();
        $qb->delete('\Cx\Modules\Block\Model\Entity\RelLangContent', 'rlc')
            ->where('rlc.block = :block')
            ->andWhere(
                $qb->expr()->notIn(
                    'rlc.locale', array_keys($arrLangActive)
                )
            )
            ->setParameter('block', $block)
            ->getQuery()
            ->getResult();

        $em->flush();
    }

    /**
     * Get GeoIp component controller
     *
     * @return \Cx\Core_Modules\GeoIp\Controller\ComponentController
     */
    public function getGeoIpComponent()
    {
        $componentRepo = \Cx\Core\Core\Controller\Cx::instanciate()
            ->getDb()
            ->getEntityManager()
            ->getRepository('Cx\Core\Core\Model\Entity\SystemComponent');
        $geoIpComponent = $componentRepo->findOneBy(array('name' => 'GeoIp'));
        if (!$geoIpComponent) {
            return null;
        }
        $geoIpComponentController = $geoIpComponent->getSystemComponentController();
        if (!$geoIpComponentController) {
            return null;
        }

        return $geoIpComponentController;
    }

    /**
     * Verify targeting options for the given block Id
     *
     * @param integer $blockId Block id
     *
     * @return boolean True when all targeting options vaild, false otherwise
     */
    public function checkTargetingOptions($blockId)
    {
        $targeting = $this->loadTargetingSettings($blockId);

        if (empty($targeting)) {
            return true;
        }

        foreach ($targeting as $targetingType => $targetingSetting) {
            switch ($targetingType) {
                case 'country':
                    if (!$this->checkTargetingCountry($targetingSetting['filter'], $targetingSetting['value'])) {
                        return false;
                    }
                    break;
                default :
                    break;
            }
        }
        return true;
    }

    /**
     * Check Country targeting option
     *
     * @param string $filter include => client country should exists in given country ids
     *                            exclude => client country should not exists in given country ids
     * @param array $countryIds Country ids to match
     *
     * @return boolean True when targeting country option matching to client country
     *                 False otherwise
     */
    public function checkTargetingCountry($filter, $countryIds)
    {
        // getClient country using GeoIp component
        $geoIpComponentController = $this->getGeoIpComponent();
        if (!$geoIpComponentController) {
            return false;
        }

        $clientRecord = $geoIpComponentController->getClientRecord();
        if (!$clientRecord) {
            return false;
        }
        $clientCountryAlpha2 = $clientRecord->country->isoCode;
        $clientCountryId = \Cx\Core\Country\Controller\Country::getIdByAlpha2($clientCountryAlpha2);

        $isCountryExists = in_array($clientCountryId, $countryIds);
        if ($filter == 'include') {
            return $isCountryExists;
        } else {
            return !$isCountryExists;
        }
    }

    /**
     * Load Targeting settings
     *
     * @param integer $blockId Content block id
     *
     * @return array Settings array
     */
    public function loadTargetingSettings($blockId)
    {
        $em = \Cx\Core\Core\Controller\Cx::instanciate()->getDb()->getEntityManager();
        $blockRepo = $em->getRepository('\Cx\Modules\Block\Model\Entity\Block');
        $block = $blockRepo->findOneBy(array('id' => $blockId));
        $targetingOption = $block->getTargetingOption();

        if (!$targetingOption) {
            return array();
        }

        $targetingArr = array();
        $targetingArr[$targetingOption->getType()] = array(
            'filter' => $targetingOption->getFilter(),
            'value' => json_decode($targetingOption->getValue())
        );

        return $targetingArr;
    }

    /**
     * Get block
     *
     * Return a block
     *
     * @access private
     * @param integer $id
     * @global ADONewConnection
     * @return mixed content on success, false on failure
     */
    function _getBlock($id)
    {
        $em = \Cx\Core\Core\Controller\Cx::instanciate()->getDb()->getEntityManager();
        $blockRepo = $em->getRepository('\Cx\Modules\Block\Model\Entity\Block');
        $block = $blockRepo->findOneBy(array('id' => $id));

        if ($block) {
            $arrContent = array();
            $arrActive = array();

            $relLangContentRepo = $em->getRepository('\Cx\Modules\Block\Model\Entity\RelLangContent');
            $relLangContents = $relLangContentRepo->findBy(array('block' => $block));
            foreach ($relLangContents as $relLangContent) {
                $arrContent[$relLangContent->getLocale()->getId()] = $relLangContent->getContent();
                $arrActive[$relLangContent->getLocale()->getId()] = $relLangContent->getActive();
            }

            $catId = 0;
            $cat = $block->getCategory();
            if ($cat) {
                $catId = $cat->getId();
            }

            return array(
                'cat' => $catId,
                'start' => $block->getStart(),
                'end' => $block->getEnd(),
                'random' => $block->getRandom(),
                'random2' => $block->getRandom2(),
                'random3' => $block->getRandom3(),
                'random4' => $block->getRandom4(),
                'global' => $block->getShowInGlobal(),
                'direct' => $block->getShowInDirect(),
                'category' => $block->getShowInCategory(),
                'active' => $block->getActive(),
                'name' => $block->getName(),
                'wysiwyg_editor' => $block->getWysiwygEditor(),
                'content' => $arrContent,
                'lang_active' => $arrActive,
            );
        }

        return false;
    }

    /**
     * Get the associated pages for a placeholder
     *
     * @param int $blockId block id
     * @param string $placeholder
     * @return array
     */
    function _getAssociatedPageIds($blockId, $placeholder)
    {
        $em = \Cx\Core\Core\Controller\Cx::instanciate()->getDb()->getEntityManager();
        $relPageRepo = $em->getRepository('\Cx\Modules\Block\Model\Entity\RelPage');
        $blockRepo = $em->getRepository('\Cx\Modules\Block\Model\Entity\Block');

        $block = $blockRepo->findOneBy(array('id' => $blockId));
        $relPages = $relPageRepo->findBy(array(
            'block' => $block,
            'placeholder' => $placeholder,
        ));

        $arrPageIds = array();
        foreach ($relPages as $relPage) {
            array_push($arrPageIds, $relPage->getPage()->getId());
        }

        return $arrPageIds;
    }

    function _getBlocksForPage($page)
    {
        $em = \Cx\Core\Core\Controller\Cx::instanciate()->getDb()->getEntityManager();

        $qb = $em->createQueryBuilder();
        $blocks = $qb->select('
                b.id,
                IDENTITY(b.category) as category,
                b.name,
                b.start,
                b.end,
                b.order,
                b.random,
                b.random2,
                b.random3,
                b.random4,
                b.showInGlobal,
                b.showInDirect,
                b.showInCategory,
                b.active
            ')
            ->from('\Cx\Modules\Block\Model\Entity\Block', 'b')
            ->from('\Cx\Modules\Block\Model\Entity\RelPage', 'rp')
            ->where('b = rp.block')
            ->andWhere('rp.page = :page')
            ->andWhere('rp.placeholder = \'global\'')
            ->groupBy('b.id')
            ->setParameter('page', $page)
            ->getQuery()
            ->getResult();

        $arrBlocks = array();
        foreach ($blocks as $block) {
            $arrBlocks[$block['id']] = array(
                'cat' => $block['category'],
                'start' => $block['start'],
                'end' => $block['end'],
                'order' => $block['order'],
                'random' => $block['random'],
                'random2' => $block['random2'],
                'random3' => $block['random3'],
                'random4' => $block['random4'],
                'global' => $block['showInGlobal'],
                'direct' => $block['showInDirect'],
                'category' => $block['showInCategory'],
                'active' => $block['active'],
                'name' => $block['name'],
            );
        }

        return $arrBlocks;
    }

    function _setBlocksForPage($page, $blockIds)
    {
        $em = \Cx\Core\Core\Controller\Cx::instanciate()->getDb()->getEntityManager();
        $blockRepo = $em->getRepository('\Cx\Modules\Block\Model\Entity\Block');
        $relPageRepo = $em->getRepository('\Cx\Modules\Block\Model\Entity\RelPage');

        $relPages = $relPageRepo->findBy(array(
            'page' => $page,
            'placeholder' => 'global',
        ));
        foreach ($relPages as $relPage) {
            $em->remove($relPage);
        }

        $blocks = array();
        foreach ($blockIds as $blockId) {
            $block = $blockRepo->findOneBy(array('id' => $blockId));
            array_push($blocks, $block);

            // block is global and will be shown on all pages, don't need to save the relation
            if ($block->getShowInGlobal() == 1) {
                continue;
            }

            $relPage = new \Cx\Modules\Block\Model\Entity\RelPage();
            $relPage->setBlock($block);
            $relPage->setPage($page);
            $relPage->setPlaceholder('global');
            $em->persist($relPage);

            // if the block was not global till now, make it global
            if ($block->getShowInGlobal() == 0) {
                $block->setShowInGlobal(1);
            }
        }

        $qb = $em->createQueryBuilder();
        $qb->delete('\Cx\Modules\Block\Model\Entity\RelPage', 'rp')
            ->where('rp.placeholder = :placeholder')
            ->andWhere('rp.page = :page')
            ->andWhere($qb->expr()->notIn('rp.block', $blocks))
            ->setParameters(array(
                'placeholder' => 'global',
                'page' => $page,
            ))
            ->getQuery()
            ->getSingleResult();

        foreach ($relPages as $relPage) {
            $em->remove($relPage);
        }
        $em->flush();
    }

    /**
     * Set block
     *
     * Parse the block with the id $id
     *
     * @access private
     * @param object $block
     * @param string &$code
     * @param object $page
     * @global ADONewConnection
     * @global integer
     */
    function _setBlock($block, &$code, $page)
    {
        $now = time();

        $em = \Cx\Core\Core\Controller\Cx::instanciate()->getDb()->getEntityManager();
        $localeRepo = $em->getRepository('\Cx\Core\Locale\Model\Entity\Locale');
        $locale = $localeRepo->findOneBy(array('id' => FRONTEND_LANG_ID));

        $qb = $em->createQueryBuilder();
        $orX = $qb->expr()->orX();
        $qb2 = $em->createQueryBuilder();
        $blocks = $qb->select('b.id')
            ->from('\Cx\Modules\Block\Model\Entity\Block', 'b')
            ->from('\Cx\Modules\Block\Model\Entity\RelLangContent', 'rlc')
            ->where('b = :block')
            ->andWhere(
                $orX->addMultiple(array(
                    'b.showInDirect = 0',
                    $qb2->select('count(rp)')
                        ->from('\Cx\Modules\Block\Model\Entity\Block', 'b')
                        ->from('\Cx\Modules\Block\Model\Entity\RelPage', 'rp')
                        ->where('rp.page = :page')
                        ->andWhere('rp.block = b')
                        ->andWhere('rp.placeholder = \'direct\'')
                        ->setParameter('page', $page)
                        ->getQuery()
                        ->getSingleResult()[1] . ' > 0'
                ))
            )
            ->andWhere('rlc.block = b')
            ->andWhere('(rlc.locale = :locale AND rlc.active = 1)')
            ->andWhere('(b.start <= :now OR b.start = 0)')
            ->andWhere('(b.end >= :now OR b.end = 0)')
            ->andWhere('b.active = 1')
            ->setParameters(array(
                'block' => $block,
                'locale' => $locale,
                'now' => $now,
            ))
            ->getQuery()
            ->getResult();

        $this->replaceBlocks(
            $this->blockNamePrefix . $block->getId(),
            $blocks,
            $page,
            $code
        );
    }

    /**
     * Set category block
     *
     * Parse the category block with the id $id
     *
     * @access private
     * @param integer $id Category ID
     * @param string &$code
     * @param int $pageId
     * @global ADONewConnection
     * @global integer
     */
    function _setCategoryBlock($category, &$code, $page)
    {
        $now = time();

        $em = \Cx\Core\Core\Controller\Cx::instanciate()->getDb()->getEntityManager();
        $localeRepo = $em->getRepository('\Cx\Core\Locale\Model\Entity\Locale');
        $locale = $localeRepo->findOneBy(array('id' => FRONTEND_LANG_ID));

        $qb = $em->createQueryBuilder();
        $orX = $qb->expr()->orX();
        $qb2 = $em->createQueryBuilder();
        $blocks = $qb->select('b.id')
            ->from('\Cx\Modules\Block\Model\Entity\Block', 'b')
            ->innerJoin('\Cx\Modules\Block\Model\Entity\RelLangContent', 'rlc', 'WITH', 'rlc.block = b')
            ->where('b.category = :category')
            ->andWhere(
                $orX->addMultiple(array(
                    'b.showInCategory = 0',
                    $qb2->select('count(rp)')
                        ->from('\Cx\Modules\Block\Model\Entity\Block', 'b')
                        ->from('\Cx\Modules\Block\Model\Entity\RelPage', 'rp')
                        ->where('rp.page = :page')
                        ->andWhere('rp.block = b')
                        ->andWhere('rp.placeholder = \'category\'')
                        ->setParameter('page', $page)
                        ->getQuery()
                        ->getSingleResult()[1] . ' > 0'
                ))
            )
            ->andWhere('b.active = 1')
            ->andWhere('(b.start <= :now OR b.start = 0)')
            ->andWhere('(b.end >= :now OR b.end = 0)')
            ->andWhere('(rlc.locale = :locale AND rlc.active = 1)')
            ->orderBy('b.order')
            ->setParameters(array(
                'category' => $category,
                'now' => $now,
                'locale' => $locale,
            ))
            ->getQuery()
            ->getResult();

        $this->replaceBlocks(
            $this->blockNamePrefix . 'CAT_' . $category->getId(),
            $blocks,
            $page,
            $code,
            $category->getSeperator()
        );
    }

    /**
     * Set block Global
     *
     * Parse the block with the id $id
     *
     * @access private
     * @param integer $id
     * @param string &$code
     * @global ADONewConnection
     * @global integer
     */
    function _setBlockGlobal(&$code, $page)
    {
        $em = \Cx\Core\Core\Controller\Cx::instanciate()->getDb()->getEntityManager();
        $settingRepo = $em->getRepository('\Cx\Modules\Block\Model\Entity\Setting');

        // fetch separator
        $separator = $settingRepo->findOneBy(array('name' => 'blockGlobalSeperator'))->getValue();

        $now = time();

        $localeRepo = $em->getRepository('\Cx\Core\Locale\Model\Entity\Locale');
        $locale = $localeRepo->findOneBy(array('id' => FRONTEND_LANG_ID));

        $qb1 = $em->createQueryBuilder();
        $result1 = $qb1->select('
                b.id AS id,
                rlc.content AS content,
                b.order AS order1
            ')
            ->from('\Cx\Modules\Block\Model\Entity\Block', 'b')
            ->innerJoin('\Cx\Modules\Block\Model\Entity\RelLangContent', 'rlc', 'WITH', 'rlc.block = b')
            ->innerJoin('\Cx\Modules\Block\Model\Entity\RelPage', 'rp', 'WITH', 'rp.block = b')
            ->where('b.showInGlobal = 2')
            ->andWhere('rp.page = :page')
            ->andWhere('rlc.locale = :locale')
            ->andWhere('rlc.active = 1')
            ->andWhere('b.active = 1')
            ->andWhere('rp.placeholder = \'global\'')
            ->andWhere('(b.start <= :now OR b.start = 0)')
            ->andWhere('(b.end >= :now OR b.end = 0)')
            ->orderBy('order1')
            ->setParameters(array(
                'locale' => $locale,
                'page' => $page,
                'now' => $now,
            ))
            ->getQuery()
            ->getResult();

        $qb2 = $em->createQueryBuilder();
        $result2 = $qb2->select('
                b.id AS id,
                rlc.content AS content,
                b.order AS order2
            ')
            ->from('\Cx\Modules\Block\Model\Entity\Block', 'b')
            ->innerJoin('\Cx\Modules\Block\Model\Entity\RelLangContent', 'rlc', 'WITH', 'rlc.block = b')
            ->where('b.showInGlobal = 1')
            ->andWhere('rlc.locale = :locale')
            ->andWhere('rlc.active = 1')
            ->andWhere('b.active = 1')
            ->andWhere('(b.start <= :now OR b.start = 0)')
            ->andWhere('(b.end >= :now OR b.end = 0)')
            ->orderBy('order2')
            ->setParameters(array(
                'locale' => $locale,
                'now' => $now,
            ))
            ->getQuery()
            ->getResult();

        $blocks = array_merge($result1, $result2);

        $this->replaceBlocks(
            $this->blockNamePrefix . 'GLOBAL',
            $blocks,
            $page,
            $code,
            $separator
        );
    }

    /**
     * Set block Random
     *
     * Parse the block with the id $id
     *
     * @access private
     * @param integer $id
     * @param string &$code
     * @global ADONewConnection
     * @global integer
     */
    function _setBlockRandom(&$code, $id, $page)
    {
        $now = time();

        $em = \Cx\Core\Core\Controller\Cx::instanciate()->getDb()->getEntityManager();
        $localeRepo = $em->getRepository('\Cx\Core\Locale\Model\Entity\Locale');
        $locale = $localeRepo->findOneBy(array('id' => FRONTEND_LANG_ID));

        $qb = $em->createQueryBuilder();
        $qb->select('b.id')
            ->from('\Cx\Modules\Block\Model\Entity\Block', 'b')
            ->from('\Cx\Modules\Block\Model\Entity\RelLangContent', 'rlc')
            ->where('rlc.block = b')
            ->andWhere('(rlc.locale = :locale AND rp.active = 1)')
            ->andWhere('(b.start <= :now OR b.start = 0)')
            ->andWhere('(b.end >= :now OR b.end = 0)')
            ->andWhere('b.active = 1');

        // Get Block Name and Status
        $blockNr = '';
        switch ($id) {
            case '1':
                $qb->andWhere('b.random = 1');
                break;
            case '2':
                $qb->andWhere('b.random2 = 1');
                $blockNr = '_2';
                break;
            case '3':
                $qb->andWhere('b.random3 = 1');
                $blockNr = '_3';
                break;
            case '4':
                $qb->andWhere('b.random4 = 1');
                $blockNr = '_4';
                break;
        }

        $blocks = $qb->setParameters(array(
            'locale' => $locale,
            'now' => $now,
        ))->getQuery()->getResult();

        if (count($blocks) <= 0) {
            return;
        }

        foreach ($blocks as $block) {
            $arrActiveBlocks[] = $block['id'];
        }

        $this->replaceBlocks(
            $this->blockNamePrefix . 'RANDOMIZER' . $blockNr,
            $blocks,
            $page,
            $code,
            '',
            true
        );
    }

    /**
     * Replaces a placeholder with block content
     * @param string $placeholderName Name of placeholder to replace
     * @param array $blocks Fetched blocks from database
     * @param object $page
     * @param string $code (by reference) Code to replace placeholder in
     * @param string $separator (optional) Separator used to separate the blocks
     * @param boolean $randomize (optional) Wheter to randomize the blocks or not, default false
     */
    protected function replaceBlocks($placeholderName, $blocks, $page, &$code, $separator = '', $randomize = false)
    {
        // find all block IDs to parse
        if (count($blocks) <= 0) {
            return;
        }
        $blockIds = array();
        foreach ($blocks as $block) {
            if (!$this->checkTargetingOptions($block['id'])) {
                continue;
            }
            $blockIds[] = $block['id'];
        }

        // parse
        $cx = \Cx\Core\Core\Controller\Cx::instanciate();
        $em = $cx->getDb()->getEntityManager();
        $systemComponentRepo = $em->getRepository('Cx\Core\Core\Model\Entity\SystemComponent');
        $frontendEditingComponent = $systemComponentRepo->findOneBy(array('name' => 'FrontendEditing'));

        if ($randomize) {
            $esiBlockInfos = array();
            foreach ($blockIds as $blockId) {
                $esiBlockInfos[] = array(
                    'Block',
                    'getBlockContent',
                    array(
                        'block' => $blockId,
                        'lang' => \FWLanguage::getLanguageCodeById(FRONTEND_LANG_ID),
                        'page' => $page->getId(),
                    )
                );
            }
            $blockContent = $cx->getComponent('Cache')->getRandomizedEsiContent(
                $esiBlockInfos
            );
            $frontendEditingComponent->prepareBlock(
                $blockId,
                $blockContent
            );
            $content = $blockContent;
        } else {
            $contentList = array();
            foreach ($blockIds as $blockId) {
                $blockContent = $cx->getComponent('Cache')->getEsiContent(
                    'Block',
                    'getBlockContent',
                    array(
                        'block' => $blockId,
                        'lang' => \FWLanguage::getLanguageCodeById(FRONTEND_LANG_ID),
                        'page' => $page->getId(),
                    )
                );
                $frontendEditingComponent->prepareBlock(
                    $blockId,
                    $blockContent
                );
                $contentList[] = $blockContent;
            }
            $content = implode($separator, $contentList);
        }

        $settingRepo = $em->getRepository('Cx\Modules\Block\Model\Entity\Setting');
        $markParsedBlock = $settingRepo->findOneBy(array('name' => 'markParsedBlock'));
        if (!empty($markParsedBlock->getValue())) {
            $content = "<!-- start $placeholderName -->$content<!-- end $placeholderName -->";
        }

        $code = str_replace('{' . $placeholderName . '}', $content, $code);
    }

    /**
     * Save the settings associated to the block system
     *
     * @access    private
     * @param    array $arrSettings
     */
    function _saveSettings($arrSettings)
    {
        \Cx\Core\Setting\Controller\Setting::init('Config', 'component', 'Yaml');
        if (isset($arrSettings['blockStatus'])) {
            if (!\Cx\Core\Setting\Controller\Setting::isDefined('blockStatus')) {
                \Cx\Core\Setting\Controller\Setting::add('blockStatus', $arrSettings['blockStatus'], 1,
                    \Cx\Core\Setting\Controller\Setting::TYPE_RADIO, '1:TXT_ACTIVATED,0:TXT_DEACTIVATED', 'component');
            } else {
                \Cx\Core\Setting\Controller\Setting::set('blockStatus', $arrSettings['blockStatus']);
                \Cx\Core\Setting\Controller\Setting::update('blockStatus');
            }
        }
        if (isset($arrSettings['blockRandom'])) {
            if (!\Cx\Core\Setting\Controller\Setting::isDefined('blockRandom')) {
                \Cx\Core\Setting\Controller\Setting::add('blockRandom', $arrSettings['blockRandom'], 1,
                    \Cx\Core\Setting\Controller\Setting::TYPE_RADIO, '1:TXT_ACTIVATED,0:TXT_DEACTIVATED', 'component');
            } else {
                \Cx\Core\Setting\Controller\Setting::set('blockRandom', $arrSettings['blockRandom']);
                \Cx\Core\Setting\Controller\Setting::update('blockRandom');
            }
        }
    }

    /**
     * create the categories dropdown
     *
     * @param array $arrCategories
     * @param array $arrOptions
     * @param integer $level
     * @return string categories as HTML options
     */
    function _getCategoriesDropdown($parent = 0, $catId = 0, $arrCategories = array(), $arrOptions = array(), $level = 0)
    {
        $first = false;
        if (count($arrCategories) == 0) {
            $first = true;
            $level = 0;
            $this->_getCategories();
            $arrCategories = $this->_categories[0]; //first array contains all root categories (parent id 0)
        }

        foreach ($arrCategories as $arrCategory) {
            $this->_categoryOptions[] =
                '<option value="' . $arrCategory['id'] . '" '
                . (
                $parent > 0 && $parent == $arrCategory['id']  //selected if parent specified and id is parent
                    ? 'selected="selected"'
                    : ''
                )
                . (
                ($catId > 0 && in_array($arrCategory['id'], $this->_getChildCategories($catId))) || $catId == $arrCategory['id'] //disable children and self
                    ? 'disabled="disabled"'
                    : ''
                )
                . ' >' // <option>
                . str_repeat('&nbsp;', $level * 4)
                . htmlentities($arrCategory['name'], ENT_QUOTES, CONTREXX_CHARSET)
                . '</option>';

            if (!empty($this->_categories[$arrCategory['id']])) {
                $this->_getCategoriesDropdown($parent, $catId, $this->_categories[$arrCategory['id']], $arrOptions, $level + 1);
            }
        }
        if ($first) {
            return implode("\n", $this->_categoryOptions);
        }
    }

    /**
     * save a block category
     *
     * @param integer $id
     * @param integer $parent
     * @param string $name
     * @param string $seperator
     * @param integer $order
     * @param integer $status
     * @throws NotEnoughArgumentsException
     * @throws NoCategoryFoundException
     * @return integer inserted ID or false on failure
     */
    function _saveCategory($id = 0, $parent = 0, $name, $seperator, $order = 1, $status = 1)
    {
        // check for necessary arguments
        if (empty($name)) {
            throw new NotEnoughArgumentsException('not enough arguments');
        }

        if ($id > 0 && $id == $parent) { //don't allow category to attach to itself
            return false;
        }

        if ($id == 0) { //if new record then set to NULL for auto increment
            $id = 'NULL';
        } else {
            $arrChildren = $this->_getChildCategories($id);
            if (in_array($parent, $arrChildren)) { //don't allow category to be attached to one of it's own children
                return false;
            }
        }
        $name = contrexx_addslashes($name);
        $seperator = contrexx_addslashes($seperator);

        $em = \Cx\Core\Core\Controller\Cx::instanciate()->getDb()->getEntityManager();
        $categoryRepo = $em->getRepository('\Cx\Modules\Block\Model\Entity\Category');
        $category = $categoryRepo->findOneBy(array('id' => $id));

        $new = false;
        if (!$category) {
            if ($id > 0) {
                throw new NoCategoryFoundException('no category found');
            }
            $category = new \Cx\Modules\Block\Model\Entity\Category();
            $new = true;
        }

        $parent = $categoryRepo->findOneBy(array('id' => $parent));
        $category->setParent($parent);
        $category->setName($name);
        $category->setSeperator($seperator);
        $category->setOrder($order);
        $category->setStatus($status);

        if ($new) {
            $em->persist($category);
            $em->flush();
            $em->refresh($category);
        } else {
            $em->flush();
        }

        return $category->getId();
    }

    /**
     * return all child caegories of a cateogory
     *
     * @param integer ID of category to get list of children from
     * @param array cumulates the child arrays, internal use
     * @return array IDs of children
     */
    function _getChildCategories($id, &$_arrChildCategories = array())
    {
        if (empty($this->_categories)) {
            $this->_getCategories();
        }
        if (!isset($this->_categories[$id])) {
            return array();
        }
        foreach ($this->_categories[$id] as $cat) {
            if (!empty($this->_categories[$cat['parent']])) {
                $_arrChildCategories[] = $cat['id'];
                $this->_getChildCategories($cat['id'], $_arrChildCategories);
            }

        }
        return $_arrChildCategories;
    }

    /**
     * delete a category by id
     *
     * @param integer $id category id
     * @throws NotEnoughArgumentsException
     * @throws NoCategoryFoundException
     * @return bool success
     */
    function _deleteCategory($id = 0)
    {
        $em = \Cx\Core\Core\Controller\Cx::instanciate()->getDb()->getEntityManager();
        $categoryRepo = $em->getRepository('\Cx\Modules\Block\Model\Entity\Category');
        $blockRepo = $em->getRepository('\Cx\Modules\Block\Model\Entity\Block');

        if ($id < 1) {
            throw new NotEnoughArgumentsException('not enough arguments');
        }

        $category = $categoryRepo->findOneBy(array('id' => $id));
        if (!$category) {
            throw new NoCategoryFoundException('no category found');
        }

        $blocks = $blockRepo->findBy(array('category' => $category));
        foreach ($blocks as $block) {
            $block->setCategory(null);
        }
        $em->remove($category);

        $categoryParent = $categoryRepo->findOneBy(array('parent' => $id));
        if ($categoryParent) {
            $categoryParent->setParent(null);
        }

        $em->flush();
        return true;
    }

    /**
     * fill and/or return the categories array
     *
     * category arrays are put in the array as first dimension elements, with their parent as key, as follows:
     * $this->_categories[$objRS->fields['parent']][] = $objRS->fields;
     *
     * just to make this clear:
     * note that $somearray['somekey'][] = $foo adds $foo to $somearray['somekey'] rather than overwriting it.
     *
     * @param bool force refresh from DB
     * @see blockManager::_parseCategories for parse example
     * @see blockLibrary::_getCategoriesDropdown for parse example
     * @global ADONewConnection
     * @global array
     * @return array all available categories
     */
    function _getCategories($refresh = false)
    {
        global $_ARRAYLANG;

        $em = \Cx\Core\Core\Controller\Cx::instanciate()->getDb()->getEntityManager();

        if (!empty($this->_categories) && !$refresh) {
            return $this->_categories;
        }

        $this->_categories = array(0 => array());
        $this->_categoryNames[0] = $_ARRAYLANG['TXT_BLOCK_NONE'];

        $categoryRepo = $em->getRepository('Cx\Modules\Block\Model\Entity\Category');
        $categories = $categoryRepo->findBy(array(), array('order' => 'ASC', 'id' => 'ASC'));

        foreach ($categories as $category) {
            $parentId = 0;
            $parent = $category->getParent();
            if ($parent) {
                $parentId = $parent->getId();
            }
            $this->_categories[$parentId][] = array(
                'id' => $category->getId(),
                'parent' => $parentId,
                'name' => $category->getName(),
                'order' => $category->getOrder(),
                'status' => $category->getStatus(),
                'seperator' => $category->getSeperator(),
            );
            $this->_categoryNames[$category->getId()] = $category->getName();
        }

        return $this->_categories;
    }
}
