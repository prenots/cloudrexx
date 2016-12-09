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
 * JsonForum
 * Json controller for forum module
 *
 * @copyright   Cloudrexx AG
 * @author      Project Team SS4U <info@cloudrexx.com>
 * @package     cloudrexx
 * @subpackage  module_forum
 */

namespace Cx\Modules\Forum\Controller;
use \Cx\Core\Json\JsonAdapter;

class JsonForumException extends \Exception {};

/**
 * JsonForum
 * Json controller for forum module
 *
 * @copyright   Cloudrexx AG
 * @author      Project Team SS4U <info@cloudrexx.com>
 * @package     cloudrexx
 * @subpackage  module_forum
 */
class JsonForum implements JsonAdapter {
    /**
     * List of messages
     * @var Array
     */
    private $messages = array();

    /**
     * Returns an array of method names accessable from a JSON request
     * @return array List of method names
     */
    public function getAccessableMethods()
    {
        return array('getForumContent', 'getForumHomeTagCloud');
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
     * Wrapper to __call()
     * @return string ComponentName
     */
    public function getName()
    {
        return 'Forum';
    }

    /**
     * Returns default permission as object
     * @return Object
     */
    public function getDefaultPermissions()
    {
        return new \Cx\Core_Modules\Access\Model\Entity\Permission(
            null, null, false
        );
    }

    /**
     * Get Forum tag content
     *
     * @param array $params
     */
    public function getForumHomeTagCloud($params)
    {
        $content = $this->getForumTemplateContent($params);
        $objForum = new ForumHomeContent($content);
        if (empty($content)){
            return array('content' => '');
        }
        return array(
            'content' => $objForum->getHomeTagCloud()
        );
    }

    /**
     * Get Forum content
     *
     * @param array $params
     */
    public function getForumContent($params)
    {
        $langId = isset($params['get']['lang'])
            ? contrexx_input2int($params['get']['lang']) : 0;

        $content = $this->getForumTemplateContent($params);
        $objForum = new ForumHomeContent($content);
        if (empty($content)){
            return array('content' => '');
        }
        return array(
            'content' => $objForum->getContent($langId)
        );
    }

    /**
     * Get Forum template content
     *
     * @param array $params
     */
    public function getForumTemplateContent($params)
    {
        global $_ARRAYLANG, $objInit;

        $pageId = isset($params['get']['page'])
            ? contrexx_input2int($params['get']['page']) : 0;
        $file   = !empty($params['get']['file'])
            ? contrexx_input2raw($params['get']['file']) : '';
        $_ARRAYLANG = array_merge(
                $_ARRAYLANG,
                $objInit->loadLanguageData('Forum')
        );
        if (!empty($pageId)) {
            $pageRepo = $this->cx
                ->getDb()
                ->getEntityManager()
                ->getRepository('Cx\Core\ContentManager\Model\Entity\Page');
            $result = $pageRepo->findOneById($pageId);
            if (!$result) {
                return '';
            }
            return $result->getContent();
        } else {
            $theme = $this->getThemeFromInput($params);
            return $theme->getContentFromFile($file);
        }
        return '';
    }

    /**
     * Get theme from the user input
     *
     * @param array $params User input array
     *
     * @return \Cx\Core\View\Model\Entity\Theme Theme instance
     * @throws JsonForumException When theme id empty or theme does not exits in the system
     */
    protected function getThemeFromInput($params)
    {
        $themeId  = !empty($params['get']['theme'])
                ? contrexx_input2int($params['get']['theme'])
                : 0;
        if (empty($themeId)) {
            throw new JsonForumException('The theme id is empty in the request');
        }
        $themeRepository = new \Cx\Core\View\Model\Repository\ThemeRepository();
        $theme           = $themeRepository->findById($themeId);
        if (!$theme) {
            throw new JsonForumException('The theme id '. $themeId .' does not exists.');
        }
        return $theme;
    }
}