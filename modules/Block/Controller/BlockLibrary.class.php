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
 * Block
 * @copyright   CLOUDREXX CMS - CLOUDREXX AG
 * @author      Cloudrexx Development Team <info@cloudrexx.com>
 * @version     1.0.0
 * @package     cloudrexx
 * @subpackage  module_block
 * @todo        Edit PHP DocBlocks!
 */

namespace Cx\Modules\Block\Controller;

/**
 * Block
 *
 * Block library class
 * @copyright   CLOUDREXX CMS - CLOUDREXX AG
 * @author      Cloudrexx Development Team <info@cloudrexx.com>
 * @access      private
 * @version     1.0.0
 * @package     cloudrexx
 * @subpackage  module_block
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
        $catId = intval($catId);

        if (!is_array($this->_arrBlocks)) {
            $em = \Cx\Core\Core\Controller\Cx::instanciate()->getDb()->getEntityManager();
            $blockRepo = $em->getRepository('\Cx\Modules\Block\Model\Entity\Block');

            if ($catId != 0) {
                $blocks = $blockRepo->findBy(array('cat' => $catId));
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

                $this->_arrBlocks[$block->getId()] = array(
                    'cat' => $block->getCat()->getId(),
                    'start' => $block->getStart(),
                    'end' => $block->getEnd(),
                    'order' => $block->getOrder(),
                    'random' => $block->getRandom(),
                    'random2' => $block->getRandom2(),
                    'random3' => $block->getRandom3(),
                    'random4' => $block->getRandom4(),
                    'global' => $block->getGlobal(),
                    'active' => $block->getActive(),
                    'direct' => $block->getDirect(),
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
        $block = new \Cx\Modules\Block\Model\Entity\Block();
        $categoryRepo = $em->getRepository('\Cx\Modules\Block\Model\Entity\Category');
        $category = $categoryRepo->findOneBy(array('id' => intval($cat)));

        $qb = $em->createQueryBuilder();
        $order = $qb->select('MAX(b.order)')
            ->from('\Cx\Modules\Block\Model\Entity\Block', 'b')
            ->getQuery()
            ->getSingleResult();

        $block->setCat($category);
        $block->setStart(intval($start));
        $block->setEnd(intval($end));
        $block->setName($name);
        $block->setRandom(intval($blockRandom));
        $block->setRandom2(intval($blockRandom2));
        $block->setRandom3(intval($blockRandom3));
        $block->setRandom4(intval($blockRandom4));
        $block->setGlobal(0);
        $block->setCategory(0);
        $block->setDirect(0);
        $block->setActive(1);
        $block->setOrder(intval($order[1]) + 1);
        $block->setWysiwygEditor(intval($blockWysiwygEditor));

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
        $block = $blockRepo->findOneBy(array('id' => intval($id)));
        $category = $categoryRepo->findOneBy(array('id' => intval($cat)));

        $block->setName($name);
        $block->setCat($category);
        $block->setStart(intval($start));
        $block->setEnd(intval($end));
        $block->setRandom(intval($blockRandom));
        $block->setRandom2(intval($blockRandom2));
        $block->setRandom3(intval($blockRandom3));
        $block->setRandom4(intval($blockRandom4));
        $block->setWysiwygEditor(intval($blockWysiwygEditor));

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
        $block = $blockRepo->findOneBy(array('id' => intval($blockId)));

        $block->setGlobal($global);
        $block->setDirect($direct);
        $block->setCategory($category);

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
        $block = $blockRepo->findOneBy(array('id' => intval($blockId)));
        $pageRepo = $em->getRepository('\Cx\Core\ContentManager\Model\Entity\Page');

        foreach ($blockAssociatedPageIds as $pageId) {
            if ($pageId > 0) {
                $page = $pageRepo->findOneBy(array('id' => intval($pageId)));
                $relPage = new \Cx\Modules\Block\Model\Entity\RelPage();
                $relPage->setBlock($block);
                $relPage->setContentPage($page);
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
        $block = $blockRepo->findOneBy(array('id' => intval($blockId)));
        $relLangContents = $block->getRelLangContents();

        $arrPresentLang = array();
        foreach ($relLangContents as $relLangContent) {
            $arrPresentLang[] = $relLangContent->getLocale()->getId();
        }

        foreach ($arrContent as $langId => $content) {
            $content = preg_replace('/\[\[([A-Z0-9_-]+)\]\]/', '{\\1}', $content);
            $locale = $localeRepo->findOneBy(array('id' => intval($langId)));
            if (in_array($langId, $arrPresentLang)) {
                $relLangContent = $relLangContentRepo->findOneBy(array('block' => $block, 'locale' => $locale));
                $relLangContent->setContent($content);
                $relLangContent->setActive(intval((isset($arrLangActive[$langId]) ? $arrLangActive[$langId] : 0)));
            } else {
                $relLangContent = new \Cx\Modules\Block\Model\Entity\RelLangContent();
                $relLangContent->setContent($content);
                $relLangContent->setActive(intval((isset($arrLangActive[$langId]) ? $arrLangActive[$langId] : 0)));
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
                    'rlc.locale', array_map('intval', array_keys($arrLangActive))
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
        $targetingOptionRepo = $em->getRepository('\Cx\Modules\Block\Model\Entity\TargetingOption');
        $targetingOptions = $targetingOptionRepo->findBy(array('blockId' => $blockId));

        if (!$targetingOptions) {
            return array();
        }

        $targetingArr = array();
        foreach ($targetingOptions as $targetingOption) {
            $targetingArr[$targetingOption->getType()] = array(
                'filter' => $targetingOption->getFilter(),
                'value' => json_decode($targetingOption->getValue())
            );
        }

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

            return array(
                'cat' => $block->getCat()->getId(),
                'start' => $block->getStart(),
                'end' => $block->getEnd(),
                'random' => $block->getRandom(),
                'random2' => $block->getRandom2(),
                'random3' => $block->getRandom3(),
                'random4' => $block->getRandom4(),
                'global' => $block->getGlobal(),
                'direct' => $block->getDirect(),
                'category' => $block->getCategory(),
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

        $block = $blockRepo->findOneBy(array('id' => intval($blockId)));
        $relPages = $relPageRepo->findBy(array(
            'block' => $block,
            'placeholder' => $placeholder,
        ));

        $arrPageIds = array();
        foreach ($relPages as $relPage) {
            array_push($arrPageIds, $relPage->getContentPage()->getId());
        }

        return $arrPageIds;
    }

    function _getBlocksForPageId($pageId)
    {
        $em = \Cx\Core\Core\Controller\Cx::instanciate()->getDb()->getEntityManager();
        $pageRepo = $em->getRepository('\Cx\Core\ContentManager\Model\Entity\Page');
        $page = $pageRepo->findOneBy(array('id' => $pageId));

        $qb = $em->createQueryBuilder();
        $blocks = $qb->select('
                b.id,
                IDENTITY(b.cat),
                b.name,
                b.start,
                b.end,
                b.order,
                b.random,
                b.random2,
                b.random3,
                b.random4,
                b.global,
                b.direct,
                b.category,
                b.active
            ')
            ->from('\Cx\Modules\Block\Model\Entity\Block', 'b')
            ->from('\Cx\Modules\Block\Model\Entity\RelPage', 'rp')
            ->where('b = rp.block')
            ->andWhere('rp.contentPage = :page')
            ->andWhere('rp.placeholder = \'global\'')
            ->groupBy('b.id')
            ->setParameter('page', $page)
            ->getQuery()
            ->getResult();

        $arrBlocks = array();
        foreach ($blocks as $block) {
            $arrBlocks[$block['id']] = array(
                'cat' => $block['id'],
                'start' => $block['start'],
                'end' => $block['end'],
                'order' => $block['order'],
                'random' => $block['random'],
                'random2' => $block['random2'],
                'random3' => $block['random3'],
                'random4' => $block['random4'],
                'global' => $block['global'],
                'direct' => $block['direct'],
                'category' => $block['category'],
                'active' => $block['active'],
                'name' => $block['name'],
            );
        }

        return $arrBlocks;
    }

    function _setBlocksForPageId($pageId, $blockIds)
    {
        $em = \Cx\Core\Core\Controller\Cx::instanciate()->getDb()->getEntityManager();
        $pageRepo = $em->getRepository('\Cx\Core\ContentManager\Model\Entity\Page');
        $blockRepo = $em->getRepository('\Cx\Modules\Block\Model\Entity\Block');
        $relPageRepo = $em->getRepository('\Cx\Modules\Block\Model\Entity\RelPage');

        $relPages = $relPageRepo->findBy(array('pageId' => intval($pageId), 'placeholder' => 'global'));
        foreach ($relPages as $relPage) {
            $em->remove($relPage);
        }

        $blocks = array();
        foreach ($blockIds as $blockId) {
            $block = $blockRepo->findOneBy(array('id' => $blockId));
            array_push($blocks, $block);

            // block is global and will be shown on all pages, don't need to save the relation
            if ($block->getGlobal() == 1) {
                continue;
            }
            // if the block was not global till now, make it global
            if ($block->getGlobal() == 0) {
                $block = $blockRepo->findOneBy(array('id' => intval($blockId)));
                $block->setGlobal(2);
            }

            $relPage = new \Cx\Modules\Block\Model\Entity\RelPage();
            $page = $pageRepo->findOneBy(array('id' => $pageId));

            $relPage->setBlock($block);
            $relPage->setContentPage($page);
            $relPage->setPlaceholder('global');
            $em->persist($relPage);
        }

        $page = $pageRepo->findOneBy(array('id' => $pageId));

        $qb = $em->createQueryBuilder();
        $qb->delete('\Cx\Modules\Block\Model\Entity\RelPage', 'rp')
            ->where('rp.placeholder = :placeholder')
            ->andWhere('rp.contentPage = :page')
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
     * @param integer $id Block ID
     * @param string &$code
     * @param int $pageId
     * @global ADONewConnection
     * @global integer
     */
    function _setBlock($id, &$code, $pageId)
    {
        if (!$this->checkTargetingOptions($id)) {
            return;
        }

        $now = time();

        $em = \Cx\Core\Core\Controller\Cx::instanciate()->getDb()->getEntityManager();
        $blockRepo = $em->getRepository('\Cx\Modules\Block\Model\Entity\Block');
        $block = $blockRepo->findOneBy(array('id' => intval($id)));
        $localeRepo = $em->getRepository('\Cx\Core\Locale\Model\Entity\Locale');
        $locale = $localeRepo->findOneBy(array('id' => FRONTEND_LANG_ID));
        $pageRepo = $em->getRepository('\Cx\Core\ContentManager\Model\Entity\Page');
        $page = $pageRepo->findOneBy(array('id' => intval($pageId)));

        $qb = $em->createQueryBuilder();
        $qb2 = $em->createQueryBuilder();
        $blocks = $qb->select('b.id')
            ->from('\Cx\Modules\Block\Model\Entity\Block', 'b')
            ->from('\Cx\Modules\Block\Model\Entity\RelLangContent', 'rlc')
            ->where('b = :block')
            ->andWhere(
                '(b.direct = 0 OR ' .
                $qb->expr()->count(
                    $qb2->select('count(1)')
                        ->from('\Cx\Modules\Block\Model\Entity\RelPage', 'rp')
                        ->where('rp.contentPage = :page')
                        ->andWhere('rp.block = b')
                        ->andWhere('rp.placeholder = \'direct\'')
                        ->setParameter('page', $page)
                        ->getQuery()
                        ->getResult()
                ) .
                ' > 0)'
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
            $this->blockNamePrefix . $id,
            $blocks,
            $pageId,
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
    function _setCategoryBlock($id, &$code, $pageId)
    {
        $category = $this->_getCategory($id);
        $separator = $category['seperator'];

        $now = time();

        $em = \Cx\Core\Core\Controller\Cx::instanciate()->getDb()->getEntityManager();
        $categoryRepo = $em->getRepository('\Cx\Modules\Block\Model\Entity\Category');
        $category = $categoryRepo->findOneBy(array('id' => intval($id)));
        $localeRepo = $em->getRepository('\Cx\Core\Locale\Model\Entity\Locale');
        $locale = $localeRepo->findOneBy(array('id' => FRONTEND_LANG_ID));
        $pageRepo = $em->getRepository('\Cx\Core\ContentManager\Model\Entity\Page');
        $page = $pageRepo->findOneBy(array('id' => intval($pageId)));

        $qb = $em->createQueryBuilder();
        $qb2 = $em->createQueryBuilder();
        $blocks = $qb->select('b.id')
            ->from('\Cx\Modules\Block\Model\Entity\Block', 'b')
            ->innerJoin('b', '\Cx\Modules\Block\Model\Entity\RelLangContent', 'rlc', 'rlc.blockId = b.id')
            ->where('b.cat = :cat')
            ->andWhere(
                '(b.category = 0 OR ' .
                $qb->expr()->count(
                    $qb2->select('count(1)')
                        ->from('\Cx\Modules\Block\Model\Entity\RelPage', 'rp')
                        ->where('rp.contentPage = :page')
                        ->andWhere('rp.block = b')
                        ->andWhere('rp.placeholder = \'category\'')
                        ->setParameter('page', $page)
                        ->getQuery()
                        ->getResult()
                ) .
                ' > 0)'
            )
            ->andWhere('b.active = 1')
            ->andWhere('(b.start <= :now OR b.start = 0)')
            ->andWhere('(b.end >= :now OR b.end = 0)')
            ->andWhere('(b.locale = :locale AND rlc.active = 1)')
            ->orderBy('order')
            ->setParameters(array(
                'cat' => $category,
                'now' => $now,
                'locale' => $locale,
            ))
            ->getQuery()
            ->getResult();

        $this->replaceBlocks(
            $this->blockNamePrefix . 'CAT_' . $id,
            $blocks,
            $pageId,
            $code,
            $separator
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
    function _setBlockGlobal(&$code, $pageId)
    {
        $em = \Cx\Core\Core\Controller\Cx::instanciate()->getDb()->getEntityManager();
        $settingRepo = $em->getRepository('\Cx\Modules\Block\Model\Entity\Setting');

        // fetch separator
        $separator = $settingRepo->findOneBy(array('name' => 'blockGlobalSeperator'))->getValue();

        $now = time();

        $localeRepo = $em->getRepository('\Cx\Core\Locale\Model\Entity\Locale');
        $locale = $localeRepo->findOneBy(array('id' => FRONTEND_LANG_ID));
        $pageRepo = $em->getRepository('\Cx\Core\ContentManager\Model\Entity\Page');
        $page = $pageRepo->findOneBy(array('id' => intval($pageId)));

        $qb1 = $em->createQueryBuilder();
        $result1 = $qb1->select('
                b.id AS id,
                rlc.content AS content,
                b.order AS order
            ')
            ->from('\Cx\Modules\Block\Model\Entity\Block', 'b')
            ->from('\Cx\Modules\Block\Model\Entity\RelLangContent', 'rlc')
            ->innerJoin('b', '\Cx\Modules\Block\Model\Entity\RelLangContent', 'rlc', 'rlc.block = b')
            ->innerJoin('b', '\Cx\Modules\Block\Model\Entity\RelPage', 'rp', 'rp.block = b')
            ->where('b.global = 2')
            ->andWhere('rp.contentPage = :page')
            ->andWhere('rlc.locale = :locale')
            ->andWhere('rlc.active = 1')
            ->andWhere('b.active = 1')
            ->andWhere('rp.placeholder = \'global\'')
            ->andWhere('(b.start <= :now OR b.start = 0)')
            ->andWhere('(b.end >= :now OR b.end = 0)')
            ->orderBy('order')
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
                b.order AS order
            ')
            ->from('\Cx\Modules\Block\Model\Entity\Block', 'b')
            ->innerJoin('b', '\Cx\Modules\Block\Model\Entity\RelLangContent', 'rlc', 'rlc.block = b')
            ->where('b.global = 1')
            ->andWhere('rlc.locale = :locale')
            ->andWhere('rlc.active = 1')
            ->andWhere('b.active = 1')
            ->andWhere('(b.start <= :now OR b.start = 0)')
            ->andWhere('(b.end >= :now OR b.end = 0)')
            ->orderBy('order')
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
            $pageId,
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
    function _setBlockRandom(&$code, $id, $pageId)
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
            $pageId,
            $code,
            '',
            true
        );
    }

    /**
     * Replaces a placeholder with block content
     * @param string $placeholderName Name of placeholder to replace
     * @param array $blocks Fetched blocks from database
     * @param string $code (by reference) Code to replace placeholder in
     * @param string $separator (optional) Separator used to separate the blocks
     * @param boolean $randomize (optional) Wheter to randomize the blocks or not, default false
     */
    protected function replaceBlocks($placeholderName, $blocks, $pageId, &$code, $separator = '', $randomize = false)
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
                        'page' => $pageId,
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
                        'page' => $pageId,
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
     * @return integer inserted ID or false on failure
     */
    function _saveCategory($id = 0, $parent = 0, $name, $seperator, $order = 1, $status = 1)
    {
        $id = intval($id);
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

        try {
            $new = false;
            if (!$category) {
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
        } catch (\Exception $e) {
            return false;
        }
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
     * @return bool success
     */
    function _deleteCategory($id = 0)
    {
        $em = \Cx\Core\Core\Controller\Cx::instanciate()->getDb()->getEntityManager();
        $categoryRepo = $em->getRepository('\Cx\Modules\Block\Model\Entity\Category');
        $blockRepo = $em->getRepository('\Cx\Modules\Block\Model\Entity\Setting');

        $id = intval($id);
        if ($id < 1) {
            return false;
        }

        $category = $categoryRepo->findOneBy(array('id' => $id));
        $em->remove($category);

        $categoryParent = $categoryRepo->findOneBy(array('parent' => $id));
        $categoryParent->setParent(null);

        $blocks = $blockRepo->findBy(array('cat' => $id));
        foreach ($blocks as $block) {
            $block->setCat(null);
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
            $this->_categories[$category->getParent()->getId()][] = array(
                'id' => $category->getId(),
                'parent' => $category->getParent()->getId(),
                'name' => $category->getName(),
                'order' => $category->getOrder(),
                'status' => $category->getStatus(),
                'seperator' => $category->getSeperator(),
            );
            $this->_categoryNames[$category->getId()] = $category->getName();
        }

        return $this->_categories;
    }

    /**
     * return the categoriy specified by ID
     *
     * @param integer $id
     * @return array category information
     */
    function _getCategory($id = 0)
    {
        $em = \Cx\Core\Core\Controller\Cx::instanciate()->getDb()->getEntityManager();

        $id = intval($id);
        if ($id == 0) {
            return false;
        }

        $categoryRepo = $em->getRepository('\Cx\Modules\Block\Model\Entity\Category');
        $category = $categoryRepo->findOneBy(array('id' => $id));

        return array(
            'id' => $category->getId(),
            'parent' => $category->getParent()->getId(),
            'name' => $category->getName(),
            'order' => $category->getOrder(),
            'status' => $category->getStatus(),
            'seperator' => $category->getSeperator(),
        );
    }
}
