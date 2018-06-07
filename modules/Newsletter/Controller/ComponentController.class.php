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
class ComponentController extends \Cx\Core\Core\Model\Entity\SystemComponentController {
    /**
     * Returns all Controller class names for this component (except this)
     *
     * Be sure to return all your controller classes if you add your own
     * @return array List of Controller class names (without namespace)
     */
    public function getControllerClasses()
    {
        return array('EsiWidget');
    }

    /**
     * Returns a list of JsonAdapter class names
     *
     * The array values might be a class name without namespace. In that case
     * the namespace \Cx\{component_type}\{component_name}\Controller is used.
     * If the array value starts with a backslash, no namespace is added.
     *
     * Avoid calculation of anything, just return an array!
     * @return array List of ComponentController classes
     */
    public function getControllersAccessableByJson()
    {
        return array('EsiWidgetController');
    }

     /**
     * Load your component.
     *
     * @param \Cx\Core\ContentManager\Model\Entity\Page $page       The resolved page
     */
    public function load(\Cx\Core\ContentManager\Model\Entity\Page $page) {
        global $_CORELANG, $objTemplate, $subMenuTitle;
        switch ($this->cx->getMode()) {
            case \Cx\Core\Core\Controller\Cx::MODE_FRONTEND:
                $newsletter = new Newsletter(\Env::get('cx')->getPage()->getContent());
                \Env::get('cx')->getPage()->setContent($newsletter->getPage());
                break;

            case \Cx\Core\Core\Controller\Cx::MODE_BACKEND:
                $this->cx->getTemplate()->addBlockfile('CONTENT_OUTPUT', 'content_master', 'LegacyContentMaster.html');
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
     * @param \Cx\Core\ContentManager\Model\Entity\Page $page       The resolved page
     */
    public function postResolve(\Cx\Core\ContentManager\Model\Entity\Page $page) {
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
     * Do something after system initialization
     *
     * USE CAREFULLY, DO NOT DO ANYTHING COSTLY HERE!
     * CALCULATE YOUR STUFF AS LATE AS POSSIBLE.
     * This event must be registered in the postInit-Hook definition
     * file config/postInitHooks.yml.
     * @param \Cx\Core\Core\Controller\Cx   $cx The instance of \Cx\Core\Core\Controller\Cx
     */
    public function postInit(\Cx\Core\Core\Controller\Cx $cx)
    {
        $widgetController = $this->getComponent('Widget');
        $widget = new \Cx\Core_Modules\Widget\Model\Entity\EsiWidget(
            $this,
            'NEWSLETTER_BLOCK'
        );
        $widget->setEsiVariable(
            \Cx\Core_Modules\Widget\Model\Entity\EsiWidget::ESI_VAR_ID_THEME |
            \Cx\Core_Modules\Widget\Model\Entity\EsiWidget::ESI_VAR_ID_CHANNEL
        );
        $widgetController->registerWidget(
            $widget
        );
    }
}
