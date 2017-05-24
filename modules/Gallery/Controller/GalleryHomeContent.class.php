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
     * Returns the last inserted image from database
     *
     * @global     ADONewConnection
     * @global     array
     * @global     array
     * @return     string     Complete <img>-tag for a randomized image
     */
    function getLastImage()
    {
        global $objDatabase;

        $picNr = 0;
        $objResult = $objDatabase->Execute(
            'SELECT `pics`.`id`,
                    `pics`.`catid`  AS CATID,
                    `pics`.`path`   AS PATH,
                    `lang`.`name`   AS NAME,
                    `cat`.`frontendProtected`,
                    `cat`.`frontend_access_id` as frontendAccessId
                FROM '.DBPREFIX.'module_gallery_pictures AS pics
                    INNER JOIN  '.DBPREFIX.'module_gallery_language_pics AS lang
                        ON `pics`.`id` = `lang`.`picture_id`
                    INNER JOIN  '.DBPREFIX.'module_gallery_categories AS cat
                        ON `pics`.`catid` = `cat`.`id`
                WHERE `cat`.`status` = \'1\'
                    AND `pics`.`validated` = \'1\'
                    AND `pics`.`status` = \'1\'
                    AND `lang`.`lang_id` = '.$this->_intLangId.'
                ORDER BY `pics`.`id` DESC
                LIMIT 1'
        );

        if ($objResult->RecordCount() == 1) {

            //check the user access permission to view the latest picture
            if ($objResult->fields['frontendProtected']) {
                \Cx\Core\Core\Controller\Cx::instanciate()
                    ->getComponent('Session')->getSession();
                $objFWUser   = \FWUser::getFWUserObject();

                if (!$objFWUser->objUser->login()) {
                    return '';
                }

                $dynamicPermissionIds = $objFWUser->objUser->getDynamicPermissionIds();
                if (
                    $objFWUser->objUser->login() &&
                    !$objFWUser->objUser->getAdminStatus() &&
                    count($dynamicPermissionIds) &&
                    !in_array(
                        $objResult->fields['frontendAccessId'],
                        $dynamicPermissionIds
                    )
                ) {
                    return '';
                }
            }

            $objPaging = $objDatabase->SelectLimit("SELECT value FROM ".DBPREFIX."module_gallery_settings WHERE name='paging'", 1);
            $paging = $objPaging->fields['value'];

            $objPos = $objDatabase->Execute('SELECT     pics.id
                                                FROM        '.DBPREFIX.'module_gallery_pictures         AS pics
                                                INNER JOIN  '.DBPREFIX.'module_gallery_language_pics    AS lang         ON pics.id = lang.picture_id
                                                INNER JOIN  '.DBPREFIX.'module_gallery_categories       AS categories   ON pics.catid = categories.id
                                                WHERE       categories.status = "1"     AND
                                                            pics.validated = "1"        AND
                                                            pics.status = "1"           AND
                                                            lang.lang_id = '.$this->_intLangId.'
                                                ORDER BY    pics.sorting');
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
        } else {
            return '';
        }
    }
}
