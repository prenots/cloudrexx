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
 * MarketEventListener
 *
 * @copyright   Cloudrexx AG
 * @author      Project Team SS4U <info@cloudrexx.com>
 * @package     cloudrexx
 * @subpackage  module_market
 */
 
namespace Cx\Modules\Market\Model\Event;

/**
 * MarketEventListener
 *
 * @copyright   Cloudrexx AG
 * @author      Project Team SS4U <info@cloudrexx.com>
 * @package     cloudrexx
 * @subpackage  module_market
 */
class MarketEventListener extends \Cx\Core\Event\Model\Entity\DefaultEventListener {
    /**
     * Add MediaSource
     *
     * @param MediaSourceManager $mediaSourceManager MediaSourceManager object
     */
    public function mediaSourceLoad(
        \Cx\Core\MediaSource\Model\Entity\MediaSourceManager $mediaSourceManager
    ) {
        $langData  = \Env::get('init')->loadLanguageData('Market');
        $mediaType = new \Cx\Core\MediaSource\Model\Entity\MediaSource(
            'market',
            $langData['TXT_MARKET_MODULE_DESCRIPTION'],
            array(
                $this->cx->getWebsiteMediaMarketPath(),
                $this->cx->getWebsiteMediaMarketWebPath()
            ),
            array(98)
        );
        $mediaSourceManager->addMediaType($mediaType);
    }
}
