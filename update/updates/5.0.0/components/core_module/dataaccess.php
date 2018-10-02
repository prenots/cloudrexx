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

function _dataaccessUpdate()
{
    global $objUpdate, $_CONFIG;

    if ($objUpdate->_isNewerVersion($_CONFIG['coreCmsVersion'], '5.0.0')) {
        try {
            \Cx\Lib\UpdateUtil::table(
                DBPREFIX.'core_module_data_access',
                array(
                    'id' => array('type' => 'INT(11)', 'notnull' => true, 'auto_increment' => true, 'primary' => true),
                    'read_permission' => array('type' => 'INT(11)', 'notnull' => false, 'after' => 'id'),
                    'write_permission' => array('type' => 'INT(11)', 'notnull' => false, 'after' => 'read_permission'),
                    'data_source_id' => array('type' => 'INT(11)', 'notnull' => false, 'after' => 'write_permission'),
                    'name' => array('type' => 'VARCHAR(255)', 'after' => 'data_source_id'),
                    'field_list' => array('type' => 'longtext', 'after' => 'name'),
                    'access_condition' => array('type' => 'longtext', 'after' => 'field_list'),
                    'allowed_output_methods' => array('type' => 'longtext', 'after' => 'access_condition'),
                ),
                array(
                    'name' => array('fields' => array('name'), 'type' => 'UNIQUE'),
                ),
                'InnoDB',
                '',
                array(
                    'read_permission' => array(
                        'table' => DBPREFIX.'core_modules_access_permission',
                        'column' => 'id',
                        'onDelete' => 'NO ACTION',
                        'onUpdate' => 'NO ACTION',
                    ),
                    'write_permission' => array(
                        'table' => DBPREFIX.'core_modules_access_permission',
                        'column' => 'id',
                        'onDelete' => 'NO ACTION',
                        'onUpdate' => 'NO ACTION',
                    ),
                    'data_source_id' => array(
                        'table' => DBPREFIX.'core_data_source',
                        'column' => 'id',
                        'onDelete' => 'NO ACTION',
                        'onUpdate' => 'NO ACTION',
                    ),
                )
            );
            \Cx\Lib\UpdateUtil::table(
                DBPREFIX.'core_module_data_access_apikey',
                array(
                    'id' => array('type' => 'INT(11)', 'notnull' => true, 'auto_increment' => true, 'primary' => true),
                    'api_key' => array('type' => 'VARCHAR(32)', 'after' => 'id')
                ),
                array(
                    'api_key' => array('fields' => array('api_key'), 'type' => 'UNIQUE')
                )
            );
            \Cx\Lib\UpdateUtil::table(
                DBPREFIX.'core_module_data_access_data_access_apikey',
                array(
                    'id' => array('type' => 'INT(11)', 'notnull' => true, 'auto_increment' => true, 'primary' => true),
                    'api_key_id' => array('type' => 'INT(11)', 'notnull' => false, 'after' => 'id'),
                    'data_access_id' => array('type' => 'INT(11)', 'notnull' => false, 'after' => 'api_key_id'),
                    'read_only' => array('type' => 'TINYINT(1)', 'notnull' => false, 'after' => 'data_access_id'),
                ),
                array(),
                'InnoDB',
                '',
                array(
                    'api_key_id' => array(
                        'table' => DBPREFIX.'core_module_data_access_apikey',
                        'column' => 'id',
                        'onDelete' => 'NO ACTION',
                        'onUpdate' => 'NO ACTION',
                    ),
                    'data_access_id' => array(
                        'table' => DBPREFIX.'core_module_data_access',
                        'column' => 'id',
                        'onDelete' => 'NO ACTION',
                        'onUpdate' => 'NO ACTION',
                    ),
                )
            );
        } catch (\Cx\Lib\UpdateException $e) {
            return \Cx\Lib\UpdateUtil::DefaultActionHandler($e);
        }
    }

    return true;
}
