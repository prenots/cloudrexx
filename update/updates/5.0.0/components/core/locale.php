<?php

/**
 * Cloudrexx
 *
 * @link      http://www.cloudrexx.com
 * @copyright Cloudrexx AG 2007-2018
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

function _localeUpdate()
{
    global $objUpdate, $_CONFIG;

    if ($objUpdate->_isNewerVersion($_CONFIG['coreCmsVersion'], '5.0.0')) {
        try {
            \Cx\Lib\UpdateUtil::table(
                DBPREFIX.'core_country_country',
                array(
                    'alpha2'     => array('type' => 'CHAR(2)', 'primary' => true, 'notnull' => true),
                    'alpha3'     => array('type' => 'CHAR(3)', 'notnull' => true, 'default' => '', 'after' => 'alpha2'),
                    'ord'        => array('type' => 'INT(5)', 'unsigned' => true, 'notnull' => true, 'default' => '0', 'after' => 'alpha3')
                ),
                array(
                    'alpha3'     => array('fields' => array('alpha3'), 'type' => 'UNIQUE')
                ),
                'InnoDB'
            );
            \Cx\Lib\UpdateUtil::table(
                DBPREFIX.'core_locale_language',
                array(
                    'iso_1'      => array('type' => 'CHAR(2)', 'primary' => true, 'notnull' => true),
                    'iso_3'      => array('type' => 'CHAR(3)', 'notnull' => false, 'after' => 'iso_1'),
                    'source'     => array('type' => 'TINYINT(1)', 'notnull' => true, 'after' => 'iso_3')
                ),
                array(),
                'InnoDB'
            );
            \Cx\Lib\UpdateUtil::table(
                DBPREFIX.'core_locale_backend',
                array(
                    'id'                                         => array('type' => 'int', 'auto_increment' => true, 'primary' => true, 'notnull' => true),
                    'iso_1'                                      => array('type' => 'CHAR(2)', 'notnull' => true, 'after' => 'id'),
                ),
                array(
                    'iso_1' => array('fields' => array('iso_1')),
                ),
                'InnoDB',
                '',
                array(
                    'iso_1' => array(
                        'table' => DBPREFIX.'core_locale_language',
                        'column'    => 'iso_1',
                        'onDelete'  => 'NO ACTION',
                        'onUpdate'  => 'NO ACTION',
                    ),
                )
            );
            \Cx\Lib\UpdateUtil::table(
                DBPREFIX.'core_locale_locale',
                array(
                    'id'                                                     => array('type' => 'int', 'auto_increment' => true, 'primary' => true, 'notnull' => true),
                    'iso_1'                                                  => array('type' => 'CHAR(2)', 'notnull' => true, 'after' => 'id'),
                    'label'                                                  => array('type' => 'VARCHAR(255)', 'notnull' => false, 'after' => 'iso_1'),
                    'country'                                                => array('type' => 'CHAR(2)', 'notnull' => false, 'after' => 'label'),
                    'fallback'                                               => array('type' => 'int', 'notnull' => false, 'after' => 'country'),
                    'source_language'                                        => array('type' => 'CHAR(2)', 'notnull' => true, 'after' => 'fallback'),
                    'order_no'                                               => array('type' => 'INT(11)', 'notnull' => true, 'after' => 'source_language'),
                ),
                array(
                    'iso_1'                                                  => array('fields' => array('iso_1', 'country'), 'type' => 'UNIQUE'),
                    'country' => array('fields' => array('country')),
                    'fallback' => array('fields' => array('fallback')),
                    'source_language' => array('fields' => array('source_language')),
                ),
                'InnoDB',
                '',
                array(
                    'country' => array(
                        'table' => DBPREFIX.'core_country_country',
                        'column'    => 'alpha2',
                        'onDelete'  => 'NO ACTION',
                        'onUpdate'  => 'NO ACTION',
                    ),
                    'fallback' => array(
                        'table' => DBPREFIX.'core_locale_locale',
                        'column'    => 'id',
                        'onDelete'  => 'NO ACTION',
                        'onUpdate'  => 'NO ACTION',
                    ),
                    'iso_1' => array(
                        'table' => DBPREFIX.'core_locale_language',
                        'column'    => 'iso_1',
                        'onDelete'  => 'NO ACTION',
                        'onUpdate'  => 'NO ACTION',
                    ),
                    'source_language' => array(
                        'table' => DBPREFIX.'core_locale_language',
                        'column'    => 'iso_1',
                        'onDelete'  => 'NO ACTION',
                        'onUpdate'  => 'NO ACTION',
                    ),
                )
            );
            \Cx\Lib\UpdateUtil::sql('ALTER TABLE `'.DBPREFIX.'skins` ENGINE = InnoDB');
            \Cx\Lib\UpdateUtil::table(
                DBPREFIX.'core_view_frontend',
                array(
                    'language'                                   => array('type' => 'int', 'notnull' => true, 'primary' => true),
                    'theme'                                      => array('type' => 'INT(2)', 'unsigned' => true, 'notnull' => true, 'primary' => true, 'after' => 'language'),
                    'channel'                                    => array('type' => 'ENUM(\'default\',\'mobile\',\'print\',\'pdf\',\'app\')', 'notnull' => true, 'primary' => true, 'after' => 'theme'),
                ),
                array(),
                'InnoDB',
                '',
                array(
                    'language' => array(
                        'table' => DBPREFIX.'core_locale_locale',
                        'column'    => 'id',
                        'onDelete'  => 'NO ACTION',
                        'onUpdate'  => 'NO ACTION',
                    ),
                    'theme' => array(
                        'table' => DBPREFIX.'skins',
                        'column'    => 'id',
                        'onDelete'  => 'NO ACTION',
                        'onUpdate'  => 'NO ACTION',
                    ),
                )
            );

            foreach (getLocaleCountryList() as $country) {
                \Cx\Lib\UpdateUtil::sql('INSERT IGNORE INTO `'.DBPREFIX.'core_country_country` (`alpha2`, `alpha3`, `ord`) VALUES (\'' . $country['alpha2'] . '\',\'' . $country['alpha3'] . '\',' . $country['ord'] . ')');
            }

            foreach (getLocaleLanguageList() as $language) {
                \Cx\Lib\UpdateUtil::sql('INSERT IGNORE INTO `'.DBPREFIX.'core_locale_language` (`iso_1`, `iso_3`, `source`) VALUES (\'' . $language['iso_1'] . '\',\'' . $language['iso_3'] . '\',' . $language['source'] . ')');
            }

            # disable foreign_key_checks
            \Cx\Lib\UpdateUtil::sql('SET FOREIGN_KEY_CHECKS=0');

            # set backend locales based on active backend languages
            if (\Cx\Lib\UpdateUtil::table_empty(DBPREFIX.'core_locale_backend')) {
                \Cx\Lib\UpdateUtil::sql('INSERT INTO `'.DBPREFIX.'core_locale_backend` (`id`, `iso_1`) SELECT id, CASE lang WHEN \'dk\' THEN \'da\' ELSE lang END FROM `'.DBPREFIX.'languages` WHERE backend = 1');
            }

            # set frontend locales based on active frontend languages
            if (\Cx\Lib\UpdateUtil::table_empty(DBPREFIX.'core_locale_locale')) {
                \Cx\Lib\UpdateUtil::sql('INSERT INTO `'.DBPREFIX.'core_locale_locale` (`id`, `iso_1`,`label`,`country`,`fallback`,`source_language`) SELECT id, CASE lang WHEN \'dk\' THEN \'da\' ELSE lang END, name, NULL, NULL, CASE lang WHEN \'de\' THEN lang WHEN \'en\' THEN lang WHEN \'fr\' THEN lang WHEN \'it\' THEN lang WHEN \'ru\' THEN lang WHEN \'dk\' THEN \'da\' ELSE \'en\' END FROM `'.DBPREFIX.'languages` WHERE frontend = 1');
                \Cx\Lib\UpdateUtil::sql('UPDATE `'.DBPREFIX.'core_locale_locale` AS locale SET locale.fallback = (SELECT lang.fallback FROM '.DBPREFIX.'languages AS lang WHERE lang.id = locale.id AND lang.fallback != \'0\')');
            }

            # set frontend views for existing locales
            if (\Cx\Lib\UpdateUtil::table_empty(DBPREFIX.'core_view_frontend')) {
                # fix invalid skins
                $result = \Cx\Lib\UpdateUtil::sql('SELECT themesid, print_themes_id, pdf_themes_id, mobile_themes_id, app_themes_id FROM '.DBPREFIX.'languages WHERE frontend=\'1\' AND is_default=\'true\'');
                \Cx\Lib\UpdateUtil::sql('UPDATE '.DBPREFIX.'languages SET themesid = '.$result->fields['themesid'].' WHERE themesid NOT IN (SELECT id FROM '.DBPREFIX.'skins)');
                \Cx\Lib\UpdateUtil::sql('UPDATE '.DBPREFIX.'languages SET print_themes_id = '.$result->fields['print_themes_id'].' WHERE print_themes_id NOT IN (SELECT id FROM '.DBPREFIX.'skins)');
                \Cx\Lib\UpdateUtil::sql('UPDATE '.DBPREFIX.'languages SET pdf_themes_id= '.$result->fields['pdf_themes_id'].' WHERE pdf_themes_id NOT IN (SELECT id FROM '.DBPREFIX.'skins)');
                \Cx\Lib\UpdateUtil::sql('UPDATE '.DBPREFIX.'languages SET mobile_themes_id= '.$result->fields['mobile_themes_id'].' WHERE mobile_themes_id NOT IN (SELECT id FROM '.DBPREFIX.'skins)');
                \Cx\Lib\UpdateUtil::sql('UPDATE '.DBPREFIX.'languages SET app_themes_id= '.$result->fields['app_themes_id'].' WHERE app_themes_id NOT IN (SELECT id FROM '.DBPREFIX.'skins)');

                # set frontend views for existing locales
                \Cx\Lib\UpdateUtil::sql('INSERT INTO `'.DBPREFIX.'core_view_frontend` (`language`, `theme`, `channel`) SELECT l.id, (SELECT `themesid` FROM `'.DBPREFIX.'languages` WHERE CONVERT(CASE `lang` WHEN \'dk\' THEN \'da\' ELSE `lang` END using utf8) = CONVERT(l.iso_1 using utf8)), \'default\' FROM `'.DBPREFIX.'core_locale_locale` AS l');
                \Cx\Lib\UpdateUtil::sql('INSERT INTO `'.DBPREFIX.'core_view_frontend` (`language`, `theme`, `channel`) SELECT l.id, (SELECT `mobile_themes_id` FROM `'.DBPREFIX.'languages` WHERE CONVERT(CASE `lang` WHEN \'dk\' THEN \'da\' ELSE `lang` END using utf8) = CONVERT(l.iso_1 using utf8)), \'mobile\' FROM `'.DBPREFIX.'core_locale_locale` AS l');
                \Cx\Lib\UpdateUtil::sql('INSERT INTO `'.DBPREFIX.'core_view_frontend` (`language`, `theme`, `channel`) SELECT l.id, (SELECT `print_themes_id` FROM `'.DBPREFIX.'languages` WHERE CONVERT(CASE `lang` WHEN \'dk\' THEN \'da\' ELSE `lang` END using utf8) = CONVERT(l.iso_1 using utf8)), \'print\' FROM `'.DBPREFIX.'core_locale_locale` AS l');
                \Cx\Lib\UpdateUtil::sql('INSERT INTO `'.DBPREFIX.'core_view_frontend` (`language`, `theme`, `channel`) SELECT l.id, (SELECT `pdf_themes_id` FROM `'.DBPREFIX.'languages` WHERE CONVERT(CASE `lang` WHEN \'dk\' THEN \'da\' ELSE `lang` END using utf8) = CONVERT(l.iso_1 using utf8)), \'pdf\' FROM `'.DBPREFIX.'core_locale_locale` AS l');
                \Cx\Lib\UpdateUtil::sql('INSERT INTO `'.DBPREFIX.'core_view_frontend` (`language`, `theme`, `channel`) SELECT l.id, (SELECT `app_themes_id` FROM `'.DBPREFIX.'languages` WHERE CONVERT(CASE `lang` WHEN \'dk\' THEN \'da\' ELSE `lang` END using utf8) = CONVERT(l.iso_1 using utf8)), \'app\' FROM `'.DBPREFIX.'core_locale_locale` AS l');
            }

            # reenable foreign_key_checks
            \Cx\Lib\UpdateUtil::sql('SET FOREIGN_KEY_CHECKS=1');
        } catch (\Cx\Lib\UpdateException $e) {
            return \Cx\Lib\UpdateUtil::DefaultActionHandler($e);
        }
    }

    return true;
}

function getLocaleCountryList() {
    return array(
		array('alpha2' => 'AF',	'alpha3' => 'AFG',	'ord' => 0),
		array('alpha2' => 'AL',	'alpha3' => 'ALB',	'ord' => 0),
		array('alpha2' => 'DZ',	'alpha3' => 'DZA',	'ord' => 0),
		array('alpha2' => 'AS',	'alpha3' => 'ASM',	'ord' => 0),
		array('alpha2' => 'AD',	'alpha3' => 'AND',	'ord' => 0),
		array('alpha2' => 'AO',	'alpha3' => 'AGO',	'ord' => 0),
		array('alpha2' => 'AI',	'alpha3' => 'AIA',	'ord' => 0),
		array('alpha2' => 'AQ',	'alpha3' => 'ATA',	'ord' => 0),
		array('alpha2' => 'AG',	'alpha3' => 'ATG',	'ord' => 0),
		array('alpha2' => 'AR',	'alpha3' => 'ARG',	'ord' => 0),
		array('alpha2' => 'AM',	'alpha3' => 'ARM',	'ord' => 0),
		array('alpha2' => 'AW',	'alpha3' => 'ABW',	'ord' => 0),
		array('alpha2' => 'AU',	'alpha3' => 'AUS',	'ord' => 0),
		array('alpha2' => 'AT',	'alpha3' => 'AUT',	'ord' => 0),
		array('alpha2' => 'AZ',	'alpha3' => 'AZE',	'ord' => 0),
		array('alpha2' => 'BS',	'alpha3' => 'BHS',	'ord' => 0),
		array('alpha2' => 'BH',	'alpha3' => 'BHR',	'ord' => 0),
		array('alpha2' => 'BD',	'alpha3' => 'BGD',	'ord' => 0),
		array('alpha2' => 'BB',	'alpha3' => 'BRB',	'ord' => 0),
		array('alpha2' => 'BY',	'alpha3' => 'BLR',	'ord' => 0),
		array('alpha2' => 'BE',	'alpha3' => 'BEL',	'ord' => 0),
		array('alpha2' => 'BZ',	'alpha3' => 'BLZ',	'ord' => 0),
		array('alpha2' => 'BJ',	'alpha3' => 'BEN',	'ord' => 0),
		array('alpha2' => 'BM',	'alpha3' => 'BMU',	'ord' => 0),
		array('alpha2' => 'BT',	'alpha3' => 'BTN',	'ord' => 0),
		array('alpha2' => 'BO',	'alpha3' => 'BOL',	'ord' => 0),
		array('alpha2' => 'BA',	'alpha3' => 'BIH',	'ord' => 0),
		array('alpha2' => 'BW',	'alpha3' => 'BWA',	'ord' => 0),
		array('alpha2' => 'BV',	'alpha3' => 'BVT',	'ord' => 0),
		array('alpha2' => 'BR',	'alpha3' => 'BRA',	'ord' => 0),
		array('alpha2' => 'IO',	'alpha3' => 'IOT',	'ord' => 0),
		array('alpha2' => 'BN',	'alpha3' => 'BRN',	'ord' => 0),
		array('alpha2' => 'BG',	'alpha3' => 'BGR',	'ord' => 0),
		array('alpha2' => 'BF',	'alpha3' => 'BFA',	'ord' => 0),
		array('alpha2' => 'BI',	'alpha3' => 'BDI',	'ord' => 0),
		array('alpha2' => 'KH',	'alpha3' => 'KHM',	'ord' => 0),
		array('alpha2' => 'CM',	'alpha3' => 'CMR',	'ord' => 0),
		array('alpha2' => 'CA',	'alpha3' => 'CAN',	'ord' => 0),
		array('alpha2' => 'CV',	'alpha3' => 'CPV',	'ord' => 0),
		array('alpha2' => 'KY',	'alpha3' => 'CYM',	'ord' => 0),
		array('alpha2' => 'CF',	'alpha3' => 'CAF',	'ord' => 0),
		array('alpha2' => 'TD',	'alpha3' => 'TCD',	'ord' => 0),
		array('alpha2' => 'CL',	'alpha3' => 'CHL',	'ord' => 0),
		array('alpha2' => 'CN',	'alpha3' => 'CHN',	'ord' => 0),
		array('alpha2' => 'CX',	'alpha3' => 'CXR',	'ord' => 0),
		array('alpha2' => 'CC',	'alpha3' => 'CCK',	'ord' => 0),
		array('alpha2' => 'CO',	'alpha3' => 'COL',	'ord' => 0),
		array('alpha2' => 'KM',	'alpha3' => 'COM',	'ord' => 0),
		array('alpha2' => 'CG',	'alpha3' => 'COG',	'ord' => 0),
		array('alpha2' => 'CK',	'alpha3' => 'COK',	'ord' => 0),
		array('alpha2' => 'CR',	'alpha3' => 'CRI',	'ord' => 0),
		array('alpha2' => 'CI',	'alpha3' => 'CIV',	'ord' => 0),
		array('alpha2' => 'HR',	'alpha3' => 'HRV',	'ord' => 0),
		array('alpha2' => 'CU',	'alpha3' => 'CUB',	'ord' => 0),
		array('alpha2' => 'CY',	'alpha3' => 'CYP',	'ord' => 0),
		array('alpha2' => 'CZ',	'alpha3' => 'CZE',	'ord' => 0),
		array('alpha2' => 'DK',	'alpha3' => 'DNK',	'ord' => 0),
		array('alpha2' => 'DJ',	'alpha3' => 'DJI',	'ord' => 0),
		array('alpha2' => 'DM',	'alpha3' => 'DMA',	'ord' => 0),
		array('alpha2' => 'DO',	'alpha3' => 'DOM',	'ord' => 0),
		array('alpha2' => 'TP',	'alpha3' => 'TMP',	'ord' => 0),
		array('alpha2' => 'EC',	'alpha3' => 'ECU',	'ord' => 0),
		array('alpha2' => 'EG',	'alpha3' => 'EGY',	'ord' => 0),
		array('alpha2' => 'SV',	'alpha3' => 'SLV',	'ord' => 0),
		array('alpha2' => 'GQ',	'alpha3' => 'GNQ',	'ord' => 0),
		array('alpha2' => 'ER',	'alpha3' => 'ERI',	'ord' => 0),
		array('alpha2' => 'EE',	'alpha3' => 'EST',	'ord' => 0),
		array('alpha2' => 'ET',	'alpha3' => 'ETH',	'ord' => 0),
		array('alpha2' => 'FK',	'alpha3' => 'FLK',	'ord' => 0),
		array('alpha2' => 'FO',	'alpha3' => 'FRO',	'ord' => 0),
		array('alpha2' => 'FJ',	'alpha3' => 'FJI',	'ord' => 0),
		array('alpha2' => 'FI',	'alpha3' => 'FIN',	'ord' => 0),
		array('alpha2' => 'FR',	'alpha3' => 'FRA',	'ord' => 0),
		array('alpha2' => 'FX',	'alpha3' => 'FXX',	'ord' => 0),
		array('alpha2' => 'GF',	'alpha3' => 'GUF',	'ord' => 0),
		array('alpha2' => 'PF',	'alpha3' => 'PYF',	'ord' => 0),
		array('alpha2' => 'TF',	'alpha3' => 'ATF',	'ord' => 0),
		array('alpha2' => 'GA',	'alpha3' => 'GAB',	'ord' => 0),
		array('alpha2' => 'GM',	'alpha3' => 'GMB',	'ord' => 0),
		array('alpha2' => 'GE',	'alpha3' => 'GEO',	'ord' => 0),
		array('alpha2' => 'DE',	'alpha3' => 'DEU',	'ord' => 0),
		array('alpha2' => 'GH',	'alpha3' => 'GHA',	'ord' => 0),
		array('alpha2' => 'GI',	'alpha3' => 'GIB',	'ord' => 0),
		array('alpha2' => 'GR',	'alpha3' => 'GRC',	'ord' => 0),
		array('alpha2' => 'GL',	'alpha3' => 'GRL',	'ord' => 0),
		array('alpha2' => 'GD',	'alpha3' => 'GRD',	'ord' => 0),
		array('alpha2' => 'GP',	'alpha3' => 'GLP',	'ord' => 0),
		array('alpha2' => 'GU',	'alpha3' => 'GUM',	'ord' => 0),
		array('alpha2' => 'GT',	'alpha3' => 'GTM',	'ord' => 0),
		array('alpha2' => 'GN',	'alpha3' => 'GIN',	'ord' => 0),
		array('alpha2' => 'GW',	'alpha3' => 'GNB',	'ord' => 0),
		array('alpha2' => 'GY',	'alpha3' => 'GUY',	'ord' => 0),
		array('alpha2' => 'HT',	'alpha3' => 'HTI',	'ord' => 0),
		array('alpha2' => 'HM',	'alpha3' => 'HMD',	'ord' => 0),
		array('alpha2' => 'HN',	'alpha3' => 'HND',	'ord' => 0),
		array('alpha2' => 'HK',	'alpha3' => 'HKG',	'ord' => 0),
		array('alpha2' => 'HU',	'alpha3' => 'HUN',	'ord' => 0),
		array('alpha2' => 'IS',	'alpha3' => 'ISL',	'ord' => 0),
		array('alpha2' => 'IN',	'alpha3' => 'IND',	'ord' => 0),
		array('alpha2' => 'ID',	'alpha3' => 'IDN',	'ord' => 0),
		array('alpha2' => 'IR',	'alpha3' => 'IRN',	'ord' => 0),
		array('alpha2' => 'IQ',	'alpha3' => 'IRQ',	'ord' => 0),
		array('alpha2' => 'IE',	'alpha3' => 'IRL',	'ord' => 0),
		array('alpha2' => 'IL',	'alpha3' => 'ISR',	'ord' => 0),
		array('alpha2' => 'IT',	'alpha3' => 'ITA',	'ord' => 0),
		array('alpha2' => 'JM',	'alpha3' => 'JAM',	'ord' => 0),
		array('alpha2' => 'JP',	'alpha3' => 'JPN',	'ord' => 0),
		array('alpha2' => 'JO',	'alpha3' => 'JOR',	'ord' => 0),
		array('alpha2' => 'KZ',	'alpha3' => 'KAZ',	'ord' => 0),
		array('alpha2' => 'KE',	'alpha3' => 'KEN',	'ord' => 0),
		array('alpha2' => 'KI',	'alpha3' => 'KIR',	'ord' => 0),
		array('alpha2' => 'KP',	'alpha3' => 'PRK',	'ord' => 0),
		array('alpha2' => 'KR',	'alpha3' => 'KOR',	'ord' => 0),
		array('alpha2' => 'KW',	'alpha3' => 'KWT',	'ord' => 0),
		array('alpha2' => 'KG',	'alpha3' => 'KGZ',	'ord' => 0),
		array('alpha2' => 'LA',	'alpha3' => 'LAO',	'ord' => 0),
		array('alpha2' => 'LV',	'alpha3' => 'LVA',	'ord' => 0),
		array('alpha2' => 'LB',	'alpha3' => 'LBN',	'ord' => 0),
		array('alpha2' => 'LS',	'alpha3' => 'LSO',	'ord' => 0),
		array('alpha2' => 'LR',	'alpha3' => 'LBR',	'ord' => 0),
		array('alpha2' => 'LY',	'alpha3' => 'LBY',	'ord' => 0),
		array('alpha2' => 'LI',	'alpha3' => 'LIE',	'ord' => 0),
		array('alpha2' => 'LT',	'alpha3' => 'LTU',	'ord' => 0),
		array('alpha2' => 'LU',	'alpha3' => 'LUX',	'ord' => 0),
		array('alpha2' => 'MO',	'alpha3' => 'MAC',	'ord' => 0),
		array('alpha2' => 'MK',	'alpha3' => 'MKD',	'ord' => 0),
		array('alpha2' => 'MG',	'alpha3' => 'MDG',	'ord' => 0),
		array('alpha2' => 'MW',	'alpha3' => 'MWI',	'ord' => 0),
		array('alpha2' => 'MY',	'alpha3' => 'MYS',	'ord' => 0),
		array('alpha2' => 'MV',	'alpha3' => 'MDV',	'ord' => 0),
		array('alpha2' => 'ML',	'alpha3' => 'MLI',	'ord' => 0),
		array('alpha2' => 'MT',	'alpha3' => 'MLT',	'ord' => 0),
		array('alpha2' => 'MH',	'alpha3' => 'MHL',	'ord' => 0),
		array('alpha2' => 'MQ',	'alpha3' => 'MTQ',	'ord' => 0),
		array('alpha2' => 'MR',	'alpha3' => 'MRT',	'ord' => 0),
		array('alpha2' => 'MU',	'alpha3' => 'MUS',	'ord' => 0),
		array('alpha2' => 'YT',	'alpha3' => 'MYT',	'ord' => 0),
		array('alpha2' => 'MX',	'alpha3' => 'MEX',	'ord' => 0),
		array('alpha2' => 'FM',	'alpha3' => 'FSM',	'ord' => 0),
		array('alpha2' => 'MD',	'alpha3' => 'MDA',	'ord' => 0),
		array('alpha2' => 'MC',	'alpha3' => 'MCO',	'ord' => 0),
		array('alpha2' => 'MN',	'alpha3' => 'MNG',	'ord' => 0),
		array('alpha2' => 'MS',	'alpha3' => 'MSR',	'ord' => 0),
		array('alpha2' => 'MA',	'alpha3' => 'MAR',	'ord' => 0),
		array('alpha2' => 'MZ',	'alpha3' => 'MOZ',	'ord' => 0),
		array('alpha2' => 'MM',	'alpha3' => 'MMR',	'ord' => 0),
		array('alpha2' => 'NA',	'alpha3' => 'NAM',	'ord' => 0),
		array('alpha2' => 'NR',	'alpha3' => 'NRU',	'ord' => 0),
		array('alpha2' => 'NP',	'alpha3' => 'NPL',	'ord' => 0),
		array('alpha2' => 'NL',	'alpha3' => 'NLD',	'ord' => 0),
		array('alpha2' => 'AN',	'alpha3' => 'ANT',	'ord' => 0),
		array('alpha2' => 'NC',	'alpha3' => 'NCL',	'ord' => 0),
		array('alpha2' => 'NZ',	'alpha3' => 'NZL',	'ord' => 0),
		array('alpha2' => 'NI',	'alpha3' => 'NIC',	'ord' => 0),
		array('alpha2' => 'NE',	'alpha3' => 'NER',	'ord' => 0),
		array('alpha2' => 'NG',	'alpha3' => 'NGA',	'ord' => 0),
		array('alpha2' => 'NU',	'alpha3' => 'NIU',	'ord' => 0),
		array('alpha2' => 'NF',	'alpha3' => 'NFK',	'ord' => 0),
		array('alpha2' => 'MP',	'alpha3' => 'MNP',	'ord' => 0),
		array('alpha2' => 'NO',	'alpha3' => 'NOR',	'ord' => 0),
		array('alpha2' => 'OM',	'alpha3' => 'OMN',	'ord' => 0),
		array('alpha2' => 'PK',	'alpha3' => 'PAK',	'ord' => 0),
		array('alpha2' => 'PW',	'alpha3' => 'PLW',	'ord' => 0),
		array('alpha2' => 'PA',	'alpha3' => 'PAN',	'ord' => 0),
		array('alpha2' => 'PG',	'alpha3' => 'PNG',	'ord' => 0),
		array('alpha2' => 'PY',	'alpha3' => 'PRY',	'ord' => 0),
		array('alpha2' => 'PE',	'alpha3' => 'PER',	'ord' => 0),
		array('alpha2' => 'PH',	'alpha3' => 'PHL',	'ord' => 0),
		array('alpha2' => 'PN',	'alpha3' => 'PCN',	'ord' => 0),
		array('alpha2' => 'PL',	'alpha3' => 'POL',	'ord' => 0),
		array('alpha2' => 'PT',	'alpha3' => 'PRT',	'ord' => 0),
		array('alpha2' => 'PR',	'alpha3' => 'PRI',	'ord' => 0),
		array('alpha2' => 'QA',	'alpha3' => 'QAT',	'ord' => 0),
		array('alpha2' => 'RE',	'alpha3' => 'REU',	'ord' => 0),
		array('alpha2' => 'RO',	'alpha3' => 'ROM',	'ord' => 0),
		array('alpha2' => 'RU',	'alpha3' => 'RUS',	'ord' => 0),
		array('alpha2' => 'RW',	'alpha3' => 'RWA',	'ord' => 0),
		array('alpha2' => 'KN',	'alpha3' => 'KNA',	'ord' => 0),
		array('alpha2' => 'LC',	'alpha3' => 'LCA',	'ord' => 0),
		array('alpha2' => 'VC',	'alpha3' => 'VCT',	'ord' => 0),
		array('alpha2' => 'WS',	'alpha3' => 'WSM',	'ord' => 0),
		array('alpha2' => 'SM',	'alpha3' => 'SMR',	'ord' => 0),
		array('alpha2' => 'ST',	'alpha3' => 'STP',	'ord' => 0),
		array('alpha2' => 'SA',	'alpha3' => 'SAU',	'ord' => 0),
		array('alpha2' => 'SN',	'alpha3' => 'SEN',	'ord' => 0),
		array('alpha2' => 'SC',	'alpha3' => 'SYC',	'ord' => 0),
		array('alpha2' => 'SL',	'alpha3' => 'SLE',	'ord' => 0),
		array('alpha2' => 'SG',	'alpha3' => 'SGP',	'ord' => 0),
		array('alpha2' => 'SK',	'alpha3' => 'SVK',	'ord' => 0),
		array('alpha2' => 'SI',	'alpha3' => 'SVN',	'ord' => 0),
		array('alpha2' => 'SB',	'alpha3' => 'SLB',	'ord' => 0),
		array('alpha2' => 'SO',	'alpha3' => 'SOM',	'ord' => 0),
		array('alpha2' => 'ZA',	'alpha3' => 'ZAF',	'ord' => 0),
		array('alpha2' => 'GS',	'alpha3' => 'SGS',	'ord' => 0),
		array('alpha2' => 'ES',	'alpha3' => 'ESP',	'ord' => 0),
		array('alpha2' => 'LK',	'alpha3' => 'LKA',	'ord' => 0),
		array('alpha2' => 'SH',	'alpha3' => 'SHN',	'ord' => 0),
		array('alpha2' => 'PM',	'alpha3' => 'SPM',	'ord' => 0),
		array('alpha2' => 'SD',	'alpha3' => 'SDN',	'ord' => 0),
		array('alpha2' => 'SR',	'alpha3' => 'SUR',	'ord' => 0),
		array('alpha2' => 'SJ',	'alpha3' => 'SJM',	'ord' => 0),
		array('alpha2' => 'SZ',	'alpha3' => 'SWZ',	'ord' => 0),
		array('alpha2' => 'SE',	'alpha3' => 'SWE',	'ord' => 0),
		array('alpha2' => 'CH',	'alpha3' => 'CHE',	'ord' => 0),
		array('alpha2' => 'SY',	'alpha3' => 'SYR',	'ord' => 0),
		array('alpha2' => 'TW',	'alpha3' => 'TWN',	'ord' => 0),
		array('alpha2' => 'TJ',	'alpha3' => 'TJK',	'ord' => 0),
		array('alpha2' => 'TZ',	'alpha3' => 'TZA',	'ord' => 0),
		array('alpha2' => 'TH',	'alpha3' => 'THA',	'ord' => 0),
		array('alpha2' => 'TG',	'alpha3' => 'TGO',	'ord' => 0),
		array('alpha2' => 'TK',	'alpha3' => 'TKL',	'ord' => 0),
		array('alpha2' => 'TO',	'alpha3' => 'TON',	'ord' => 0),
		array('alpha2' => 'TT',	'alpha3' => 'TTO',	'ord' => 0),
		array('alpha2' => 'TN',	'alpha3' => 'TUN',	'ord' => 0),
		array('alpha2' => 'TR',	'alpha3' => 'TUR',	'ord' => 0),
		array('alpha2' => 'TM',	'alpha3' => 'TKM',	'ord' => 0),
		array('alpha2' => 'TC',	'alpha3' => 'TCA',	'ord' => 0),
		array('alpha2' => 'TV',	'alpha3' => 'TUV',	'ord' => 0),
		array('alpha2' => 'UG',	'alpha3' => 'UGA',	'ord' => 0),
		array('alpha2' => 'UA',	'alpha3' => 'UKR',	'ord' => 0),
		array('alpha2' => 'AE',	'alpha3' => 'ARE',	'ord' => 0),
		array('alpha2' => 'GB',	'alpha3' => 'GBR',	'ord' => 0),
		array('alpha2' => 'US',	'alpha3' => 'USA',	'ord' => 0),
		array('alpha2' => 'UM',	'alpha3' => 'UMI',	'ord' => 0),
		array('alpha2' => 'UY',	'alpha3' => 'URY',	'ord' => 0),
		array('alpha2' => 'UZ',	'alpha3' => 'UZB',	'ord' => 0),
		array('alpha2' => 'VU',	'alpha3' => 'VUT',	'ord' => 0),
		array('alpha2' => 'VA',	'alpha3' => 'VAT',	'ord' => 0),
		array('alpha2' => 'VE',	'alpha3' => 'VEN',	'ord' => 0),
		array('alpha2' => 'VN',	'alpha3' => 'VNM',	'ord' => 0),
		array('alpha2' => 'VG',	'alpha3' => 'VGB',	'ord' => 0),
		array('alpha2' => 'VI',	'alpha3' => 'VIR',	'ord' => 0),
		array('alpha2' => 'WF',	'alpha3' => 'WLF',	'ord' => 0),
		array('alpha2' => 'EH',	'alpha3' => 'ESH',	'ord' => 0),
		array('alpha2' => 'YE',	'alpha3' => 'YEM',	'ord' => 0),
		array('alpha2' => 'YU',	'alpha3' => 'YUG',	'ord' => 0),
		array('alpha2' => 'ZR',	'alpha3' => 'ZAR',	'ord' => 0),
		array('alpha2' => 'ZM',	'alpha3' => 'ZMB',	'ord' => 0),
		array('alpha2' => 'ZW',	'alpha3' => 'ZWE',	'ord' => 0),
    );
}

function getLocaleLanguageList() {
    return array(
		array('iso_1' => 'aa',	'iso_3' => 'aar',	'source' => 0),
		array('iso_1' => 'ab',	'iso_3' => 'abk',	'source' => 0),
		array('iso_1' => 'ae',	'iso_3' => 'ave',	'source' => 0),
		array('iso_1' => 'af',	'iso_3' => 'afr',	'source' => 0),
		array('iso_1' => 'ak',	'iso_3' => 'aka',	'source' => 0),
		array('iso_1' => 'am',	'iso_3' => 'amh',	'source' => 0),
		array('iso_1' => 'an',	'iso_3' => 'arg',	'source' => 0),
		array('iso_1' => 'ar',	'iso_3' => 'ara',	'source' => 0),
		array('iso_1' => 'as',	'iso_3' => 'asm',	'source' => 0),
		array('iso_1' => 'av',	'iso_3' => 'ava',	'source' => 0),
		array('iso_1' => 'ay',	'iso_3' => 'aym',	'source' => 0),
		array('iso_1' => 'az',	'iso_3' => 'aze',	'source' => 0),
		array('iso_1' => 'ba',	'iso_3' => 'bak',	'source' => 0),
		array('iso_1' => 'be',	'iso_3' => 'bel',	'source' => 0),
		array('iso_1' => 'bg',	'iso_3' => 'bul',	'source' => 0),
		array('iso_1' => 'bh',	'iso_3' => '',	'source' => 0),
		array('iso_1' => 'bi',	'iso_3' => 'bis',	'source' => 0),
		array('iso_1' => 'bm',	'iso_3' => 'bam',	'source' => 0),
		array('iso_1' => 'bn',	'iso_3' => 'ben',	'source' => 0),
		array('iso_1' => 'bo',	'iso_3' => 'bod',	'source' => 0),
		array('iso_1' => 'br',	'iso_3' => 'bre',	'source' => 0),
		array('iso_1' => 'bs',	'iso_3' => 'bos',	'source' => 0),
		array('iso_1' => 'ca',	'iso_3' => 'cat',	'source' => 0),
		array('iso_1' => 'ce',	'iso_3' => 'che',	'source' => 0),
		array('iso_1' => 'ch',	'iso_3' => 'cha',	'source' => 0),
		array('iso_1' => 'co',	'iso_3' => 'cos',	'source' => 0),
		array('iso_1' => 'cr',	'iso_3' => 'cre',	'source' => 0),
		array('iso_1' => 'cs',	'iso_3' => 'ces',	'source' => 0),
		array('iso_1' => 'cu',	'iso_3' => 'chu',	'source' => 0),
		array('iso_1' => 'cv',	'iso_3' => 'chv',	'source' => 0),
		array('iso_1' => 'cy',	'iso_3' => 'cym',	'source' => 0),
		array('iso_1' => 'da',	'iso_3' => 'dan',	'source' => 1),
		array('iso_1' => 'de',	'iso_3' => 'deu',	'source' => 1),
		array('iso_1' => 'dv',	'iso_3' => 'div',	'source' => 0),
		array('iso_1' => 'dz',	'iso_3' => 'dzo',	'source' => 0),
		array('iso_1' => 'ee',	'iso_3' => 'ewe',	'source' => 0),
		array('iso_1' => 'el',	'iso_3' => 'ell',	'source' => 0),
		array('iso_1' => 'en',	'iso_3' => 'eng',	'source' => 1),
		array('iso_1' => 'eo',	'iso_3' => 'epo',	'source' => 0),
		array('iso_1' => 'es',	'iso_3' => 'spa',	'source' => 0),
		array('iso_1' => 'et',	'iso_3' => 'est',	'source' => 0),
		array('iso_1' => 'eu',	'iso_3' => 'eus',	'source' => 0),
		array('iso_1' => 'fa',	'iso_3' => 'fas',	'source' => 0),
		array('iso_1' => 'ff',	'iso_3' => 'ful',	'source' => 0),
		array('iso_1' => 'fi',	'iso_3' => 'fin',	'source' => 0),
		array('iso_1' => 'fj',	'iso_3' => 'fij',	'source' => 0),
		array('iso_1' => 'fo',	'iso_3' => 'fao',	'source' => 0),
		array('iso_1' => 'fr',	'iso_3' => 'fra',	'source' => 1),
		array('iso_1' => 'fy',	'iso_3' => 'fry',	'source' => 0),
		array('iso_1' => 'ga',	'iso_3' => 'gle',	'source' => 0),
		array('iso_1' => 'gd',	'iso_3' => 'gla',	'source' => 0),
		array('iso_1' => 'gl',	'iso_3' => 'glg',	'source' => 0),
		array('iso_1' => 'gn',	'iso_3' => 'grn',	'source' => 0),
		array('iso_1' => 'gu',	'iso_3' => 'guj',	'source' => 0),
		array('iso_1' => 'gv',	'iso_3' => 'glv',	'source' => 0),
		array('iso_1' => 'ha',	'iso_3' => 'hau',	'source' => 0),
		array('iso_1' => 'he',	'iso_3' => 'heb',	'source' => 0),
		array('iso_1' => 'hi',	'iso_3' => 'hin',	'source' => 0),
		array('iso_1' => 'ho',	'iso_3' => 'hmo',	'source' => 0),
		array('iso_1' => 'hr',	'iso_3' => 'hrv',	'source' => 0),
		array('iso_1' => 'ht',	'iso_3' => 'hat',	'source' => 0),
		array('iso_1' => 'hu',	'iso_3' => 'hun',	'source' => 0),
		array('iso_1' => 'hy',	'iso_3' => 'hye',	'source' => 0),
		array('iso_1' => 'hz',	'iso_3' => 'her',	'source' => 0),
		array('iso_1' => 'ia',	'iso_3' => 'ina',	'source' => 0),
		array('iso_1' => 'id',	'iso_3' => 'ind',	'source' => 0),
		array('iso_1' => 'ig',	'iso_3' => 'ibo',	'source' => 0),
		array('iso_1' => 'ii',	'iso_3' => 'iii',	'source' => 0),
		array('iso_1' => 'ik',	'iso_3' => 'ipk',	'source' => 0),
		array('iso_1' => 'io',	'iso_3' => 'ido',	'source' => 0),
		array('iso_1' => 'is',	'iso_3' => 'isl',	'source' => 0),
		array('iso_1' => 'it',	'iso_3' => 'ita',	'source' => 1),
		array('iso_1' => 'iu',	'iso_3' => 'iku',	'source' => 0),
		array('iso_1' => 'ja',	'iso_3' => 'jpn',	'source' => 0),
		array('iso_1' => 'jv',	'iso_3' => 'jav',	'source' => 0),
		array('iso_1' => 'ka',	'iso_3' => 'kat',	'source' => 0),
		array('iso_1' => 'kg',	'iso_3' => 'kon',	'source' => 0),
		array('iso_1' => 'ki',	'iso_3' => 'kik',	'source' => 0),
		array('iso_1' => 'kj',	'iso_3' => 'kua',	'source' => 0),
		array('iso_1' => 'kk',	'iso_3' => 'kaz',	'source' => 0),
		array('iso_1' => 'kl',	'iso_3' => 'kal',	'source' => 0),
		array('iso_1' => 'km',	'iso_3' => 'khm',	'source' => 0),
		array('iso_1' => 'kn',	'iso_3' => 'kan',	'source' => 0),
		array('iso_1' => 'ko',	'iso_3' => 'kor',	'source' => 0),
		array('iso_1' => 'kr',	'iso_3' => 'kau',	'source' => 0),
		array('iso_1' => 'ks',	'iso_3' => 'kas',	'source' => 0),
		array('iso_1' => 'ku',	'iso_3' => 'kur',	'source' => 0),
		array('iso_1' => 'kv',	'iso_3' => 'kom',	'source' => 0),
		array('iso_1' => 'kw',	'iso_3' => 'cor',	'source' => 0),
		array('iso_1' => 'ky',	'iso_3' => 'kir',	'source' => 0),
		array('iso_1' => 'la',	'iso_3' => 'lat',	'source' => 0),
		array('iso_1' => 'lb',	'iso_3' => 'ltz',	'source' => 0),
		array('iso_1' => 'lg',	'iso_3' => 'lug',	'source' => 0),
		array('iso_1' => 'li',	'iso_3' => 'lim',	'source' => 0),
		array('iso_1' => 'ln',	'iso_3' => 'lin',	'source' => 0),
		array('iso_1' => 'lo',	'iso_3' => 'lao',	'source' => 0),
		array('iso_1' => 'lt',	'iso_3' => 'lit',	'source' => 0),
		array('iso_1' => 'lu',	'iso_3' => 'lub',	'source' => 0),
		array('iso_1' => 'lv',	'iso_3' => 'lav',	'source' => 0),
		array('iso_1' => 'mg',	'iso_3' => 'mlg',	'source' => 0),
		array('iso_1' => 'mh',	'iso_3' => 'mah',	'source' => 0),
		array('iso_1' => 'mi',	'iso_3' => 'mri',	'source' => 0),
		array('iso_1' => 'mk',	'iso_3' => 'mkd',	'source' => 0),
		array('iso_1' => 'ml',	'iso_3' => 'mal',	'source' => 0),
		array('iso_1' => 'mn',	'iso_3' => 'mon',	'source' => 0),
		array('iso_1' => 'mr',	'iso_3' => 'mar',	'source' => 0),
		array('iso_1' => 'ms',	'iso_3' => 'msa',	'source' => 0),
		array('iso_1' => 'mt',	'iso_3' => 'mlt',	'source' => 0),
		array('iso_1' => 'my',	'iso_3' => 'mya',	'source' => 0),
		array('iso_1' => 'na',	'iso_3' => 'nau',	'source' => 0),
		array('iso_1' => 'nb',	'iso_3' => 'nob',	'source' => 0),
		array('iso_1' => 'nd',	'iso_3' => 'nde',	'source' => 0),
		array('iso_1' => 'ne',	'iso_3' => 'nep',	'source' => 0),
		array('iso_1' => 'ng',	'iso_3' => 'ndo',	'source' => 0),
		array('iso_1' => 'nl',	'iso_3' => 'nld',	'source' => 0),
		array('iso_1' => 'nn',	'iso_3' => 'nno',	'source' => 0),
		array('iso_1' => 'no',	'iso_3' => 'nor',	'source' => 0),
		array('iso_1' => 'nr',	'iso_3' => 'nbl',	'source' => 0),
		array('iso_1' => 'nv',	'iso_3' => 'nav',	'source' => 0),
		array('iso_1' => 'ny',	'iso_3' => 'nya',	'source' => 0),
		array('iso_1' => 'oc',	'iso_3' => 'oci',	'source' => 0),
		array('iso_1' => 'oj',	'iso_3' => 'oji',	'source' => 0),
		array('iso_1' => 'om',	'iso_3' => 'orm',	'source' => 0),
		array('iso_1' => 'or',	'iso_3' => 'ori',	'source' => 0),
		array('iso_1' => 'os',	'iso_3' => 'oss',	'source' => 0),
		array('iso_1' => 'pa',	'iso_3' => 'pan',	'source' => 0),
		array('iso_1' => 'pi',	'iso_3' => 'pli',	'source' => 0),
		array('iso_1' => 'pl',	'iso_3' => 'pol',	'source' => 0),
		array('iso_1' => 'ps',	'iso_3' => 'pus',	'source' => 0),
		array('iso_1' => 'pt',	'iso_3' => 'por',	'source' => 0),
		array('iso_1' => 'qu',	'iso_3' => 'que',	'source' => 0),
		array('iso_1' => 'rm',	'iso_3' => 'roh',	'source' => 0),
		array('iso_1' => 'rn',	'iso_3' => 'run',	'source' => 0),
		array('iso_1' => 'ro',	'iso_3' => 'ron',	'source' => 0),
		array('iso_1' => 'ru',	'iso_3' => 'rus',	'source' => 1),
		array('iso_1' => 'rw',	'iso_3' => 'kin',	'source' => 0),
		array('iso_1' => 'sa',	'iso_3' => 'san',	'source' => 0),
		array('iso_1' => 'sc',	'iso_3' => 'srd',	'source' => 0),
		array('iso_1' => 'sd',	'iso_3' => 'snd',	'source' => 0),
		array('iso_1' => 'se',	'iso_3' => 'sme',	'source' => 0),
		array('iso_1' => 'sg',	'iso_3' => 'sag',	'source' => 0),
		array('iso_1' => 'si',	'iso_3' => 'sin',	'source' => 0),
		array('iso_1' => 'sk',	'iso_3' => 'slk',	'source' => 0),
		array('iso_1' => 'sl',	'iso_3' => 'slv',	'source' => 0),
		array('iso_1' => 'sm',	'iso_3' => 'smo',	'source' => 0),
		array('iso_1' => 'sn',	'iso_3' => 'sna',	'source' => 0),
		array('iso_1' => 'so',	'iso_3' => 'som',	'source' => 0),
		array('iso_1' => 'sq',	'iso_3' => 'sqi',	'source' => 0),
		array('iso_1' => 'sr',	'iso_3' => 'srp',	'source' => 0),
		array('iso_1' => 'ss',	'iso_3' => 'ssw',	'source' => 0),
		array('iso_1' => 'st',	'iso_3' => 'sot',	'source' => 0),
		array('iso_1' => 'su',	'iso_3' => 'sun',	'source' => 0),
		array('iso_1' => 'sv',	'iso_3' => 'swe',	'source' => 0),
		array('iso_1' => 'sw',	'iso_3' => 'swa',	'source' => 0),
		array('iso_1' => 'ta',	'iso_3' => 'tam',	'source' => 0),
		array('iso_1' => 'te',	'iso_3' => 'tel',	'source' => 0),
		array('iso_1' => 'tg',	'iso_3' => 'tgk',	'source' => 0),
		array('iso_1' => 'th',	'iso_3' => 'tha',	'source' => 0),
		array('iso_1' => 'ti',	'iso_3' => 'tir',	'source' => 0),
		array('iso_1' => 'tk',	'iso_3' => 'tuk',	'source' => 0),
		array('iso_1' => 'tl',	'iso_3' => 'tgl',	'source' => 0),
		array('iso_1' => 'tn',	'iso_3' => 'tsn',	'source' => 0),
		array('iso_1' => 'to',	'iso_3' => 'ton',	'source' => 0),
		array('iso_1' => 'tr',	'iso_3' => 'tur',	'source' => 0),
		array('iso_1' => 'ts',	'iso_3' => 'tso',	'source' => 0),
		array('iso_1' => 'tt',	'iso_3' => 'tat',	'source' => 0),
		array('iso_1' => 'tw',	'iso_3' => 'twi',	'source' => 0),
		array('iso_1' => 'ty',	'iso_3' => 'tah',	'source' => 0),
		array('iso_1' => 'ug',	'iso_3' => 'uig',	'source' => 0),
		array('iso_1' => 'uk',	'iso_3' => 'ukr',	'source' => 0),
		array('iso_1' => 'ur',	'iso_3' => 'urd',	'source' => 0),
		array('iso_1' => 'uz',	'iso_3' => 'uzb',	'source' => 0),
		array('iso_1' => 've',	'iso_3' => 'ven',	'source' => 0),
		array('iso_1' => 'vi',	'iso_3' => 'vie',	'source' => 0),
		array('iso_1' => 'vo',	'iso_3' => 'vol',	'source' => 0),
		array('iso_1' => 'wa',	'iso_3' => 'wln',	'source' => 0),
		array('iso_1' => 'wo',	'iso_3' => 'wol',	'source' => 0),
		array('iso_1' => 'xh',	'iso_3' => 'xho',	'source' => 0),
		array('iso_1' => 'yi',	'iso_3' => 'yid',	'source' => 0),
		array('iso_1' => 'yo',	'iso_3' => 'yor',	'source' => 0),
		array('iso_1' => 'za',	'iso_3' => 'zha',	'source' => 0),
		array('iso_1' => 'zh',	'iso_3' => 'zho',	'source' => 0),
		array('iso_1' => 'zu',	'iso_3' => 'zul',	'source' => 0),
    );
}
