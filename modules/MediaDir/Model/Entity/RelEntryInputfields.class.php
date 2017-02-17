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
 * Simple "dummy" representation of an input field in order to get widget contents
 * This class might later be used as a doctrine entity
 * @author Michael Ritter <michael.ritter@cloudrexx.com>
 * @package cloudrexx
 * @subpackage modules_mediadir
 */

namespace Cx\Modules\MediaDir\Model\Entity;

/**
 * Simple "dummy" representation of an input field in order to get widget contents
 * This class might later be used as a doctrine entity
 * @author Michael Ritter <michael.ritter@cloudrexx.com>
 * @package cloudrexx
 * @subpackage modules_mediadir
 */
class RelEntryInputFields extends \Cx\Core_Modules\Widget\Model\Entity\WidgetParseTarget {
    /**
     * Entry ID
     *
     * @var int
     */
    protected $entryId;

    /**
     * Form ID
     * @var int
     */
    protected $formId;

    /**
     * Field ID
     * @var int
     */
    protected $fieldId;

    /**
     * Value per language ID
     * @var array
     */
    protected $value = array();

    /**
     * Creates a new RelEntryInputfield entity used for WidgetParseTarget
     * @param int $entryId Entry ID
     * @param int $langId Language ID
     * @param int $formId Form ID
     * @param int $fieldId Field ID
     */
    public function __construct($entryId, $formId, $fieldId) {
        $this->entryId = $entryId;
        $this->formId = $formId;
        $this->fieldId = $fieldId;
    }

    /**
     * Returns the entry ID
     * @return int Entry ID
     */
    public function getEntryId() {
        return $this->entryId;
    }

    /**
     * Returns the form ID
     * @return int Form ID
     */
    public function getFormId() {
        return $this->formId;
    }

    /**
     * Returns the field ID
     * @return int Field ID
     */
    public function getFieldId() {
        return $this->fieldId;
    }

    /**
     * Returns this Inputfield's value. Required WidgetParseTarget getter
     * @param int $langId Internal language/locale ID
     * @return string Inputfield value
     */
    public function getValue($langId) {
        if (!isset($this->value[$langId])) {
            $query = '
                SELECT
                    `value`
                FROM
                    `' . DBPREFIX . 'module_mediadir_rel_entry_inputfields`
                WHERE
                    `entry_id` = ' . contrexx_raw2db($this->getEntryId()) . '
                    AND `form_id` = ' . contrexx_raw2db($this->getFormId()) . '
                    AND `field_id` = ' . contrexx_raw2db($this->getFieldId()) . '
                    AND `lang_id` = ' . contrexx_raw2db($langId) . '
            ';
            $result = $this->cx->getDb()->getAdoDb()->execute($query);
            if (!$result) {
                throw new \Exception('Could not fetch content for inputfield');
            }
            $this->value[$langId] = $result->fields['value'];
        }
        return $this->value[$langId];
    }

    /**
     * Returns the name of the attribute which contains content that may contain a widget
     * @param string $widgetName
     * @return string Attribute name
     */
    public function getWidgetContentAttributeName($widgetName) {
        return 'value';
    }
}
