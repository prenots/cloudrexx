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
                        'email',
                        'username',
                        'regdate',
                        'lastActivity',
                        'lastAuth',
                    ),
                    'form' => array(
                        'email',
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
                        'showOverview' => true,
                        'showDetail' => false,
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
                        'showOverview' => true,
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
                        'showOverview' => true,
                        'showDetail' => false,
                    )
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
}


