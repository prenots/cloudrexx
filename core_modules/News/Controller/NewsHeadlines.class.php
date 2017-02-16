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
 * News headlines
 * @copyright   CLOUDREXX CMS - CLOUDREXX AG
 * @author Cloudrexx Development Team <info@cloudrexx.com>
 * @version 1.0.0
 * @package     cloudrexx
 * @subpackage  coremodule_news
 * @todo        Edit PHP DocBlocks!
 */

namespace Cx\Core_Modules\News\Controller;

/**
 * News headlines
 *
 * Gets all the news headlines
 * @copyright   CLOUDREXX CMS - CLOUDREXX AG
 * @author Cloudrexx Development Team <info@cloudrexx.com>
 * @access public
 * @version 1.0.0
 * @package     cloudrexx
 * @subpackage  coremodule_news
 */
class NewsHeadlines extends \Cx\Core_Modules\News\Controller\NewsLibrary
{
    public $_pageContent;
    public $_objTemplate;
    public $arrSettings = array();

    function __construct($pageContent)
    {
        parent::__construct();
        $this->getSettings();
        $this->_pageContent = $pageContent;
        $this->_objTemplate = new \Cx\Core\Html\Sigma('.');
        \Cx\Core\Csrf\Controller\Csrf::add_placeholder($this->_objTemplate);
    }


    function getSettings()
    {
        global $objDatabase;

        $objResult = $objDatabase->Execute("
            SELECT name, value FROM ".DBPREFIX."module_news_settings");
        if ($objResult !== false) {
            while (!$objResult->EOF) {
                $this->arrSettings[$objResult->fields['name']] = $objResult->fields['value'];
                $objResult->MoveNext();
            }
        }
    }


    function getHomeHeadlines($catId = 0, $langId = 0)
    {
        global $_CORELANG, $objDatabase;

        $i = 0;
        $catId= intval($catId);

        if (empty($langId)) {
            $langId = \Env::get('init')->getDefaultFrontendLangId();
        }

        $this->_objTemplate->setTemplate($this->_pageContent,true,true);

        $this->_objTemplate->setGlobalVariable(array(
            'TXT_MORE_NEWS'         => $_CORELANG['TXT_MORE_NEWS'],
            'TXT_NEWS_MORE'         => $_ARRAYLANG['TXT_NEWS_MORE'],
            'TXT_NEWS_MORE_INFO'    => $_ARRAYLANG['TXT_NEWS_MORE_INFO'],
            'TXT_NEWS_HEADLINE'     => $_ARRAYLANG['TXT_NEWS_HEADLINE'],
        ));

        $newsLimit = intval($this->arrSettings['news_headlines_limit']);
        if ($newsLimit>50) { //limit to a maximum of 50 news
            $newsLimit=50;
        }

        if ($newsLimit<1) { //do not get any news if 0 was specified as the limit.
            $objResult=false;
        } else {//fetch news
            $objResult = $objDatabase->SelectLimit("
                SELECT DISTINCT(tblN.id) AS newsid,
                       tblN.`date` AS newsdate,
                       tblN.teaser_image_path,
                       tblN.teaser_image_thumbnail_path,
                       tblN.redirect,
                       tblN.publisher,
                       tblN.publisher_id,
                       tblN.author,
                       tblN.author_id,
                       tblN.redirect_new_window AS redirectNewWindow,
                       tblN.changelog,
                       tblN.source,
                       tblN.allow_comments AS commentactive,
                       tblN.enable_tags,
                       tblN.url1,
                       tblN.url2,
                       tblL.text NOT REGEXP '^(<br type=\"_moz\" />)?\$' AS newscontent,
                       tblL.text AS text,
                       tblL.title AS newstitle,
                       tblL.teaser_text
                  FROM ".DBPREFIX."module_news AS tblN
            INNER JOIN ".DBPREFIX."module_news_locale AS tblL ON tblL.news_id=tblN.id
            INNER JOIN ".DBPREFIX."module_news_rel_categories AS tblC ON tblC.news_id=tblL.news_id
                  WHERE tblN.status=1".
                   ($catId > 0 ? " AND tblC.category_id=$catId" : '')."
                   AND tblN.teaser_only='0'
                   AND tblL.lang_id=".$langId."
                   AND tblL.is_active=1
                   AND (startdate<='".date('Y-m-d H:i:s')."' OR startdate='0000-00-00 00:00:00')
                   AND (enddate>='".date('Y-m-d H:i:s')."' OR enddate='0000-00-00 00:00:00')".
                   ($this->arrSettings['news_message_protection'] == '1' && !\Permission::hasAllAccess()
                      ? (($objFWUser = \FWUser::getFWUserObject()) && $objFWUser->objUser->login()
                          ? " AND (frontend_access_id IN (".
                            implode(',', array_merge(array(0), $objFWUser->objUser->getDynamicPermissionIds())).
                            ") OR userid=".$objFWUser->objUser->getId().") "
                          : " AND frontend_access_id=0 ")
                      : '').
                   "ORDER BY date DESC", $newsLimit);
        }

        if ($objResult !== false && $objResult->RecordCount() >= 0) {
            while (!$objResult->EOF) {
                $newsid = $objResult->fields['newsid'];
                $newsCategories = $this->getCategoriesByNewsId($newsid);
                $newsUrl   = empty($objResult->fields['redirect'])
                                ? (empty($objResult->fields['newscontent'])
                                    ? ''
                                    : \Cx\Core\Routing\Url::fromModuleAndCmd('News', $this->findCmdById('details', self::sortCategoryIdByPriorityId(array_keys($newsCategories), array($catId))), FRONTEND_LANG_ID, array('newsid' => $newsid)))
                                : $objResult->fields['redirect'];

                //Parse all the news placeholders
                $this->parseNewsPlaceholders($this->_objTemplate, $objResult, $newsUrl);

                $this->_objTemplate->setVariable(array(
                    'NEWS_CSS'          => 'row'.($i % 2 + 1),
                ));

                $this->_objTemplate->parse('headlines_row');
                $i++;
                $objResult->MoveNext();
            }
        } else {
            $this->_objTemplate->hideBlock('headlines_row');
        }

        return $this->_objTemplate->get();
    }
}
