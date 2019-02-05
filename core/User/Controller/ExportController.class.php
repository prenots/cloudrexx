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
 * Cloudrexx App by Comvation AG
 *
 * @category  CloudrexxApp
 * @package   User
 * @author    Cloudrexx AG <dario.graf@comvation.com>
 * @copyright Cloudrexx AG
 * @link      https://www.cloudrexx.com
 *
 * Unauthorized copying, changing or deleting
 * of any file from this app is strictly prohibited
 *
 * Authorized copying, changing or deleting
 * can only be allowed by a separate contract
 **/

namespace Cx\Core\User\Controller;

/**
 * Specific ExportController for this Component.
 *
 * Export controller to export all users of a group into a .csv file
 *
 * @category  CloudrexxApp
 * @package   User
 * @author    Cloudrexx AG <dario.graf@comvation.com>
 * @copyright Cloudrexx AG
 * @link      https://www.cloudrexx.com
 */

class ExportController extends \Cx\Core\Core\Model\Entity\Controller
{
    /**
     * Export users of a group as CSV
     *
     * @param   integer $groupId Id of a user group to filter the export by
     * @param   integer $langId Id of frontend locale to filter the export by
     * @throws  \Cx\Core\Core\Controller\InstanceException  At the end of the
     *          CSV export to properly end the script execution.
     */
    public function exportUsers($groupId = 0, $langId = null)
    {
        global $objInit;

        $_CORELANG = \Env::get('init')->getComponentSpecificLanguageData('Core');

        $_ARRAYLANG = \Env::get('init')->getComponentSpecificLanguageData('Access');

        $csvSeparator = ";";

        $objFWUser = \FWUser::getFWUserObject();
        $arrLangs = \FWLanguage::getLanguageArray();

        if($groupId){
            $objGroup = $objFWUser->objGroup->getGroup($groupId);
            $groupName = $objGroup->getName(LANG_ID);
        }else{
            $groupName = $_CORELANG['TXT_USER_ALL'];
        }

        header("Content-Type: text/comma-separated-values", true);
        header(
            "Content-Disposition: attachment; filename=\"".
            str_replace(array(' ', ',', '.', '\'', '"'), '_', $groupName).
            ($langId != null ? '_lang_'.$arrLangs[$langId]['lang'] : '').
            '.csv"', true);

        // check if we're in frontend mode
        $cx = \Cx\Core\Core\Controller\Cx::instanciate();
        $isFrontend =
            $cx->getMode() == \Cx\Core\Core\Controller\Cx::MODE_FRONTEND;

        // used to hold the list of CSV columns
        $arrFields = array();

        // output active status of users only if we're not in frontend mode
        if (!$isFrontend) {
            $arrFields = array (
                'active'            => $_ARRAYLANG['TXT_ACCESS_ACTIVE'],
            );
        }

        // add core user attributes to CSV
        $arrFields = array_merge($arrFields, array(
            'frontend_lang_id'  => $_ARRAYLANG['TXT_ACCESS_LANGUAGE'] . ' ('.$_CORELANG['TXT_LANGUAGE_FRONTEND'].')',
            'backend_lang_id'   => $_ARRAYLANG['TXT_ACCESS_LANGUAGE'] . ' ('.$_CORELANG['TXT_LANGUAGE_BACKEND'].')',
            'username'          => $_ARRAYLANG['TXT_ACCESS_USERNAME'],
            'email'             => $_ARRAYLANG['TXT_ACCESS_EMAIL'],
            'regdate'           => $_ARRAYLANG['TXT_ACCESS_REGISTERED_SINCE'],
        ));

        // fetch custom attributes
        $customAttributeIds = $objFWUser->objUser->objAttribute->getCustomAttributeIds();
        foreach ($customAttributeIds as $idx => $customAttributeId) {
            // fetch custom attribute
            $objCustomAttribute = $objFWUser->objUser->objAttribute->getById(
                $customAttributeId
            );
            if ($objCustomAttribute->EOF) {
                continue;
            }
            // filter out child attributes
            switch ($objCustomAttribute->getType()) {
                case 'menu_option':
                    unset($customAttributeIds[$idx]);
                    break;
                default:
                    break;
            }
        }

        // set profile attributes
        $arrProfileFields = array_merge(
            $objFWUser->objUser->objAttribute->getCoreAttributeIds(),
            $customAttributeIds
        );

        // print header for core attributes
        foreach ($arrFields as $field) {
            print $this->escapeCsvValue($field).$csvSeparator;
        }

        // print header for user groups
        print $this->escapeCsvValue($_ARRAYLANG['TXT_ACCESS_GROUPS']).$csvSeparator;

        // print header for profile attributes
        foreach ($arrProfileFields as $profileField) {
            $arrFields[$profileField] = $objFWUser->objUser->objAttribute->getById($profileField)->getName();
            print $this->escapeCsvValue($arrFields[$profileField]).$csvSeparator;
        }
        print "\n";

        $filter = array();
        if (!empty($groupId)) {
            $filter['group_id'] = $groupId;
        }
        if (!empty($langId)) {
            if (\FWLanguage::getLanguageParameter($langId, 'is_default') == 'true') {
                $filter['frontend_lang_id'] = array($langId, 0);
            } else {
                $filter['frontend_lang_id'] = $langId;
            }
        }
        $objUser = $objFWUser->objUser->getUsers($filter, null, array('username'), array_keys($arrFields));
        if ($objUser) {
            while (!$objUser->EOF) {
                // do not export users without any group membership
                // in frontend export
                if (
                    $isFrontend &&
                    empty($objUser->getAssociatedGroupIds(true))
                ) {
                    $objUser->next();
                    continue;
                }

                // fetch associated user groups
                $groups = $this->getGroupListOfUser($objUser);

                // do not export users without any group membership
                // in frontend export
                if (
                    $isFrontend &&
                    empty($groups)
                ) {
                    $objUser->next();
                    continue;
                }

                $frontendLangId = $objUser->getFrontendLanguage();
                if (empty($frontendLangId)) {
                    $frontendLangId = $objInit->getDefaultFrontendLangId();
                }
                $frontendLang = $arrLangs[$frontendLangId]['name']." (".$arrLangs[$frontendLangId]['lang'].")";

                $backendLangId = $objUser->getBackendLanguage();
                if (empty($backendLangId)) {
                    $backendLangId = $objInit->getDefaultBackendLangId();
                }
                $backendLang = $arrLangs[$backendLangId]['name']." (".$arrLangs[$backendLangId]['lang'].")";

                // active status of user
                // note: do not output in frontend
                if (!$isFrontend) {
                    $activeStatus = $objUser->getActiveStatus() ? $_CORELANG['TXT_YES'] : $_CORELANG['TXT_NO'];
                    print $this->escapeCsvValue($activeStatus).$csvSeparator;
                }

                // frontend_lang_id
                print $this->escapeCsvValue($frontendLang).$csvSeparator;

                // backend_lang_id
                print $this->escapeCsvValue($backendLang).$csvSeparator;

                // username
                print $this->escapeCsvValue($objUser->getUsername()).$csvSeparator;

                // email
                print $this->escapeCsvValue($objUser->getEmail()).$csvSeparator;

                // regdate
                print $this->escapeCsvValue(date(ASCMS_DATE_FORMAT_DATE, $objUser->getRegistrationDate())).$csvSeparator;

                // user groups
                print $this->escapeCsvValue(join(',', $groups)).$csvSeparator;

                // profile attributes
                foreach ($arrProfileFields as $field) {
                    $value = $objUser->getProfileAttribute($field);

                    switch ($field) {
                        case 'gender':
                            switch ($value) {
                                case 'gender_male':
                                    $value = $_CORELANG['TXT_ACCESS_MALE'];
                                    break;

                                case 'gender_female':
                                    $value = $_CORELANG['TXT_ACCESS_FEMALE'];
                                    break;

                                default:
                                    $value = $_CORELANG['TXT_ACCESS_NOT_SPECIFIED'];
                                    break;
                            }
                            break;

                        case 'title':
                        case 'country':
                            $title = '';
                            $value = $objUser->objAttribute->getById($field . '_' . $value)->getName();
                            break;

                        default:
                            $objAttribute = $objUser->objAttribute->getById($field);
                            if (!empty($value) && $objAttribute->getType() == 'date') {
                                $date = new \DateTime();
                                $date ->setTimestamp($value);
                                $value = $date->format(ASCMS_DATE_FORMAT_DATE);
                            }
                            if ($objAttribute->getType() == 'menu') {
                                $option = '';
                                if (!empty($value)) {
                                    $objAttributeChild = $objUser->objAttribute->getById($value);
                                    if (!$objAttributeChild->EOF) {
                                        $option = $objAttributeChild->getName();
                                    }
                                }
                                $value = $option;
                            }
                            break;
                    }
                    print $this->escapeCsvValue($value).$csvSeparator;
                }

                // add line break at end of row
                print "\n";

                $objUser->next();
            }
        }
    }

    /**
     * Escape a value that it could be inserted into a csv file.
     *
     * @param string $value
     * @return string
     */
    protected function escapeCsvValue($value) {
        $csvSeparator = ";";
        $value = in_array(strtolower(CONTREXX_CHARSET), array('utf8', 'utf-8')) ? utf8_decode($value) : $value;
        $value = preg_replace('/\r\n/', "\n", $value);
        $valueModified = str_replace('"', '""', $value);

        if ($valueModified != $value || preg_match('/['.$csvSeparator.'\n]+/', $value)) {
            $value = '"'.$valueModified.'"';
        }
        return $value;
    }

    /**
     * Get a list of user groups a user is a member of
     *
     * Returns an array of all user groups the supplied user (identified by
     * $objUser) is a member of.
     * In frontend mode, this method does only return frontend user groups.
     * Whereas in every other mode, it does return all associated user groups.
     *
     * @param   \User   $objUser    The user of whom the associated groups
     *                              shall be returned.
     * @return  array   An array containing the names of the associated groups.
     */
    protected function getGroupListOfUser($objUser) {
        // check if we're in frontend mode
        $cx = \Cx\Core\Core\Controller\Cx::instanciate();
        $activeOnly =
            $cx->getMode() == \Cx\Core\Core\Controller\Cx::MODE_FRONTEND;
        $groupIds = $objUser->getAssociatedGroupIds($activeOnly);
        $arrGroupNames = array();

        foreach ($groupIds as $groupId) {
            $objGroup = \FWUser::getFWUserObject()->objGroup->getGroup($groupId);
            if ($objGroup->EOF) {
                continue;
            }

            if (
                $activeOnly &&
                $objGroup->getType() != 'frontend'
            ) {
                continue;
            }

            $arrGroupNames[] = $objGroup->getName();
        }

        return $arrGroupNames;
    }
}