<?php

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
 * This file is used as the JsonController of the core user.
 *
 * @category  CloudrexxApp
 * @package   User
 * @author    Cloudrexx AG <dario.graf@comvation.com>
 * @copyright Cloudrexx AG
 * @link      https://www.cloudrexx.com
 */

class JsonUserController extends \Cx\Core\Core\Model\Entity\Controller implements \Cx\Core\Json\JsonAdapter
{
    /**
     * Returns the internal name used as identifier for this adapter
     * @return String Name of this adapter
     */
    public function getName() {
        return 'JsonUser';
    }

    /**
     * Returns an array of method names accessable from a JSON request
     * @return array List of method names
     */
    public function getAccessableMethods() {
        return array('verifyCode', 'deleteLink');
    }

    /**
     * Returns all messages as string
     * @return String HTML encoded error messages
     */
    public function getMessagesAsString() {
        return '';
    }

    /**
     * Returns default permission as object
     * @return Object
     */
    public function getDefaultPermissions()
    {
        return null;
    }

    /**
     * Verify the generated code and return html structure with corresponding
     * response, depending on the case
     *
     * @param $arguments array Transferred get arguments from ajax request
     * @return array           Show depending on the case, succes or error message
     */
    public function verifyCode($arguments)
    {
        global $_ARRAYLANG, $objInit;

        //get the language interface text
        $langData   = $objInit->getComponentSpecificLanguageData(
            'User',
            false
        );
        $_ARRAYLANG = array_merge($_ARRAYLANG, $langData);

        $errorMessage = new \Cx\Core\Html\Model\Entity\HtmlElement('div');
        $errorMessage->setAttribute('id', 'user-response-error');
        $errorText = new \Cx\Core\Html\Model\Entity\TextElement($_ARRAYLANG['TXT_CORE_TWOFACTOR_CODE_ERROR']);
        $successMessage = new \Cx\Core\Html\Model\Entity\HtmlElement('div');
        $successMessage->setAttribute('id', 'user-response-success');
        $successText = new \Cx\Core\Html\Model\Entity\TextElement($_ARRAYLANG['TXT_CORE_TWOFACTOR_CODE_SUCCESS']);

        $code = contrexx_input2raw($arguments['get']['code']);
        $secret = contrexx_input2raw($arguments['get']['secret']);

        if (empty($code) && empty($secret)) {
            $errorMessage->addChild($errorText);

            return array('content' => $errorMessage->render());
        }

        $tfa = new \Cx\Core_Modules\Login\Controller\TwoFactorAuthentication();

        $result = $tfa->verifyCode($secret, $code);

        if (!$result) {
            $errorMessage->addChild($errorText);

            return array('content' => $errorMessage->render());
        }

        $successMessage->addChild($successText);

        return array('content' => $successMessage->render());
    }

    /**
     * @param $arguments
     */
    public function deleteLink($arguments)
    {
        global $_ARRAYLANG, $objInit;

        //get the language interface text
        $langData   = $objInit->getComponentSpecificLanguageData(
            'User',
            false
        );
        $_ARRAYLANG = array_merge($_ARRAYLANG, $langData);

        $userId = contrexx_input2raw($arguments['get']['user']);

        $errorMessage = new \Cx\Core\Html\Model\Entity\HtmlElement('div');
        $errorMessage->setAttribute('id', 'user-response-error');
        $errorText = new \Cx\Core\Html\Model\Entity\TextElement($_ARRAYLANG['TXT_CORE_TWOFACTOR_DELETE_ERROR']);
        $successMessage = new \Cx\Core\Html\Model\Entity\HtmlElement('div');
        $successMessage->setAttribute('id', 'user-response-success');
        $successText = new \Cx\Core\Html\Model\Entity\TextElement($_ARRAYLANG['TXT_CORE_TWOFACTOR_DELETE_SUCCESS']);

        if (empty($userId)) {
            $errorMessage->addChild($errorText);

            return array('content' => $errorMessage->render());
        }

        $em = \Cx\Core\Core\Controller\Cx::instanciate()->getDb()->getEntityManager();

        $userRepo = $em->getRepository(
            '\Cx\Core\User\Model\Entity\User'
        );

        $user = $userRepo->findOneBy(array('id' => $userId));

        $repo = $em->getRepository(
            '\Cx\Core\User\Model\Entity\TwoFactorAuthentication'
        );

        $twoFactorEntry = $repo->findOneBy(array('user' => $user));

        $user->setTwoFaActive(0);

        $em->remove($twoFactorEntry, $user);
        $em->flush();


        $successMessage->addChild($successText);

        return array('content' => $successMessage->render());
    }
}