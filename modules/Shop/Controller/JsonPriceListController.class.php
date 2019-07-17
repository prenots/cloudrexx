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
 * JsonController for Pricelist
 *
 * @copyright  Cloudrexx AG
 * @author     Sam Hawkes <info@cloudrexx.com>
 * @package    cloudrexx
 * @subpackage module_shop
 * @version    5.0.0
 */
namespace Cx\Modules\Shop\Controller;


class JsonPriceListController
    extends \Cx\Core\Core\Model\Entity\Controller
    implements \Cx\Core\Json\JsonAdapter
{
    /**
     * @var array messages from this controller
     */
    protected $messages;

    /**
     * Returns the internal name used as identifier for this adapter
     * @return String Name of this adapter
     */
    public function getName()
    {
        return 'PriceList';
    }

    /**
     * Returns an array of method names accessable from a JSON request
     *
     * @return array List of method names
     */
    public function getAccessableMethods()
    {
        return array(
            'getLineFieldFooter',
            'getLineFieldHeader',
            'getAllCategoriesCheckbox',
            'storeAllCategories',
            'getCategoryCheckboxesForPricelist',
            'getGeneratedPdfLink',
            'getLinkElement'
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
     * Two header elements are returned in a wrapper so that they can be
     * displayed in one line
     *
     * @param array $params contains the parameters of the callback function
     * @return \Cx\Core\Html\Model\Entity\HtmlElement line with two elements
     */
    public function getLineFieldHeader($params)
    {
        $name = !empty($params['name']) ? $params['name'] : '';
        $value = !empty($params['value']) ? $params['value'] : '';

        return $this->getLineField($name, $value, 'headerRight');
    }

    /**
     * Two footer elements are returned in a wrapper so that they can be
     * displayed in one line
     *
     * @param array $params contains the parameters of the callback function
     * @return \Cx\Core\Html\Model\Entity\HtmlElement line with two elements
     */
    public function getLineFieldFooter($params)
    {
        global $_ARRAYLANG;

        $name = !empty($params['name']) ? $params['name'] : '';
        $value = !empty($params['value']) ? $params['value'] : '';
        $placeholders = array(
            '[DATE]' => $_ARRAYLANG[
                'TXT_DATE'
            ],
            '[PAGENUMBER]' =>$_ARRAYLANG[
                'TXT_PAGENUMBER'
            ]
        );
        return $this->getLineField($name, $value, 'footerRight', $placeholders);
    }

    /**
     * Two elements are returned in a wrapper so that they can be displayed in
     * one line
     *
     * @param string $nameLeft     name of the left element
     * @param string $valueLeft    value of the left element
     * @param string $nameRight    name of the right element
     * @param array  $placeholders the placeholders are displayed below the
     *                             element as a hint.
     * @return \Cx\Core\Html\Model\Entity\HtmlElement
     */
    protected function getLineField(
        $nameLeft, $valueLeft, $nameRight, $placeholders = array()
    ) {
        $wrapper = new \Cx\Core\Html\Model\Entity\HtmlElement('div');
        $headerLeft = new \Cx\Core\Html\Model\Entity\HtmlElement('textarea');
        $headerRight = new \Cx\Core\Html\Model\Entity\HtmlElement('textarea');
        $leftText = new \Cx\Core\Html\Model\Entity\TextElement($valueLeft);
        $rightText = new \Cx\Core\Html\Model\Entity\TextElement('');

        $headerLeft->setAttributes(
            array(
                'name' => $nameLeft,
                'id' => $nameLeft,
                'rows' => 4
            )
        );
        $headerRight->setAttributes(
            array(
                'id' => $nameRight,
                'rows' => 4
            )
        );

        $headerLeft->addChild($leftText);
        $headerRight->addChild($rightText);
        $wrapper->addChild($headerLeft);
        $wrapper->addChild($headerRight);

        if (empty($placeholders)) return $wrapper;

        $wrapperPlaceholders = new \Cx\Core\Html\Model\Entity\HtmlElement(
            'div'
        );
        foreach ($placeholders as $placeholder=>$lang) {
            $block = new \Cx\Core\Html\Model\Entity\HtmlElement('div');
            $name = new \Cx\Core\Html\Model\Entity\TextElement(
                $placeholder
            );
            $tt = new \Cx\Core\Html\Model\Entity\HtmlElement('tt');
            $tt->addChild($name);
            $block->addChild($tt);
            $block->addChild(
                new \Cx\Core\Html\Model\Entity\TextElement(
                    ': ' . $lang
                )
            );
            $wrapperPlaceholders->addChild($block);
        }
        $wrapper->addChild($wrapperPlaceholders);
        return $wrapper;
    }
}