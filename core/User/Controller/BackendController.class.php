<?php declare(strict_types=1);

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

use Cx\Core\Core\Controller\Cx;

class BackendController extends
    \Cx\Core\Core\Model\Entity\SystemComponentBackendController
{
    /**
     * This function returns the ViewGeneration options for a given entityClass
     *
     * @param string $entityClassName   contains the FQCN from entity
     * @param string $dataSetIdentifier if entityClassName is DataSet, this is
     *                                  used for better partition
     *
     * @access protected
     *
     * @global $_ARRAYLANG
     *
     * @return array with options
     */
    protected function getViewGeneratorOptions(
        $entityClassName, $dataSetIdentifier = ''
    )
    {
        global $_ARRAYLANG;

        if ($this->cx->getRequest()->hasParam('editid')) {
            $this->userId = explode(
                '}',
                explode(
                    ',',
                    $this->cx->getRequest()->getParam('editid')
                )[1]
            )[0];
        }

        $options = parent::getViewGeneratorOptions(
            $entityClassName,
            $dataSetIdentifier
        );

        $uId = $this->getUserId();

        switch ($entityClassName) {
            case 'Cx\Core\User\Model\Entity\User':
                $options['order'] = array(
                    'overview' => array(
                        'id',
                        'active',
                        'isAdmin',
                        'username',
                        'company',
                        'firstname',
                        'lastname',
                        'email',
                        'regdate',
                        'lastActivity',
                    ),
                    'form' => array(
                        'email',
                        'username',
                        'password',
                        'backendLangId',
                        'frontendLangId',
                        'isAdmin',
                        'emailAccess',
                        'profileAccess',
                    ),
                );
                $options['functions'] = array(
                    'searching' => true,
                    'filtering' => true,
                    'edit' => true,
                    'delete' => true,
                    'add' => true,
                );
                $options['fields'] = array(
                    'active' => array(
                        'showOverview' => true,
                        'showDetail' => false,
                    ),
                    'username' => array(
                        'table' => array(
                            'parse' => function ($value, $rowData) {
                                return $this->addEditUrl($value, $rowData);
                            }
                        ),
                        'showOverview' => true,
                        'showDetail' => true,
                    ),
                    'email' => array(
                        'table' => array(
                            'parse' => function ($value, $rowData) {
                                return $this->addEmailUrl($value, $rowData);
                            }
                        ),
                        'showOverview' => true,
                        'showDetail' => true,
                    ),
                    'company' => array(
                        'showOverview' => true,
                    ),
                    'firstname' => array(
                        'showOverview' => true,
                    ),
                    'lastname' => array(
                        'showOverview' => true,
                    ),
                    'password' => array(
                        'showOverview' => false,
                    ),
                    'authToken' => array(
                        'showOverview' => false,
                        'showDetail' => false,
                    ),
                    'authTokenTimeout' => array(
                        'showOverview' => false,
                        'showDetail' => false,
                    ),
                    'regdate' => array(
                        'table' => array(
                            'parse' => function ($value, $rowData) {
                                return $this->formatDate($value);
                            }
                        ),
                        'showOverview' => true,
                        'showDetail' => false,
                    ),
                    'expiration' => array(
                        'showOverview' => true,
                        'showDetail' => true,
                        'formfield' =>
                            function ($fieldname, $fieldtype, $fieldlength, $fieldvalue){
                                return $this->getExpirationDropdown($fieldname, $fieldvalue);
                            },
                    ),
                    'validity' => array(
                        'showOverview' => false,
                        'showDetail' => false,
                    ),
                    'lastAuthStatus' => array(
                        'showOverview' => false,
                        'showDetail' => false,
                    ),
                    'lastAuth' => array(
                        'showOverview' => false,
                        'showDetail' => false,
                    ),
                    'emailAccess' => array(
                        'showOverview' => false,
                        'showDetail' => true,
                        'type' => 'hidden',
                    ),
                    'frontendLangId' => array(
                        'showOverview' => false,
                        'formfield' =>
                            function ($fieldname, $fieldtype, $fieldlength, $fieldvalue){
                                return $this->getLangDropdown($fieldname, $fieldvalue);
                            },
                    ),
                    'backendLangId' => array(
                        'showOverview' => false,
                        'showDetail' => true,
                        'type' => 'hidden',
                    ),
                    'verified' => array(
                        'showOverview' => false,
                        'showDetail' => false,
                    ),
                    'primaryGroup' => array(
                        'showOverview' => false,
                        'showDetail' => true,
                        'mode' => 'associate',
                        'formfield' =>
                            function ($fieldname, $fieldtype, $fieldlength, $fieldvalue){
                                return $this->primaryGroupDropdown($fieldname, $fieldvalue);
                            },
                    ),
                    'profileAccess' => array(
                        'showOverview' => false,
                        'formfield' =>
                            function ($fieldname, $fieldtype, $fieldlength, $fieldvalue){
                                return $this->getProfileAccessDropdown($fieldname, $fieldvalue);
                            },
                    ),
                    'restoreKey' => array(
                        'showOverview' => false,
                        'showDetail' => false,
                    ),
                    'restoreKeyTime' => array(
                        'showOverview' => false,
                        'showDetail' => false,
                    ),
                    'u2uActive' => array(
                        'showOverview' => false,
                        'showDetail' => false,
                    ),
                    'userProfile' => array(
                        'showOverview' => false,
                        'showDetail' => false,
                    ),
                    'group' => array(
                        'showOverview' => false,
                        'showDetail' => false,
                    ),
                    'lastActivity' => array(
                        'table' => array(
                            'parse' => function ($value, $rowData) {
                                return $this->formatDate($value);
                            }
                        ),
                        'showOverview' => true,
                        'showDetail' => false,
                    ),
                    'userAttributeValue' => array(
                        'showOverview' => false,
                        'showDetail' => false,
                    ),
                );
                break;
            case 'Cx\Core\User\Model\Entity\Group':
                $options['order'] = array(
                    'overview' => array(
                        'groupId',
                        'isActive',
                        'groupName',
                        'groupDescription',
                        'type',
                        'user',
                    ),
                    'form' => array(
                        'groupName',
                        'groupDescription',
                        'isActive',
                        'type',
                        'user',
                        'homepage',
                    )
                );
                $options['functions'] = array(
                    'searching' => true,
                    'filtering' => true,
                    'add' => true,
                    'edit' => true,
                    'delete' => true,
                );
                $options['fields'] = array(
                    'isActive' => array(
                        'showOverview' => true,
                    ),
                    'groupId' => array(
                        'showOverview' => true,
                    ),
                    'groupName' => array(
                        'showOverview' => true,
                    ),
                    'groupDescription' => array(
                        'showOverview' => true,
                    ),
                    'type' => array(
                        'showOverview' => true,
                    ),
                    'homepage' => array(
                        'showOverview' => false,
                    ),
                    'toolbar' => array(
                        'showOverview' => false,
                        'showDetail' => false,
                    ),
                    'toolbar' => array(
                        'showOverview' => true,
                        'allowFiltering' => false,
                        'showDetail' => false,
                        'custom' => true,
                        'table' => array(
                            'parse' => function ($value, $rowData) {
                                return $this->getExportLink($value, $rowData);
                            }
                        ),
                    ),
                    'user' => array(
                        'showOverview' => true,
                        'mode' => 'associate',
                        'table' => array(
                            'parse' => function ($rowData) use ($uId) {
                                return $this->addGroupUserEditUrl(
                                    $rowData, $uId
                                );
                            }
                        ),
                        'allowFiltering' => false,
                    ),
                );
                break;
            case 'Cx\Core\User\Model\Entity\Settings':
                $options['fields'] = array(
                    'title' => array(
                        'showOverview' => true,
                    ),
                );
                break;
            case 'Cx\Core\User\Model\Entity\UserAttribute':
                $options['fields'] = array(
                    'title' => array(
                        'showOverview' => false,
                    ),
                );
                break;
            case 'Cx\Core\User\Model\Entity\ProfileTitle':
                $options['fields'] = array(
                    'title' => array(
                        'showOverview' => false,
                    ),
                );
                break;
            case 'Cx\Core\User\Model\Entity\CoreAttribute':
                $options['fields'] = array(
                    'title' => array(
                        'showOverview' => false,
                    ),
                );
                break;
            case 'Cx\Core\User\Model\Entity\UserAttributeValue':
                $options['fields'] = array(
                    'title' => array(
                        'showOverview' => false,
                    ),
                );
                break;
            case 'Cx\Core\User\Model\Entity\UserAttributeName':
                $options['fields'] = array(
                    'title' => array(
                        'showOverview' => false,
                    ),
                );
                break;
            case 'Cx\Core\User\Model\Entity\UserProfile':
                $options['fields'] = array(
                    'title' => array(
                        'showOverview' => false,
                    ),
                );
                break;
        }
        return $options;
    }

    protected function getEntityClasses()
    {
        return array(
            'Cx\Core\User\Model\Entity\User',
            'Cx\Core\User\Model\Entity\Group',
        );
    }

    /**
     * Return true here if you want the first tab to be an entity view
     *
     * @return boolean True if overview should be shown, false otherwise
     */
    protected function showOverviewPage() : bool
    {
        return false;
    }

    /**
     * Count users in groups
     *
     * @return mixed
     */
    protected function getUserId()
    {
        $em = $this->cx->getDb()->getEntityManager();
        $qb = $em->createQueryBuilder();
        $query = $qb->select('g.groupId', 'count(u.id)')->from('Cx\Core\User\Model\Entity\Group', 'g')
            ->leftJoin('g.user', 'u')
            ->groupBy('g.groupId')
            ->getQuery();
        $result = $query->getResult();

        $getGroupUser = array_reduce($result,
            function($groups, $group){
                $groups[$group['groupId']] = $group[1];
                return $groups;
            }
        );

        return $getGroupUser;
    }

    /**
     * Format the date for the overview list
     *
     * @param int $value date
     *
     * @return string
     */
    protected function formatDate($value)
    {
        $date = '@' . $value;

        $dateElement = new \DateTime($date);

        $dateElement = $dateElement->format('d.m.Y');

        return $dateElement;
    }

    /**
     * Format the date for the overview list
     *
     * @param string $value email
     *
     * @return \Cx\Core\Html\Model\Entity\HtmlElement
     */
    protected function addEmailUrl($value)
    {
        global $_ARRAYLANG;

        $email = new \Cx\Core\Html\Model\Entity\TextElement($value);

        $setEmailUrl = new \Cx\Core\Html\Model\Entity\HtmlElement(
            'a'
        );

        $setEmailUrl->setAttributes(array('href' => "mailto:$value", 'title' => $_ARRAYLANG['TXT_CORE_USER_EMAIL_TITLE'] . ' ' . $value));

        $setEmailUrl->addChild($email);

        return $setEmailUrl;
    }

    /**
     * @param $value
     * @param $rowData
     * @return \Cx\Core\Html\Model\Entity\HtmlElement
     */
    protected function addEditUrl($value,$rowData)
    {
        global $_ARRAYLANG;

        $username = new \Cx\Core\Html\Model\Entity\TextElement($value);

        $setEditUrl = new \Cx\Core\Html\Model\Entity\HtmlElement(
            'a'
        );

        $editUrl = \Cx\Core\Routing\Url::fromMagic(
            \Cx\Core\Core\Controller\Cx::instanciate()->getWebsiteBackendPath() .
            '/' . $this->getName() . '/User'
        );

        $userId = $rowData['id'];

        $editUrl->setParam('editid', $userId);

        $setEditUrl->setAttributes(array('href' => $editUrl, 'title' => $_ARRAYLANG['TXT_CORE_USER_EDIT_TITLE']));

        $setEditUrl->addChild($username);

        return $setEditUrl;
    }


    /**
     * Add edit url
     *
     * @param $rowData array      Data of row
     * @param $getGroupUser array Users per group
     * @return \Cx\Core\Html\Model\Entity\HtmlElement
     */
    protected function addGroupUserEditUrl($rowData, $getGroupUser)
    {
        global $_ARRAYLANG;

        $userId = new \Cx\Core\Html\Model\Entity\TextElement($getGroupUser[$rowData['groupId']]);

        $setEditUrl = new \Cx\Core\Html\Model\Entity\HtmlElement(
            'a'
        );

        $editUrl = \Cx\Core\Routing\Url::fromMagic(
            \Cx\Core\Core\Controller\Cx::instanciate()->getWebsiteBackendPath() .
            '/' . $this->getName() . '/User'
        );

        $editUrl->setParam('search', $rowData['groupId']);

        $setEditUrl->setAttributes(array('href' => $editUrl));

        $setEditUrl->addChild($userId);

        return $setEditUrl;
    }

    /**
     * Get the export link of users in a group
     *
     * @param $value   string fieldvalue
     * @param $rowData array  all data
     * @return \Cx\Core\Html\Model\Entity\HtmlElement return generated div
     *                                                with links
     */
    protected function getExportLink($value, $rowData)
    {
        global $_ARRAYLANG;

        $wrapper = new \Cx\Core\Html\Model\Entity\HtmlElement('div');
        $groupId = $rowData['groupId'];

        $em = $this->cx->getDb()->getEntityManager();
        $languages = $em->getRepository(
            'Cx\Core\Locale\Model\Entity\Locale'
        )->findAll();

        /*Add a seperator*/
        $seperator = new \Cx\Core\Html\Model\Entity\TextElement(', ');

        /*Add icon for csv export*/
        $iconExport =  new \Cx\Core\Html\Model\Entity\HtmlElement('img');
        $iconPath = $this->cx->getCodeBaseCoreWebPath()
            . '/Core/View/Media/icons/csv.gif';

        $iconExport->setAttributes(
            array(
                'src' => $iconPath,
                'title' => 'Export',
                'alt' => 'Export',
                'align' => 'absmiddle'
            )
        );

        $wrapper->addChild($iconExport);

        /*Add link to export all*/
        $linkAll = new \Cx\Core\Html\Model\Entity\HtmlElement('a');
        $csvLinkUrlAll = \Cx\Core\Routing\Url::fromApi('exportUser', array());
        $csvLinkUrlAll->setParam('groupId', $groupId);
        $textAll = new \Cx\Core\Html\Model\Entity\TextElement(
            $_ARRAYLANG['TXT_CORE_USER_EXPORT_ALL']
        );
        $linkAll->addChild($textAll);

        $linkAll->setAttribute('href', $csvLinkUrlAll);

        $wrapper->addChild($linkAll);

        /*Add link for every source language*/
        foreach ($languages as $lang) {
            $link = new \Cx\Core\Html\Model\Entity\HtmlElement('a');

            $csvLinkUrl = \Cx\Core\Routing\Url::fromApi('exportUser', array());
            $csvLinkUrl->setParam('groupId', $groupId);
            $csvLinkUrl->setParam('langId', $lang->getId());

            $textLang = new \Cx\Core\Html\Model\Entity\TextElement(

                $lang->getShortForm()
            );

            $wrapper->addChild($seperator);

            $link->addChild($textLang);

            $link->setAttribute('href', $csvLinkUrl);

            $wrapper->addChild($link);
        }

        return $wrapper;
    }


    /**
     * Generate the primary group dropdown
     *
     * @param $fieldname string  Name of the field
     * @param $fieldvalue string Value of the field
     * @return \Cx\Core\Html\Model\Entity\DataElement|void
     */
    protected function primaryGroupDropdown($fieldname, $fieldvalue)
    {
        global $_ARRAYLANG;

        $em = $this->cx->getDb()->getEntityManager();

        $userId = intval($this->userId);

        $user = $em->getRepository('Cx\Core\User\Model\Entity\User')->findOneBy(array('id' => $userId));

        if (empty($user)) {
            return;
        }

        $userGroups = $user->getGroup();

        $validValues = array();

        foreach ($userGroups as $group) {
            $validValues[$group->getGroupId()] = $group->getGroupName();
        }

        $dropdown = new \Cx\Core\Html\Model\Entity\DataElement(
            $fieldname,
            $fieldvalue,
            'select',
            null,
            $validValues
        );

        return $dropdown;
    }

    /**
     * Generate the expiration dropdown
     *
     * @param $fieldname string  Name of the field
     * @param $fieldvalue string Value of the field
     * @return \Cx\Core\Html\Model\Entity\DataElement
     */
    protected function getExpirationDropdown($fieldname, $fieldvalue)
    {
        $validValues = array();

        foreach (\FWUser::getUserValidities() as $validity) {
            $strValidity = \FWUser::getValidityString($validity);

            $validValues[$validity] = $strValidity;
        }

        $dropdown = new \Cx\Core\Html\Model\Entity\DataElement(
            $fieldname,
            $fieldvalue,
            'select',
            null,
            $validValues
        );

        return $dropdown;
    }

    /**
     * Generate the profile access dropdown
     *
     * @param $fieldname string  Name of the field
     * @param $fieldvalue string Value of the field
     * @return \Cx\Core\Html\Model\Entity\HtmlElement
     */
    protected function getProfileAccessDropdown($fieldname, $fieldvalue)
    {
        global $_CORELANG;

        $wrapper = new \Cx\Core\Html\Model\Entity\HtmlElement('div');

        $validValuesEmail = array(
            'everyone'=> $_CORELANG['TXT_ACCESS_EVERYONE_ALLOWED_SEEING_EMAIL'],
            'members_only' => $_CORELANG['TXT_ACCESS_MEMBERS_ONLY_ALLOWED_SEEING_EMAIL'],
            'nobody' => $_CORELANG['TXT_ACCESS_NOBODY_ALLOWED_SEEING_EMAIL']
        );

        $emailDropdown = new \Cx\Core\Html\Model\Entity\DataElement(
            'emailAccess',
            $fieldvalue,
            'select',
            null,
            $validValuesEmail
        );

        $validValuesProfile = array(
            'everyone' => $_CORELANG['TXT_ACCESS_EVERYONE_ALLOWED_SEEING_PROFILE'],
            'members_only' => $_CORELANG['TXT_ACCESS_MEMBERS_ONLY_ALLOWED_SEEING_PROFILE'],
            'nobody' => $_CORELANG['TXT_ACCESS_NOBODY_ALLOWED_SEEING_PROFILE']
        );

        $profileDropdown = new \Cx\Core\Html\Model\Entity\DataElement(
            $fieldname,
            $fieldvalue,
            'select',
            null,
            $validValuesProfile
        );

        $wrapper->addChildren(array($emailDropdown, $profileDropdown));

        return $wrapper;
    }

    /**
     * Generate the language dropdown
     *
     * @param $fieldname string  Name of the field
     * @param $fieldvalue string Value of the field
     * @return \Cx\Core\Html\Model\Entity\HtmlElement
     */
    protected function getLangDropdown($fieldname, $fieldvalue)
    {
        $wrapper = new \Cx\Core\Html\Model\Entity\HtmlElement('div');

        $frontendLang = array();

        $arrLangs = \FWLanguage::getActiveFrontendLanguages();

        $frontendLang[0] = 'Default';

        foreach ($arrLangs as $lang){
            $frontendLang[$lang['id']] = $lang['name'];
        }

        $frontendLangDropdown = new \Cx\Core\Html\Model\Entity\DataElement(
            $fieldname,
            $fieldvalue,
            'select',
            null,
            $frontendLang
        );

        /*generate backend languages dropdown*/
        $backendLang = array();

        $arrLangs = \FWLanguage::getActiveFrontendLanguages();

        $backendLang[0] = 'Default';

        foreach ($arrLangs as $lang){
            $backendLang[$lang['id']] = $lang['name'];
        }

        $backendLangDropdown = new \Cx\Core\Html\Model\Entity\DataElement(
            'backendLangId',
            $fieldvalue,
            'select',
            null,
            $backendLang
        );

        $wrapper->addChildren(array($frontendLangDropdown, $backendLangDropdown));

        return $wrapper;
    }
}


