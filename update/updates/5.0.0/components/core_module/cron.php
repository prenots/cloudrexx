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

function _cronUpdate()
{
    global $objUpdate, $_CONFIG;

    if ($objUpdate->_isNewerVersion($_CONFIG['coreCmsVersion'], '5.0.0')) {
        try {
            \Cx\Lib\UpdateUtil::table(
                DBPREFIX.'core_module_cron_job',
                array(
                    'id'         => array('type' => 'INT(11)', 'notnull' => true, 'auto_increment' => true, 'primary' => true),
                    'active'     => array('type' => 'TINYINT(1)', 'notnull' => true, 'after' => 'id'),
                    'expression' => array('type' => 'VARCHAR(255)', 'notnull' => true, 'after' => 'active'),
                    'command'    => array('type' => 'VARCHAR(255)', 'notnull' => true, 'after' => 'expression'),
                    'last_ran'   => array('type' => 'DATETIME', 'notnull' => true, 'after' => 'command')
                )
            );

            $result = \Cx\Lib\UpdateUtil::sql('SELECT 1 FROM `'.DBPREFIX.'core_module_cron_job` WHERE `command` = \'Newsletter autoclean\'');
            if ($result->EOF) {
                \Cx\Lib\UpdateUtil::sql('INSERT INTO `'.DBPREFIX.'core_module_cron_job` (`id`, `active`, `expression`, `command`, `last_ran`) VALUES(1, 1, \'@hourly\', \'Newsletter autoclean\', \'2018-06-11 09:00:00\')');
            }
        } catch (\Cx\Lib\UpdateException $e) {
            return \Cx\Lib\UpdateUtil::DefaultActionHandler($e);
        }
    }

    return true;
}
