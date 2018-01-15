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
 * EventListener for FileSharing
 *
 * @copyright   Cloudrexx AG
 * @author      Project Team SS4U <info@cloudrexx.com>
 * @package     cloudrexx
 * @subpackage  module_filesharing
 */

namespace Cx\Modules\FileSharing\Model\Event;

/**
 * EventListener for FileSharing
 *
 * @copyright   Cloudrexx AG
 * @author      Project Team SS4U <info@cloudrexx.com>
 * @package     cloudrexx
 * @subpackage  module_filesharing
 */
class FileSharingEventListener extends \Cx\Core\Event\Model\Entity\DefaultEventListener {

    /**
     * Add MediaSource
     *
     * @param \Cx\Core\MediaSource\Model\Entity\MediaSourceManager $mediaBrowserConfiguration Object of MediaSourceManager
     */
    public function mediasourceLoad(
        \Cx\Core\MediaSource\Model\Entity\MediaSourceManager $mediaBrowserConfiguration
    ) {
        $langData  = \Env::get('init')->loadLanguageData('FileSharing');
        $mediaType = new \Cx\Core\MediaSource\Model\Entity\MediaSource(
            'filesharing',
            $langData['TXT_FILESHARING_MODULE'],
            array(
                $this->cx->getWebsiteMediaFileSharingPath(),
                $this->cx->getWebsiteMediaFileSharingWebPath(),
            )
        );
        $mediaType->setAccessIds(array(8));
        $mediaBrowserConfiguration->addMediaType($mediaType);
    }
}
