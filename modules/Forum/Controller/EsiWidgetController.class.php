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
 * @author      Project Team SS4U <info@cloudrexx.com>
 * @package     cloudrexx
 * @subpackage  module_forum
 * @version     1.0.0
 */

namespace Cx\Modules\Forum\Controller;

/**
 * JsonAdapter Controller to handle EsiWidgets
 * Usage:
 * - Create a subclass that implements parseWidget()
 * - Register it as a Controller in your ComponentController
 *
 * @copyright   CLOUDREXX CMS - Cloudrexx AG Thun
 * @author      Project Team SS4U <info@cloudrexx.com>
 * @package     cloudrexx
 * @subpackage  module_forum
 * @version     1.0.0
 */

class EsiWidgetController extends \Cx\Core_Modules\Widget\Controller\EsiWidgetController {
    /**
     * Parses a widget
     *
     * @param string                                 $name     Widget name
     * @param \Cx\Core\Html\Sigma                    $template Widget template
     * @param \Cx\Core\Routing\Model\Entity\Response $response Response object
     * @param array                                  $params   Get parameters
     */
    public function parseWidget($name, $template, $response, $params)
    {
        //Parse Forum latest entries content
        if (
            $name == 'FORUM_FILE' &&
            \Cx\Core\Setting\Controller\Setting::getValue(
                'forumHomeContent',
                'Config'
            ) == '1'
        ) {
            if (!$params['theme']) {
                return;
            }
            $filePath   = $params['theme']->getFolderName() . '/forum.html';
            $fileSystem = \Cx\Core\Core\Controller\Cx::instanciate()
                ->getMediaSourceManager()
                ->getMediaType('themes')
                ->getFileSystem();
            $file = new \Cx\Core\ViewManager\Model\Entity\ViewManagerFile(
                $filePath,
                $fileSystem
            );
            if (!$fileSystem->fileExists($file)) {
                return;
            }

            $content  = file_get_contents($fileSystem->getFullPath($file));
            $objForum = new ForumHomeContent($content, $params['lang']);
            $template->setVariable(
                $name,
                $objForum->getContent()
            );
            return;
        }

        //Parse Forum tagcloud
        if (
            $name !== 'FORUM_TAG_CLOUD' ||
            empty(
                \Cx\Core\Setting\Controller\Setting::getValue(
                    'forumTagContent',
                    'Config'
                )
            )
        ) {
            return;
        }
        $objForumHome = new ForumHomeContent('');
        $template->setVariable($name, $objForumHome->getHomeTagCloud());
    }
}
