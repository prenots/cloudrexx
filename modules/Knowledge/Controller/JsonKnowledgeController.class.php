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
 * JsonKnowledgeController
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
 * JsonKnowledgeController
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
        return array(
            'categorySwitchState',
            'sortCategories',
            'deleteCategory',
            'sortArticles',
            'articleSwitchState',
            'deleteArticle',
            'settingsTidyTags',
            'settingsResetVotes',
            'getTags',
            'getArticles',
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
        return new \Cx\Core_Modules\Access\Model\Entity\Permission(null, null, false);
    }

    /**
     * Switch category to activate or deactivate
     *
     * @param array $params Array of GET or POST parameters
     */
    public function categorySwitchState($params = array())
    {
        $this->checkAjaxAccess(KnowledgeLibrary::ACCESS_ID_EDIT_CATEGORIES);

        $id     = contrexx_input2int($params['get']['id']);
        $action = contrexx_input2int($params['get']['switchTo']);

        $langData = \Env::get('init')->loadLanguageData('Knowledge');
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
     * Sort a categories
     *
     * @param array $params Array of GET or POST parameters
     */
    public function sortCategories($params = array())
    {
        $this->checkAjaxAccess(KnowledgeLibrary::ACCESS_ID_EDIT_CATEGORIES);

        $keys = array_keys($params['post']);
        try {
            $category = new KnowledgeCategory();
            if (preg_match("/ul_[0-9]*/", $keys[0])) {
                foreach ($params['post'][$keys[0]] as $position => $id) {
                    $category->setSort($id, $position);
                }
            }
        } catch (DatabaseError $e) {
            throw new KnowledgeJsonException($e->getMessage());
        }

        $langData = \Env::get('init')->loadLanguageData('Knowledge');
        $this->message = $langData['TXT_KNOWLEDGE_MSG_SORT'];
    }

    /**
     * Delete a category
     *
     * @param array $params Array of GET or POST parameters
     */
    public function deleteCategory($params = array())
    {
        $this->checkAjaxAccess(KnowledgeLibrary::ACCESS_ID_EDIT_CATEGORIES);

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

        $langData = \Env::get('init')->loadLanguageData('Knowledge');
        $this->message = $langData['TXT_KNOWLEDGE_ENTRY_DELETE_SUCCESSFULL'];
    }

    /**
     * Sort an articles
     *
     * @param array $params Array of GET or POST parameters
     */
    public function sortArticles($params = array())
    {
        $this->checkAjaxAccess(KnowledgeLibrary::ACCESS_ID_EDIT_ARTICLES);

        try {
            $articles = new KnowledgeArticles();
            foreach ($params['post']['articlelist'] as $position => $id) {
                $articles->setSort($id, $position);
            }
        } catch (DatabaseError $e) {
            throw new KnowledgeJsonException($e->getMessage());
        }

        $langData = \Env::get('init')->loadLanguageData('Knowledge');
        $this->message = $langData['TXT_KNOWLEDGE_MSG_SORT'];
    }

    /**
     * Switch article to activate or deactivate
     *
     * @param array $params Array of GET or POST parameters
     */
    public function articleSwitchState($params = array())
    {
        $this->checkAjaxAccess(KnowledgeLibrary::ACCESS_ID_EDIT_ARTICLES);

        $id     = contrexx_input2int($params['get']['id']);
        $action = contrexx_input2int($params['get']['switchTo']);

        $langData = \Env::get('init')->loadLanguageData('Knowledge');
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
     * @param array $params Array of GET or POST parameters
     */
    public function deleteArticle($params = array())
    {
        $this->checkAjaxAccess(KnowledgeLibrary::ACCESS_ID_EDIT_ARTICLES);

        $id = contrexx_input2int($params['get']['id']);

        try {
            $articles = new KnowledgeArticles();
            $articles->deleteOneArticle($id);
            $tags = new KnowledgeTags();
            $tags->clearTags();
        } catch (DatabaseError $e) {
            throw new KnowledgeJsonException($e->getMessage());
        }

        $langData = \Env::get('init')->loadLanguageData('Knowledge');
        $this->message = $langData['TXT_KNOWLEDGE_ENTRY_DELETE_SUCCESSFULL'];
    }

    /**
     * Check access for AJAX request
     * When the page is AJAX requested the response should be
     * different so that the page can display a message that the user
     * hasn't got permissions to do what he tried.
     * Hence, this function returns a JSON object containing a status
     * code (0 for fail) and an error message.
     *
     * @param integer $id Id to check access
     */
    protected function checkAjaxAccess($id)
    {
        if (!\Permission::checkAccess($id, 'static', true)) {
            $langData = \Env::get('init')->loadLanguageData('Knowledge');
            throw new KnowledgeJsonException($langData['TXT_KNOWLEDGE_ACCESS_DENIED']);
        }
    }

    /**
     * Tidy the tags
     * Call the function to remove unnecessary tags
     */
    public function settingsTidyTags()
    {
        try {
            $tags = new KnowledgeTags();
            $tags->tidy();
        } catch (DatabaseError $e) {
            throw new KnowledgeJsonException($e->getMessage());
        }

        $langData = \Env::get('init')->loadLanguageData('Knowledge');
        $this->message = $langData['TXT_KNOWLEDGE_TIDY_TAGS_SUCCESSFUL'];
    }

    /**
     * Reset the vote statistics
     */
    public function settingsResetVotes()
    {
        try {
            $articles = new KnowledgeArticles();
            $articles->resetVotes();
        } catch (DatabaseError $e) {
            throw new KnowledgeJsonException($e->getMessage());
        }

        $langData = \Env::get('init')->loadLanguageData('Knowledge');
        $this->message = $langData['TXT_KNOWLEDGE_RESET_VOTES_SUCCESSFUL'];
    }

    /**
     * Get list of tags
     *
     * @param array $params Array of GET or POST parameters
     * @return array Array of tags in JSON data
     */
    public function getTags($params = array())
    {
        $this->checkAjaxAccess(KnowledgeLibrary::ACCESS_ID_OVERVIEW);

        $lang = (isset($params['get']['lang'])) ? $params['get']['lang'] : 1;
        try {
            $knowledgeTags = new KnowledgeTags();
            if ($params['get']['sort'] === 'popularity') {
                $tags = $knowledgeTags->getAllOrderByPopularity($lang);
            } else {
                $tags = $knowledgeTags->getAllOrderAlphabetically($lang);
            }
        } catch (DatabaseError $e) {
            throw new KnowledgeJsonException($e->getMessage());
        }

        $tpl = new \Cx\Core\Html\Sigma(
            $this->cx->getCodeBaseModulePath() . '/Knowledge/View/Template/Backend'
        );
        \Cx\Core\Csrf\Controller\Csrf::add_placeholder($tpl);
        $tpl->setErrorHandling(PEAR_ERROR_DIE);
        $tpl->loadTemplateFile('module_knowledge_articles_edit_taglist.html');

        $return_tags = array();
        $classnumber = 1;
        foreach ($tags as $tag) {
            $tpl->setVariable(array(
                'TAG'         => $tag['name'],
                'TAGID'       => $tag['id'],
                'CLASSNUMBER' => (++$classnumber % 2) + 1,
                'LANG'        => $lang,
            ));
            $tpl->parse('tag');
            $return_tags[$tag['id']] = $tag['name'];
        }
        $tpl->parse('taglist');
        $taglist = $tpl->get('taglist');

        return array('html' => $taglist, 'available_tags' => $return_tags);
    }

    /**
     * Get list of articles
     *
     * @param array $params Array of GET or POST parameters
     * @return array Array of articles in JSON data
     */
    public function getArticles($params = array())
    {
        global $_LANGID;

        $this->checkAjaxAccess(KnowledgeLibrary::ACCESS_ID_OVERVIEW);

        $id = contrexx_input2int($params['get']['id']);
        $langData = \Env::get('init')->loadLanguageData('Knowledge');

        try {
            $knowledgeArticles = new KnowledgeArticles();
            $articles = $knowledgeArticles->getArticlesByCategory($id);
            $knowledgeCategory = new KnowledgeCategory();
            $category = $knowledgeCategory->getOneCategory($id);
        } catch (DatabaseError $e) {
            throw new KnowledgeJsonException($e->getMessage());
        }

        $tpl = new \Cx\Core\Html\Sigma(
            $this->cx->getCodeBaseModulePath() . '/Knowledge/View/Template/Backend/'
        );
        $tpl->setErrorHandling(PEAR_ERROR_DIE);
        $tpl->loadTemplateFile('module_knowledge_articles_overview_articlelist.html');
        \Cx\Core\Csrf\Controller\Csrf::add_placeholder($tpl);

        $tpl->setGlobalVariable(array(
            // language variables
            'TXT_NAME'          => $langData['TXT_NAME'],
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
}
