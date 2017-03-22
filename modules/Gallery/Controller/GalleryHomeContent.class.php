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
     * Constructor php5
     */
    function __construct() {
        global $_LANGID;

        $this->getSettings();
        $this->_intLangId   = $_LANGID;
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
     * Get all the image ids
     *
     * @return array
     */
    public function getImageIds()
    {
        $objDatabase = \Cx\Core\Core\Controller\Cx::instanciate()
            ->getDb()->getAdoDb();
        \Cx\Core\Core\Controller\Cx::instanciate()
            ->getComponent('Session')->getSession();
        $objFWUser   = \FWUser::getFWUserObject();

        $where = '';
        if (!$objFWUser->objUser->login()) {
            $where = ' AND `categories`.`frontendProtected` = 0';
        }

        if (
            $objFWUser->objUser->login() &&
            !$objFWUser->objUser->getAdminStatus()
        ) {
            $where = ' AND (`categories`.`frontendProtected` = 0';
            $dynamicPermissionIds = $objFWUser->objUser->getDynamicPermissionIds();
            if (count($dynamicPermissionIds)) {
                $where .= ' OR `categories`.`frontend_access_id` IN (' .
                    implode(', ', $dynamicPermissionIds) . ')';
            }
            $where .= ')';
        }
        $query = '
            SELECT `pics`.`id` as picId
                FROM  `' . DBPREFIX . 'module_gallery_categories` AS categories
                INNER JOIN `' . DBPREFIX . 'module_gallery_pictures` AS pics
                    ON `pics`.`catid` = `categories`.`id`
                WHERE `categories`.`status` = \'1\'
                    AND `pics`.`validated`  = \'1\'
                    AND `pics`.`status`     = \'1\'' . $where . '
                ORDER BY `pics`.`sorting`';
        $objResult = $objDatabase->Execute($query);
        $entryIds  = array();
        if (!$objResult || $objResult->RecordCount() == 0) {
            return $entryIds;
        }

        while (!$objResult->EOF) {
            $entryIds[] = $objResult->fields['picId'];
            $objResult->MoveNext();
        }

        return $entryIds;
    }

    /**
     * Returns an randomized image from database
     *
     * @return     string     Complete <img>-tag for a randomized image
     */
    function getRandomImage()
    {
        $objDatabase = \Cx\Core\Core\Controller\Cx::instanciate()->getDb()->getAdoDb();

        $objFWUser = \FWUser::getFWUserObject();
        $baseQuery = '
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
                lang.lang_id = ' . $this->_intLangId;
        if ($objFWUser->objUser->login()) {
            // user is authenticated
            if (!$objFWUser->objUser->getAdminStatus()) {
                // user is not administrator
                $baseQuery .= ' AND
                (
                    categories.frontendProtected = 0';
                if (count($objFWUser->objUser->getDynamicPermissionIds())) {
                    $baseQuery .= ' OR
                    categories.frontend_access_id IN (' . implode(
                        ', ',
                        $objFWUser->objUser->getDynamicPermissionIds()
                    ) . ')';
                }
                $baseQuery .= '
                )';
            }
        } else {
            $baseQuery .= ' AND categories.frontendProtected = 0';
        }
        $baseQuery .= '
            GROUP BY
                categories.id
            ORDER BY
                categories.id
        ';

        $query = '
            SELECT
                SUM(1) AS catCount';
        $query .= $baseQuery;
        $objResult = $objDatabase->Execute($query);

        if ($objResult === false || $objResult->RecordCount() == 0) {
            return '';
        }

        $catNr = mt_rand(0, $objResult->RecordCount()-1);

        $query = '
            SELECT
                categories.id';
        $query .= $baseQuery;

        $objResult = $objDatabase->SelectLimit($query, 1, $catNr);
        if ($objResult === false || $objResult->RecordCount() == 0) {
            return '';
        }
        $catId = $objResult->fields['id'];

        $objResult = $objDatabase->SelectLimit(
            'SELECT
                SUM(1) AS picCount
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
            ,
            1
        );

        if ($objResult === false || $objResult->RecordCount() == 0) {
            return '';
        }
        $picNr = mt_rand(0, $objResult->fields['picCount']-1);

        $objResult = $objDatabase->SelectLimit(
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
        $paging = $objResult->fields['value'];

        $objResult = $objDatabase->SelectLimit(
            '
                SELECT
                    pics.catid AS CATID,
                    pics.path AS PATH,
                    lang.name AS NAME
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
                    lang.lang_id = ' . $this->_intLangId . '
                ORDER BY
                    pics.sorting
            ',
            1,
            $picNr
        );

        if ($objResult === false) {
            return '';
        }
        $pagingUrl = \Cx\Core\Routing\Url::fromModuleAndCmd('Gallery');
        $pagingUrl->setParam('cid', $objResult->fields['CATID']);
        if ($picNr >= $paging) {
            $pagingPosition = floor($picNr / $paging) * $paging;
            $pagingUrl->setParam('pos', $pagingPosition);
        }
        $pictureName = contrexx_raw2xhtml($objResult->fields['NAME']);
        $picturePath = $this->_strWebPath.$objResult->fields['PATH'];
        $strReturn = '<a href="' . $pagingUrl->toString() . '" target="_self">';
        $strReturn .= '<img alt="' . $pictureName . '" title="' . $pictureName . '" src="' . $picturePath . '" />';
        $strReturn .= '</a>';
        return $strReturn;
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
     * @param integer $id picture ID
     *
     * @return string
     */
    public function getImageById($id)
    {
        if (empty($id)) {
            return;
        }

        $objDatabase = \Cx\Core\Core\Controller\Cx::instanciate()
            ->getDb()->getAdoDb();
        $query = '
            SELECT `catid` AS catId,
                   `path` AS path
                FROM `' . DBPREFIX . 'module_gallery_pictures` AS pics
                WHERE `validated` = \'1\'
                    AND `status`  = \'1\'
                    AND `id`      = ' . contrexx_input2db($id);
        $objResult = $objDatabase->Execute($query);
        if (!$objResult || $objResult->RecordCount() == 0) {
            return '';
        }

        //Loggedin user have permission to view the gallery picture
        $objFWUser = \FWUser::getFWUserObject();
        if (!$objFWUser->objUser->login()) {
            $where = ' AND `cat`.`frontendProtected` = 0';
        }

        if (
            $objFWUser->objUser->login() &&
            !$objFWUser->objUser->getAdminStatus()
        ) {
            $where = ' AND (`cat`.`frontendProtected` = 0';
            $dynamicPermissionIds = $objFWUser->objUser->getDynamicPermissionIds();
            if (count($dynamicPermissionIds)) {
                $where .= ' OR `cat`.`frontend_access_id` IN (' .
                    implode(', ', $dynamicPermissionIds) . ')';
            }
            $where .= ')';
        }

        $query = '
            SELECT 1
                FROM `' . DBPREFIX . 'module_gallery_pictures` as pic
                LEFT JOIN `' . DBPREFIX . 'module_gallery_categories` as cat
                    ON `pic`.`catid` = `cat`.`id`
                WHERE `pic`.`id` = ' . contrexx_input2db($id) . $where;

        $result = $objDatabase->Execute($query);
        if (!$result || $result->RecordCount() == 0) {
            return '';
        }

        //Get paging count from DB
        $paging = $objDatabase->getOne(
            'SELECT `value`
                FROM `' . DBPREFIX . 'module_gallery_settings`
                 WHERE `name` = \'paging\''
        );

        //Preparing paging link for the current gallery picture ID
        $objPos = $objDatabase->Execute(
            'SELECT `pics`.`id`
                FROM `' . DBPREFIX . 'module_gallery_pictures` AS pics
                WHERE   `pics`.`validated` = \'1\'
                    AND `pics`.`status` = \'1\'
                    AND `pics`.`catid`  = ' . $objResult->fields['catId'] . '
                ORDER BY `pics`.`sorting`'
        );
        $picNr = 0;
        if ($objPos !== false) {
            while (!$objPos->EOF) {
                if ($objPos->fields['id'] == $id) {
                    break;
                } else {
                    $picNr++;
                }
                $objPos->MoveNext();
            }
        }

        $pos = 0;
        if ($picNr > $paging) {
            $pageNum = ceil($picNr / $paging);
            $pos     = (($pageNum - 1) * $paging);
        }
        if ($picNr == $paging) {
            $pos = $picNr;
        }
        $url = \Cx\Core\Routing\Url::fromModuleAndCmd(
            'Gallery',
            '',
            '',
            array('cid' => $objResult->fields['catId'], 'pos' => $pos)
        )->toString();

        $imgName = $this->getPictureNameByCriteria($id, $this->_intLangId);
        if (!$imgName) {
            $imgName = $this->getPictureNameByCriteria($id);
        }

        $image = \Html::getImageByPath(
            '/' . $this->_strWebPath . $objResult->fields['path'],
            'alt=\'' . contrexx_raw2xhtml($imgName) .
            '\' title=\'' . contrexx_raw2xhtml($imgName) . '\''
        );

        return \Html::getLink($url, $image, '_self');
    }

    /**
     * Get picture name
     *
     * @param integer $picId  picture ID
     * @param integer $langId language ID
     *
     * @return type
     */
    public function getPictureNameByCriteria($picId, $langId)
    {
        if (empty($picId)) {
            return;
        }

        global $objDatabase;

        if (empty($langId)) {
            $langId = \FWLanguage::getDefaultLangId();
        }

        $query = '
            SELECT `picLang`.`name`
                FROM `' . DBPREFIX . 'module_gallery_language_pics` as picLang
                WHERE   `picture_id` = ' . contrexx_input2db($picId) . '
                    AND `lang_id`    = ' . contrexx_input2db($langId);
        $picName = $objDatabase->Execute($query);
        if (!$picName || $picName->RecordCount() == 0) {
            return;
        }

        return $picName->fields['name'];
    }
}
