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
 * JsonMarket
 * Json controller for market module
 *
 * @copyright   Cloudrexx AG
 * @author      Project Team SS4U <info@cloudrexx.com>
 * @package     cloudrexx
 * @subpackage  module_market
 */

namespace Cx\Modules\Market\Controller;
use \Cx\Core\Json\JsonAdapter;

class JsonMarketException extends \Exception {};

/**
 * JsonMarket
 * Json controller for market module
 *
 * @copyright   Cloudrexx AG
 * @author      Project Team SS4U <info@cloudrexx.com>
 * @package     cloudrexx
 * @subpackage  module_market
 */
class JsonMarket implements JsonAdapter {
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
        return array('getMarketLatest');
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
        return 'Market';
//        return parent::getName();
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
     * 
     *
     * @param array $params
     */
    public function getMarketLatest($params)
    {
        global $objTemplate, $_ARRAYLANG, $_CORELANG;
        $pageId =  !empty($params['get']['page'])
                 ? contrexx_input2int($params['get']['page']) : 0;
        if (!empty($pageId)) {
            $pageRepo = $this->cx
                             ->getDb()
                             ->getEntityManager()
                             ->getRepository('Cx\Core\ContentManager\Model\Entity\Page');
            $result = $pageRepo->findOneById($pageId);
            if (!$result) {
                return array('content' => '');
            }
            $page    = $result[0];
            $matches = null;
            if (preg_match(
                '/<!--\s+BEGIN\s+(marketLatest)\s+-->(.*)<!--\s+END\s+\1\s+-->/s',
                $page->getContent(),
                $matches
            )) {
                $content = $matches[2];
            }
        } else {
            $content = $this->getMarketContentBlock(
                $params,
                'marketLatest'
            );
        }
        $template = new \Cx\Core\Html\Sigma();
        $template->setTemplate($content);
        $objMarket = new Market('');
        $objMarket->getBlockLatest($template);
        return array('content' => $template->get());
    }

    /**
     * 
     *
     * @param array $params
     */
    public function getMarketContentBlock(
        $params = array(),
        $block = ''
    ) {
        try {
            $theme = $this->getThemeFromInput($params);
            $file  =  !empty($params['get']['file'])
                    ? contrexx_input2raw($params['get']['file']) : '';
            if (empty($file)) {
                throw new JsonMarketException(
                    __METHOD__ .': the input file cannot be empty'
                );
            }
            $content = $theme->getContentFromFile($file);
           
            $matches = null;
            if (   $content
                && preg_match(
                    '/<!--\s+BEGIN\s+('. $block .')\s+-->(.*)<!--\s+END\s+\1\s+-->/s',
                    $content,
                    $matches
                )
            ) {
                return $matches[2];
            }
        } catch (\Exception $ex) {
            \DBG::log($ex->getMessage());
        }
        throw new JsonMarketException('The block '. $block .' not exists');
    }
    
    /**
     * Get theme from the user input
     *
     * @param array $params User input array
     *
     * @return \Cx\Core\View\Model\Entity\Theme Theme instance
     * @throws JsonMarketException When theme id empty or theme does not exits in the system
     */
    protected function getThemeFromInput($params)
    {
        $themeId  = !empty($params['get']['theme'])
                ? contrexx_input2int($params['get']['theme'])
                : 0;
        if (empty($themeId)) {
            throw new JsonMarketException('The theme id is empty in the request');
        }
        $themeRepository = new \Cx\Core\View\Model\Repository\ThemeRepository();
        $theme           = $themeRepository->findById($themeId);
        if (!$theme) {
            throw new JsonMarketException('The theme id '. $themeId .' does not exists.');
        }
        return $theme;
    }
}