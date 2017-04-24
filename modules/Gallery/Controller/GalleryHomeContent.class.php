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
 * Gallery home content
 * @copyright   CLOUDREXX CMS - CLOUDREXX AG
 * @author      Cloudrexx Development Team
 * @version     1.0.0
 * @package     cloudrexx
 * @subpackage  module_gallery
 * @todo        Edit PHP DocBlocks!
 */
namespace Cx\Modules\Gallery\Controller;
/**
 * Gallery home content
 *
 * Show Gallery Block Content (Random, Last)
 * @copyright   CLOUDREXX CMS - CLOUDREXX AG
 * @author      Cloudrexx Development Team
 * @access      public
 * @version     1.0.0
 * @package     cloudrexx
 * @subpackage  module_gallery
 */
class GalleryHomeContent extends GalleryLibrary
{
    public $_intLangId;
    public $_strWebPath;

    /**
     * Initialize the settings, webpath and language.
     *
     * @param integer $langId Language id
     */
    function __construct($langId = 0)
    {
        if ($langId) {
            $this->_intLangId = $langId;
        }
        $this->getSettings();
        $this->_strWebPath  = ASCMS_GALLERY_THUMBNAIL_WEB_PATH . '/';
    }


    /**
     * Check if the random-function is activated
     * @return boolean
     */
    function checkRandom() {
        if ($this->arrSettings['show_random'] == 'on') {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Check if the latest-function is activated
     *
     * @return boolean
     */
    function checkLatest() {
        if ($this->arrSettings['show_latest'] == 'on') {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Returns the number of images per category that are accessible for randomizer
     *
     * @return array Index is the category ID, value is the number of accessible pictures in it
     */
    function getPictureIdsForRandomizer()
    {
        $objDatabase = \Cx\Core\Core\Controller\Cx::instanciate()->getDb()->getAdoDb();

        // get categories
        $objFWUser = \FWUser::getFWUserObject();
        $query = '
            SELECT
                pics.id
            FROM
                '.DBPREFIX.'module_gallery_categories AS categories
            INNER JOIN
                '.DBPREFIX.'module_gallery_pictures AS pics
            ON
                pics.catid = categories.id
            WHERE
                categories.status = "1" AND
                pics.validated = "1" AND
                pics.status = "1"
        ';
        if ($objFWUser->objUser->login()) {
            // user is authenticated
            if (!$objFWUser->objUser->getAdminStatus()) {
                // user is not administrator
                $query .= ' AND
                (
                    categories.frontendProtected = 0';
                if (count($objFWUser->objUser->getDynamicPermissionIds())) {
                    $query .= ' OR
                    categories.frontend_access_id IN (' . implode(
                        ', ',
                        $objFWUser->objUser->getDynamicPermissionIds()
                    ) . ')';
                }
                $query .= '
                )';
            }
        } else {
            $query .= ' AND categories.frontendProtected = 0';
        }

        $objResult = $objDatabase->query($query);
        if ($objResult === false || $objResult->RecordCount() == 0) {
            return array();
        }

        $pics = array();
        while (!$objResult->EOF) {
            $pics[] = $objResult->fields['id'];
            $objResult->MoveNext();
        }
        return $pics;
    }

    /**
     * Returns the last inserted image from database
     *
     * @return     string     Complete <img>-tag for a randomized image
     */
    function getLastImage()
    {
        $objDatabase = \Cx\Core\Core\Controller\Cx::instanciate()->getDb()->getAdoDb();

        $picNr = 0;
        $objResult = $objDatabase->Execute('
            SELECT
                pics.id,
                pics.catid  AS CATID,
                pics.path   AS PATH,
                lang.name   AS NAME
            FROM
                '.DBPREFIX.'module_gallery_pictures AS pics
            INNER JOIN
                '.DBPREFIX.'module_gallery_language_pics AS lang
            ON
                pics.id = lang.picture_id
            INNER JOIN
                '.DBPREFIX.'module_gallery_categories AS categories
            ON
                pics.catid = categories.id
            WHERE
                categories.status = "1" AND
                pics.validated = "1" AND
                pics.status = "1" AND
                lang.lang_id = ' . $this->_intLangId . '
            ORDER BY
                pics.id DESC
            LIMIT       1
        ');

        if ($objResult->RecordCount() != 1) {
            return '';
        }
        $objPaging = $objDatabase->SelectLimit(
            '
                SELECT
                    value
                FROM
                    '.DBPREFIX.'module_gallery_settings
                WHERE
                    name = "paging"
            ',
            1
        );
        $paging = $objPaging->fields['value'];

        $objPos = $objDatabase->Execute('
            SELECT
                pics.id
            FROM
                '.DBPREFIX.'module_gallery_pictures AS pics
            INNER JOIN
                '.DBPREFIX.'module_gallery_language_pics AS lang
            ON
                pics.id = lang.picture_id
            INNER JOIN
                '.DBPREFIX.'module_gallery_categories AS categories
            ON
                pics.catid = categories.id
            WHERE
                categories.status = "1" AND
                pics.validated = "1" AND
                pics.status = "1" AND
                lang.lang_id = ' . $this->_intLangId . '
            ORDER BY
                pics.sorting
        ');
        if ($objPos !== false) {
            while (!$objPos->EOF) {
                if ($objPos->fields['id'] == $objResult->fields['id']) {
                    break;
                } else {
                    $picNr++;
                }
                $objPos->MoveNext();
            }
        }

        $strReturn =    '<a href="'.CONTREXX_DIRECTORY_INDEX.'?section=Gallery&amp;cid='.$objResult->fields['CATID'].($picNr >= $paging ? '&amp;pos='.(floor($picNr/$paging)*$paging) : '').'" target="_self">';
        $strReturn .=   '<img alt="'.$objResult->fields['NAME'].'" title="'.$objResult->fields['NAME'].'" src="'.$this->_strWebPath.$objResult->fields['PATH'].'" /></a>';
        return $strReturn;
    }

    /**
     * Get image tag by ID
     *
     * This checks if the current user has access to the picture's category
     * and if the picture is available (/has a name) in the current language
     * @param integer $id picture ID
     * @return string img tag with surrounding link or empty string on failure
     */
    public function getImageById($id)
    {
        $objDatabase = \Cx\Core\Core\Controller\Cx::instanciate()->getDb()->getAdoDb();

        // check if the picture exists and we have access to it
        $objFWUser = \FWUser::getFWUserObject();
        $query = '
            SELECT
                pics.catid AS CATID,
                pics.path AS PATH,
                lang.name AS NAME
            FROM
                '.DBPREFIX.'module_gallery_categories AS categories
            INNER JOIN
                '.DBPREFIX.'module_gallery_pictures AS pics
            ON
                pics.catid = categories.id
            INNER JOIN
                '.DBPREFIX.'module_gallery_language_pics AS lang
            ON
                lang.picture_id = pics.id
            WHERE
                categories.status = "1" AND
                pics.validated = "1" AND
                pics.status = "1" AND
                pics.id = ' . contrexx_raw2db($id) . ' AND
                lang.lang_id = ' . $this->_intLangId;
        if ($objFWUser->objUser->login()) {
            // user is authenticated
            if (!$objFWUser->objUser->getAdminStatus()) {
                // user is not administrator
                $query .= ' AND
                (
                    categories.frontendProtected = 0';
                if (count($objFWUser->objUser->getDynamicPermissionIds())) {
                    $query .= ' OR
                    categories.frontend_access_id IN (' . implode(
                        ', ',
                        $objFWUser->objUser->getDynamicPermissionIds()
                    ) . ')';
                }
                $query .= '
                )';
            }
        } else {
            $query .= ' AND categories.frontendProtected = 0';
        }

        $result = $objDatabase->query($query);
        if (!$result || $result->EOF) {
            return '';
        }

        // Picture exists, is visible in our lang and we have access to its cat
        $catId = $result->fields['CATID'];
        $path = $result->fields['PATH'];
        $name = $result->fields['NAME'];

        // Get paging count from DB
        $result = $objDatabase->query('
            SELECT
                `value`
            FROM
                `' . DBPREFIX . 'module_gallery_settings`
            WHERE
                `name` = "paging"
        ');
        if (!$result || $result->EOF) {
            return '';
        }
        $paging = $result->fields['value'];

        // Get number of pictures in our category
        $result = $objDatabase->query('
            SELECT
                pics.id
            FROM
                '.DBPREFIX.'module_gallery_pictures AS pics
            INNER JOIN
                '.DBPREFIX.'module_gallery_language_pics AS lang
            ON
                pics.id = lang.picture_id
            WHERE
                pics.validated = "1" AND
                pics.status = "1" AND
                pics.catid = ' . $catId . ' AND
                lang.lang_id = ' . $this->_intLangId
        );
        if (!$result || $result->EOF) {
            return '';
        }

        // calculate our picture's number
        $offset = 0;
        while (!$result->EOF) {
            $offset++;
            if ($result->fields['id'] == $id) {
                break;
            }
            $result->MoveNext();
        }

        // Create HTML including link with paging
        $pagingUrl = \Cx\Core\Routing\Url::fromModuleAndCmd('Gallery');
        $pagingUrl->setParam('cid', $catId);
        if ($offset >= $paging) {
            $pagingPosition = floor($offset / $paging) * $paging;
            $pagingUrl->setParam('pos', $pagingPosition);
        }
        $pictureName = contrexx_raw2xhtml($name);
        $picturePath = $this->_strWebPath.$path;
        $strReturn = '<a href="' . $pagingUrl->toString() . '" target="_self">';
        $strReturn .= '<img alt="' . $pictureName . '" title="' . $pictureName . '" src="' . $picturePath . '" />';
        $strReturn .= '</a>';
        return $strReturn;
    }
}
