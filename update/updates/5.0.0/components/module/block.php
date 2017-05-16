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


function _blockUpdate()
{
    global $_ARRAYLANG, $objUpdate, $_CONFIG;

    if ($objUpdate->_isNewerVersion($_CONFIG['coreCmsVersion'], '5.0.0')) {
        // migrate path to images and media
        $pathsToMigrate = \Cx\Lib\UpdateUtil::getMigrationPaths();
        try {
            foreach ($pathsToMigrate as $oldPath => $newPath) {
                \Cx\Lib\UpdateUtil::migratePath(
                    '`' . DBPREFIX . 'module_block_rel_lang_content`',
                    '`content`',
                    $oldPath,
                    $newPath
                );
            }
        } catch (\Cx\Lib\Update_DatabaseException $e) {
            \DBG::log($e->getMessage());
            setUpdateMsg(sprintf(
                $_ARRAYLANG['TXT_UNABLE_TO_MIGRATE_MEDIA_PATH'],
                'Inhaltscontainer (Block)'
            ));
            return false;
        }

        // migrate database structure and data
        try {
            \Cx\Lib\UpdateUtil::table(
                DBPREFIX . 'module_block_blocks',
                array(
                    'id' => array('type' => 'INT(11)', 'unsigned' => true, 'notnull' => true, 'auto_increment' => true, 'primary' => true),
                    'start' => array('type' => 'INT(11)', 'notnull' => true, 'default' => '0', 'after' => 'id'),
                    'end' => array('type' => 'INT(11)', 'notnull' => true, 'default' => '0', 'after' => 'start'),
                    'name' => array('type' => 'VARCHAR(255)', 'notnull' => true, 'default' => '', 'after' => 'end'),
                    'random' => array('type' => 'INT(1)', 'notnull' => true, 'default' => '0', 'after' => 'name'),
                    'random_2' => array('type' => 'INT(1)', 'notnull' => true, 'default' => '0', 'after' => 'random'),
                    'random_3' => array('type' => 'INT(1)', 'notnull' => true, 'default' => '0', 'after' => 'random_2'),
                    'random_4' => array('type' => 'INT(1)', 'notnull' => true, 'default' => '0', 'after' => 'random_3'),
                    'global' => array('type' => 'INT(1)', 'notnull' => true, 'default' => '0', 'after' => 'random_4'),
                    'category' => array('type' => 'INT(1)', 'notnull' => true, 'default' => '0', 'after' => 'global'),
                    'direct' => array('type' => 'INT(1)', 'notnull' => true, 'default' => '0', 'after' => 'category'),
                    'active' => array('type' => 'INT(1)', 'notnull' => true, 'default' => '0', 'after' => 'direct'),
                    'order' => array('type' => 'INT(1)', 'notnull' => true, 'default' => '0', 'after' => 'active'),
                    'cat' => array('type' => 'INT(11)', 'unsigned' => true, 'notnull' => false, 'after' => 'order'),
                    'wysiwyg_editor' => array('type' => 'INT(1)', 'notnull' => true, 'default' => '1', 'after' => 'cat'),
                    'module_block_blocks_ibfk_cat' => array('type' => 'FOREIGN', 'after' => 'wysiwyg_editor')
                ),
                array(
                    'module_block_blocks_ibfk_cat_idx' => array('fields' => array('cat'))
                )
            );

            \Cx\Lib\UpdateUtil::table(
                DBPREFIX . 'module_block_categories',
                array(
                    'id' => array('type' => 'INT(11)', 'unsigned' => true, 'notnull' => true, 'auto_increment' => true, 'primary' => true),
                    'parent' => array('type' => 'INT(11)', 'unsigned' => true, 'notnull' => false, 'after' => 'id'),
                    'name' => array('type' => 'VARCHAR(255)', 'notnull' => true, 'default' => '', 'after' => 'parent'),
                    'seperator' => array('type' => 'VARCHAR(255)', 'notnull' => true, 'default' => '', 'after' => 'name'),
                    'order' => array('type' => 'INT(11)', 'notnull' => true, 'default' => '0', 'after' => 'seperator'),
                    'status' => array('type' => 'TINYINT(1)', 'notnull' => true, 'default' => '1', 'after' => 'order'),
                    'module_block_category_ibfk_parent' => array('type' => 'FOREIGN', 'after' => 'status')
                ),
                array(
                    'module_block_category_ibfk_parent_idx' => array('fields' => array('parent'))
                )
            );

            \Cx\Lib\UpdateUtil::table(
                DBPREFIX . 'module_block_rel_lang_content',
                array(
                    'id' => array('type' => 'INT(11)', 'unsigned' => true, 'notnull' => true, 'auto_increment' => true, 'primary' => true),
                    'block_id' => array('type' => 'INT(11)', 'unsigned' => true, 'default' => '0', 'after' => 'id'),
                    'lang_id' => array('type' => 'INT(11)', 'notnull' => true, 'default' => '0', 'after' => 'block_id'),
                    'content' => array('type' => 'mediumtext', 'after' => 'lang_id'),
                    'active' => array('type' => 'INT(1)', 'notnull' => true, 'default' => '0', 'after' => 'content'),
                    'module_block_rel_lang_content_ibfk_block_id' => array('type' => 'FOREIGN', 'after' => 'active'),
                    'module_block_rel_lang_content_ibfk_lang_id' => array('type' => 'FOREIGN', 'after' => 'module_block_rel_lang_content_ibfk_block_id')
                ),
                array(
                    'unique_block_page' => array('fields' => array('block_id', 'lang_id'), 'type' => 'UNIQUE'),
                    'module_block_rel_lang_content_ibfk_lang_id_idx' => array('fields' => array('lang_id'))
                )
            );

            \Cx\Lib\UpdateUtil::table(
                DBPREFIX . 'module_block_rel_pages',
                array(
                    'id' => array('type' => 'INT(11)', 'unsigned' => true, 'notnull' => true, 'auto_increment' => true, 'primary' => true),
                    'block_id' => array('type' => 'INT(11)', 'unsigned' => true, 'default' => '0', 'after' => 'id'),
                    'page_id' => array('type' => 'INT(11)', 'notnull' => true, 'default' => '0', 'after' => 'block_id'),
                    'placeholder' => array('type' => 'ENUM(\'global\',\'direct\',\'category\')', 'notnull' => true, 'default' => 'global', 'after' => 'page_id'),
                    'module_block_rel_page_ibfk_block_id' => array('type' => 'FOREIGN', 'after' => 'placeholder'),
                    'module_block_rel_page_ibfk_page_id' => array('type' => 'FOREIGN', 'after' => 'module_block_rel_page_ibfk_block_id')
                ),
                array(
                    'unique_block_page_placeholder' => array('fields' => array('block_id', 'page_id', 'placeholder'), 'type' => 'UNIQUE'),
                    'module_block_rel_pages_ibfk_page_id_idx' => array('fields' => array('page_id'))
                )
            );

            \Cx\Lib\UpdateUtil::table(
                DBPREFIX . 'module_block_targeting_option',
                array(
                    'id' => array('type' => 'INT(11)', 'unsigned' => true, 'notnull' => true, 'auto_increment' => true, 'primary' => true),
                    'block_id' => array('type' => 'INT(11)', 'unsigned' => true, 'notnull' => false, 'after' => 'id'),
                    'filter' => array('type' => 'ENUM(\'include\',\'exclude\')', 'notnull' => true, 'default' => 'include', 'after' => 'block_id'),
                    'type' => array('type' => 'ENUM(\'country\')', 'notnull' => true, 'default' => 'country', 'after' => 'filter'),
                    'value' => array('type' => 'text', 'after' => 'type'),
                    'module_block_targeting_option_ibfk_block_id' => array('type' => 'FOREIGN', 'after' => 'value')
                ),
                array(
                    'unique_block_type' => array('fields' => array('block_id', 'type'), 'type' => 'UNIQUE')
                )
            );

            \Cx\Core\Setting\Controller\Setting::init('Block', 'setting');
            $settings = \Cx\Lib\UpdateUtil::sql('SELECT * FROM `contrexx_module_block_settings`');
            foreach ($settings as $setting) {
                switch ($setting->fields['name']) {
                    case 'blockGlobalSeperator':
                        if (!\Cx\Core\Setting\Controller\Setting::isDefined($setting->fields['name'])) {
                            \Cx\Core\Setting\Controller\Setting::add(
                                $setting->fields['name'],
                                $setting->fields['value'],
                                $setting->fields['id'],
                                \Cx\Core\Setting\Controller\Setting::TYPE_TEXT,
                                '',
                                'setting'
                            );
                        } else {
                            \Cx\Core\Setting\Controller\Setting::set(
                                $setting->fields['name'],
                                $setting->fields['value']
                            );
                            \Cx\Core\Setting\Controller\Setting::update($setting->fields['name']);
                        }
                        break;
                    case 'markParsedBlock':
                        if (!\Cx\Core\Setting\Controller\Setting::isDefined($setting->fields['name'])) {
                            \Cx\Core\Setting\Controller\Setting::add(
                                $setting->fields['name'],
                                $setting->fields['value'],
                                $setting->fields['id'],
                                \Cx\Core\Setting\Controller\Setting::TYPE_CHECKBOX,
                                '0',
                                'setting'
                            );
                        } else {
                            \Cx\Core\Setting\Controller\Setting::set(
                                $setting->fields['name'],
                                $setting->fields['value']
                            );
                            \Cx\Core\Setting\Controller\Setting::update($setting->fields['name']);
                        }
                        break;
                    default:
                        break;
                }
            }
            \Cx\Lib\UpdateUtil::drop_table('contrexx_module_block_settings');

        } catch (\Cx\Lib\Update_DatabaseException $e) {
            return \Cx\Lib\UpdateUtil::DefaultActionHandler($e);
        }
    }

    return true;
}

