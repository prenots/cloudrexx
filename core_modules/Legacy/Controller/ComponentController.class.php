<?php declare(strict_types=1);

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
 * Replaces legacy blocks and placeholders
 * @copyright   Cloudrexx AG
 * @author      Michael Ritter <michael.ritter@cloudrexx.com>
 * @package     cloudrexx
 * @subpackage  core_module_legacy
 */

namespace Cx\Core_Modules\Legacy\Controller;

/**
 * Replaces legacy blocks and placeholders
 * @copyright   Cloudrexx AG
 * @author      Michael Ritter <michael.ritter@cloudrexx.com>
 * @package     cloudrexx
 * @subpackage  core_module_legacy
 */
class ComponentController extends \Cx\Core\Core\Model\Entity\SystemComponentController implements \Cx\Core\Event\Model\Entity\EventListener {

    /**
     * @var array List of placeholders (needle=>replacement)
     */
    protected $blocksToReplace = array(
        'shopJsCart' => 'shop_js_cart',
    );

    /**
     * @inheritDoc
     */
    public function getControllerClasses() {
        return array();
    }

    /**
     * @inheritDoc
     */
    public function registerEventListeners() {
        $this->cx->getEvents()->addEventListener('View.Sigma:loadContent', $this);
        $this->cx->getEvents()->addEventListener('View.Sigma:setVariable', $this);
    }

    /**
     * @inheritDoc
     */
    public function onEvent($eventName, array $eventArgs) {
        if (!isset($eventArgs['content'])) {
            return;
        }
        foreach ($this->blocksToReplace as $legacyName=>$niceName) {
            $eventArgs['content'] = preg_replace(
                '/(<!--\s+(?:BEGIN|END)\s+)' . $legacyName . '(\s+-->)/',
                '\1' . $niceName . '\2',
                $eventArgs['content']
            );
        }
        // TODO: Add placeholder replacement list.
        // TODO: Handle callback widgets. These are parsed already!
    }
}
