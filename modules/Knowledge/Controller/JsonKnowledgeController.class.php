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

define('ACCESS_ID_EDIT_CATEGORIES', 133);
define('ACCESS_ID_EDIT_ARTICLES', 131);

/**
 * JsonKnowledgeController
 * Json controller for knowledge module
 *
 * @copyright   Cloudrexx AG
 * @author      Project Team SS4U <info@cloudrexx.com>
 * @package     cloudrexx
 * @subpackage  module_knowledge
 */
class JsonKnowledgeController extends \Cx\Core\Core\Model\Entity\Controller implements \Cx\Core\Json\JsonAdapter {
    /**
     * Returns the internal name used as identifier for this adapter
     *
     * @return String Name of this adapter
     */
    public function getName()
    {
        return parent::getName();
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
            'deleteArticle'
        );
    }

    /**
     * Returns all messages as string
     *
     * @return String HTML encoded error messages
     */
    public function getMessagesAsString()
    {
        return '';
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
        $this->checkAjaxAccess(ACCESS_ID_EDIT_CATEGORIES);
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
            $this->sendAjaxError($e->formatted());
        }

        echo json_encode(array(
            'status'  => 1,
            'message' => $msg
        ));
        exit();
    }

    /**
     * Sort a categories
     *
     * @param array $params Array of GET or POST parameters
     */
    public function sortCategories($params = array())
    {
        $this->checkAjaxAccess(ACCESS_ID_EDIT_CATEGORIES);
        $keys = array_keys($params['post']);
        try {
            $category = new KnowledgeCategory();
            if (preg_match("/ul_[0-9]*/", $keys[0])) {
                foreach ($params['post'][$keys[0]] as $position => $id) {
                    $category->setSort($id, $position);
                }
            }
        } catch (DatabaseError $e) {
            $this->sendAjaxError($e->formatted());
        }

        $langData = \Env::get('init')->loadLanguageData('Knowledge');
        echo json_encode(array(
            'status'  => 1,
            'message' => $langData['TXT_KNOWLEDGE_MSG_SORT']
        ));
        exit();
    }

    /**
     * Delete a category
     *
     * @param array $params Array of GET or POST parameters
     */
    public function deleteCategory($params = array())
    {
        \Permission::checkAccess(ACCESS_ID_EDIT_CATEGORIES, 'static');
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
            $this->sendAjaxError($e->formatted());
        }

        $langData = \Env::get('init')->loadLanguageData('Knowledge');
        echo json_encode(array(
            'status'  => 1,
            'message' => $langData['TXT_KNOWLEDGE_ENTRY_DELETE_SUCCESSFULL']
        ));
        exit();
    }

    /**
     * Sort an articles
     *
     * @param array $params Array of GET or POST parameters
     */
    public function sortArticles($params = array())
    {
        $this->checkAjaxAccess(ACCESS_ID_EDIT_ARTICLES);
        try {
            $articles = new KnowledgeArticles();
            foreach ($params['post']['articlelist'] as $position => $id) {
                $articles->setSort($id, $position);
            }
        } catch (DatabaseError $e) {
            $this->sendAjaxError($e->formatted());
        }

        $langData = \Env::get('init')->loadLanguageData('Knowledge');
        echo json_encode(array(
            'status'  => 1,
            'message' => $langData['TXT_KNOWLEDGE_MSG_SORT']
        ));
    }

    /**
     * Switch article to activate or deactivate
     *
     * @param array $params Array of GET or POST parameters
     */
    public function articleSwitchState($params = array())
    {
        $this->checkAjaxAccess(ACCESS_ID_EDIT_ARTICLES);
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
            $this->sendAjaxError($e->formatted());
        }

        echo json_encode(array(
            'status'  => 1,
            'message' => $msg
        ));
        exit();
    }

    /**
     * Delete an article
     *
     * @param array $params Array of GET or POST parameters
     */
    public function deleteArticle($params = array())
    {
        $this->checkAjaxAccess(ACCESS_ID_EDIT_ARTICLES);
        $id = contrexx_input2int($params['get']['id']);

        try {
            $articles = new KnowledgeArticles();
            $articles->deleteOneArticle($id);
            $tags = new KnowledgeTags();
            $tags->clearTags();
        } catch (DatabaseError $e) {
            $this->sendAjaxError($e->formatted());
        }

        $langData = \Env::get('init')->loadLanguageData('Knowledge');
        echo json_encode(array(
            'status'  => 1,
            'message' => $langData['TXT_KNOWLEDGE_ENTRY_DELETE_SUCCESSFULL']
        ));
        exit();
    }

    /**
     * Check acces for ajax request
     * When the page is ajax requested the response should be
     * different so that the page can display a message that the user
     * hasn't got permissions to do what he tried.
     * Hence, this function returns a JSON object containing a status
     * code (0 for fail) and an error message.
     *
     * @param integer $id Id to check access
     */
    protected function checkAjaxAccess($id)
    {
        $langData = \Env::get('init')->loadLanguageData('Knowledge');
        if (!\Permission::checkAccess($id, 'static', true)) {
            $this->sendAjaxError($langData['TXT_KNOWLEDGE_ACCESS_DENIED']);
        }
    }

    /**
     * Send ajax error message
     * Sends an json object for ajax request to communcate that there has been
     * an error.
     *
     * @param string $message String of message
     */
    protected function sendAjaxError($message)
    {
        echo json_encode(array(
            'status'  => 0,
            'message' => $message
        ));
        exit();
    }
}
