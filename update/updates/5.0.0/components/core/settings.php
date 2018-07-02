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

function _updateSettings()
{
    global $objUpdate, $objDatabase, $_ARRAYLANG, $_CORELANG, $_CONFIG, $arrSettings, $arrSettingsByName;

    if (
        !in_array('settingsPre5', ContrexxUpdate::_getSessionArray($_SESSION['contrexx_update']['update']['done'])) &&
        $objUpdate->_isNewerVersion($_CONFIG['coreCmsVersion'], '5.0.0')
    ) {
		$arrSettings = array(
		
	);

		$arrSettingsByName = array();
		foreach ($arrSettings as $setid => $data) {
			$arrSettingsByName[$data['setname']] = $setid;
		}


		// change googleSitemapStatus to xmlSitemapStatus
		$query = "SELECT 1 FROM `".DBPREFIX."settings` WHERE `setname`='googleSitemapStatus'";
		$objResult = $objDatabase->SelectLimit($query, 1);
		if ($objResult) {
			if ($objResult->RecordCount() == 1) {
				$query = "UPDATE `".DBPREFIX."settings` SET `setname` = 'xmlSitemapStatus' WHERE `setname` = 'googleSitemapStatus'";
				if ($objDatabase->Execute($query) === false) {
					return _databaseError($query, $objDatabase->ErrorMsg());
				}
			}
		} else {
			return _databaseError($query, $objDatabase->ErrorMsg());
		}

		try {
			//remove fileuploader setting
			\Cx\Lib\UpdateUtil::sql('DELETE FROM '.DBPREFIX.'settings WHERE setid=70 AND setname="fileUploaderStatus"');
		}
		catch (\Cx\Lib\UpdateException $e) {
			DBG::trace();
			return \Cx\Lib\UpdateUtil::DefaultActionHandler($e);
		}

		foreach ($arrSettings as $setId => $arrSetting) {
			if (!_updateSettingsTable($setId, $arrSetting)) {
				return false;
			}
		}

		$query = "UPDATE `".DBPREFIX."settings` SET `setmodule`=1 WHERE `setmodule`=0";
		if ($objDatabase->Execute($query) === false) {
			return _databaseError($query, $objDatabase->ErrorMsg());
		}


		//timezone (Contrexx 3.0.1)
        if (!isset($_CONFIG['timezone'])) {
            $arrTimezoneIdentifiers = timezone_identifiers_list();
            if (isset($_POST['timezone']) && array_key_exists($_POST['timezone'], $arrTimezoneIdentifiers)) {
                $_SESSION['contrexx_update']['update']['timezone'] = $_POST['timezone'];
            }
            if (isset($_SESSION['contrexx_update']['update']['timezone']) && array_key_exists(ContrexxUpdate::_getSessionArray($_SESSION['contrexx_update']['update']['timezone']), $arrTimezoneIdentifiers)) {
                try {
                    \Cx\Lib\UpdateUtil::sql('UPDATE `'.DBPREFIX.'settings` SET `setvalue` = "'.$arrTimezoneIdentifiers[$_SESSION['contrexx_update']['update']['timezone']].'" WHERE `setname` = "timezone"');
                    // add timezone to $_CONFIG array so it will be written in configuration.php in components/core/core.php
                    $_CONFIG['timezone'] = $arrTimezoneIdentifiers[$_SESSION['contrexx_update']['update']['timezone']];
                } catch (\Cx\Lib\UpdateException $e) {
                    return \Cx\Lib\UpdateUtil::DefaultActionHandler($e);
                }
            } else {
                $selected = -1;
                if (($defaultTimezoneId = array_search(@date_default_timezone_get(), $arrTimezoneIdentifiers)) && !empty($defaultTimezoneId)) {
                    $selected = $defaultTimezoneId;
                }

                $options = '<option value="-1"'.($selected == -1 ? ' selected="selected"' : '').'>'.$_CORELANG['TXT_PLEASE_SELECT'].'</option>';
                foreach ($arrTimezoneIdentifiers as $id => $name) {
                    $dateTimeZone = new DateTimeZone($name);
                    $dateTime = new DateTime('now', $dateTimeZone);
                    $timeOffset = $dateTimeZone->getOffset($dateTime);
                    $sign = $timeOffset < 0 ? '-' : '+';
                    $gmt = 'GMT '.$sign.gmdate('g:i', $timeOffset);
                    $options .= '<option value="'.$id.'"'.($selected == $id ? ' selected="selected"' : '').'>'.$name.' ('.$gmt.')'.'</option>';
                }

                setUpdateMsg($_CORELANG['TXT_TIMEZONE'], 'title');
                setUpdateMsg($_CORELANG['TXT_TIMEZONE_INTRODUCTION'].' <select name="timezone">'.$options.'</select>', 'msg');
                setUpdateMsg('<input type="submit" value="'.$_CORELANG['TXT_UPDATE_NEXT'].'" name="updateNext" /><input type="hidden" name="processUpdate" id="processUpdate" />', 'button');
                return false;
            }
        }


		// write settings
		$strFooter = '';
		$arrModules = '';

		\Cx\Lib\FileSystem\FileSystem::makeWritable(ASCMS_DOCUMENT_ROOT.'/config/');

		if (!file_exists(ASCMS_DOCUMENT_ROOT.'/config/settings.php')) {
			if (!touch(ASCMS_DOCUMENT_ROOT.'/config/settings.php')) {
				setUpdateMsg(sprintf($_ARRAYLANG['TXT_UNABLE_CREATE_SETTINGS_FILE'], ASCMS_DOCUMENT_ROOT.'/config/settings.php'));
				setUpdateMsg(sprintf($_ARRAYLANG['TXT_SET_WRITE_PERMISSON_TO_DIR'], ASCMS_DOCUMENT_ROOT.'/config/', $_CORELANG['TXT_UPDATE_TRY_AGAIN']), 'msg');
				return false;
			}
		}

		\Cx\Lib\FileSystem\FileSystem::makeWritable(ASCMS_DOCUMENT_ROOT.'/config/settings.php');

		if (is_writable(ASCMS_DOCUMENT_ROOT.'/config/settings.php')) {
			try {
				$objFile = new \Cx\Lib\FileSystem\File(ASCMS_DOCUMENT_ROOT.'/config/settings.php');
				//Header & Footer
				$strHeader    = "<?php\n";
				$strHeader .= "/**\n";
				$strHeader .= "* This file is generated by the \"settings\"-menu in your CMS.\n";
				$strHeader .= "* Do not try to edit it manually!\n";
				$strHeader .= "*/\n\n";

				$strFooter .= "?>";

				//Get module-names
				$objResult = $objDatabase->Execute('SELECT    id, name FROM '.DBPREFIX.'modules');
				if ($objResult->RecordCount() > 0) {
					while (!$objResult->EOF) {
						$arrModules[$objResult->fields['id']] = $objResult->fields['name'];
						$objResult->MoveNext();
					}
				}

				//Get values
				$objResult = $objDatabase->Execute('SELECT        setname,
																setmodule,
																setvalue
													FROM        '.DBPREFIX.'settings
													ORDER BY    setmodule ASC,
																setname ASC
												');
				$intMaxLen = 0;
				$arrValues = array();
				while ($objResult && !$objResult->EOF) {
					$intMaxLen = (strlen($objResult->fields['setname']) > $intMaxLen) ? strlen($objResult->fields['setname']) : $intMaxLen;
					$arrValues[$objResult->fields['setmodule']][$objResult->fields['setname']] = $objResult->fields['setvalue'];
					$objResult->MoveNext();
				}
				$intMaxLen += strlen('$_CONFIG[\'\']') + 1; //needed for formatted output

				$fileContent = $strHeader;

				foreach ($arrValues as $intModule => $arrInner) {
					$fileContent .= "/**\n";
					$fileContent .= "* -------------------------------------------------------------------------\n";
					if (isset($arrModules[$intModule])) {
						$fileContent .= "* ".ucfirst($arrModules[$intModule])."\n";
					} else {
						$fileContent .= "* ".$intModule."\n";
					}
					$fileContent .= "* -------------------------------------------------------------------------\n";
					$fileContent .= "*/\n";

					foreach($arrInner as $strName => $strValue) {
						$fileContent .= sprintf("%-".$intMaxLen."s",'$_CONFIG[\''.$strName.'\']');
						$fileContent .= "= ";
						$fileContent .= (is_numeric($strValue) ? $strValue : '"'.str_replace('"', '\"', $strValue).'"').";\n";
					}
					$fileContent .= "\n";
				}

				$fileContent .= $strFooter;

				$objFile->write($fileContent);
			} catch (\Cx\Lib\FileSystem\FileSystemException $e) {}
		} else {
			setUpdateMsg(sprintf($_ARRAYLANG['TXT_UNABLE_WRITE_SETTINGS_FILE'], ASCMS_DOCUMENT_ROOT.'/config/settings.php'));
			setUpdateMsg(sprintf($_ARRAYLANG['TXT_SET_WRITE_PERMISSON_TO_FILE'], ASCMS_DOCUMENT_ROOT.'/config/settings.php', $_CORELANG['TXT_UPDATE_TRY_AGAIN']), 'msg');
			return false;
		}

		$query = "
		ALTER TABLE ".DBPREFIX."settings
		CHANGE COLUMN setid setid integer(6) UNSIGNED NOT NULL auto_increment;
		";
		if (!$objDatabase->Execute($query)) {
			return _databaseError($query, $objDatabase->ErrorMsg());
		}

        $_SESSION['contrexx_update']['update']['done'][] = 'settingsPre5';
        // force reload to ensure any settings migrations as well as any fixes have been loaded
        return 'timeout';
    }

    if (
        !in_array('imageSettings', ContrexxUpdate::_getSessionArray($_SESSION['contrexx_update']['update']['done'])) &&
        $objUpdate->_isNewerVersion($_CONFIG['coreCmsVersion'], '5.0.0')
    ) {
		try {
			\Cx\Lib\UpdateUtil::table(
				DBPREFIX.'settings_image',
				array(
					'id'         => array('type' => 'INT(10)', 'unsigned' => true, 'notnull' => true, 'auto_increment' => true, 'primary' => true),
					'name'       => array('type' => 'VARCHAR(50)', 'after' => 'id'),
					'value'      => array('type' => 'text', 'after' => 'name')
				)
			);
		} catch (\Cx\Lib\UpdateException $e) {
			return \Cx\Lib\UpdateUtil::DefaultActionHandler($e);
		}

		if ($objUpdate->_isNewerVersion($_CONFIG['coreCmsVersion'], '3.0.3')) {
			try {
				\Cx\Lib\UpdateUtil::sql("INSERT IGNORE INTO `".DBPREFIX."settings_image` (`name`, `value`) VALUES (?, ?)", array('image_cut_width', '500'));
				\Cx\Lib\UpdateUtil::sql("INSERT IGNORE INTO `".DBPREFIX."settings_image` (`name`, `value`) VALUES (?, ?)", array('image_cut_height', '500'));
				\Cx\Lib\UpdateUtil::sql("INSERT IGNORE INTO `".DBPREFIX."settings_image` (`name`, `value`) VALUES (?, ?)", array('image_scale_width', '800'));
				\Cx\Lib\UpdateUtil::sql("INSERT IGNORE INTO `".DBPREFIX."settings_image` (`name`, `value`) VALUES (?, ?)", array('image_scale_height', '800'));
				\Cx\Lib\UpdateUtil::sql("INSERT IGNORE INTO `".DBPREFIX."settings_image` (`name`, `value`) VALUES (?, ?)", array('image_compression', '100'));
			} catch (\Cx\Lib\UpdateException $e) {
				return \Cx\Lib\UpdateUtil::DefaultActionHandler($e);
			}
		}
        $_SESSION['contrexx_update']['update']['done'][] = 'imageSettings';
	}

    return true;
}

function migrateSettingsToSettingDb() {
    global $objUpdate, $_CONFIG;

    if ($objUpdate->_isNewerVersion($_CONFIG['coreCmsVersion'], '5.0.0')) {
        try {
            // init new config
            $config = new \Cx\Core\Config\Controller\Config();

            // load config for manual overwrite below
            \Cx\Core\Setting\Controller\Setting::init('Config',null,'Yaml');

            // reset version to current version
            \Cx\Core\Setting\Controller\Setting::set('coreCmsVersion', $_CONFIG['coreCmsVersion']);
            \Cx\Core\Setting\Controller\Setting::update('coreCmsVersion');
            // force disabling of customizings
            \Cx\Core\Setting\Controller\Setting::set('useCustomizings', 'off');
            \Cx\Core\Setting\Controller\Setting::update('useCustomizings');

            // manually update config/settings.php
            \Cx\Core\Config\Controller\Config::updatePhpCache();
        } catch (\Exception $e) {
            \DBG::msg($e->getMessage());
            return \Cx\Lib\UpdateUtil::DefaultActionHandler($e);
        }
    }

    return true;
}

function _updateSettingsTable($setId, $arrSetting)
{
    global $objDatabase, $arrSettings, $arrSettingsByName, $arrCurrentSettingsTable, $_CONFIG;

    if (!isset($arrCurrentSettingsTable)) {
        $arrCurrentSettingsTable = array();
    }

    $query = "SELECT setid FROM `".DBPREFIX."settings` WHERE `setname`='".$arrSetting['setname']."'";
    // select stored ID of option
    if (($objSettings = $objDatabase->SelectLimit($query, 1)) !== false) {
        if ($objSettings->RecordCount() == 0) {
            // option isn't yet present => ok, check if the associated ID isn't already used
            $query = "SELECT `setname` FROM `".DBPREFIX."settings` WHERE `setid` = ".intval($setId);
            if (($objSettings = $objDatabase->SelectLimit($query, 1)) !== false) {
                if ($objSettings->RecordCount() == 0) {
                    // option ID isn't already in use => ok, add it
                    $value = $arrSetting['setvalue'];

                    // we must set coreCmsVersion to the currently installed version,
                    // otherwise if we would set it to the new version,
                    // the update will be stopped before getting everything done
                    if ($arrSetting['setname'] == 'coreCmsVersion') {
                        $value = $_CONFIG['coreCmsVersion'];
                    }

                    $query = "INSERT INTO `".DBPREFIX."settings` ( `setid` , `setname` , `setvalue` , `setmodule` ) VALUES (".intval($setId).", '".$arrSetting['setname']."', '".$value."', '".intval($arrSetting['setmodule'])."')";
                    if ($objDatabase->Execute($query) !== false) {
                        return true;
                    } else {
                        return _databaseError($query, $objDatabase->ErrorMsg());
                    }
                } else {
                    // option ID is already in use => update the option who uses the wrong ID to it's right ID
                    $setname = $objSettings->fields['setname'];
                    if (in_array($setname, $arrCurrentSettingsTable)) {
                        // set a free ID which could be used as a temporary ID
                        $query = "SELECT MAX(`setid`) AS lastInsertId FROM `".DBPREFIX."settings`";
                        if (($objSettings = $objDatabase->SelectLimit($query, 1)) !== false) {
                            $query = "UPDATE `".DBPREFIX."settings` SET `setid` = ".($objSettings->fields['lastInsertId']+1)." WHERE `setid` = ".intval($setId);
                            // associated a temportary ID to the option who uses the wrong ID
                            if ($objDatabase->Execute($query) !== false) {
                                unset($arrCurrentSettingsTable[$setname]);
                                if (_updateSettingsTable($setId, $arrSetting)) {
                                    return true;
                                } else {
                                    return false;
                                }
                            } else {
                                return _databaseError($query, $objDatabase->ErrorMsg());
                            }
                        } else {
                            return _databaseError($query, $objDatabase->ErrorMsg());
                        }
                    } else {
                        $arrCurrentSettingsTable[] = $setname;
                        if (_updateSettingsTable($arrSettingsByName[$setname], $arrSettings[$arrSettingsByName[$setname]]) && _updateSettingsTable($setId, $arrSetting)) {
                            return true;
                        } else {
                            return false;
                        }
                    }
                }
            } else {
                return _databaseError($query, $objDatabase->ErrorMsg());
            }
        } elseif ($objSettings->fields['setid'] != intval($setId)) {
            $currentSetId = $objSettings->fields['setid'];
            // option is already present but uses a wrong ID => check if the right associated ID of the option is already used by an other option
            $query = "SELECT `setname` FROM `".DBPREFIX."settings` WHERE `setid` = ".intval($setId);
            if (($objSettings = $objDatabase->SelectLimit($query, 1)) !== false) {
                if ($objSettings->RecordCount() == 0) {
                    // ID isn't already used => ok, set the correct ID of the option
                    $query = "UPDATE `".DBPREFIX."settings` SET `setid` = ".intval($setId)." WHERE `setid` = ".$currentSetId;
                    if ($objDatabase->Execute($query) !== false) {
                        return true;
                    } else {
                        return _databaseError($query, $objDatabase->ErrorMsg());
                    }
                } else {
                    // option ID is already in use => update the option who uses the wrong ID to it's right ID
                    $setname = $objSettings->fields['setname'];
                    if (in_array($setname, $arrCurrentSettingsTable)) {
                        // set a free ID which could be used as a temporary ID
                        $query = "SELECT MAX(`setid`) AS lastInsertId FROM `".DBPREFIX."settings`";
                        if (($objSettings = $objDatabase->SelectLimit($query, 1)) !== false) {
                            $query = "UPDATE `".DBPREFIX."settings` SET `setid` = ".($objSettings->fields['lastInsertId']+1)." WHERE `setid` = ".intval($setId);
                            // associated a temportary ID to the option who uses the wrong ID
                            if ($objDatabase->Execute($query) !== false) {
                                unset($arrCurrentSettingsTable[$setname]);
                                if (_updateSettingsTable($setId, $arrSetting)) {
                                    return true;
                                } else {
                                    return false;
                                }
                            } else {
                                return _databaseError($query, $objDatabase->ErrorMsg());
                            }
                        } else {
                            return _databaseError($query, $objDatabase->ErrorMsg());
                        }
                    } else {
                        $arrCurrentSettingsTable[] = $setname;
                        if (_updateSettingsTable($arrSettingsByName[$setname], $arrSettings[$arrSettingsByName[$setname]]) && _updateSettingsTable($setId, $arrSetting)) {
                            return true;
                        } else {
                            return false;
                        }
                    }
                }
            } else {
                return _databaseError($query, $objDatabase->ErrorMsg());
            }
        } else {
            return true;
        }
    } else {
        return _databaseError($query, $objDatabase->ErrorMsg());
    }
}
