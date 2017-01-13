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

function _localeInstall() {
    global $objUpdate, $_CONFIG;

    if ($objUpdate->_isNewerVersion($_CONFIG['coreCmsVersion'], '5.0.0')) {
        try {
            // structure
            \Cx\Lib\UpdateUtil::table(
                DBPREFIX.'core_locale_backend',
                array(
                    'id'                                         => array('type' => 'INT(11)', 'notnull' => true, 'auto_increment' => true, 'primary' => true),
                    'iso_1'                                      => array('type' => 'CHAR(2)', 'after' => 'id'),
                    'contrexx_core_locale_backend_ibfk_iso_1'    => array('type' => 'FOREIGN', 'after' => 'iso_1')
                ),
                array(
                    'contrexx_core_locale_backend_ibfk_iso_1'    => array('fields' => array('iso_1'))
                ),
                'InnoDB'
            );
            \Cx\Lib\UpdateUtil::table(
                DBPREFIX.'core_locale_language',
                array(
                    'iso_1'      => array('type' => 'CHAR(2)', 'primary' => true),
                    'iso_3'      => array('type' => 'CHAR(3)', 'notnull' => false, 'after' => 'iso_1'),
                    'source'     => array('type' => 'TINYINT(1)', 'after' => 'iso_3')
                ),
                array(),
                'InnoDB'
            );
            \Cx\Lib\UpdateUtil::table(
                DBPREFIX.'core_locale_locale',
                array(
                    'id'                                                     => array('type' => 'INT(11)', 'notnull' => true, 'auto_increment' => true, 'primary' => true),
                    'iso_1'                                                  => array('type' => 'CHAR(2)', 'after' => 'id'),
                    'label'                                                  => array('type' => 'VARCHAR(255)', 'notnull' => false, 'after' => 'iso_1'),
                    'country'                                                => array('type' => 'CHAR(2)', 'notnull' => false, 'after' => 'label'),
                    'fallback'                                               => array('type' => 'INT(11)', 'notnull' => false, 'after' => 'country'),
                    'source_language'                                        => array('type' => 'CHAR(2)', 'after' => 'fallback'),
                    'contrexx_core_locale_locale_ibfk_country'               => array('type' => 'FOREIGN', 'after' => 'source_language'),
                    'contrexx_core_locale_locale_ibfk_fallback'              => array('type' => 'FOREIGN', 'after' => 'contrexx_core_locale_locale_ibfk_country'),
                    'contrexx_core_locale_locale_ibfk_iso_1'                 => array('type' => 'FOREIGN', 'after' => 'contrexx_core_locale_locale_ibfk_fallback'),
                    'contrexx_core_locale_locale_ibfk_source_language'       => array('type' => 'FOREIGN', 'after' => 'contrexx_core_locale_locale_ibfk_iso_1')
                ),
                array(
                    'iso_1'                                                  => array('fields' => array('iso_1','country'), 'type' => 'UNIQUE'),
                    'contrexx_core_locale_locale_ibfk_country'               => array('fields' => array('country')),
                    'contrexx_core_locale_locale_ibfk_fallback'              => array('fields' => array('fallback')),
                    'contrexx_core_locale_locale_ibfk_source_language'       => array('fields' => array('source_language'))
                ),
                'InnoDB'
            );
            // data
            \Cx\Lib\UpdateUtil::sql("
                INSERT IGNORE INTO `".DBPREFIX."core_locale_language` (`iso_1`,`iso_3`,`source`) 
                VALUES ('aa','aar',0),
                       ('ab','abk',0),
                       ('ae','ave',0),
                       ('af','afr',0),
                       ('ak','aka',0),
                       ('am','amh',0),
                       ('an','arg',0),
                       ('ar','ara',0),
                       ('as','asm',0),
                       ('av','ava',0),
                       ('ay','aym',0),
                       ('az','aze',0),
                       ('ba','bak',0),
                       ('be','bel',0),
                       ('bg','bul',0),
                       ('bh','',0),
                       ('bi','bis',0),
                       ('bm','bam',0),
                       ('bn','ben',0),
                       ('bo','bod',0),
                       ('br','bre',0),
                       ('bs','bos',0),
                       ('ca','cat',0),
                       ('ce','che',0),
                       ('ch','cha',0),
                       ('co','cos',0),
                       ('cr','cre',0),
                       ('cs','ces',0),
                       ('cu','chu',0),
                       ('cv','chv',0),
                       ('cy','cym',0),
                       ('da','dan',1),
                       ('de','deu',1),
                       ('dv','div',0),
                       ('dz','dzo',0),
                       ('ee','ewe',0),
                       ('el','ell',0),
                       ('en','eng',1),
                       ('eo','epo',0),
                       ('es','spa',0),
                       ('et','est',0),
                       ('eu','eus',0),
                       ('fa','fas',0),
                       ('ff','ful',0),
                       ('fi','fin',0),
                       ('fj','fij',0),
                       ('fo','fao',0),
                       ('fr','fra',1),
                       ('fy','fry',0),
                       ('ga','gle',0),
                       ('gd','gla',0),
                       ('gl','glg',0),
                       ('gn','grn',0),
                       ('gu','guj',0),
                       ('gv','glv',0),
                       ('ha','hau',0),
                       ('he','heb',0),
                       ('hi','hin',0),
                       ('ho','hmo',0),
                       ('hr','hrv',0),
                       ('ht','hat',0),
                       ('hu','hun',0),
                       ('hy','hye',0),
                       ('hz','her',0),
                       ('ia','ina',0),
                       ('id','ind',0),
                       ('ig','ibo',0),
                       ('ii','iii',0),
                       ('ik','ipk',0),
                       ('io','ido',0),
                       ('is','isl',0),
                       ('it','ita',1),
                       ('iu','iku',0),
                       ('ja','jpn',0),
                       ('jv','jav',0),
                       ('ka','kat',0),
                       ('kg','kon',0),
                       ('ki','kik',0),
                       ('kj','kua',0),
                       ('kk','kaz',0),
                       ('kl','kal',0),
                       ('km','khm',0),
                       ('kn','kan',0),
                       ('ko','kor',0),
                       ('kr','kau',0),
                       ('ks','kas',0),
                       ('ku','kur',0),
                       ('kv','kom',0),
                       ('kw','cor',0),
                       ('ky','kir',0),
                       ('la','lat',0),
                       ('lb','ltz',0),
                       ('lg','lug',0),
                       ('li','lim',0),
                       ('ln','lin',0),
                       ('lo','lao',0),
                       ('lt','lit',0),
                       ('lu','lub',0),
                       ('lv','lav',0),
                       ('mg','mlg',0),
                       ('mh','mah',0),
                       ('mi','mri',0),
                       ('mk','mkd',0),
                       ('ml','mal',0),
                       ('mn','mon',0),
                       ('mr','mar',0),
                       ('ms','msa',0),
                       ('mt','mlt',0),
                       ('my','mya',0),
                       ('na','nau',0),
                       ('nb','nob',0),
                       ('nd','nde',0),
                       ('ne','nep',0),
                       ('ng','ndo',0),
                       ('nl','nld',0),
                       ('nn','nno',0),
                       ('no','nor',0),
                       ('nr','nbl',0),
                       ('nv','nav',0),
                       ('ny','nya',0),
                       ('oc','oci',0),
                       ('oj','oji',0),
                       ('om','orm',0),
                       ('or','ori',0),
                       ('os','oss',0),
                       ('pa','pan',0),
                       ('pi','pli',0),
                       ('pl','pol',0),
                       ('ps','pus',0),
                       ('pt','por',0),
                       ('qu','que',0),
                       ('rm','roh',0),
                       ('rn','run',0),
                       ('ro','ron',0),
                       ('ru','rus',1),
                       ('rw','kin',0),
                       ('sa','san',0),
                       ('sc','srd',0),
                       ('sd','snd',0),
                       ('se','sme',0),
                       ('sg','sag',0),
                       ('si','sin',0),
                       ('sk','slk',0),
                       ('sl','slv',0),
                       ('sm','smo',0),
                       ('sn','sna',0),
                       ('so','som',0),
                       ('sq','sqi',0),
                       ('sr','srp',0),
                       ('ss','ssw',0),
                       ('st','sot',0),
                       ('su','sun',0),
                       ('sv','swe',0),
                       ('sw','swa',0),
                       ('ta','tam',0),
                       ('te','tel',0),
                       ('tg','tgk',0),
                       ('th','tha',0),
                       ('ti','tir',0),
                       ('tk','tuk',0),
                       ('tl','tgl',0),
                       ('tn','tsn',0),
                       ('to','ton',0),
                       ('tr','tur',0),
                       ('ts','tso',0),
                       ('tt','tat',0),
                       ('tw','twi',0),
                       ('ty','tah',0),
                       ('ug','uig',0),
                       ('uk','ukr',0),
                       ('ur','urd',0),
                       ('uz','uzb',0),
                       ('ve','ven',0),
                       ('vi','vie',0),
                       ('vo','vol',0),
                       ('wa','wln',0),
                       ('wo','wol',0),
                       ('xh','xho',0),
                       ('yi','yid',0),
                       ('yo','yor',0),
                       ('za','zha',0),
                       ('zh','zho',0),
                       ('zu','zul',0)
            ");
            if (\Cx\Lib\UpdateUtil::table_exist(DBPREFIX.'languages')) {
                \Cx\Lib\UpdateUtil::sql("
                    INSERT IGNORE INTO `" . DBPREFIX . "core_locale_backend` (`iso_1`) 
                    SELECT 
                        lang 
                    FROM `" . DBPREFIX . "languages` 
                    WHERE backend = 1
                ");
                \Cx\Lib\UpdateUtil::sql("
                    INSERT IGNORE INTO `" . DBPREFIX . "core_locale_locale` (`iso_1`,`label`,`country`,`fallback`,`source_language`) 
                    SELECT 
                        lang, 
                        name, 
                        NULL, 
                        fallback, 
                        lang 
                    FROM `" . DBPREFIX . "languages` 
                    WHERE frontend = 1;
                ");
            }
        } catch (\Cx\Lib\UpdateException $e) {
            return \Cx\Lib\UpdateUtil::DefaultActionHandler($e);
        }
    }
    return true;
}

function dropOldLangTable() {
    try {
        if (\Cx\Lib\UpdateUtil::table_exist(DBPREFIX.'languages')) {
            \Cx\Lib\UpdateUtil::drop_table(DBPREFIX.'languages');
        }
    } catch (\Cx\Lib\UpdateException $e) {
        return \Cx\Lib\UpdateUtil::DefaultActionHandler($e);
    }
    return true;
}