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
 * @subpackage  module_knowledge
 * @version     1.0.0
 */

namespace Cx\Modules\Knowledge\Controller;

/**
 * JsonAdapter Controller to handle EsiWidgets
 * Usage:
 * - Create a subclass that implements parseWidget()
 * - Register it as a Controller in your ComponentController
 *
 * @copyright   CLOUDREXX CMS - Cloudrexx AG Thun
 * @author      Project Team SS4U <info@comvation.com>
 * @package     cloudrexx
 * @subpackage  module_knowledge
 * @version     1.0.0
 */

class EsiWidgetController extends \Cx\Core_Modules\Widget\Controller\EsiWidgetController {
    /**
     * {@inheritDoc}
     */
    public function parseWidget($name, $template, $response, $params)
    {
        global $_LANGID;


        // The global $_LANGID is required in the getTagCloud(), getMostRead(),
        // getBestRated() methods
        $_LANGID = \FWLanguage::getLangIdByIso639_1($params['locale']);

        // get knowledge content
        $knowledgeInterface = new KnowledgeInterface();
        if ($name == 'KNOWLEDGE_TAG_CLOUD') {
            $template->setVariable($name, $knowledgeInterface->getTagCloud());
        }

        if ($name == 'KNOWLEDGE_MOST_READ') {
            $template->setVariable($name, $knowledgeInterface->getMostRead());
        }

        if ($name == 'KNOWLEDGE_BEST_RATED') {
            $template->setVariable($name, $knowledgeInterface->getBestRated());
        }
    }
}
