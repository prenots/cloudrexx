<?php

/**
 * Cloudrexx
 *
 * @link      http://www.cloudrexx.com
 * @copyright Cloudrexx AG 2007-2016
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

namespace Cx\Modules\Gallery\Model\Event;

/**
 * LocaleLocaleEventListener
 *
 * @copyright   CLOUDREXX CMS - Cloudrexx AG Thun
 * @author      Project Team SS4U <info@cloudrexx.com>
 * @package     cloudrexx
 * @subpackage  module_gallery
 * @version     1.0.0
 */
class LocaleLocaleEventListener extends \Cx\Core\Event\Model\Entity\DefaultEventListener
{
    /**
     * Fills the new gallery locales (categories and picture) with the default
     * locale's values when adding a new Cx\Core\Locale\Model\Entity\Locale
     *
     * @param object $eventArgs
     */
    public function postPersist($eventArgs)
    {
        $persistedLocale = $eventArgs->getEntity(); // Get persisted locale
        $defaultLocaleId = \FWLanguage::getDefaultLangId();
        $localeId        = $persistedLocale->getId();
        $db              = \Cx\Core\Core\Controller\Cx::instanciate()->getDb()->getAdoDb();

        // Add new category locales
        $categoryQuery =
            'INSERT IGNORE INTO `' . DBPREFIX . 'module_gallery_language`
            (
                `gallery_id`,
                `lang_id`,
                `name`,
                `value`
            )
            SELECT
                `gallery_id`,
                ' . $localeId . ',
                `name`,
                `value`
            FROM `' . DBPREFIX . 'module_gallery_language`
            WHERE lang_id = ' . $defaultLocaleId;
        $db->Execute($categoryQuery);

        // Add new picture locales
        $pictureQuery =
            'INSERT IGNORE INTO `' . DBPREFIX . 'module_gallery_language_pics`
            (
                `picture_id`,
                `lang_id`,
                `name`,
                `desc`
            )
            SELECT
                `picture_id`,
                ' . $localeId . ',
                `name`,
                `desc`
            FROM `' . DBPREFIX . 'module_gallery_language_pics`
            WHERE lang_id = ' . $defaultLocaleId;
        $db->Execute($pictureQuery);
    }

    /**
     * Deletes the gallery locales (categories and picture) when deleting
     * a Cx\Core\Locale\Model\Entity\Locale
     *
     * @param object $eventArgs
     */
    public function preRemove($eventArgs)
    {
        $delLocale = $eventArgs->getEntity(); // Get locale, which will be deleted
        $localeId  = $delLocale->getId();
        $db        = \Cx\Core\Core\Controller\Cx::instanciate()->getDb()->getAdoDb();
        // Delete the category locales
        $categoryQuery = 'DELETE FROM `' . DBPREFIX . 'module_gallery_language`
            WHERE lang_id = ' . $localeId;
        $db->Execute($categoryQuery);

        // Delete the picture locales
        $pictureQuery = 'DELETE FROM `' . DBPREFIX . 'module_gallery_language_pics`
            WHERE lang_id = ' . $localeId;
        $db->Execute($pictureQuery);
    }
}
