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
            'addEditLink'
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
            // Images outside the above directory are copied to the shop image folder.
            // Note that the image paths below do not include the document root, but
            // are relative to it.
            $picture = contrexx_input2raw($params['postedValue'][$i]);
            // Ignore the picture if it's the default image!
            // Storing it would be pointless.
            // Images outside the above directory are copied to the shop image folder.
            // Note that the image paths below do not include the document root, but
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
}