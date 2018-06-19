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

function _feedUpdate()
{
    try {
        \Cx\Lib\UpdateUtil::table(
            DBPREFIX.'module_feed_newsml_documents',
            array(
                'id'                     => array('type' => 'INT(10)', 'unsigned' => true, 'notnull' => true, 'auto_increment' => true, 'primary' => true),
                'publicIdentifier'       => array('type' => 'VARCHAR(255)', 'notnull' => true, 'default' => '', 'after' => 'id'),
                'providerId'             => array('type' => 'text', 'after' => 'publicIdentifier'),
                'dateId'                 => array('type' => 'INT(8)', 'unsigned' => true, 'notnull' => true, 'default' => '0', 'after' => 'providerId'),
                'newsItemId'             => array('type' => 'text', 'after' => 'dateId'),
                'revisionId'             => array('type' => 'INT(5)', 'unsigned' => true, 'notnull' => true, 'default' => '0', 'after' => 'newsItemId'),
                'thisRevisionDate'       => array('type' => 'INT(14)', 'notnull' => true, 'default' => '0', 'after' => 'revisionId'),
                'urgency'                => array('type' => 'SMALLINT(5)', 'unsigned' => true, 'notnull' => true, 'default' => '0', 'after' => 'thisRevisionDate'),
                'subjectCode'            => array('type' => 'INT(10)', 'unsigned' => true, 'notnull' => true, 'default' => '0', 'after' => 'urgency'),
                'headLine'               => array('type' => 'VARCHAR(67)', 'notnull' => true, 'default' => '', 'after' => 'subjectCode'),
                'dataContent'            => array('type' => 'text', 'after' => 'headLine'),
                'is_associated'          => array('type' => 'TINYINT(1)', 'unsigned' => true, 'notnull' => true, 'default' => '0', 'after' => 'dataContent'),
                'media_type'             => array('type' => 'ENUM(\'Text\',\'Graphic\',\'Photo\',\'Audio\',\'Video\',\'ComplexData\')', 'notnull' => true, 'default' => 'Text', 'after' => 'is_associated'),
                'source'                 => array('type' => 'text', 'after' => 'media_type'),
                'properties'             => array('type' => 'text', 'after' => 'source')
            ),
            array(
                'unique'                 => array('fields' => array('publicIdentifier'), 'type' => 'UNIQUE')
            )
        );
    } catch (\Cx\Lib\UpdateException $e) {
        return \Cx\Lib\UpdateUtil::DefaultActionHandler($e);
    }

    return true;
}
