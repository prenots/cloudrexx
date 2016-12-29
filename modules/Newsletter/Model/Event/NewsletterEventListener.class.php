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
 * NewsletterEventListener
 *
 * @copyright   Cloudrexx AG
 * @author      Project Team SS4U <info@cloudrexx.com>
 * @package     cloudrexx
 * @subpackage  module_newsletter
 */

namespace Cx\Modules\Newsletter\Model\Event;

/**
 * NewsletterEventListener
 *
 * @copyright   Cloudrexx AG
 * @author      Project Team SS4U <info@cloudrexx.com>
 * @package     cloudrexx
 * @subpackage  module_newsletter
 */
class NewsletterEventListener extends \Cx\Core\Event\Model\Entity\DefaultEventListener
{
    /**
     * Clear all Esi cache
     *
     * @param string $eventArg event argument
     *
     * @return null
     */
    public function clearEsiCache($eventArg)
    {
        if (empty($eventArg) || $eventArg != 'Newsletter') {
            return;
        }

        //Clear ESI cache for best rated/most viewed Articles or tag cloud
        $cache = $this->cx->getComponent('Cache');
        foreach (\FWLanguage::getActiveFrontendLanguages() as $lang) {
            $cache->clearSsiCachePage(
                'Newsletter',
                'getForm',
                array('lang' => $lang['id'])
            );
        }
    }
}
