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
 * Cx\Modules\Block\Controller\JsonBlockException
 *
 * @copyright   CLOUDREXX CMS - CLOUDREXX AG
 * @author      Cloudrexx Development Team <info@cloudrexx.com>
 * @version     1.0.0
 * @package     cloudrexx
 * @subpackage  module_block
 */
class JsonBlockException extends \Exception
{
}

/**
 * Cx\Modules\Block\Controller\NotEnoughArgumentsException
 *
 * @copyright   CLOUDREXX CMS - CLOUDREXX AG
 * @author      Cloudrexx Development Team <info@cloudrexx.com>
 * @version     1.0.0
 * @package     cloudrexx
 * @subpackage  module_block
 */
class NotEnoughArgumentsException extends JsonBlockException
{
}

/**
 * Cx\Modules\Block\Controller\NoBlockFoundException
 *
 * @copyright   CLOUDREXX CMS - CLOUDREXX AG
 * @author      Cloudrexx Development Team <info@cloudrexx.com>
 * @version     1.0.0
 * @package     cloudrexx
 * @subpackage  module_block
 */
class NoBlockFoundException extends JsonBlockException
{
}

/**
 * Cx\Modules\Block\Controller\NoBlockFoundException
 *
 * @copyright   CLOUDREXX CMS - CLOUDREXX AG
 * @author      Manuel Schenk <manuel.schenk@comvation.com>
 * @version     1.0.0
 * @package     cloudrexx
 * @subpackage  module_block
 */
class NoBlockVersionFoundException extends JsonBlockException
{
}

/**
 * Cx\Modules\Block\Controller\JsonBlockController
 *
 * JSON Adapter for Block
 *
 * @copyright   CLOUDREXX CMS - CLOUDREXX AG
 * @author      Cloudrexx Development Team <info@cloudrexx.com>
 * @version     1.0.0
 * @package     cloudrexx
 * @subpackage  module_block
 */
class JsonBlockController extends \Cx\Core\Core\Model\Entity\Controller implements \Cx\Core\Json\JsonAdapter
{
    /**
     * List of messages
     * @var Array
     */
    protected $messages = array();

    /**
     * Returns the internal name used as identifier for this adapter
     * @return String Name of this adapter
     */
    public function getName()
    {
        return 'Block';
    }

    /**
     * Returns default permission as object
     * @return Object
     */
    public function getDefaultPermissions()
    {
        return new \Cx\Core_Modules\Access\Model\Entity\Permission(null, array('get', 'post'), true);
    }

    /**
     * Returns an array of method names accessable from a JSON request
     * @return array List of method names
     */
    public function getAccessableMethods()
    {
        return array(
            'getCountries',
            'getBlocks',
            'getBlockContent' => new \Cx\Core_Modules\Access\Model\Entity\Permission(
                null,
                array('get', 'cli', 'post'),
                false
            ),
            'saveBlockContent' => new \Cx\Core_Modules\Access\Model\Entity\Permission(
                null,
                array('post', 'cli'),
                true,
                array(),
                array(76)
            ),
            'getBlock',
        );
    }

    /**
     * Returns all messages as string
     * @return String HTML encoded error messages
     */
    public function getMessagesAsString()
    {
        return implode('<br />', $this->messages);
    }

    /**
     * Get countries from given name
     *
     * @param array $params Get parameters,
     *
     * @return array Array of countries
     */
    public function getCountries($params)
    {
        $countries = array();
        $term = !empty($params['get']['term']) ? contrexx_input2raw($params['get']['term']) : '';
        if (empty($term)) {
            return array(
                'countries' => $countries
            );
        }
        if (!defined('FRONTEND_LANG_ID')) {
            define('FRONTEND_LANG_ID', 1);
        }
        $arrCountries = \Cx\Core\Country\Controller\Country::searchByName($term, null, false);
        foreach ($arrCountries as $country) {
            $countries[] = array(
                'id' => $country['id'],
                'label' => $country['name'],
                'val' => $country['name'],
            );
        }
        return array(
            'countries' => $countries
        );
    }

    /**
     * Returns all available blocks for each language
     *
     * @return array List of blocks (lang => id )
     */
    public function getBlocks()
    {
        global $objInit, $_CORELANG;

        if (!\FWUser::getFWUserObject()->objUser->login() || $objInit->mode != 'backend') {
            throw new \Exception($_CORELANG['TXT_ACCESS_DENIED_DESCRIPTION']);
        }
        if (!defined('FRONTEND_LANG_ID')) {
            define('FRONTEND_LANG_ID', 1);
        }

        $blockLib = new \Cx\Modules\Block\Controller\BlockLibrary();
        $blocks = $blockLib->getBlocks();
        $data = array();
        foreach ($blocks as $id => $block) {
            $data[$id] = array(
                'id' => $id,
                'name' => $block['name'],
                'disabled' => $block['global'] == 1,
                'selected' => $block['global'] == 1,
            );
        }
        return $data;
    }

    /**
     * Get the block content as html
     *
     * @param array $params all given params from http request
     * @throws NotEnoughArgumentsException
     * @throws NoBlockFoundException
     * @return string the html content of the block
     */
    public function getBlockContent($params)
    {
        $parsing = true;
        if ($params['get']['parsing'] == 'false') {
            $parsing = false;
        }

        // check for necessary arguments
        if (
            empty($params['get']) ||
            empty($params['get']['block']) ||
            empty($params['get']['lang']) ||
            (
                empty($params['get']['page']) &&
                $parsing
            )
        ) {
            throw new NotEnoughArgumentsException('not enough arguments');
        }

        // get id and langugage id
        $id = intval($params['get']['block']);
        $lang = \FWLanguage::getLanguageIdByCode($params['get']['lang']);
        if (!defined('FRONTEND_LANG_ID')) {
            if (!$lang) {
                $lang = 1;
            }
            define('FRONTEND_LANG_ID', $lang);
        }
        if (!$lang) {
            $lang = FRONTEND_LANG_ID;
        }

        // database query to get the html content of a block by block id and
        // language id
        $em = \Cx\Core\Core\Controller\Cx::instanciate()->getDb()->getEntityManager();

        $blockRepo = $em->getRepository('\Cx\Modules\Block\Model\Entity\Block');
        $block = $blockRepo->findOneBy(array('id' => $id));
        $localeRepo = $em->getRepository('\Cx\Core\Locale\Model\Entity\Locale');
        $locale = $localeRepo->findOneBy(array('id' => $lang));

        $relLangContentRepo = $em->getRepository('Cx\Modules\Block\Model\Entity\RelLangContent');
        $relLangContent = $relLangContentRepo->findOneBy(array(
            'block' => $block,
            'locale' => $locale,
            'active' => 1,
        ));

        // nothing found
        if (!$relLangContent) {
            throw new NoBlockFoundException('no block content found with id: ' . $id);
        }

        $content = $relLangContent->getContent();

        $cx = \Cx\Core\Core\Controller\Cx::instanciate();
        $cx->parseGlobalPlaceholders($content);
        $template = new \Cx\Core\Html\Sigma();
        $template->setTemplate($content);
        $this->getComponent('Widget')->parseWidgets(
            $template,
            'Block',
            'Block',
            $id
        );
        $content = $template->get();

        if ($parsing) {
            $pageRepo = $em->getRepository('Cx\Core\ContentManager\Model\Entity\Page');
            $page = $pageRepo->find($params['get']['page']);
            \Cx\Modules\Block\Controller\Block::setBlocks($content, $page);
        }

        \LinkGenerator::parseTemplate($content);
        $ls = new \LinkSanitizer(
            $cx,
            $cx->getCodeBaseOffsetPath() . \Env::get('virtualLanguageDirectory') . '/',
            $content
        );
        return array('content' => $ls->replace());
    }

    /**
     * Save the block content
     *
     * @param array $params all given params from http request
     * @throws NotEnoughArgumentsException
     * @return boolean true if everything finished with success
     */
    public function saveBlockContent($params)
    {
        global $_CORELANG;

        // check arguments
        if (empty($params['get']['block']) || empty($params['get']['lang'])) {
            throw new NotEnoughArgumentsException('not enough arguments');
        }

        // get language and block id
        $id = intval($params['get']['block']);
        $lang = \FWLanguage::getLanguageIdByCode($params['get']['lang']);
        if (!defined('FRONTEND_LANG_ID')) {
            if (!$lang) {
                $lang = 1;
            }
            define('FRONTEND_LANG_ID', $lang);
        }
        if (!$lang) {
            $lang = FRONTEND_LANG_ID;
        }
        $content = $params['post']['content'];

        // query to update content in database
        $em = \Cx\Core\Core\Controller\Cx::instanciate()->getDb()->getEntityManager();
        $localeRepo = $em->getRepository('\Cx\Core\Locale\Model\Entity\Locale');
        $blockRepo = $em->getRepository('\Cx\Modules\Block\Model\Entity\Block');
        $relLangContentRepo = $em->getRepository('\Cx\Modules\Block\Model\Entity\RelLangContent');

        $locale = $localeRepo->findOneBy(array('id' => $lang));
        $block = $blockRepo->findOneBy(array('id' => $id));
        $relLangContent = $relLangContentRepo->findOneBy(array('locale' => $locale, 'block' => $block));
        if ($relLangContent) {
            $relLangContent->setContent($content);
        }

        $em->flush();

        \LinkGenerator::parseTemplate($content);

        $cx = \Cx\Core\Core\Controller\Cx::instanciate();
        $ls = new \LinkSanitizer(
            $cx,
            $cx->getCodeBaseOffsetPath() . \Env::get('virtualLanguageDirectory') . '/',
            $content
        );
        $this->messages[] = $_CORELANG['TXT_CORE_SAVED_BLOCK'];

        return array('content' => $ls->replace());
    }

    /**
     * Gets every block attribute for given block and version
     *
     * @param array $params all given params from http request
     * @throws NotEnoughArgumentsException if not enough arguments are provided
     * @throws NoBlockFoundException if the requested block can't be found
     * @throws NoBlockVersionFoundException if the requested block version can't be found
     * @return array $blockVersion all attributes from block
     */
    public function getBlock($params)
    {
        // throws exception if not enough arguments are provided
        if (empty($params['get']['id']) || empty($params['get']['version'])) {
            throw new NotEnoughArgumentsException('not enough arguments');
        }

        // gets params from request
        $id = intval($params['get']['id']);
        $version = intval($params['get']['version']);

        // gets block by id parameter
        $em = \Cx\Core\Core\Controller\Cx::instanciate()->getDb()->getEntityManager();
        $blockRepo = $em->getRepository('\Cx\Modules\Block\Model\Entity\Block');
        $block = $blockRepo->findOneBy(array('id' => $id));

        // throws exception if no block found
        if (!$block) {
            throw new NoBlockFoundException('no block found with id: ' . $id);
        }

        // gets log entry repository
        $blockLogRepo = $em->getRepository('Cx\Modules\Block\Model\Entity\LogEntry');
        // gets requested block version
        $revertedBlock = $blockLogRepo->revertEntity($block, $version);

        if (!$revertedBlock) {
            throw new NoBlockVersionFoundException('no block found under version: ' . $version);
        }

        // process targeting option version
        $versionTargetingOption = unserialize($revertedBlock->getVersionTargetingOption());
        $targetingOptionRepo = $em->getRepository('\Cx\Modules\Block\Model\Entity\TargetingOption');
        $targetingOptionValue = array();
        foreach ($versionTargetingOption as $id => $version) {
            $targetingOption = $targetingOptionRepo->findOneBy(
                array(
                    'id' => $id,
                )
            );

            if ($targetingOption) {
                $blockLogRepo = $em->getRepository('Cx\Modules\Block\Model\Entity\LogEntry');
                $revertedTargetingOption = $blockLogRepo->revertEntity($targetingOption, $version);
                array_push(
                    $targetingOptionValue,
                    array(
                        'type' => $revertedTargetingOption->getType(),
                        'filter' => $revertedTargetingOption->getFilter(),
                        'value' => $revertedTargetingOption->getValue(),
                    )
                );
            }
        }

        // process rel lang content version
        $versionRelLangContent = unserialize($revertedBlock->getVersionRelLangContent());
        $relLangContentRepo = $em->getRepository('\Cx\Modules\Block\Model\Entity\RelLangContent');
        $relLangContentValue = array();
        foreach ($versionRelLangContent as $id => $version) {
            $relLangContent = $relLangContentRepo->findOneBy(
                array(
                    'id' => $id,
                )
            );

            if ($relLangContent) {
                $blockLogRepo = $em->getRepository('Cx\Modules\Block\Model\Entity\LogEntry');
                $revertedRelLangContent = $blockLogRepo->revertEntity($relLangContent, $version);
                array_push(
                    $relLangContentValue,
                    array(
                        'content' => $revertedRelLangContent->getContent(),
                        'locale' => $revertedRelLangContent->getLocale()->getId(),
                    )
                );
            }
        }

        // process rel page global version
        $versionRelPageGlobal = unserialize($revertedBlock->getVersionRelPageGlobal());
        $relPageGlobalRepo = $em->getRepository('\Cx\Modules\Block\Model\Entity\RelPage');
        $relPageGlobalValue = array();
        foreach ($versionRelPageGlobal as $id => $version) {
            $relPageGlobal = $relPageGlobalRepo->findOneBy(
                array(
                    'id' => $id,
                )
            );

            if ($relPageGlobal) {
                $blockLogRepo = $em->getRepository('Cx\Modules\Block\Model\Entity\LogEntry');
                $revertedRelPageGlobal = $blockLogRepo->revertEntity($relPageGlobal, $version);
                array_push(
                    $relPageGlobalValue,
                    array(
                        'page' => $revertedRelPageGlobal->getPage()->getId(),
                    )
                );
            }
        }

        // process rel page category version
        $versionRelPageCategory = unserialize($revertedBlock->getVersionRelPageCategory());
        $relPageCategoryRepo = $em->getRepository('\Cx\Modules\Block\Model\Entity\RelPage');
        $relPageCategoryValue = array();
        foreach ($versionRelPageCategory as $id => $version) {
            $relPageCategory = $relPageCategoryRepo->findOneBy(
                array(
                    'id' => $id,
                )
            );

            if ($relPageCategory) {
                $blockLogRepo = $em->getRepository('Cx\Modules\Block\Model\Entity\LogEntry');
                $revertedRelPageCategory = $blockLogRepo->revertEntity($relPageCategory, $version);
                array_push(
                    $relPageCategoryValue,
                    array(
                        'page' => $revertedRelPageCategory->getPage()->getId(),
                    )
                );
            }
        }

        // process rel page direct version
        $versionRelPageDirect = unserialize($revertedBlock->getVersionRelPageDirect());
        $relPageDirectRepo = $em->getRepository('\Cx\Modules\Block\Model\Entity\RelPage');
        $relPageDirectValue = array();
        foreach ($versionRelPageDirect as $id => $version) {
            $relPageDirect = $relPageDirectRepo->findOneBy(
                array(
                    'id' => $id,
                )
            );

            if ($relPageDirect) {
                $blockLogRepo = $em->getRepository('Cx\Modules\Block\Model\Entity\LogEntry');
                $revertedRelPageDirect = $blockLogRepo->revertEntity($relPageDirect, $version);
                array_push(
                    $relPageDirectValue,
                    array(
                        'page' => $revertedRelPageDirect->getPage()->getId(),
                    )
                );
            }
        }

        // gets all data from block
        $blockVersion = array(
            'id' => $revertedBlock->getId(),
            'start' => $revertedBlock->getStart(),
            'end' => $revertedBlock->getEnd(),
            'name' => $revertedBlock->getName(),
            'random' => $revertedBlock->getRandom(),
            'random2' => $revertedBlock->getRandom2(),
            'random3' => $revertedBlock->getRandom3(),
            'random4' => $revertedBlock->getRandom4(),
            'showInCategory' => $revertedBlock->getShowInCategory(),
            'showInGlobal' => $revertedBlock->getShowInGlobal(),
            'showInDirect' => $revertedBlock->getShowInDirect(),
            'active' => $revertedBlock->getActive(),
            'order' => $revertedBlock->getOrder(),
            'wysiwygEditor' => $revertedBlock->getWysiwygEditor(),
            'category' => $revertedBlock->getCategory()->getId(),
            'targetingOption' => $targetingOptionValue,
            'relLangContent' => $relLangContentValue,
            'relPageGlobal' => $relPageGlobalValue,
            'relPageCategory' => $relPageCategoryValue,
            'relPageDirect' => $relPageDirectValue,
        );

        // return requested block version array
        return $blockVersion;
    }
}
