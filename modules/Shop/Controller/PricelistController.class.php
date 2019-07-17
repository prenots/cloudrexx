<?php
/**
 * Cloudrexx
 *
 * @link      http://www.cloudrexx.com
 * @copyright Cloudrexx AG 2007-2018
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
 * PricelistController to handle pricelists
 *
 * @copyright   Cloudrexx AG
 * @author      Sam Hawkes <info@cloudrexx.com>
 * @package     cloudrexx
 * @subpackage  module_shop
 */
namespace Cx\Modules\Shop\Controller;

/**
 * PricelistController to handle pricelists
 *
 * @copyright   Cloudrexx AG
 * @author      Sam Hawkes <info@cloudrexx.com>
 * @package     cloudrexx
 * @subpackage  module_shop
 */
class PricelistController extends \Cx\Core\Core\Model\Entity\Controller
{
    /**
     * Get ViewGenerator options for entity
     *
     * @param $options array predefined ViewGenerator options
     * @return array includes ViewGenerator options for entity
     */
    public function getViewGeneratorOptions($options)
    {
        global $_ARRAYLANG;

        $options['order']['form'] = array(
            'pdfLink',
            'name',
            'lang',
        );

        $options['fields'] = array(
            'id' => array(
                'table' => array(
                    'attributes' => array(
                        'class' => 'pricelist-id',
                    ),
                ),
            ),
            'name' => array(
                'table' => array(
                    'attributes' => array(
                        'class' => 'pricelist-name',
                    ),
                ),
            ),
            'langId' => array(
                'showOverview' => false,
                'showDetail' => false,
            ),
            'borderOn' => array(
                'showOverview' => false
            ),
            'headerOn' => array(
                'showOverview' => false
            ),
            'headerLeft' => array(
                'showOverview' => false,
                'formfield' => array(
                    'adapter' => 'PriceList',
                    'method' => 'getLineFieldHeader'
                ),
            ),
            'headerRight' => array(
                'showOverview' => false,
                'type' => 'hidden'
            ),
            'footerOn' => array(
                'showOverview' => false
            ),
            'footerLeft' => array(
                'showOverview' => false,
                'formfield' => array(
                    'adapter' => 'PriceList',
                    'method' => 'getLineFieldFooter'
                ),
            ),
            'footerRight' => array(
                'showOverview' => false,
                'type' => 'hidden'
            ),
            'allCategories' => array(
                'showOverview' => false,
                'formfield' => function($fieldname, $fieldtype, $fieldlength, $fieldvalue) {
                    return $this->getAllCategoriesCheckbox($fieldvalue);
                },
                'storecallback' => function(){
                    return $this->cx->getRequest()->hasParam(
                        'category-all',
                        false
                    );
                }
            ),
            'lang' => array(
                'showOverview' => false
            ),
            'categories' => array(
                'showOverview' => false,
                'mode' => 'associate',
                'formfield' => function() {
                    return $this->getCategoryCheckboxesForPricelist();
                },
            ),
            'pdfLink' => array(
                'custom' => true,
                'header' => $_ARRAYLANG['TXT_PDF_LINK'],
                'type' => 'div',
                'valueCallback' => function($fieldvalue, $fieldname, $rowData) {
                    return $this->getGeneratedPdfLink($rowData);
                },
                'table' => array(
                    'parse' => function($value) {
                        return $this->getLinkElement($value);
                    }
                ),
                'formfield' => function($name, $type, $length, $value) {
                    return $this->getLinkElement($value);
                }
            )
        );

        return $options;
    }

    /**
     * @return \Cx\Core\Html\Model\Entity\HtmlElement
     * @throws \Doctrine\ORM\ORMException
     */
    protected function getCategoryCheckboxesForPricelist()
    {
        // Until we know how to get the editId without the $_GET param
        $pricelistId = 0;
        if ($this->cx->getRequest()->hasParam('editid')) {
            $pricelistId = explode(
                '}',
                explode(
                    ',',
                    $this->cx->getRequest()->getParam('editid')
                )[1]
            )[0];
        }

        $categories = $this->cx->getDb()->getEntityManager()->getRepository(
            '\Cx\Modules\Shop\Model\Entity\Category'
        )->findBy(array('active' => 1, 'parentId' => null));
        $wrapper = new \Cx\Core\Html\Model\Entity\HtmlElement('div');

        foreach ($categories as $category) {
            $wrapper->addChild(
                $this->getCategoryCheckbox(
                    $category, $pricelistId
                )
            );

            foreach ($category->getChildren() as $child) {
                $childWrapper = new \Cx\Core\Html\Model\Entity\HtmlElement('span');
                $childWrapper->addClass('child');

                $childCheckbox = $this->getCategoryCheckbox(
                    $child, $pricelistId
                );

                $childWrapper->addChild($childCheckbox);
                $wrapper->addChild($childWrapper);
            }
        }
        return $wrapper;
    }

    protected function getCategoryCheckbox($category, $pricelistId)
    {
        $repo = $this->cx->getDb()->getEntityManager()->getRepository(
            '\Cx\Modules\Shop\Model\Entity\Pricelist'
        );
        $label = new \Cx\Core\Html\Model\Entity\HtmlElement('label');
        $label->setAttributes(
            array(
                'class' => 'category',
                'for' => 'category-'. $category->getId()
            )
        );
        $text = new \Cx\Core\Html\Model\Entity\TextElement(
            $category->getName()
        );
        $checkbox = new \Cx\Core\Html\Model\Entity\DataElement(
            'categories[' . $category->getId() . ']',
            $category->getId()

        );

        $isActive = (boolean)$repo->getPricelistByCategoryAndId(
            $category,
            $pricelistId
        );
        $checkbox->setAttributes(
            array(
                'type' => 'checkbox',
                'id' => 'category-' . $category->getId(),
                empty($isActive) ? '' : 'checked' => 'checked'
            )
        );

        $label->addChild($checkbox);
        $label->addChild($text);

        return $label;
    }

    protected function getAllCategoriesCheckbox($isActive)
    {
        global $_ARRAYLANG;

        $wrapper = new \Cx\Core\Html\Model\Entity\HtmlElement('div');

        $label = new \Cx\Core\Html\Model\Entity\HtmlElement('label');
        $label->setAttributes(
            array(
                'class' => 'category',
                'for' => 'category-all'
            )
        );
        $text = new \Cx\Core\Html\Model\Entity\TextElement(
            $_ARRAYLANG['TXT_SHOP_ALL_CATEGORIES']
        );
        $checkbox = new \Cx\Core\Html\Model\Entity\DataElement(
            'category-all',
            1
        );
        $checkbox->setAttributes(
            array(
                'type' => 'checkbox',
                'id' => 'category-all',
                empty($isActive) ? '' : 'checked' => 'checked'
            )
        );

        $label->addChild($checkbox);
        $label->addChild($text);
        $wrapper->addChild($label);

        return $wrapper;
    }

    protected function getGeneratedPdfLink($rowData)
    {
        $url = $this->cx->getRequest()->getUrl();
        $protcol = $url->getProtocol();
        $domain = $url->getDomain();
        $pdfLinkUrl = \Cx\Core\Routing\Url::fromApi(
            'generatePdfPricelist', array()
        );

        $locale = \FWLanguage::getLanguageCodeById($rowData['langId']);
        $pdfLinkUrl->setParam('id', $rowData['id']);
        $pdfLinkUrl->setParam('locale', $locale);

        $link = $protcol . '://' . $domain . $pdfLinkUrl;

        return $link;
    }

    protected function getLinkElement($value)
    {
        $link = new \Cx\Core\Html\Model\Entity\HtmlElement('a');
        $text = new \Cx\Core\Html\Model\Entity\TextElement($value);
        $link->setAttributes(
            array(
                'href' => $value,
                'target' => '_blank'
            )
        );
        $link->addChild($text);

        return $link;
    }
}