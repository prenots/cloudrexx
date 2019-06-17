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
 * Json controller for knowledge module
 *
 * @copyright   Cloudrexx AG
 * @author      Project Team SS4U <info@cloudrexx.com>
 * @package     cloudrexx
 * @subpackage  module_knowledge
 */
namespace Cx\Modules\Knowledge\Controller;

/**
 * Class KnowledgeJsonException for this Component.
 *
 * @copyright   Cloudrexx AG
 * @author      Project Team SS4U <info@cloudrexx.com>
 * @package     cloudrexx
 * @subpackage  module_knowledge
 */
class KnowledgeJsonException extends \Exception {}

/**
 * Json controller for knowledge module
 *
 * @copyright   Cloudrexx AG
 * @author      Project Team SS4U <info@cloudrexx.com>
 * @package     cloudrexx
 * @subpackage  module_knowledge
 */
class JsonKnowledgeController extends \Cx\Core\Core\Model\Entity\Controller
                              implements \Cx\Core\Json\JsonAdapter {

    /**
     * Status message
     *
     * @var String String of message
     */
    protected $message = '';

    /**
     * Returns the internal name used as identifier for this adapter
     *
     * @return String Name of this adapter
     */
    public function getName()
    {
        return 'Knowledge';
    }

    /**
     * Returns an array of method names accessable from a JSON request
     *
     * @return array List of method names
     */
    public function getAccessableMethods()
    {
        $articlePermissionIds  = array(
            KnowledgeLibrary::ACCESS_ID_KNOWLEDGE,
            KnowledgeLibrary::ACCESS_ID_EDIT_ARTICLES
        );
        $categoryPermissionIds = array(
            KnowledgeLibrary::ACCESS_ID_KNOWLEDGE,
            KnowledgeLibrary::ACCESS_ID_EDIT_CATEGORIES
        );
        $overviewPermissionIds = array(
            KnowledgeLibrary::ACCESS_ID_KNOWLEDGE,
            KnowledgeLibrary::ACCESS_ID_OVERVIEW
        );
        $settingsPermissionIds = array(
            KnowledgeLibrary::ACCESS_ID_KNOWLEDGE,
            KnowledgeLibrary::ACCESS_ID_SETTINGS
        );

        return array(
            'categorySwitchState' => new \Cx\Core_Modules\Access\Model\Entity\Permission(array(), array('get'), true, array(), $categoryPermissionIds),
            'sortCategories' => new \Cx\Core_Modules\Access\Model\Entity\Permission(array(), array('post'), true, array(), $categoryPermissionIds),
            'deleteCategory' => new \Cx\Core_Modules\Access\Model\Entity\Permission(array(), array('get'), true, array(), $categoryPermissionIds),
            'sortArticles' => new \Cx\Core_Modules\Access\Model\Entity\Permission(array(), array('post'), true, array(), $articlePermissionIds),
            'articleSwitchState' => new \Cx\Core_Modules\Access\Model\Entity\Permission(array(), array('get'), true, array(), $articlePermissionIds),
            'deleteArticle' => new \Cx\Core_Modules\Access\Model\Entity\Permission(array(), array('get'), true, array(), $articlePermissionIds),
            'settingsTidyTags' => new \Cx\Core_Modules\Access\Model\Entity\Permission(array(), array('get'), true, array(), $settingsPermissionIds),
            'settingsResetVotes' => new \Cx\Core_Modules\Access\Model\Entity\Permission(array(), array('get'), true, array(), $settingsPermissionIds),
            'getTags' => new \Cx\Core_Modules\Access\Model\Entity\Permission(array(), array('get'), true, array(), $overviewPermissionIds),
            'getArticles' => new \Cx\Core_Modules\Access\Model\Entity\Permission(array(), array('get'), true, array(), $overviewPermissionIds),
            'rate' => new \Cx\Core_Modules\Access\Model\Entity\Permission(array(), array('post'), false),
            'hitArticle',
            'liveSearch'
        );
    }

    /**
     * Return the message
     *
     * @return String Status message
     */
    public function getMessagesAsString()
    {
        return $this->message;
    }

    /**
     * Returns default permission as object
     *
     * @return Object
     */
    public function getDefaultPermissions()
    {
        return new \Cx\Core_Modules\Access\Model\Entity\Permission(array(), array('get'), false);
    }

    /**
     * Switch the category entry status to 'Active' or 'Deactive'
     *
     * @param array $params Array of parameters
     */
    public function categorySwitchState($params = array())
    {
        $id     = contrexx_input2int($params['get']['id']);
        $action = contrexx_input2int($params['get']['switchTo']);

        $langData = $this->getLangData();
        try {
            $category = new KnowledgeCategory();
            if ($action == 1) {
                $category->activate($id);
                $msg = $langData['TXT_KNOWLEDGE_MSG_ACTIVE'];
            } else {
                $category->deactivate($id);
                $msg = $langData['TXT_KNOWLEDGE_MSG_DEACTIVE'];
            }
        } catch (DatabaseError $e) {
            throw new KnowledgeJsonException($e->getMessage());
        }

        $this->message = $msg;
    }

    /**
     * Sort category
     *
     * @param array $params Array of parameters
     */
    public function sortCategories($params = array())
    {
        $keys = array_keys($params['post']);
        try {
            $category = new KnowledgeCategory();
            if (preg_match('/ul_[0-9]*/', $keys[0])) {
                foreach ($params['post'][$keys[0]] as $position => $id) {
                    $category->setSort(contrexx_input2int($id), contrexx_input2int($position));
                }
            }
        } catch (DatabaseError $e) {
            throw new KnowledgeJsonException($e->getMessage());
        }

        $langData = $this->getLangData();
        $this->message = $langData['TXT_KNOWLEDGE_MSG_SORT'];
    }

    /**
     * Delete category
     *
     * @param array $params Array of parameters
     */
    public function deleteCategory($params = array())
    {
        $id = contrexx_input2int($params['get']['id']);
        try {
            $category = new KnowledgeCategory();
            $deletedCategories = $category->deleteCategory($id);
            // delete the articles that were assigned to the deleted categories
            $articles = new KnowledgeArticles();
            foreach ($deletedCategories as $cat) {
                $articles->deleteArticlesByCategory($cat);
            }
            $tags = new KnowledgeTags();
            $tags->clearTags();
        } catch (DatabaseError $e) {
            throw new KnowledgeJsonException($e->getMessage());
        }

        $langData = $this->getLangData();
        $this->message = $langData['TXT_KNOWLEDGE_ENTRY_DELETE_SUCCESSFULL'];
    }

    /**
     * Sort articles
     *
     * @param array $params Array of parameters
     */
    public function sortArticles($params = array())
    {
        try {
            $articles = new KnowledgeArticles();
            foreach ($params['post']['articlelist'] as $position => $id) {
                $articles->setSort(contrexx_input2int($id), contrexx_input2int($position));
            }
        } catch (DatabaseError $e) {
            throw new KnowledgeJsonException($e->getMessage());
        }

        $langData = $this->getLangData();
        $this->message = $langData['TXT_KNOWLEDGE_MSG_SORT'];
    }

    /**
     * Switch the article entry status to 'Active or 'Deactive'
     *
     * @param array $params Array of parameters
     */
    public function articleSwitchState($params = array())
    {
        $id     = contrexx_input2int($params['get']['id']);
        $action = contrexx_input2int($params['get']['switchTo']);

        $langData = $this->getLangData();
        try {
            $articles = new KnowledgeArticles();
            if ($action == 1) {
                $articles->activate($id);
                $msg = $langData['TXT_KNOWLEDGE_MSG_ACTIVE'];
            } else {
                $articles->deactivate($id);
                $msg = $langData['TXT_KNOWLEDGE_MSG_DEACTIVE'];
            }
        } catch (DatabaseError $e) {
            throw new KnowledgeJsonException($e->getMessage());
        }

        $this->message = $msg;
    }

    /**
     * Delete an article
     *
     * @param array $params Array of parameters
     */
    public function deleteArticle($params = array())
    {
        $id = contrexx_input2int($params['get']['id']);

        try {
            $articles = new KnowledgeArticles();
            $articles->deleteOneArticle($id);
            $tags = new KnowledgeTags();
            $tags->clearTags();
        } catch (DatabaseError $e) {
            throw new KnowledgeJsonException($e->getMessage());
        }

        $langData = $this->getLangData();
        $this->message = $langData['TXT_KNOWLEDGE_ENTRY_DELETE_SUCCESSFULL'];
    }

    /**
     * Use this method to remove unnecessary tags
     */
    public function settingsTidyTags()
    {
        try {
            $tags = new KnowledgeTags();
            $tags->tidy();
        } catch (DatabaseError $e) {
            throw new KnowledgeJsonException($e->getMessage());
        }

        $langData = $this->getLangData();
        $this->message = $langData['TXT_KNOWLEDGE_TIDY_TAGS_SUCCESSFUL'];
    }

    /**
     * Reset the 'Vote' statistics
     */
    public function settingsResetVotes()
    {
        try {
            $articles = new KnowledgeArticles();
            $articles->resetVotes();
        } catch (DatabaseError $e) {
            throw new KnowledgeJsonException($e->getMessage());
        }

        $langData = $this->getLangData();
        $this->message = $langData['TXT_KNOWLEDGE_RESET_VOTES_SUCCESSFUL'];
    }

    /**
     * Get the list of 'Tags'
     *
     * @param array $params Array of parameters
     * @return array Array of 'Tags' in HTML and Array format
     */
    public function getTags($params = array())
    {
        $lang = (isset($params['get']['lang']))
            ? contrexx_input2int($params['get']['lang'])
            : \FWLanguage::getDefaultBackendLangId();
        try {
            $knowledgeTags = new KnowledgeTags();
            if ($params['get']['sort'] == 'popularity') {
                $tags = $knowledgeTags->getAllOrderByPopularity($lang);
            } else {
                $tags = $knowledgeTags->getAllOrderAlphabetically($lang);
            }
        } catch (DatabaseError $e) {
            throw new KnowledgeJsonException($e->getMessage());
        }

        $tpl = new \Cx\Core\Html\Sigma(
            $this->getDirectory() . '/View/Template/Backend'
        );
        $tpl->setErrorHandling(PEAR_ERROR_DIE);
        $tpl->loadTemplateFile('module_knowledge_articles_edit_taglist.html');

        $tagList = array();
        $classnumber = 1;
        foreach ($tags as $tag) {
            $tpl->setVariable(array(
                'TAG'         => $tag['name'],
                'TAGID'       => $tag['id'],
                'CLASSNUMBER' => (++$classnumber % 2) + 1,
                'LANG'        => $lang,
            ));
            $tpl->parse('tag');
            $tagList[$tag['id']] = $tag['name'];
        }
        $tpl->parse('taglist');

        return array('html_format' => $tpl->get('taglist'), 'array_format' => $tagList);
    }

    /**
     * Get the list of 'Articles'
     *
     * @param array $params Array of parameters
     * @return array Array of article list in HTML format
     * @global $_LANGID Language id
     */
    public function getArticles($params = array())
    {
        global $_LANGID;

        $id = contrexx_input2int($params['get']['id']);
        $langData = $this->getLangData();

        try {
            $knowledgeLibrary = new KnowledgeLibrary();
            $articles = $knowledgeLibrary->getArticle()->getArticlesByCategory($id);
            $category = $knowledgeLibrary->getCategory()->getOneCategory($id);
        } catch (DatabaseError $e) {
            throw new KnowledgeJsonException($e->getMessage());
        }

        $tpl = new \Cx\Core\Html\Sigma(
            $this->getDirectory() . '/View/Template/Backend/'
        );
        $tpl->setErrorHandling(PEAR_ERROR_DIE);
        $tpl->loadTemplateFile('module_knowledge_articles_overview_articlelist.html');
        \Cx\Core\Csrf\Controller\Csrf::add_placeholder($tpl);

        $tpl->setGlobalVariable(array(
            // language variables
            'TXT_VIEWED'        => $langData['TXT_KNOWLEDGE_VIEWED'],
            'TXT_SORT'          => $langData['TXT_KNOWLEDGE_SORT'],
            'TXT_STATE'         => $langData['TXT_KNOWLEDGE_STATE'],
            'TXT_QUESTION'      => $langData['TXT_KNOWLEDGE_QUESTION'],
            'TXT_HITS'          => $langData['TXT_KNOWLEDGE_HITS'],
            'TXT_RATING'        => $langData['TXT_KNOWLEDGE_RATING'],
            'TXT_ACTIONS'       => $langData['TXT_KNOWLEDGE_ACTIONS'],
            'TXT_CATEGORY_NAME' => $category['content'][$_LANGID]['name'] ,
            // getPaging(count, position, extraargv, paging-text, showeverytime, limit)
            //"PAGING"            => getPaging()
            'TXT_BY'            => $langData['TXT_KNOWLEDGE_AT'],
            'TXT_VOTINGS'       => $langData['TXT_KNOWLEDGE_VOTERS']
        ));

        if (!empty($articles)) {
            $settings = new KnowledgeSettings();
            foreach ($articles as $key => $article) {
                $tpl->setVariable(array(
                    'ARTICLEID'             => $key,
                    'QUESTION'              => contrexx_raw2xhtml($article['content'][$_LANGID]['question']),
                    'ACTIVE_STATE'          => abs($article['active']-1),
                    'CATEGORY_ACTIVE_LED'   => ($article['active']) ? 'green' : 'red',
                    'HITS'                  => $article['hits'],
                    'VOTEVALUE'             => round(
                        (($article['votes'] > 0) ? $article['votevalue'] / $article['votes'] : 0), 2
                    ),
                    'VOTECOUNT'             => $article['votes'],
                    'MAX_RATING'            => $settings->get('max_rating')
                ));
                $tpl->parse('row');
            }
        } else {
            $tpl->setVariable(array(
                'TXT_NO_ARTICLES' => $langData['TXT_KNOWLEDGE_NO_ARTICLES_IN_CAT']
            ));
            $tpl->parse('no_articles');
        }

        $tpl->parse('content');

        return array('list' => $tpl->get('content'));
    }

    /**
     * Get the 'Language' data based on component
     *
     * @return array Array of language data
     */
    protected function getLangData()
    {
        return \Env::get('init')->getComponentSpecificLanguageData(
            $this->getName(),
            false
        );
    }

    /**
     * Rate an Article
     *
     * @param array $params Array of parameters
     */
    public function rate($params = array())
    {
        $id    = contrexx_input2int($params['post']['id']);
        $rated = contrexx_input2int($params['post']['rated']);
        if (!isset($_COOKIE['knowledge_rating_' . $id])) {
            try {
                $knowledgeArticles = new KnowledgeArticles();
                $knowledgeArticles->vote($id, $rated);
            } catch (DatabaseError $e) {
                throw new KnowledgeJsonException($e->getMessage());
            }
        }
    }

    /**
     * Hit an Article
     *
     * @param array $params Array of parameters
     */
    public function hitArticle($params = array())
    {
        $id = contrexx_input2int($params['get']['id']);
        try {
            $knowledgeArticles = new KnowledgeArticles();
            $knowledgeArticles->hit($id);
        } catch (DatabaseError $e) {
            throw new KnowledgeJsonException($e->getMessage());
        }
    }

    /**
     * Live Search
     *
     * @return array Array of search result in JSON data
     */
    public function liveSearch()
    {
        $search = new Search();
        return $search->performSearch();
    }
}
