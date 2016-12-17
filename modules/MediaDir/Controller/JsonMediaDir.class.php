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
 * JsonAccess
 * Json controller for MediaDir component
 *
 * @copyright   Cloudrexx AG
 * @author      Project Team SS4U <info@cloudrexx.com>
 * @package     cloudrexx
 * @subpackage  module_mediadir
 */

namespace Cx\Modules\MediaDir\Controller;

/**
 * JsonAccess
 * Json controller for MediaDir component
 *
 * @copyright   Cloudrexx AG
 * @author      Project Team SS4U <info@cloudrexx.com>
 * @package     cloudrexx
 * @subpackage  module_mediadir
 */
class JsonMediaDir implements \Cx\Core\Json\JsonAdapter
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
        return 'MediaDir';
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
            'getLatestPlacholder'
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
            contrexx_input2raw($params['get']['lang']) : '';
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
     * @param type $params all given params from http request
     *
     * @return array content of latest placeholders
     */
    public function getLatestPlacholder($params)
    {
        $lang = isset($params['get']['lang']) ?
            contrexx_input2raw($params['get']['lang']) : '';
        if (empty($lang)) {
            return array('content' => '');
        }

        $mediaDirPlaceholders = new MediaDirectoryPlaceholders($this->getName());
        return array(
            'content' => $mediaDirPlaceholders->getLatestPlacholder($lang)
        );
    }
}