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
 * Shopnavbar
 * @author Michael Ritter <michael.ritter@cloudrexx.com>
 * @package cloudrexx
 * @subpackage modules_shop
 */

namespace Cx\Modules\Shop\Model\Entity;

/**
 * Shopnavbar
 * @author Michael Ritter <michael.ritter@cloudrexx.com>
 * @package cloudrexx
 * @subpackage modules_shop
 */
class Shopnavbar extends \Cx\Core_Modules\Widget\Model\Entity\WidgetParseTarget {

    /**
     * ID
     * @var int
     */
    protected $id;

    /**
     * Constructor for Shopnavbars
     * @param int $id ID between 1 and 3
     */
    public function __construct($id) {
        $this->id = $id;
    }

    /**
     * Returns this Shopnavbar's ID
     * @return int ID between 1 and 3
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Returns the name of the attribute which contains content that may contain a widget
     * @param string $widgetName
     * @return string Attribute name
     */
    protected function getWidgetContentAttributeName($widgetName) {
        return 'content';
    }

    /**
     * Returns the content of this shop navbar
     * @return string Whop navbar content
     * @throws \Cx\Lib\FileSystem\FileSystemException If file cannot be read
     */
    public function getContent() {
        $fileSuffix = $this->id;
        if ($fileSuffix == 1) {
            $fileSuffix = '';
        }
        $theme = $this->cx->getResponse()->getTheme();
        $filePath = $theme->getFilePath(
            $theme->getFolderName() . '/shopnavbar' . $fileSuffix . '.html'
        );
        $objFile = new \Cx\Lib\FileSystem\File($filePath);
        return $objFile->getData();
    }
}

