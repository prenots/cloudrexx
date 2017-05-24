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
 * Class EsiWidgetController
 *
 * @copyright   CLOUDREXX CMS - Cloudrexx AG Thun
 * @author      Project Team SS4U <info@comvation.com>
 * @package     cloudrexx
 * @subpackage  module_gallery
 * @version     1.0.0
 */

namespace Cx\Modules\Gallery\Controller;

/**
 * JsonAdapter Controller to handle EsiWidgets
 * Usage:
 * - Create a subclass that implements parseWidget()
 * - Register it as a Controller in your ComponentController
 *
 * @copyright   CLOUDREXX CMS - Cloudrexx AG Thun
 * @author      Project Team SS4U <info@comvation.com>
 * @package     cloudrexx
 * @subpackage  module_gallery
 * @version     1.0.0
 */
class EsiWidgetController extends \Cx\Core_Modules\Widget\Controller\RandomEsiWidgetController {

    /**
     * Parses a widget
     *
     * @param string                     $name     Widget name
     * @param \Cx\Core\Html\Sigma Widget $template Template
     * @param string                     $locale   RFC 3066 locale identifier
     */
    public function parseWidget($name, $template, $locale, $params = array())
    {
        $gallery             = new GalleryHomeContent();
        $gallery->_intLangId = \FWLanguage::getLangIdByIso639_1($locale);
        if ($name === 'GALLERY_LATEST' && $gallery->checkLatest()) {
            $template->setVariable($name, $gallery->getLastImage());
            return;
        }
        if ($name != 'gallery_image' || !$gallery->checkRandom()) {
            return;
        }
        $template->setVariable(
            $name,
            $this->getImageTag($params['id'], $params['lang'])
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function getRandomEsiWidgetContentInfos($widget, $params) {
        if ($widget->getName() != 'GALLERY_RANDOM') {
            return array();
        }

        // fetch all images
        $langId = \FWLanguage::getLanguageIdByCode($params['get']['lang']);
        $imageIds = $this->getRandomizableImageIds($langId);

        // foreach image, get ESI infos:
        $esiInfos = array();
        foreach ($imageIds as $imageId) {
            // adapter name, adapter method, params
            $esiInfos[] = array(
                $this->getName(),
                'getWidget',
                array(
                    'name' => 'gallery_image',
                    'id' => $imageId,
                    'lang' => $params['get']['lang'],
                ),
            );
        }
        return $esiInfos;
    }

    /**
     * Returns the query snippet necessary to check access to the category
     *
     * Make sure to include this in the WHERE part of a query which joins
     * to the category table as "category"
     * @return string SQL snippet
     */
    protected function getCategoryPermissionSqlPart() {
        $objFWUser = \FWUser::getFWUserObject();

        // default case: user is not authenticated
        if (!$objFWUser->objUser->login()) {
            return ' AND
                `category`.`frontendProtected` = 0';
        }

        // user is administrator: no condition necessary
        if ($objFWUser->objUser->getAdminStatus()) {
            return '';
        }

        // user is authenticated but no administrator: check permission IDs
        $categoryAccessCheckQueryPart = ' AND
        (
            `category`.`frontendProtected` = 0';
        if (count($objFWUser->objUser->getDynamicPermissionIds())) {
            $categoryAccessCheckQueryPart .= ' OR
            `category`.`frontend_access_id` IN (
                ' . implode(
                        ', ',
                        $objFWUser->objUser->getDynamicPermissionIds()
                ) . '
            )';
        }
        $categoryAccessCheckQueryPart = '
        )';
        return $categoryAccessCheckQueryPart;
    }

    /**
     * Returns the HTML content for an image
     *
     * This will return an empty string if the picture cannot be accessed
     * @param integer $id Picture ID
     * @param integer $languageId Language ID
     * @return string HTML code for an image
     */
    protected function getImageTag($id, $languageId) {
        $objResult = $this->cx->getDb()->getAdoDb()->Execute('
            SELECT
                `value`
            FROM
                `' . DBPREFIX . 'module_gallery_settings`
            WHERE
                `name` = "paging"
        ');
        $paging = $objResult->fields['value'];
        $objResult = $this->cx->getDb()->getAdoDb()->Execute('
            SELECT
                `picture`.`id`,
                `picture`.`path`,
                `category`.`id` AS `cat_id`,
                `language`.`name`,
                (
                    SELECT
                        COUNT(*)
                    FROM
                        `' . DBPREFIX . 'module_gallery_pictures` AS `pic_count`
                    WHERE `pic_count`.`id` <= `picture`.`id`
                ) AS `picNr`
            FROM
                `' . DBPREFIX . 'module_gallery_pictures` AS `picture`
            JOIN
                `' . DBPREFIX . 'module_gallery_categories` AS `category`
            ON
                `picture`.`catid` = `category`.`id`
            JOIN
                `' . DBPREFIX . 'module_gallery_language_pics` AS `language`
            ON
                `language`.`picture_id` = `picture`.`id`
            WHERE
                `category`.`status` = "1" AND
                `picture`.`validated` = "1" AND
                `picture`.`status` = "1" AND
                `language`.`lang_id` = ' . contrexx_raw2db($languageId) . '
                ' . $this->getCategoryPermissionSqlPart() . ' AND
                `picture`.`id` = ' . contrexx_raw2db($id) . '
        ');

        if ($objResult === false || $objResult->RecordCount() != 1) {
            return '';
        }

        // TODO: This should be moved to a Sigma template
        $picNr = $objResult->fields['picNr'];
        $imageLinkTarget = CONTREXX_DIRECTORY_INDEX.'?section=Gallery&amp;cid='.$objResult->fields['cat_id'].($picNr >= $paging ? '&amp;pos='.(floor($picNr/$paging)*$paging) : '');
        $imageTitle = contrexx_raw2xhtml($objResult->fields['name']);
        $imageWebPath = ASCMS_GALLERY_THUMBNAIL_WEB_PATH . '/' . $objResult->fields['path'];
        $strReturn = '
            <a href="' . $imageLinkTarget . '" target="_self">
                <img alt="' . $imageTitle . '" title="' . $imageTitle . '" src="' . $imageWebPath . '" />
            </a>
        ';
        return $strReturn;
    }

    /**
     * Returns the IDs of all images that can be randomized
     * @param integer $languageId ID of the language to get images of
     * @return array List of image IDs
     */
    protected function getRandomizableImageIds($languageId) {
        $objResult = $this->cx->getDb()->getAdoDb()->Execute('
            SELECT
                `picture`.`id`
            FROM
                `' . DBPREFIX . 'module_gallery_pictures` AS `picture`
            JOIN
                `' . DBPREFIX . 'module_gallery_categories` AS `category`
            ON
                `picture`.`catid` = `category`.`id`
            JOIN
                `' . DBPREFIX . 'module_gallery_language_pics` AS `language`
            ON
                `language`.`picture_id` = `picture`.`id`
            WHERE
                `category`.`status` = "1" AND
                `picture`.`validated` = "1" AND
                `picture`.`status` = "1" AND
                `language`.`lang_id` = ' . contrexx_raw2db($languageId) . '
                ' . $this->getCategoryPermissionSqlPart() . '
        ');

        if ($objResult === false || $objResult->RecordCount() == 0) {
            return array();
        }

        $randomizableImageIds = array();
        while (!$objResult->EOF) {
            $randomizableImageIds[] = $objResult->fields['id'];
            $objResult->MoveNext();
        }
        return $randomizableImageIds;
    }
}
