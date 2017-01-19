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
 * Framework language
 *
 * @copyright   CLOUDREXX CMS - CLOUDREXX AG
 * @author      Cloudrexx Development Team <info@cloudrexx.com>
 * @version     2.3.0
 * @package     cloudrexx
 * @subpackage  lib_framework
 * @todo        Edit PHP DocBlocks!
 */

/**
 * Framework language
 *
 * @copyright   CLOUDREXX CMS - CLOUDREXX AG
 * @author      Cloudrexx Development Team <info@cloudrexx.com>
 * @version     2.3.0
 * @package     cloudrexx
 * @subpackage  lib_framework
 */
class FWLanguage
{
    /**
     * @var array Mapping of language ID to locale
     * @todo make it complete (at least six elements)
     * @todo make it configurable
     * @todo make it support full locale (ab_CD)
     * @author  Reto Kohli <reto.kohli@comvation.com>
     */
    protected static $locales = array( 99 => 'sm_PL', );

    /**
     * Array containing the active frontend languages
     * @var array
     */
    protected static $arrFrontendLanguages;

    /**
     * Array containing the active backend languages
     * @var array
     */
    protected static $arrBackendLanguages;

    /**
     * ID of the default frontend language
     *
     * @var integer
     * @access protected
     */
    protected static $defaultFrontendLangId;

    /**
     * ID of the default backend language
     *
     * @var integer
     * @access protected
     */
    protected static $defaultBackendLangId;


    /**
     * Loads the language config from the database
     *
     * This used to be in __construct but is also
     * called from core/language.class.php to reload
     * the config, so core/settings.class.php can
     * rewrite .htaccess (virtual lang dirs).
     */
    public static function init()
    {
        global $_CONFIG, $objDatabase;

        $objLocaleResult = $objDatabase->Execute('SELECT * FROM '.DBPREFIX.'core_locale_locale');

        // frontend locales
        while (!$objLocaleResult->EOF) {
            // get the theme for each channel of the locale's language
            $themeId = $mobileThemeId = $printThemeId = $pdfThemeId = $appThemeId = 0;
            $objFrontendResult = $objDatabase->Execute('SELECT theme, channel FROM '.DBPREFIX.'core_view_frontend WHERE language = '.$objLocaleResult->fields['id']);
            while (!$objFrontendResult->EOF) {
                switch ($objFrontendResult->fields['channel']) {
                    case \Cx\Core\View\Model\Entity\Theme::THEME_TYPE_MOBILE:
                        $mobileThemeId = $objFrontendResult->fields['theme'];
                        break;
                    case \Cx\Core\View\Model\Entity\Theme::THEME_TYPE_PRINT:
                        $printThemeId = $objFrontendResult->fields['theme'];
                        break;
                    case \Cx\Core\View\Model\Entity\Theme::THEME_TYPE_PDF:
                        $pdfThemeId = $objFrontendResult->fields['theme'];
                        break;
                    case \Cx\Core\View\Model\Entity\Theme::THEME_TYPE_APP:
                        $appThemeId = $objFrontendResult->fields['theme'];
                        break;
                    default: // web
                        $themeId = $objFrontendResult->fields['theme'];
                        break;
                }
                $objLocaleResult->moveNext();
            }
            // check if locale is default
            $isFrontendDefault = $objLocaleResult->fields['id'] == $_CONFIG['defaultLocaleId'];
            static::$arrFrontendLanguages[$objLocaleResult->fields['id']] = array(
                'id'  => $objLocaleResult->fields['id'],
                'lang' => $objLocaleResult->fields['alpha'] ? $objLocaleResult->fields['iso_1'].'-'.$objLocaleResult->fields['country'] : $objLocaleResult->fields['iso_1'],
                'name' => $objLocaleResult->fields['label'],
                'iso1' => $objLocaleResult->fields['iso_1'],
                'source_lang' => $objLocaleResult->fields['source_language'],
                'themesid'   => $themeId,
                'print_themes_id' => $printThemeId,
                'pdf_themes_id' => $pdfThemeId,
                'mobile_themes_id' => $mobileThemeId,
                'app_themes_id' => $appThemeId,
                'frontend'   => true, // every existing locale is active
                'is_default' => $isFrontendDefault,
                'fallback'   => $objLocaleResult->fields['fallback'] ? $objLocaleResult->fields['fallback'] : false,
            );
            if ($isFrontendDefault) {
                static::$defaultFrontendLangId = $objLocaleResult->fields['id'];
            }
            $objLocaleResult->moveNext();
        }

        // backend languages
        $objBackendResult = $objDatabase->Execute('SELECT * FROM '.DBPREFIX.'core_locale_backend');
        while (!$objBackendResult->EOF) {
            // check if language is default
            $isBackendDefault = $objBackendResult->fields['id'] == $_CONFIG['defaultLanguageId'];
            static::$arrBackendLanguages[$objBackendResult->fields['id']] = array(
                'id' => $objBackendResult->fields['id'],
                'lang' => $objBackendResult->fields['iso_1'],
                'name' => \Locale::getDisplayLanguage($objBackendResult->fields['iso_1']),
                'backend' => true,
                'is_default' => $isBackendDefault
            );
            if ($isBackendDefault) {
                static::$defaultBackendLangId = $objBackendResult->fields['id'];
            }
            $objBackendResult->moveNext();
        }
    }


    /**
     * Returns an array of active language names, indexed by language ID
     * @param   string  $mode     'frontend' or 'backend' languages.
     *                            Defaults to 'frontend'
     * @return  array             The array of enabled language names
     * @author  Reto Kohli <reto.kohli@comvation.com>
     */
    public static function getNameArray($mode='frontend')
    {
        switch($mode) {
            case 'backend':
                if (!isset(static::$arrBackendLanguages)) static::init();
                $arrLanguages = static::$arrBackendLanguages;
                break;
            case 'frontend':
            default:
                if (!isset(static::$arrFrontendLanguages)) static::init();
                $arrLanguages = static::$arrFrontendLanguages;
                break;
        }
        $arrName = array();
        foreach ($arrLanguages as $lang_id => $arrLanguage) {
            if (empty($arrLanguage[$mode])) continue;
            $arrName[$lang_id] = $arrLanguage['name'];
        }
        return $arrName;
    }


    /**
     * Returns an array of active language IDs
     *
     * Note that the array returned contains the language ID both as
     * key and value, for your convenience.
     * @param   string  $mode     'frontend' or 'backend' languages.
     *                            Defaults to 'frontend'
     * @return  array             The array of enabled language IDs
     * @author  Reto Kohli <reto.kohli@comvation.com>
     */
    public static function getIdArray($mode='frontend')
    {
        switch($mode) {
            case 'backend':
                if (!isset(static::$arrBackendLanguages)) static::init();
                $arrLanguages = static::$arrBackendLanguages;
                break;
            case 'frontend':
            default:
                if (!isset(static::$arrFrontendLanguages)) static::init();
                $arrLanguages = static::$arrFrontendLanguages;
                break;
        }
        $arrId = array();
        foreach ($arrLanguages as $lang_id => $arrLanguage) {
            if (empty($arrLanguage[$mode])) continue;
            $arrId[$lang_id] = $lang_id;
        }
        return $arrId;
    }


    /**
     * Returns the ID of the default frontend language
     * @return integer Language ID
     */
    public static function getDefaultLangId()
    {
        if (empty(static::$defaultFrontendLangId)) {
            static::init();
        }
        return static::$defaultFrontendLangId;
    }

    /**
     * Returns the ID of the default backend language
     * @return integer Language ID
     */
    public static function getDefaultBackendLangId()
    {
        if (empty(static::$defaultBackendLangId)) {
            static::init();
        }
        return static::$defaultBackendLangId;
    }


    /**
     * Returns the complete frontend language data
     * @see     FWLanguage()
     * @return  array           The language data
     * @access  public
     */
    public static function getLanguageArray()
    {
        if (empty(static::$arrFrontendLanguages)) static::init();
        return static::$arrFrontendLanguages;
    }

    /**
     * Returns the complete backend language data
     * @see     FWLanguage()
     * @return  array           The language data
     * @access  public
     */
    public static function getBackendLanguageArray()
    {
        if (empty(static::$arrBackendLanguages)) static::init();
        return static::$arrBackendLanguages;
    }


    /**
     * Return only the languages active in the frontend
     * @author     Stefan Heinemann <sh@adfinis.com>
     * @return     array(
     *                 array(
     *                     'id'         => {lang_id},
     *                     'lang'       => {iso_639-1},
     *                     'name'       => {name},
     *                     'themesid'   => {theme_id},
     *                     'frontend'   => {bool},
     *                     'backend'    => {bool},
     *                     'is_default' => {bool},
     *                     'fallback'   => {language_id},
     *                 )
     *             )
     */
    public static function getActiveFrontendLanguages()
    {
        if (empty(static::$arrFrontendLanguages)) {
            static::init();
        }
        $arr = array();
        foreach (static::$arrFrontendLanguages as $id => $lang) {
            if ($lang['frontend']) {
                $arr[$id] = $lang;
            }
        }
        return $arr;
    }


    /**
     * Return only the languages active in the backend
     * @author     Stefan Heinemann <sh@adfinis.com>
     * @return     array(
     *                 array(
     *                     'id'         => {lang_id},
     *                     'lang'       => {iso_639-1},
     *                     'name'       => {name},
     *                     'themesid'   => {theme_id},
     *                     'frontend'   => {bool},
     *                     'backend'    => {bool},
     *                     'is_default' => {bool},
     *                     'fallback'   => {language_id},
     *                 )
     *             )
     */
    public static function getActiveBackendLanguages()
    {
        if (empty(static::$arrBackendLanguages)) {
            static::init();
        }
        $arr = array();
        foreach (static::$arrBackendLanguages as $id => $lang) {
            if ($lang['backend']) {
                $arr[$id] = $lang;
            }
        }
        return $arr;
    }


    /**
     * Returns single frontend language related fields
     *
     * Access language data by specifying the language ID and the index
     * as initialized by {@link FWLanguage()}.
     * @return  mixed           Language data field content
     * @access  public
     */
    public static function getLanguageParameter($id, $index)
    {
        if (empty(static::$arrFrontendLanguages)) static::init();
        return (isset(static::$arrFrontendLanguages[$id][$index])
            ? static::$arrFrontendLanguages[$id][$index] : false);
    }

    /**
     * Returns single backend language related fields
     *
     * Access language data by specifying the language ID and the index
     * as initialized by {@link FWLanguage()}.
     * @return  mixed           Language data field content
     * @access  public
     */
    public static function getBackendLanguageParameter($id, $index)
    {
        if (empty(static::$arrBackendLanguages)) static::init();
        return (isset(static::$arrBackendLanguages[$id][$index])
            ? static::$arrBackendLanguages[$id][$index] : false);
    }


    /**
     * Returns HTML code to display a language selection dropdown menu.
     *
     * Does only contain the <select> tag pair if the optional $menuName
     * is specified and evaluates to a true value.
     * @param   integer $selectedId The optional preselected language ID
     * @param   string  $menuName   The optional menu name
     * @param   string  $onchange   The optional onchange code
     * @return  string              The dropdown menu HTML code
     * @author  Reto Kohli <reto.kohli@comvation.com>
     * @todo    Use the Html class instead
     */
    public static function getMenu($selectedId=0, $menuName='', $onchange='')
    {
        $menu = static::getMenuoptions($selectedId, true);
        if ($menuName) {
            $menu = "<select id='$menuName' name='$menuName'".
                ($onchange ? ' onchange="'.$onchange.'"' : '').
                ">\n$menu</select>\n";
        }
        return $menu;
    }


    /**
     * Returns HTML code to display a language selection dropdown menu
     * for the active frontend languages only.
     *
     * Does only contain the <select> tag pair if the optional $menuName
     * is specified and evaluates to a true value.
     * Frontend use only.
     * @param   integer $selectedId The optional preselected language ID
     * @param   string  $menuName   The optional menu name
     * @param   string  $onchange   The optional onchange code
     * @return  string              The dropdown menu HTML code
     * @author  Reto Kohli <reto.kohli@comvation.com>
     * @todo    Use the Html class instead
     */
    public static function getMenuActiveOnly($selectedId=0, $menuName='', $onchange='')
    {
        $menu = static::getMenuoptions($selectedId, false);
        if ($menuName) {
            $menu = "<select id='$menuName' name='$menuName'".
                ($onchange ? ' onchange="'.$onchange.'"' : '').
                ">\n$menu</select>\n";
        }
//echo("getMenu(select=$selectedId, name=$menuName, onchange=$onchange): made menu: ".htmlentities($menu)."<br />");
        return $menu;
    }


    /**
     * Returns HTML code for the language menu options
     * @param   integer $selectedId   The optional preselected language ID
     * @param   boolean $flagInactive If true, all languages are added,
     *                                only the active ones otherwise
     * @return  string                The menu options HTML code
     * @author  Reto Kohli <reto.kohli@comvation.com>
     * @todo    Use the Html class instead
     */
    public static function getMenuoptions($selectedId=0, $flagInactive=false)
    {
        if (empty(static::$arrFrontendLanguages)) static::init();
        $menuoptions = '';
        foreach (static::$arrFrontendLanguages as $id => $arrLanguage) {
            // Skip inactive ones if desired
            if (!$flagInactive && empty($arrLanguage['frontend']))
                continue;
            $menuoptions .=
                "<option value='$id'".
                ($selectedId == $id ? ' selected="selected"' : '').
                ">{$arrLanguage['name']}</option>\n";
        }
        return $menuoptions;
    }


    /**
     * Return the language ID for the ISO 639-1 code specified.
     *
     * If the code cannot be found, returns the default language.
     * If that isn't set either, returns the first language encountered.
     * If none can be found, returns null.
     * Note that you can supply the complete string from the Accept-Language
     * HTTP header.  This method will take care of chopping it into pieces
     * and trying to pick a suitable language.
     * However, it will not pick the most suitable one according to RFC2616,
     * but only returns the first language that fits.
     * @static
     * @param   string    $langCode         The ISO 639-1 language code
     * @return  mixed                       The language ID on success,
     *                                      null otherwise
     *
     * @author  Reto Kohli <reto.kohli@comvation.com>
     * @author  Nicola Tommasi <nicola.tommasi@comvation.com>
     */
    public static function getLangIdByIso639_1($langCode)
    {
        global $objDatabase;
        // Don't bother if the "code" looks like an ID already
        if (is_numeric($langCode)) return $langCode;

        // Something like "fr; q=1.0, en-gb; q=0.5"
        $arrLangCode = preg_split('/,\s*/', $langCode);
        $strLangCode = "'".
            join("','",
                preg_replace(
            '/(?:-\w+)?(?:;\s*q(?:\=\d?\.?\d*)?)?/i',
            '',
                    $arrLangCode)
            ).
            "'";
        // search for locale with matching iso1 code
        $objResult = $objDatabase->Execute("
            SELECT id 
            FROM ".DBPREFIX."core_locale_locale 
            WHERE iso_1 IN ($strLangCode)  
            LIMIT 1
        ");
        if ($objResult && $objResult->RecordCount()) {
            return $objResult->fields['id'];
        }
        // The code was not found.  Pick the default.
        $defaultLocaleId = \Cx\Core\Setting\Controller\Setting::getValue('defaultLocaleId');
        if (isset($defaultLocaleId)) {
            return $defaultLocaleId;
        }
        // Still nothing.  Pick the first frontend language available.
        $objResult = $objDatabase->Execute("
            SELECT id
            FROM ".DBPREFIX."core_locale_locale
            LIMIT 1;
        ");
        if ($objResult && $objResult->RecordCount()) {
            return $objResult->fields['id'];
        }
        // Give up.
        return null;
    }


    /**
     * Return the language code from the database for the given frontend language ID
     *
     * Returns false on failure, or if the ID is invalid
     * @param   integer $langId         The frontend language ID
     * @return  mixed                   The two letter code, or false
     * @static
     */
    public static function getLanguageCodeById($langId)
    {
        if (empty(static::$arrFrontendLanguages)) static::init();
        return static::getLanguageParameter($langId, 'lang');
    }


    /**
     * Return the language code from the database for the given backend language ID
     *
     * Returns false on failure, or if the ID is invalid
     * @param   integer $langId         The frontend language ID
     * @return  mixed                   The two letter code, or false
     * @static
     */
    public static function getBackendLanguageCodeById($langId)
    {
        if (empty(static::$arrBackendLanguages)) static::init();
        return static::getBackendLanguageParameter($langId, 'lang');
    }


    /**
     * Return the frontend language ID for the given code
     *
     * Returns false on failure, or if the code is unknown
     * @param   string                    The two letter code
     * @return  integer   $langId         The language ID, or false
     * @static
     */
    public static function getLanguageIdByCode($code)
    {
        if (empty(static::$arrFrontendLanguages)) static::init();
        foreach (static::$arrFrontendLanguages as $id => $arrLanguage) {
            if ($arrLanguage['lang'] == $code) return $id;
        }
        return false;
    }


    /**
     * Return the backend language ID for the given code
     *
     * Returns false on failure, or if the code is unknown
     * @param   string                    The two letter code
     * @return  integer   $langId         The language ID, or false
     * @static
     */
    public static function getBackendLanguageIdByCode($code)
    {
        if (empty(static::$arrBackendLanguages)) static::init();
        foreach (static::$arrBackendLanguages as $id => $arrLanguage) {
            if ($arrLanguage['lang'] == $code) return $id;
        }
        return false;
    }

    /**
     * Return the locale for the given Language ID
     *
     * If no proper locale is found, returns the two-letter Language ISO code.
     * Returns null if that isn't found either.
     * @param   integer $langId         The Language ID
     * @return  string|null             The locale, Language code, or null
     * @static
     * @author  Reto Kohli <reto.kohli@comvation.com>
     */
    public static function getLocaleByFrontendId($langId)
    {
        if (array_key_exists($langId, static::$locales)) {
            return static::$locales[$langId];
        }
        // Note that this SHOULD NOT pretend the *code* to be a locale!
        // (FTTB, Language code and locale are identical)
        $locale = static::getLanguageParameter($langId, 'lang');
        if ($locale) {
            return $locale;
        }
        return null;
    }

    /**
     * Return the ID of the given locale
     *
     * If no matching locale is found, returns the ID matching the
     * two-letter language ISO code.
     * Returns null if that isn't found either.
     * @param   string  $locale         The locale
     * @return  string|null             The Language ID
     * @static
     * @author  Reto Kohli <reto.kohli@comvation.com>
     */
    public static function getFrontendIdByLocale($locale)
    {
        // TODO: Inefficient, and pointless FTTB (no locales!)
        $key = array_search($locale, static::$locales);
        if ($key !== false) {
            return static::$locales[$key];
        }
        // Note that this SHOULD NOT pretend the *code* to be a locale!
        // (FTTB, Language code and locale are identical)
        $id = static::getLanguageIdByCode($locale, 'lang');
        if ($id) {
            return $id;
        }
        return null;
    }

    /**
     * Return the fallback language ID for the given frontend language ID
     *
     * Returns false on failure, or if the ID is invalid
     * @param   integer $langId         The language ID
     * @return  integer   $langId         The language ID, or false
     * @static
     */
    public static function getFallbackLanguageIdById($langId)
    {
        if (empty(static::$arrFrontendLanguages)) static::init();
        if ($langId == static::getDefaultLangId()) return false;
        $fallback_lang = static::getLanguageParameter($langId, 'fallback');
        if ($fallback_lang == 0) $fallback_lang = intval(static::getDefaultLangId());;
        if ($langId == $fallback_lang) return false;
        return $fallback_lang;
    }

    /**
     * Builds an array mapping frontend language ids to fallback language ids.
     *
     * @return array ( language id => fallback language id )
     */
    public static function getFallbackLanguageArray() {
        if (empty(static::$arrFrontendLanguages)) {
            static::init();
        }
        $arr = array();
        foreach(static::$arrFrontendLanguages as $frontendLanguage) {
            $langId = $frontendLanguage['id'];
            $fallbackLangId = $frontendLanguage['fallback'];

            if ($langId == $fallbackLangId || $langId == static::getDefaultLangId()) {
                $fallbackLangId =false;
            }

            $arr[$langId] = $fallbackLangId;
        }
        return $arr;
    }

}