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
 * Main controller for Newsletter
 *
 * @copyright   Cloudrexx AG
 * @author      Project Team SS4U <info@cloudrexx.com>
 * @package     cloudrexx
 * @subpackage  module_newsletter
 */

namespace Cx\Modules\Newsletter\Controller;

/**
 * Main controller for Newsletter
 *
 * @copyright   Cloudrexx AG
 * @author      Project Team SS4U <info@cloudrexx.com>
 * @package     cloudrexx
 * @subpackage  module_newsletter
 */
class ComponentController extends \Cx\Core\Core\Model\Entity\SystemComponentController implements \Cx\Core\Json\JsonAdapter {

    /**
     * Get controller classes
     *
     * @return array
     */
    public function getControllerClasses()
    {
        // Return an empty array here to let the component handler know that there
        // does not exist a backend, nor a frontend controller of this component.
        return array();
    }

    /**
     * Returns a list of JsonAdapter class names
     *
     * @return array list of JsonAdapter class names
     */
    public function getControllersAccessableByJson()
    {
        return array('ComponentController');
    }

    /**
     * Load your component.
     *
     * @param \Cx\Core\ContentManager\Model\Entity\Page $page The resolved page
     */
    public function load(\Cx\Core\ContentManager\Model\Entity\Page $page)
    {
        global $_CORELANG, $objTemplate, $subMenuTitle;
        switch ($this->cx->getMode()) {
            case \Cx\Core\Core\Controller\Cx::MODE_FRONTEND:
                $newsletter = new Newsletter(\Env::get('cx')->getPage()->getContent());
                \Env::get('cx')->getPage()->setContent($newsletter->getPage());
                break;

            case \Cx\Core\Core\Controller\Cx::MODE_BACKEND:
                $this->cx->getTemplate()->addBlockfile(
                    'CONTENT_OUTPUT',
                    'content_master',
                    'LegacyContentMaster.html'
                );
                $objTemplate = $this->cx->getTemplate();

                $subMenuTitle = $_CORELANG['TXT_CORE_EMAIL_MARKETING'];
                $objNewsletter = new NewsletterManager();
                $objNewsletter->getPage();
                break;
        }
    }

    /**
     * Do something after resolving is done
     *
     * @param \Cx\Core\ContentManager\Model\Entity\Page $page The resolved page
     */
    public function postResolve(\Cx\Core\ContentManager\Model\Entity\Page $page)
    {
        global $command;
        switch ($this->cx->getMode()) {
            case \Cx\Core\Core\Controller\Cx::MODE_FRONTEND:
                if (Newsletter::isTrackLink()) {
                    //handle link tracker from newsletter, since user should be redirected to the link url
                    /*
                    * Newsletter Module
                    *
                    * Generates no output, requests are answered by a redirect to foreign site
                    *
                    */
                    Newsletter::trackLink();
                    //execution should never reach this point, but let's be safe and call exit anyway
                    exit;
                } elseif ($command == 'displayInBrowser') {
                    Newsletter::displayInBrowser();
                    //execution should never reach this point, but let's be safe and call exit anyway
                    exit;
                }
                // regular newsletter request (like subscribing, profile management, etc).
                // must not abort by an exit call here!
                break;
        }
    }

    /**
     * Do something before content is loaded from DB
     *
     * @param \Cx\Core\ContentManager\Model\Entity\Page $page The resolved page
     */
    public function preContentLoad(
        \Cx\Core\ContentManager\Model\Entity\Page $page
    ) {
        global $_ARRAYLANG, $page_template, $themesPages;

        if ($this->cx->getMode() != \Cx\Core\Core\Controller\Cx::MODE_FRONTEND) {
            return;
        }
        // get Newsletter
        $_ARRAYLANG = array_merge(
            $_ARRAYLANG,
            \Env::get('init')->loadLanguageData('Newsletter')
        );
        $newsletter = new Newsletter('');
        $cache      = \Cx\Core\Core\Controller\Cx::instanciate()
            ->getComponent('Cache');
        $content    = $this->cx->getPage()->getContent();
        if (preg_match('/{NEWSLETTER_BLOCK}/', $content)) {
            $newsletter->setBlock($cache, $content, $this->cx->getPage());
        }
        if (preg_match('/{NEWSLETTER_BLOCK}/', $page_template)) {
            $newsletter->setBlock($cache, $page_template);
        }
        if (preg_match('/{NEWSLETTER_BLOCK}/', $themesPages['index'])) {
            $newsletter->setBlock($cache, $themesPages['index']);
        }
    }

    /**
      * Register your event listeners here
      *
      * USE CAREFULLY, DO NOT DO ANYTHING COSTLY HERE!
      * CALCULATE YOUR STUFF AS LATE AS POSSIBLE.
      * Keep in mind, that you can also register your events later.
      * Do not do anything else here than initializing your event listeners and
      * list statements like
      * $this->cx->getEvents()->addEventListener($eventName, $listener);
      */
    public function registerEventListeners()
    {
        $eventListener =
            new \Cx\Modules\Newsletter\Model\Event\NewsletterEventListener($this->cx);
        $this->cx->getEvents()->addEventListener('clearEsiCache', $eventListener);
    }

    /**
     * Returns an array of method names accessable from a JSON request
     *
     * @return array List of method names
     */
    public function getAccessableMethods()
    {
        return array('getForm');
    }

    /**
     * Returns default permission as object
     *
     * @return Object
     */
    public function getDefaultPermissions()
    {
        return new \Cx\Core_Modules\Access\Model\Entity\Permission(
            null,
            null,
            false
        );
    }

    /**
     * Returns all messages as string
     *
     * @return String HTML encoded error messages
     */
    public function getMessagesAsString()
    {
        return '';
    }

    /**
     * Wrapper to __call()
     *
     * @return string ComponentName
     */
    public function getName()
    {
        return parent::getName();
    }

    /**
     * Get newsletter subscription form
     *
     * @param array $params all given params from http request
     *
     * @return array form content
     */
    public function getForm($params)
    {
        $langId = !empty($params['get']['lang'])
            ? contrexx_input2int($params['get']['lang']) : 0;
        if (empty($langId)) {
            return array('content' => '');
        }

        try {
            $newsletterLibrary = new NewsletterLib();
            return array(
                'content' => $newsletterLibrary->_getHTML(false, $langId)
            );
        } catch (\Exception $ex) {
            \DBG::log($ex->getMessage());
            return array('content' => '');
        }
    }
}
