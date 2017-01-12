<?php

/**
 * Cloudrexx
 *
 * @link      http://www.cloudrexx.com
 * @copyright Cloudrexx AG 2007-2017
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

function _viewInstall() {
    global $objUpdate, $_CONFIG;

    if ($objUpdate->_isNewerVersion($_CONFIG['coreCmsVersion'], '5.0.0')) {
        try {
            // structure
            \Cx\Lib\UpdateUtil::table(
                DBPREFIX.'core_view_frontend',
                array(
                    'language'                                   => array('type' => 'INT(11)'),
                    'theme'                                      => array('type' => 'INT(2)', 'unsigned' => true, 'notnull' => true, 'default' => '0', 'after' => 'language'),
                    'channel'                                    => array('type' => 'ENUM(\'default\',\'mobile\',\'print\',\'pdf\',\'app\')', 'notnull' => true, 'default' => 'default', 'after' => 'theme'),
                    'contrexx_core_view_frontend_ibfk_locale'    => array('type' => 'FOREIGN', 'after' => 'channel'),
                    'contrexx_core_view_frontend_ibfk_theme'     => array('type' => 'FOREIGN', 'after' => 'contrexx_core_view_frontend_ibfk_locale')
                ),
                array(
                    'contrexx_core_view_frontend_ibfk_theme'     => array('fields' => array('theme'))
                )
            );
            // data
            $themesArray = array(
                'themesid' => 'default',
                'mobile_themes_id' => 'mobile',
                'print_themes_id' => 'print',
                'pdf_themes_id' => 'pdf',
                'app_themes_id' => 'app'
            );
            foreach ($themesArray as $themesIdCol => $channel) {
                \Cx\Lib\UpdateUtil::sql("
                    INSERT INTO `".DBPREFIX."core_view_frontend` (`language`, `theme`, `channel`) 
                    SELECT 
                        l.id, 
                        (SELECT `".$themesIdCol."` FROM `".DBPREFIX."languages` WHERE `lang` = l.iso_1), 
                        ".$channel."
                    FROM `".DBPREFIX."core_locale_locale` AS l;
                ");
            }
        } catch (\Cx\Lib\UpdateException $e) {
            return \Cx\Lib\UpdateUtil::DefaultActionHandler($e);
        }
    }
    return true;
}