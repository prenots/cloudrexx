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
 * JsonController for Product
 *
 * @copyright   Cloudrexx AG
 * @author      Sam Hawkes <info@cloudrexx.com>
 * @package     cloudrexx
 * @subpackage  module_shop
 * @version     5.0.0
 */
namespace Cx\Modules\Shop\Controller;


/**
 * JsonController for Product
 *
 * @copyright   Cloudrexx AG
 * @author      Sam Hawkes <info@cloudrexx.com>
 * @package     cloudrexx
 * @subpackage  module_shop
 * @version     5.0.0
 */
class JsonProductController extends \Cx\Core\Core\Model\Entity\Controller
    implements \Cx\Core\Json\JsonAdapter
{
    /**
     * @var array messages from this controller
     */
    protected $messages;

    /**
     * @var int number of product images
     */
    protected $numberOfImages = 3;

    /**
     * Returns the internal name used as identifier for this adapter
     * @return String Name of this adapter
     */
    public function getName()
    {
        return 'Product';
    }

    /**
     * Returns an array of method names accessable from a JSON request
     *
     * @return array List of method names
     */
    public function getAccessableMethods()
    {
        return array(
            'getImageBrowser',
            'storePicture',
            'addEditLink',
            'setEmptyDateToNull',
            'getProductAttributes',
            'storeProductAttributes',
            'getDistributionDropdown'
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
     * Get image browser to select images
     *
     * @param array $params contains the parameters of the callback function
     *
     * @return \Cx\Core\Html\Model\Entity\HtmlElement image browser
     * @throws \Cx\Core_Modules\MediaBrowser\Model\Entity\MediaBrowserException
     */
    public function getImageBrowser($params)
    {
        global $_ARRAYLANG;

        $name = $params['name'];
        $imgInfo = array();

        if (!empty($params['value'])) {
            $imgInfo = \Cx\Modules\Shop\Controller\ProductController::get_image_array_from_base64(
                $params['value']
            );
        }

        $websiteImagesShopPath    = $this->cx->getWebsiteImagesShopPath() . '/';
        $websiteImagesShopWebPath = $this->cx->getWebsiteImagesShopWebPath().'/';

        if (
            file_exists(
                $this->cx->getWebsiteImagesShopPath() . '/'
                . ShopLibrary::noPictureName
            )
        ) {
            $defaultImage = $this->cx->getWebsiteImagesShopWebPath() . '/'
            . ShopLibrary::noPictureName;
        } else {
            $defaultImage = $this->cx->getCodeBaseOffsetPath(). '/images/Shop/'
            . ShopLibrary::noPictureName;
        }

        $wrapper = new \Cx\Core\Html\Model\Entity\HtmlElement('div');

        $mediaBrowser = new \Cx\Core_Modules\MediaBrowser\Model\Entity\MediaBrowser();
        $mediaBrowser->setCallback('setSelectedImage');
        $mediaBrowser->setOptions(
            array(
                'type'           => 'button',
                'startmediatype' => 'shop',
                'views'          => 'filebrowser',
                'id'             => 'media_browser_shop',
                'style'          => 'display:none'
            )
        );

        // Add MediaBrowser button with a TextElement. This because MediaBrowser
        // is not a HtmlElement
        $wrapper->addChild(
            new \Cx\Core\Html\Model\Entity\TextElement(
                $mediaBrowser->getXHtml(
                    $_ARRAYLANG['TXT_SHOP_EDIT_OR_ADD_IMAGE']
                )
            )
        );

        for ($i = 1; $i <= $this->numberOfImages; $i++) {
            if (
                !empty($imgInfo[$i]['img']) &&
                is_file($websiteImagesShopPath . $imgInfo[$i]['img'])
            ) {
                $imgSrc = $websiteImagesShopWebPath . $imgInfo[$i]['img'];
            } else {
                $imgSrc = $defaultImage;
            }

            $imgHeight = '';
            if (!empty($imgInfo[$i]['height'])) {
                $imgHeight = $imgInfo[$i]['height'];
            }
            $imgWidth = '';
            if (!empty($imgInfo[$i]['width'])) {
                $imgWidth = $imgInfo[$i]['width'];
            }

            $imageWrapper = new \Cx\Core\Html\Model\Entity\HtmlElement('div');
            $imageWrapper->setAttribute('class', 'product-images-wrapper');

            $imageLink = new \Cx\Core\Html\Model\Entity\HtmlElement('a');
            $imageLink->setAttribute('onclick', 'openBrowser('.$i.')');
            $imageLink->setAttribute('id', 'product-image-link-' . $i);

            $image = new \Cx\Core\Html\Model\Entity\HtmlElement('img');
            $image->setAttributes(
                array(
                    'id' => 'product-image-' . $i,
                    'src' => $imgSrc,
                    'class' => 'product-images',
                )
            );

            $txtAddImg = new \Cx\Core\Html\Model\Entity\TextElement(
                $_ARRAYLANG['TXT_SHOP_EDIT_OR_ADD_IMAGE']
            );

            $imageDeleteLink = new \Cx\Core\Html\Model\Entity\HtmlElement('a');
            $imageDeleteLink->setAttributes(
                array(
                    'onclick' => 'deleteImage(' . $i .')',
                    'title' => $_ARRAYLANG['TXT_SHOP_DEL_ICON']
                )
            );

            $deleteImage = new \Cx\Core\Html\Model\Entity\HtmlElement('img');
            $deleteImage->setAttribute(
                'src',
                $this->cx->getCodeBaseCoreWebPath() .
                '/Core/View/Media/icons/delete.gif'
            );

            $srcInput = new \Cx\Core\Html\Model\Entity\DataElement(
                $name. '['. $i . '][src]',
                $imgSrc

            );
            $srcInput->setAttribute('type', 'hidden');
            $srcInput->setAttribute('id', 'product-image-src-' . $i);

            $widthInput = new \Cx\Core\Html\Model\Entity\DataElement(
                $name. '['. $i . '][width]',
                $imgWidth
            );
            $widthInput->setAttribute('type', 'hidden');
            $widthInput->setAttribute('id', 'product-image-width-' . $i);

            $heightInput = new \Cx\Core\Html\Model\Entity\DataElement(
                $name. '['. $i . '][height]',
                $imgHeight
            );
            $heightInput->setAttribute('type', 'hidden');
            $heightInput->setAttribute('id', 'product-image-height-' . $i);

            $imageLink->addChildren(array($txtAddImg, $image));
            $imageDeleteLink->addChild($deleteImage);
            $imageWrapper->addChildren(
                array(
                    $imageLink,
                    $imageDeleteLink,
                    $srcInput,
                    $widthInput,
                    $heightInput
                )
            );
            $wrapper->addChild($imageWrapper);
        }

        // Register JS to select or delete images
        \JS::registerJS('modules/Shop/View/Script/BrowseImages.js');
        \ContrexxJavascript::getInstance()->setVariable(
            'SHOP_NO_PICTURE_ICON',
            $defaultImage,
            'shopProduct'
        );

        return $wrapper;
    }

    /**
     * Get the selected product picture as base64 encoded
     *
     * @param array $params contains the parameters of the callback function
     *
     * @return string base64 encoded picture
     */
    public function storePicture($params)
    {
        if (empty($params['postedValue'])) {
            return '';
        }

        if (
        file_exists(
            $this->cx->getWebsiteImagesShopPath() . '/'
            . ShopLibrary::noPictureName
        )
        ) {
            $defaultImage = $this->cx->getWebsiteImagesShopWebPath() . '/'
                . ShopLibrary::noPictureName;
        } else {
            $defaultImage = $this->cx->getCodeBaseOffsetPath(). '/images/Shop/'
                . ShopLibrary::noPictureName;
        }

        $delemiter = '';
        $value = '';
        for ($i = 1; $i <= $this->numberOfImages; ++$i) {
            // Images outside the above directory are copied to the shop image
            // folder. Note that the image paths below do not include the
            // document root, but
            // are relative to it.
            $picture = contrexx_input2raw($params['postedValue'][$i]);
            // Ignore the picture if it's the default image!
            // Storing it would be pointless.
            // Images outside the above directory are copied to the shop image
            // folder. Note that the image paths below do not include the
            // document root, but
            // are relative to it.
            if ($picture['src'] == $defaultImage ||
                !\Cx\Modules\Shop\Controller\ShopLibrary::moveImage(
                    $picture['src']
                )) {
                $picture['src'] = '';
            }

            $picturePath = $this->cx->getWebsiteImagesShopPath(). '/'
                . $picture['src'];
            if (!\Cx\Lib\FileSystem\FileSystem::exists($picturePath)) {
                continue;
            }

            $pictureSize = getimagesize($picturePath);
            $picture['width']  = $pictureSize[0];
            $picture['height'] = $pictureSize[1];

            $value .=
                $delemiter . base64_encode($picture['src'])
                .'?'.base64_encode($picture['width'])
                .'?'.base64_encode($picture['height']);
            $delemiter = ':';
        }

        return $value;
    }

    /**
     * Adds a link around the text
     *
     * @param $param array callback values
     *
     * @return \Cx\Core\Html\Model\Entity\HtmlElement
     */
    public function addEditLink($param)
    {
        global $_ARRAYLANG;

        if (empty($param['rows']) || empty($param['rows']['id'])) {
            return $param['data'];
        }

        $id = $param['rows']['id'];
        $text = $param['data'];
        $vgId = $param['vgId'];


        $linkText = new \Cx\Core\Html\Model\Entity\TextElement($text);
        $link = new \Cx\Core\Html\Model\Entity\HtmlElement('a');
        $editUrl = \Cx\Core\Html\Controller\ViewGenerator::getVgEditUrl(
            $vgId,
            $id
        );

        $link->setAttributes(
            array(
                'href' => $editUrl,
                'title' => $_ARRAYLANG['TXT_SHOP_EDIT_ENTRY']
            )
        );
        $link->addChild($linkText);

        return $link;
    }

    /**
     * Set date to null if no date is set
     *
     * @param array $params contains the parameters of the callback function
     *
     * @return \Cx\Modules\Shop\Model\Entity\Product
     */
    public function setEmptyDateToNull($params)
    {
        if (empty($params['postedValue'])) {
            $method = 'set' . ucfirst($params['fieldName']);
            $params['entity']->$method(null);
        }
        return $params['entity'];
    }

    /**
     * Get all possible attributes that can be assigned to this product
     *
     * @param array $params contains the parameters of the callback function
     *
     * @return \Cx\Core\Html\Model\Entity\HtmlElement product attributes
     * @throws \Doctrine\ORM\ORMException handle orm interaction fails
     */
    public function getProductAttributes($params)
    {
        $name = $params['name'];
        $entityId = $params['id'];

        $wrapper = new \Cx\Core\Html\Model\Entity\HtmlElement('div');
        $wrapper->setAttribute('name', $name);

        $attributeRepo = $this->cx->getDb()->getEntityManager()->getRepository(
            'Cx\Modules\Shop\Model\Entity\Attribute'
        );
        $relProductAttrRepo = $this->cx->getDb()->getEntityManager()
            ->getRepository('Cx\Modules\Shop\Model\Entity\RelProductAttribute');
        $attributes = $attributeRepo->findAll();
        $productAttributes = $relProductAttrRepo->findBy(
            array('productId' => $entityId)
        );

        // All option IDs written into an array to simplify the check
        $productOptions = array();
        foreach ($productAttributes as $productAttribute) {
            $productOptions[] = $productAttribute->getOptionId();
        }

        $wrapper = $this->getProductOptionCheckboxes(
            $attributes,
            $name,
            $wrapper,
            $productOptions
        );
        return $wrapper;
    }

    /**
     * Get checkboxes to select the possible product options
     *
     * @param array  $options possible options that can be assigned to this product
     * @param string $name    name of the parent element
     * @param \Cx\Core\Html\Model\Entity\HtmlElement $wrapper wrapper around the
     *                                                        checkboxes
     * @param array $productOptions possible product options
     * @param \Cx\Core\Html\Model\Entity\HtmlElement $parentElement if a option
     *                                                              has children
     * @return \Cx\Core\Html\Model\Entity\HtmlElement wrapper with append
     *                                                checkboxes
     * @throws \Doctrine\ORM\ORMException handle orm interaction fails
     */
    protected function getProductOptionCheckboxes(
        $options, $name, $wrapper, $productOptions, $parentElement = null
    ) {
        foreach ($options as $option) {
            $optionWrapper = new \Cx\Core\Html\Model\Entity\HtmlElement('div');
            $attrName = $name;
            $attrId = $name . '-' . $option->getId();
            $attrValue = $option->getName();
            if (
                method_exists(get_class($option), 'getPrice') &&
                !empty($option->getPrice())
            ) {
                $defaultCurrency = $this->cx->getDb()->getEntityManager()
                    ->getRepository(
                        'Cx\Modules\Shop\Model\Entity\Currency'
                    )->getDefaultCurrency();

                $attrValue .= ' (' . $option->getPrice() . ' ' .
                    $defaultCurrency->getSymbol() . ')';
            }
            $checkbox = new \Cx\Core\Html\Model\Entity\HtmlElement(
                'input'
            );
            $checkbox->setAttribute('type', 'checkbox');
            $checkbox->setAttribute('value', 1);
            $checkbox->addClass('product-option-checkbox');

            $label = new \Cx\Core\Html\Model\Entity\HtmlElement('label');
            $label->addChild(
                new \Cx\Core\Html\Model\Entity\TextElement($attrValue)
            );
            $label->addClass('product-option-label');

            if (!empty($parentElement)) {
                $parentElement->addClass('parent');
                $optionWrapper->addClass('child');
                $parentInput = null;
                foreach ($parentElement->getChildren() as $child) {
                    if ($child->getName() == 'input') {
                        $parentInput = $child;
                    }
                }
                if (in_array($option->getId(), $productOptions)) {
                    $checkbox->setAttribute('checked');
                    if (isset($parentInput)) {
                        $parentInput->setAttribute('checked');
                    }
                    $parentElement->addClass('open');
                }

                $attrName = $parentInput->getAttribute('name') . '[' .
                    $option->getId() . ']';
                $attrId = $parentInput->getAttribute('id') . '-' .
                    $option->getId();

                $parentElement->addChild($optionWrapper);
                $label->setAttribute('for', $attrId);
            } else {
                $wrapper->addChild($optionWrapper);
            }

            $checkbox->setAttribute('name', $attrName);
            $checkbox->setAttribute('id', $attrId);
            $optionWrapper->addChild($checkbox);
            $optionWrapper->addChild($label);

            if (
                method_exists(get_class($option), 'getOptions') &&
                !empty($option->getOptions())
            ) {
                $wrapper = $this->getProductOptionCheckboxes(
                    $option->getOptions(),
                    $name,
                    $wrapper,
                    $productOptions,
                    $optionWrapper
                );
            }
        }
        return $wrapper;
    }

    /**
     * Store the assigned product attributes
     *
     * @param array $params contains the parameters of the callback function
     *
     * @return \Cx\Modules\Shop\Model\Entity\Product product with attributes
     */
    public function storeProductAttributes($params)
    {
        if (empty($params['entity'])) {
            return $params['entity'];
        }
        $entity = $params['entity'];
        $options = $params['postedValue'];
        if (empty($options)) {
            $options = array();
        }
        $relProductAttributeRepo = $this->cx->getDb()->getEntityManager()
            ->getRepository(
                'Cx\Modules\Shop\Model\Entity\RelProductAttribute'
            );
        $optionRepo = $this->cx->getDb()->getEntityManager()->getRepository(
            'Cx\Modules\Shop\Model\Entity\Option'
        );

        $existingProductAttributes = $entity->getRelProductAttributes();

        foreach ($existingProductAttributes as $relProductAttribute) {
            if (!isset($options[$relProductAttribute->getOptionId()])) {
                $this->cx->getDb()->getEntityManager()->remove(
                    $relProductAttribute
                );
                $entity->removeRelProductAttribute($relProductAttribute);
            }
        }

        foreach ($options as $optionId=>$value) {
            $productAttribute = $relProductAttributeRepo->findOneBy(
                array(
                    'productId' => $entity->getId(),
                    'optionId' => $optionId
                )
            );
            if (empty($productAttribute)) {
                $productAttribute = new \Cx\Modules\Shop\Model\Entity\RelProductAttribute();
                $productAttribute->setProductId($entity->getId());
                $productAttribute->setProduct($entity);
                $productAttribute->setOptionId($optionId);
                $productAttribute->setOption($optionRepo->find($optionId));
                $productAttribute->setOrd(0);
                $this->cx->getDb()->getEntityManager()->persist(
                    $productAttribute
                );
            }
            $entity->addRelProductAttribute($productAttribute);
        }
        return $entity;
    }

    /**
     * Get a dropdown to select the product distribution
     *
     * @param array $params contains the parameters of the callback function
     *
     * @return \Cx\Core\Html\Model\Entity\DataElement distribution dropdown
     */
    public function getDistributionDropdown($params)
    {
        global $_ARRAYLANG;

        $validValues = array();
        $distributionTypes = \Cx\Modules\Shop\Controller\Distribution::getArrDistributionTypes();

        foreach ($distributionTypes as $distributionType) {
            $validValues[$distributionType] = $_ARRAYLANG[
                'TXT_DISTRIBUTION_' . strtoupper($distributionType)
            ];
        }

        $dropdown = new \Cx\Core\Html\Model\Entity\DataElement(
            $params['name'],
            $params['value'],
            'select',
            null,
            $validValues
        );

        return $dropdown;
    }
}