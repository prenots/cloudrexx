<?php
/**
 * Cloudrexx
 *
 * @link      http://www.cloudrexx.com
 * @copyright Cloudrexx AG 2007-2019
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
 * JsonController for Currency
 *
 * @copyright  Cloudrexx AG
 * @author     Sam Hawkes <info@cloudrexx.com>
 * @package    cloudrexx
 * @subpackage module_shop
 * @version    5.0.0
 */
namespace Cx\Modules\Shop\Controller;

/**
 * JsonController for Currency
 *
 * @copyright  Cloudrexx AG
 * @author     Sam Hawkes <info@cloudrexx.com>
 * @package    cloudrexx
 * @subpackage module_shop
 * @version    5.0.0
 */
class JsonAttributeController
    extends \Cx\Core\Core\Model\Entity\Controller
    implements \Cx\Core\Json\JsonAdapter
{
    /**
     * @var array messages from this controller
     */
    protected $messages = array();

    /**
     * Returns the internal name used as identifier for this adapter
     * @return String Name of this adapter
     */
    public function getName()
    {
        return 'Attribute';
    }

    /**
     * Returns an array of method names accessable from a JSON request
     *
     * @return array List of method names
     */
    public function getAccessableMethods()
    {
        return array(
            'getOptions',
            'getTypes',
            'storeOptions',
            'storeType',
            'getTypesDetail',
            'getOptionsDetail'
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
     * @return \Cx\Core_Modules\Access\Model\Entity\Permission
     */
    public function getDefaultPermissions()
    {
        $permission = new \Cx\Core_Modules\Access\Model\Entity\Permission(
            array('http', 'https'),
            array('get', 'post'),
            true,
            array()
        );

        return $permission;
    }

    /**
     * Get a select with all options assigned to this attribute
     *
     * @param array $params contains the parameters of the callback function
     *
     * @return \Cx\Core\Html\Model\Entity\DataElement select with options
     */
    public function getOptions($params)
    {
        $id = empty($params['rows']['id']) ? 0 : $params['rows']['id'];
        $data = $this->cx->getDb()->getEntityManager()->getRepository(
            'Cx\Modules\Shop\Model\Entity\Attribute'
        )->getOptionsWithNameAndPrice($id);

        return new \Cx\Core\Html\Model\Entity\DataElement(
            'options-' . $id,
            '',
            'select',
            null,
            $data
        );
    }

    /**
     * Get a select with all attribute types
     *
     * @param array $params contains the parameters of the callback function
     *
     * @return \Cx\Core\Html\Model\Entity\DataElement select with types
     */
    public function getTypes($params)
    {
        $attributeRepo = $this->cx->getDb()->getEntityManager()->getRepository(
            'Cx\Modules\Shop\Model\Entity\Attribute'
        );
        $attribute = $attributeRepo->find($params['rows']['id']);

        $type = 0;
        if (!empty($attribute)) {
            $type = $attribute->getType();
        }

        $data = $attributeRepo->getTypes();
        return new \Cx\Core\Html\Model\Entity\DataElement(
            'type-' .$params['rows']['id'],
            $type,
            'select',
            null,
            $data
        );
    }

    public function getTypesDetail($params)
    {
        $id = empty($params['id']) ? 0 : $params['id'];

        $attributeRepo = $this->cx->getDb()->getEntityManager()->getRepository(
            'Cx\Modules\Shop\Model\Entity\Attribute'
        );
        $attribute = $attributeRepo->find($id);

        $type = 0;
        $options = array(
            new \Cx\Modules\Shop\Model\Entity\Attribute()
        );
        if (!empty($attribute)) {
            $type = $attribute->getType();
            $options = $attribute->getOptions();
        }

        $wrapper = new \Cx\Core\Html\Model\Entity\HtmlElement('div');

        $data = $attributeRepo->getTypes();
        $select = new \Cx\Core\Html\Model\Entity\DataElement(
            'type',
            $type,
            'select',
            null,
            $data
        );
        $select->setAttribute('id', 'option-type');

        $wrapperName = new \Cx\Core\Html\Model\Entity\HtmlElement('div');
        $wrapperPrice = new \Cx\Core\Html\Model\Entity\HtmlElement('div');

        foreach ($options as $option) {
            $inputName = new \Cx\Core\Html\Model\Entity\DataElement(
                'option-name-' . (int)$option->getId()
            );
            $inputName->setAttribute('id', 'option-name-' . (int)$option->getId());

            $inputPrice = new \Cx\Core\Html\Model\Entity\DataElement(
                'option-price'
            );
            $inputPrice->setAttribute('id', 'option-price');

            $wrapperName->addChild($inputName);
            $wrapperPrice->addChild($inputPrice);
        }

        $labelName = new \Cx\Core\Html\Model\Entity\HtmlElement('label');
        $textName = new \Cx\Core\Html\Model\Entity\TextElement('Wert');
        $labelName->addChild($textName);

        $labelPrice = new \Cx\Core\Html\Model\Entity\HtmlElement('label');
        $textPrice = new \Cx\Core\Html\Model\Entity\TextElement('Preis');
        $labelPrice->addChild($textPrice);

        $btnNew = new \Cx\Core\Html\Model\Entity\DataElement(
            'add-option',
            'Neuen Wert hinzufügen'
        );
        $btnNew->setAttribute('type', 'button');
        $btnNew->setAttribute('id', 'add-option');

        $wrapper->addChildren(array($select, $labelName, $wrapperName, $labelPrice, $wrapperPrice, $btnNew));
        return $wrapper;
    }

    public function getOptionsDetail($params)
    {
        $id = empty($params['id']) ? 0 : $params['id'];
        $data = $this->cx->getDb()->getEntityManager()->getRepository(
            'Cx\Modules\Shop\Model\Entity\Attribute'
        )->getOptionsWithNameAndPrice($id);

        $select = new \Cx\Core\Html\Model\Entity\DataElement(
            'options',
            '',
            'select',
            null,
            $data
        );
        $select->setAttribute('size', 10);
        return $select;
    }

    public function storeOptions($params)
    {
        \Doctrine\Common\Util\Debug::dump($params); die();
        //if ()
    }

    public function storeType($params)
    {
        return $params['postedValue'];
    }
}