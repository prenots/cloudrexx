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
        $options = parent::getViewGeneratorOptions(
            $entityClassName,
            $dataSetIdentifier
        );

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
                        'frontendLangId',
                        'backendLangId',
                        'isAdmin',
                        'profileAccess',
                    ),
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
                        'showDetail' => true,
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
                        'showOverview' => false,
                        'showDetail' => false,
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
                        'showDetail' => false,
                    ),
                    'frontendLangId' => array(
                        'showOverview' => false,
                    ),
                    'backendLangId' => array(
                        'showOverview' => false,
                    ),
                    'verified' => array(
                        'showOverview' => false,
                        'showDetail' => false,
                    ),
                    'primaryGroup' => array(
                        'showOverview' => false,
                        'showDetail' => false,
                    ),
                    'profileAccess' => array(
                        'showOverview' => false,
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
                    ),
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
                    'user' => array(
                        'showOverview' => true,
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
}


