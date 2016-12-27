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
 * KnowledgeInterface
 *
 * @copyright   CLOUDREXX CMS - CLOUDREXX AG
 * @author      CLOUDREXX Development Team <info@cloudrexx.com>
 * @package     cloudrexx
 * @subpackage  module_knowledge
 */

namespace Cx\Modules\Knowledge\Controller;

/**
 * KnowledgeInterface
 *
 * @copyright   CLOUDREXX CMS - CLOUDREXX AG
 * @author      CLOUDREXX Development Team <info@cloudrexx.com>
 * @package     cloudrexx
 * @subpackage  module_knowledge
 */
class KnowledgeInterface extends KnowledgeLibrary
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Replace the placeholders with content
     *
     * @param string                                    $content template content
     * @param \Cx\Core\ContentManager\Model\Entity\Page $page    page object
     */
    public function parse(&$content, $page = null)
    {
        $cache = \Env::get('cx')->getComponent('Cache');
        $this->parseContent(
            $cache,
            '{KNOWLEDGE_TAG_CLOUD}',
            $content,
            'getTagCloud'
        );
        $this->parseContent(
            $cache,
            '{KNOWLEDGE_MOST_READ}',
            $content,
            'getMostRead'
        );
        $this->parseContent(
            $cache,
            '{KNOWLEDGE_BEST_RATED}',
            $content,
            'getBestRated'
        );

        if ($page instanceof \Cx\Core\ContentManager\Model\Entity\Page) {
            $page->setContent($content);
        }
    }

    /**
     * Parse the content
     *
     * @param \Cx\Core_Modules\Cache\Controller\Cache $cache       cache object
     * @param string                                  $placeholder pattern
     * @param string                                  $content     template content
     * @param string                                  $methodName  name of the method
     *
     * @return null
     */
    public function parseContent(
        \Cx\Core_Modules\Cache\Controller\ComponentController $cache,
        $placeholder,
        &$content,
        $methodName
    ) {
        if (empty($placeholder) || empty($methodName)) {
            return;
        }

        $pattern = '/\{' . $placeholder . '\}/i';
        if (!preg_match($pattern, $content)) {
            return;
        }
        global $_LANGID;

        $content = preg_replace(
            $pattern,
            $cache->getEsiContent(
                'Knowledge',
                'getArticlesOrTags',
                array('lang' => $_LANGID, 'method' => $methodName)
            ),
            $content
        );
    }

    /**
     * Return a tag cloud
     *
     * @param integer $langId lang ID
     *
     * @return string content of tag
     */
    public function getTagCloud($langId = null)
    {
        global $_LANGID;

        if (!$langId) {
            $langId = $_LANGID;
        }

        $tpl = new \Cx\Core\Html\Sigma();
        \Cx\Core\Csrf\Controller\Csrf::add_placeholder($tpl);
        $tpl->setErrorHandling(PEAR_ERROR_DIE);
        $template = $this->settings->formatTemplate(
            $this->settings->get('tag_cloud_sidebar_template')
        );
        $tpl->setTemplate($template);

        try {
            $tags_pop = $this->tags->getAllOrderByPopularity($langId, true);
            $tags     = $this->tags->getAll($langId, true);
        } catch (DatabaseError $e) {
            echo $e->plain();
        }

        $tagCloud = new TagCloud();
        $tagCloud->setTags($tags);
        $tagCloud->setTagVals(
            $tags_pop[0]['popularity'],
            $tags_pop[count($tags_pop) - 1]['popularity']
        );
        $tagCloud->setFont(20, 10);
        $tagCloud->setUrlFormat(
            'index.php?section=Knowledge' . MODULE_INDEX . '&amp;tid=%id'
        );
        $tpl->setVariable('CLOUD', $tagCloud->getCloud());

        //$tpl->parse("cloud");
        return $tpl->get();
    }

    /**
     * Return the best rated articles
     *
     * @param integer $langId lang ID
     *
     * @return null|string content of best related articles
     */
    public function getBestRated($langId = null)
    {
        global $_LANGID;

        if (!$langId) {
            $langId = $_LANGID;
        }
        try {
            $articles = $this->articles->getBestRated(
                $langId,
                $this->settings->get('best_rated_sidebar_amount')
            );
        } catch (DatabaseError $e) {
            return;
        }

        $template = $this->settings->formatTemplate(
            $this->settings->get('best_rated_sidebar_template')
        );

        $objTemplate = new \Cx\Core\Html\Sigma(ASCMS_THEMES_PATH);
        \Cx\Core\Csrf\Controller\Csrf::add_placeholder($objTemplate);
        $objTemplate->setErrorHandling(PEAR_ERROR_DIE);
        $objTemplate->setTemplate($template);

        $max_length = $this->settings->get('best_rated_sidebar_length');
        foreach ($articles as $key => $article) {
            $question = $article['content'][$langId]['question'];
            if (strlen($question) >= $max_length) {
                $question = substr($question, 0, $max_length-3) . '...';
            }
            $objTemplate->setVariable(array(
                'URL'     => 'index.php?section=Knowledge&amp;cmd=article&amp;id=' . $key,
                'ARTICLE' => $question
            ));
            $objTemplate->parse('article');
        }

        return $objTemplate->get();
    }

    /**
     * Get the most viewed articles
     *
     * @param integer $langId lang ID
     *
     * @return null|string content of most viewed articles
     */
    public function getMostRead($langId = null)
    {
        global $_LANGID;

        if (!$langId) {
            $langId = $_LANGID;
        }
        try {
            $articles = $this->articles->getMostRead(
                $langId,
                $this->settings->get('best_rated_sidebar_amount')
            );
        } catch (DatabaseError $e) {
            return;
        }

        $template = $this->settings->formatTemplate(
            $this->settings->get('most_read_sidebar_template')
        );

        $objTemplate = new \Cx\Core\Html\Sigma(ASCMS_THEMES_PATH);
        \Cx\Core\Csrf\Controller\Csrf::add_placeholder($objTemplate);
        $objTemplate->setErrorHandling(PEAR_ERROR_DIE);
        $objTemplate->setTemplate($template);

        $max_length = $this->settings->get('most_read_sidebar_length');
        foreach ($articles as $key => $article) {
            $question = $article['content'][$langId]['question'];
            if (strlen($question) >= $max_length) {
                $question = substr($question, 0, $max_length-3) . '...';
            }
            $objTemplate->setVariable(array(
                'URL'     => 'index.php?section=Knowledge&amp;cmd=article&amp;id=' . $key,
                'ARTICLE' => $question
            ));
            $objTemplate->parse("article");
        }

        return $objTemplate->get();
    }
}
