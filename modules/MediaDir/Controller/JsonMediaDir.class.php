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
 * JsonMediaDir
 * Json controller for MediaDir component
 *
 * @copyright   Cloudrexx AG
 * @author      Project Team SS4U <info@cloudrexx.com>
 * @package     cloudrexx
 * @subpackage  module_mediadir
 */

namespace Cx\Modules\MediaDir\Controller;

class JsonMediaDirException extends \Exception {}

/**
 * JsonMediaDir
 * Json controller for MediaDir component
 *
 * @copyright   Cloudrexx AG
 * @author      Project Team SS4U <info@cloudrexx.com>
 * @package     cloudrexx
 * @subpackage  module_mediadir
 */
class JsonMediaDir extends \Cx\Core\Core\Model\Entity\Controller implements \Cx\Core\Json\JsonAdapter
{
    /**
     * List of messages
     *
     * @var Array
     */
    protected $messages = array();

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
            'getNavigationPlacholder',
            'getLatestPlacholder',
            'getHeadlines',
            'getLatestEntries'
        );
    }

    /**
     * Returns all messages as string
     *
     * @return String HTML encoded error messages
     */
    public function getMessagesAsString()
    {
        return implode('<br />', $this->messages);
    }

    /**
     * Returns default permission as object
     *
     * @return Object
     */
    public function getDefaultPermissions()
    {
        return new \Cx\Core_Modules\Access\Model\Entity\Permission(
            null,
            null,
            false
        );
    }

    /**
     * Get the navigation placeholder
     *
     * @param array $params all given params from http request
     *
     * @return array content of navigation placeholder
     */
    public function getNavigationPlacholder($params)
    {
        $lang = isset($params['get']['lang']) ?
            contrexx_input2int($params['get']['lang']) : 0;
        if (empty($lang)) {
            return array('content' => '');
        }

        $mediaDirPlaceholders = new MediaDirectoryPlaceholders($this->getName());
        return array(
            'content' => $mediaDirPlaceholders->getNavigationPlacholder($lang)
        );
    }

    /**
     * Get the latest placeholders
     *
     * @param array $params all given params from http request
     *
     * @return array content of latest placeholders
     */
    public function getLatestPlacholder($params)
    {
        $lang = isset($params['get']['lang']) ?
            contrexx_input2int($params['get']['lang']) : 0;
        if (empty($lang)) {
            return array('content' => '');
        }

        $mediaDirPlaceholders = new MediaDirectoryPlaceholders($this->getName());
        return array(
            'content' => $mediaDirPlaceholders->getLatestPlacholder($lang)
        );
    }

    /**
     * Get headlines
     *
     * @param array $params all given params from http request
     *
     * @return array headline entries
     */
    public function getHeadlines($params)
    {
        global $objInit;

        $file        =  !empty($params['get']['file'])
                ? contrexx_input2raw($params['get']['file']) : '';
        $blockId     =  !empty($params['get']['blockId'])
                ? contrexx_input2int($params['get']['blockId']) : 0;
        $langId      =  !empty($params['get']['lang'])
                ? contrexx_input2int($params['get']['lang']) : 0;
        $position    =  !empty($params['get']['position'])
                ? contrexx_input2int($params['get']['position']) : 0;
        $totalBlocks =  !empty($params['get']['totalBlocks'])
                ? contrexx_input2int($params['get']['totalBlocks']) : 0;
        if (
            empty($blockId) ||
            empty($langId) ||
            empty($position) ||
            empty($totalBlocks)
        ) {
            return array('content' => '');
        }

        try {
            $theme   = $this->getThemeFromInput($params);
            $content = $theme->getContentBlockFromTpl(
                $file,
                'mediadirLatest_row_' . $blockId,
                true
            );

            if (empty($content)) {
                return array('content' => '');
            }

            $objInit->loadLanguageData($this->getName());
            $template = new \Cx\Core\Html\Sigma();
            $template->setTemplate($content);
            $mediadir = new MediaDirectory('', $this->getName());
            $mediadir->getHeadlines(
                $template,
                $blockId,
                $position,
                $totalBlocks,
                $langId
            );
            return array('content' => $template->get());
        } catch (\Exception $ex) {
            \DBG::dump($ex->getMessage());
            return array('content' => '');
        }
    }

    /**
     * Get the latest entries
     *
     * @param array $params all given params from http request
     *
     * @return array List of latest entries
     */
    public function getLatestEntries($params)
    {
        $file    =  !empty($params['get']['file'])
                ? contrexx_input2raw($params['get']['file']) : '';
        $block   =  !empty($params['get']['block'])
                ? contrexx_input2raw($params['get']['block']) : '';
        $langId  =  !empty($params['get']['lang'])
                ? contrexx_input2int($params['get']['lang']) : 0;
        $formId  =  !empty($params['get']['formId'])
                ? contrexx_input2int($params['get']['formId']) : 0;
        if (empty($langId)) {
            return array('content' => '');
        }

        try {
            $theme   = $this->getThemeFromInput($params);
            $content = $theme->getContentBlockFromTpl($file, $block, true);
            if (empty($content)) {
                return array('content' => '');
            }

            $template = new \Cx\Core\Html\Sigma();
            $template->setTemplate($content);
            $mediadir = new MediaDirectory('', $this->getName());
            $mediadir->getLatestEntries(
                $template,
                $formId,
                $block,
                $langId
            );
            return array('content' => $template->get());
        } catch (\Exception $ex) {
            \DBG::log($ex->getMessage());
            return array('content' => '');
        }
    }

    /**
     * Get theme from the user input
     *
     * @param array $params User input array
     *
     * @return \Cx\Core\View\Model\Entity\Theme Theme instance
     * @throws JsonMediaDirException When theme id empty or theme does not exits in the system
     */
    protected function getThemeFromInput($params)
    {
        $themeId  = !empty($params['get']['template'])
            ? contrexx_input2int($params['get']['template']) : 0;
        if (empty($themeId)) {
            throw new JsonMediaDirException('The theme id is empty in the request');
        }
        $themeRepository = new \Cx\Core\View\Model\Repository\ThemeRepository();
        $theme           = $themeRepository->findById($themeId);
        if (!$theme) {
            throw new JsonMediaDirException('The theme id '. $themeId .' does not exists.');
        }
        return $theme;
    }
}