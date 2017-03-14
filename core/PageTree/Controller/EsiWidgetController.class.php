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
 * Class EsiWidgetController
 *
 * @copyright   CLOUDREXX CMS - Cloudrexx AG Thun
 * @author      Project Team SS4U <info@comvation.com>
 * @package     cloudrexx
 * @subpackage  core_pagetree
 * @version     1.0.0
 */

namespace Cx\Core\PageTree\Controller;

/**
 * JsonAdapter Controller to handle EsiWidgets
 * Usage:
 * - Create a subclass that implements parseWidget()
 * - Register it as a Controller in your ComponentController
 *
 * @copyright   CLOUDREXX CMS - Cloudrexx AG Thun
 * @author      Project Team SS4U <info@comvation.com>
 * @package     cloudrexx
 * @subpackage  core_pagetree
 * @version     1.0.0
 */

class EsiWidgetController extends \Cx\Core_Modules\Widget\Controller\EsiWidgetController {
    /**
     * currentThemeId
     *
     * @var integer
     */
    protected $currentThemeId;

    /**
     * current page ID
     *
     * @var integer
     */
    protected $currentPageId;

    /**
     * Parses a widget
     *
     * @param string              $name     Widget name
     * @param \Cx\Core\Html\Sigma $template Widget template
     * @param string              $locale   RFC 3066 locale identifier
     */
    public function parseWidget($name, $template, $locale)
    {
        $em       = $this->cx->getDb()->getEntityManager();
        $pageRepo = $em->getRepository('\Cx\Core\ContentManager\Model\Entity\Page');
        $page     = $pageRepo->find($this->currentPageId);
        if (!$page) {
            return;
        }
        $navbar = new \Navigation($page->getId(), $page);
        if ($name == 'NAVTREE') {
            $template->setVariable($name, $navbar->getTrail());
            return;
        }

        $themeRepository = new \Cx\Core\View\Model\Repository\ThemeRepository();
        $theme           = $themeRepository->findById($this->currentThemeId);
        if (!$theme) {
            return;
        }

        $matches = null;
        if (preg_match('/^((?:SUB)?NAVBAR)(\d{0,2})_FILE$/', $name, $matches)) {
            $content = $this->getFileContent(
                $theme,
                strtolower($matches[1]) . $matches[2] . '.html'
            );
            if (!$content) {
                return;
            }
            $method = 'getNavigation';
            if ($matches[1] == 'SUBNAVBAR') {
                $method = 'getSubnavigation';
            }
            $navBarContent = $navbar->$method(
                $content,
                $this->cx->getLicense()
            );
            $template->setVariable($name, $navBarContent);
        }
    }

    /**
     * Returns the content of a widget
     *
     * @param array $params JsonAdapter parameters
     *
     * @return array Content in an associative array
     */
    public function getWidget($params)
    {
        if (isset($params['get'])) {
            if (isset($params['get']['theme'])) {
                $this->currentThemeId = $params['get']['theme'];
            }
            if (isset($params['get']['page'])) {
                $this->currentPageId = $params['get']['page'];
            }
        }
        return parent::getWidget($params);
    }

    /**
     * Get file content
     *
     * @param \Cx\Core\View\Model\Entity\Theme $theme    theme object
     * @param string                           $fileName name of the file
     *
     * @return string
     */
    protected function getFileContent($theme, $fileName)
    {
        if (!($theme instanceof \Cx\Core\View\Model\Entity\Theme)) {
            return;
        }

        return file_get_contents(
            $theme->getFilePath($theme->getFolderName() . '/' . $fileName)
        );
    }
}
