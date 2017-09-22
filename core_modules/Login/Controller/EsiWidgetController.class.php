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
 * @subpackage  coremodule_login
 * @version     1.0.0
 */

namespace Cx\Core_Modules\Login\Controller;

/**
 * JsonAdapter Controller to handle EsiWidgets
 * Usage:
 * - Create a subclass that implements parseWidget()
 * - Register it as a Controller in your ComponentController
 *
 * @copyright   CLOUDREXX CMS - Cloudrexx AG Thun
 * @author      Project Team SS4U <info@cloudrexx.com>
 * @package     cloudrexx
 * @subpackage  coremodule_login
 * @version     1.0.0
 */

class EsiWidgetController extends \Cx\Core_Modules\Widget\Controller\EsiWidgetController {

    /**
     * Parses a widget
     *
     * @param string                                 $name     Widget name
     * @param \Cx\Core\Html\Sigma                    $template WidgetTemplate
     * @param \Cx\Core\Routing\Model\Entity\Response $response Response object
     * @param array                                  $params   Get parameters
     */
    public function parseWidget($name, $template, $response, $params)
    {
        $langId = $params['locale']->getId();
        if ($name === 'LOGIN_URL') {
            // Need to manually load language data here since we need "Core"
            $coreLang = \Env::get('init')->getComponentSpecificLanguageData(
                'Core',
                true,
                $langId
            );
            $link = \Html::getLink(
                \Cx\Core\Routing\Url::fromModuleAndCmd('Login', '', $langId),
                $coreLang['TXT_FRONTEND_EDITING_LOGIN'],
                null,
                ' class="start-frontend-editing" '
            );
            $template->setVariable($name, $link);
            return;
        }

        if ($name === 'LOGOUT_URL') {
            $template->setVariable(
                $name,
                \Cx\Core\Routing\Url::fromDocumentRoot(
                    array('section' => 'logout'),
                    $langId
                )
            );
        }
    }
}
