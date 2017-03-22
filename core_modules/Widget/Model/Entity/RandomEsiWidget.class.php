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
 * Represents a template widget that is handled by ESI
 *
 * @copyright   CLOUDREXX CMS - Cloudrexx AG Thun
 * @author      Project Team SS4U <info@comvation.com>
 * @package     cloudrexx
 * @subpackage  coremodules_widget
 * @version     1.0.0
 */

namespace Cx\Core_Modules\Widget\Model\Entity;

/**
 * Represents a template widget that is handled by ESI
 *
 * @todo Add an additional ESI layer to allow de/-activation of Widgets
 * @copyright   CLOUDREXX CMS - Cloudrexx AG Thun
 * @author      Project Team SS4U <info@comvation.com>
 * @package     cloudrexx
 * @subpackage  coremodules_widget
 * @version     1.0.0
 */
class RandomEsiWidget extends EsiWidget {
    /**
     * Random widget names
     *
     * @var array
     */
    protected $randomNames;

    /**
     * Instanciates a new widget
     *
     * @param \Cx\Core\Core\Model\Entity\SystemComponentController $component Component registering this widget
     * @param string  $name            Name of this widget
     * @param array   $randomParams    Array of additional params for each entry of the random list
     * @param boolean $hasContent      (optional) Wheter this widget has content or not
     * @param string  $jsonAdapterName (optional) Name of the JsonAdapter to call. If not specified, $component->getName() is used
     * @param string  $jsonMethodName  (optional) Name of the JsonAdapter method to call. If not specified, "getWidget" is used
     */
    public function __construct(
        $component,
        $name,
        $randomParams,
        $hasContent = false,
        $jsonAdapterName = '',
        $jsonMethodName = ''
    ) {
        parent::__construct(
            $component,
            $name,
            $hasContent,
            $jsonAdapterName,
            $jsonMethodName
        );
        $this->randomParams = $randomParams;
    }

    /**
     * Get the random widget params
     *
     * @return array List of additional params
     */
    public function getRandomParams()
    {
        return $this->randomParams;
    }

    /*
     * Really parses this widget into $template
     * If this Widget has no content, the replacement can simply be returned
     * as string. Otherwise the replacement must be done in $template.
     *
     * @param \HTML_Template_Sigma                  $template        Template to parse this widget into
     * @param \Cx\Core\Routing\Model\Entity\Reponse $response        Current response object
     * @param string                                $targetComponent Parse target component name
     * @param string                                $targetEntity    Parse target entity name
     * @param string                                $targetId Parse  target entity ID
     *
     * @return string Replacement for widgets without content, NULL otherwise
     */
    public function internalParse(
        $template,
        $response,
        $targetComponent,
        $targetEntity,
        $targetId
    ) {
        $randomEsiParams   = array();
        $esiParams         = $this->getEsiParams(
            $targetComponent,
            $targetEntity,
            $targetId
        );
        foreach ($this->getRandomParams() as $randomParams) {
            $randomEsiParams[] = array(
                $this->getJsonAdapterName(),
                $this->getJsonMethodName(),
                array_merge($esiParams, $randomParams)
            );
        }
        $esiContent = $this->getComponent('Cache')->getRandomizedEsiContent(
            $randomEsiParams
        );
        if (!$this->hasContent()) {
            return $esiContent;
        }
        $template->replaceBlock($this->getName(), $esiContent);
        $template->touchBlock($this->getName());
    }
}
