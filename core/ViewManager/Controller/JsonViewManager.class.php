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
 * JSON Adapter for View Manager
 * @copyright   Cloudrexx AG
 * @author      Project Team SS4U <info@cloudrexx.com>
 * @package     cloudrexx
 * @subpackage  core_viewmanager
 */

namespace Cx\Core\ViewManager\Controller;

/**
 * JSON Adapter for View Manager
 * @copyright   Cloudrexx AG
 * @author      Project Team SS4U <info@cloudrexx.com>
 * @package     cloudrexx
 * @subpackage  core_viewmanager
 */
class JsonViewManager implements \Cx\Core\Json\JsonAdapter {
    /**
     * List of messages
     * @var Array
     */
    private $messages = array();

    /**
     * Returns the internal name used as identifier for this adapter
     * @return String Name of this adapter
     */
    public function getName() {
        return 'ViewManager';
    }

    /**
     * Returns an array of method names accessable from a JSON request
     * @return array List of method names
     */
    public function getAccessableMethods() {
        return array(
            'activateTheme'             => new \Cx\Core_Modules\Access\Model\Entity\Permission(array('http', 'https'), array('post'), true, null, array(ViewManager::ENABLE_THEMES_ACCESS_ID), null),
            'activateLanguages'         => new \Cx\Core_Modules\Access\Model\Entity\Permission(array('http', 'https'), array('post'), true, null, array(ViewManager::ENABLE_THEMES_ACCESS_ID), null),
            'checkThemeExistsByThemeId' => new \Cx\Core_Modules\Access\Model\Entity\Permission(array('http', 'https'), array('post'), true, null, array(ViewManager::EDIT_THEMES_ACCESS_ID), null),
            'deleteThemeById'           => new \Cx\Core_Modules\Access\Model\Entity\Permission(array('http', 'https'), array('post'), true, null, array(ViewManager::EDIT_THEMES_ACCESS_ID), null),
            'delete'                    => new \Cx\Core_Modules\Access\Model\Entity\Permission(array('http', 'https'), array('post'), true, null, array(ViewManager::EDIT_THEMES_ACCESS_ID), null),
            'rename'                    => new \Cx\Core_Modules\Access\Model\Entity\Permission(array('http', 'https'), array('post'), true, null, array(ViewManager::EDIT_THEMES_ACCESS_ID), null),
            'newWithin'                 => new \Cx\Core_Modules\Access\Model\Entity\Permission(array('http', 'https'), array('post'), true, null, array(ViewManager::EDIT_THEMES_ACCESS_ID), null),
        );
    }

    /**
     * Returns all messages as string
     * @return String HTML encoded error messages
     */
    public function getMessagesAsString() {
        return implode('<br />', $this->messages);
    }

    /**
     * Returns default permission as object
     * @return Object
     */
    public function getDefaultPermissions() {
        return null;
    }
    /**
     * activate the selected theme as standard theme
     *
     * @global type $objDatabase
     */
    public function activateTheme() {
        // array contains the database value name for each theme type
        $themeChannels = \Cx\Core\View\Model\Entity\Theme::$channels;

        $themeId   = isset($_POST['themeId']) ? $_POST['themeId'] : '';
        $themeChannel = isset($_POST['themeType']) && array_key_exists($_POST['themeType'], $themeChannels) ? $themeChannels[intval($_POST['themeType'])] : 0;

        $em = \Cx\Core\Core\Controller\Cx::instanciate()
            ->getDb()
            ->getEntityManager();
        $frontendRepo = $em->getRepository('Cx\Core\View\Model\Entity\Frontend');

        if (!empty($themeId)) {
            if (count(\FWLanguage::getActiveFrontendLanguages()) > 1) {
                if (isset($_POST['themesLangId'])) { // set theme for given languages
                    foreach ($_POST['themesLangId'] as $langId) {
                        $criteria = array(
                            'language' => $langId,
                            'channel' => $themeChannel
                        );
                        $frontend = $frontendRepo->findOneBy($criteria);
                        $frontend->setTheme($themeId);
                        $em->persist($frontend);
                    }
                }
            } else { // set theme for all active languages
                $criteria = array(
                    'channel' => $themeChannel
                );
                $frontends = $frontendRepo->findBy($criteria);
                foreach ($frontends as $frontend) {
                    $frontend->setTheme($themeId);
                    $em->persist($frontend);
                }
            }
            $em->flush();
        }

    }

    /**
     * activate selected languages for the corresponding theme
     *
     * @return array result
     */
    public function activateLanguages() {

        $themeTypes = array(
          \Cx\Core\View\Model\Entity\Theme::THEME_TYPE_WEB,
          \Cx\Core\View\Model\Entity\Theme::THEME_TYPE_MOBILE,
          \Cx\Core\View\Model\Entity\Theme::THEME_TYPE_PRINT,
          \Cx\Core\View\Model\Entity\Theme::THEME_TYPE_PDF,
          \Cx\Core\View\Model\Entity\Theme::THEME_TYPE_APP
        );

        $themeId   = isset($_POST['themeId']) ? $_POST['themeId'] : 0;
        $themeType = isset($_POST['themeType']) && array_key_exists($_POST['themeType'], $themeTypes) ? intval($_POST['themeType']) : 0;

        $selectThemeNotInLanguages = array();

        $themeRepository = new \Cx\Core\View\Model\Repository\ThemeRepository();
        if (!empty($themeId)) {
            $theme = $themeRepository->findById($themeId);

            $selectThemeNotInLanguages = array_diff_key(
                \FWLanguage::getActiveFrontendLanguages(),
                $theme->getLanguagesByType($themeTypes[$themeType])
            );
        }


        foreach ($selectThemeNotInLanguages as $selectThemeNotInLanguage){
            $result[] = array(
                "lang_id"     => $selectThemeNotInLanguage['id'],
                "lang_name"   => $selectThemeNotInLanguage['name']
            );
        }

        return $result;
    }

    /**
     * Check whether the theme is selected for any of the active languages/custom theme for active languages/other theme
     *
     * @global \Cx\Core\ViewManager\Controller\type $_ARRAYLANG
     * @return array result
     */
    public function checkThemeExistsByThemeId() {
        global $_ARRAYLANG;

        $_ARRAYLANG = \Env::get('init')->loadLanguageData('ViewManager');
        $themeRepository = new \Cx\Core\View\Model\Repository\ThemeRepository();
        $themeId         = isset($_GET['themeId']) ? $_GET['themeId'] : 0;

        $pageRepo = \Env::get('em')->getRepository('Cx\Core\ContentManager\Model\Entity\Page');
        $pages = $pageRepo->findBy(array(
            'skin' => intval($themeId)
        ));

        if (isset($themeId)) {
            $theme = $themeRepository->findById($themeId);
            if ($theme) {
                $activeLanguages = $theme->getLanguages();
            } else {
                return array(
                    'isTrue'  => true,
                    'Content' => 'Theme does not exist!'
                );
            }

            //Check whether the theme is selected for any of the active languages
            if (!empty($activeLanguages)) {
                $result = array(
                    "isTrue"  => true,
                    "Content" => $_ARRAYLANG['TXT_THEME_DELETE_CURRENT_ACTIVE_ALERT']
                );
            } else {
                if (empty($pages)) {
                    $result = array(
                            "isTrue"  => false,
                            "Content" => $_ARRAYLANG['TXT_THEME_DELETE_ALERT']
                        );
                } else {
                    foreach ($pages as $page) {
                        //Check whether the selected theme is selected for custom theme for any of the content pages of the active frontend languages
                        if ($page->getSkin() == $themeId) {
                           $result = array(
                               "isTrue"  => false,
                               "Content" => $_ARRAYLANG['TXT_THEME_DELETE_CUSTOM_ACTIVE_ALERT']
                            );
                        }
                    }
                }

            }

        }

        return $result;
    }
    /**
     * Delete selected theme and its theme folder
     *
     * @global type $_ARRAYLANG
     * @return array status
     */
    function deleteThemeById()
    {
        global $_ARRAYLANG;

        $_ARRAYLANG = \Env::get('init')->loadLanguageData('ViewManager');
        $delThemeId         = isset($_GET['delThemeId']) ? $_GET['delThemeId'] : 0;
        $themeRepository    = new \Cx\Core\View\Model\Repository\ThemeRepository();
        if (!empty($delThemeId)) {
            $theme = $themeRepository->findById($delThemeId);

            if (!$theme) {
                return array('status' => 'error', 'message' => $_ARRAYLANG['TXT_STATUS_CANNOT_DELETE']);
            }
            $themeFolder = $theme->getFileByPath('/' . $theme->getFoldername());

            //Check whether the selected theme is selected for any of the active languages
            $activeLanguages     = $theme->getLanguages();
            $isThemeFolderExists = $themeFolder->getFileSystem()->fileExists($themeFolder);
            if (!empty($activeLanguages) && $isThemeFolderExists) {
                return array('status' => 'error', 'message' => $_ARRAYLANG['TXT_STATUS_CANNOT_DELETE']);
            }

            // delete whole folder with subfolders in case it exists
            if (
                $isThemeFolderExists &&
                !$themeFolder->getFileSystem()->removeFile($themeFolder)
            ) {
                //error
                return array('status' => 'error', 'message' => $_ARRAYLANG['TXT_STATUS_CANNOT_DELETE']);
            }

            //setting 0 for the custom theme for any of the content pages of the active frontend languages
            $pageRepo = \Env::get('em')->getRepository('Cx\Core\ContentManager\Model\Entity\Page');
            $pages = $pageRepo->findBy(array(
                'skin' => intval($theme->getId()),
            ));
            foreach ($pages as $page) {
                $page->setSkin(0);
                \Env::get('em')->persist($page);
            }
            \Env::get('em')->flush();

            //Remove theme details from the database.
            if ($themeRepository->remove($theme)) {
                return array('status' => 'success', 'message' => contrexx_raw2xhtml($theme->getThemesname()) .": ". $_ARRAYLANG['TXT_STATUS_SUCCESSFULLY_DELETE']);
            }
        }


    }

    /**
     * Name of the file or folder
     *
     * @global array $_CORELANG
     *
     * @param string $themeFilePath   themeFilePath
     * @param string $currentFileName currentFilePath
     *
     * @return string filename
     */
    public  function copyTitle($themeFilePath, $currentFileName)
    {
        global $_CORELANG;

        //check the input is file or not
        $currentFileExtn = '';
        $fileInfo = pathinfo($currentFileName);
        if (!empty($fileInfo['extension'])) {
            $currentFileName = $fileInfo['filename'];
            $currentFileExtn = $fileInfo['extension'];
        }

        if (preg_match('#\(copy\)#i', $currentFileName) || preg_match('#\(copy \d\)#i', $currentFileName)) {
            $oldFileName = $currentFileName;
            $newFileName = $currentFileName . ' (' . $_CORELANG['TXT_CORE_CM_COPY_OF_PAGE'] . ')';
        } else {
            $oldFileName = preg_replace('@\([^\)]*\)$@','',$currentFileName);
            $newFileName = $oldFileName . ' (' . $_CORELANG['TXT_CORE_CM_COPY_OF_PAGE'] . ')';
        }

        $i = 1;
        while (\Cx\Lib\FileSystem\FileSystem::exists($themeFilePath.$newFileName)) {
            $i++;
            $newFileName = $oldFileName. ' (' . sprintf($_CORELANG['TXT_CORE_CM_COPY_N_OF_PAGE'], $i) . ')';
        }
        return !empty($currentFileExtn) ? $newFileName.'.'.$currentFileExtn : $newFileName;
    }


    /**
     * delete the file or directory
     *
     * @param array $params supplied arguments from JsonData-request
     * @return string
     */
    public function delete($params)
    {
        global $_ARRAYLANG, $objInit;

        $operation = isset($params['post']['reset']) && !empty($params['post']['reset']) ? 'RESET' : 'DELETE';

        $_ARRAYLANG = $objInit->loadLanguageData('ViewManager');
        if (empty($params['post']['themes']) || empty($params['post']['themesPage'])) {
            return array('status' => 'error', 'message' => $_ARRAYLANG['TXT_THEME_OPERATION_FAILED_FOR_EMPTY_PARAMS']);
        }

        $filePath  = contrexx_input2raw($params['post']['themesPage']);
        $themeName = '/' . contrexx_input2raw($params['post']['themes']);

        $pathStripped = ltrim($filePath, '/');
        if (empty($pathStripped)) {
            return array('status' => 'error', 'message' => $_ARRAYLANG['TXT_THEME_OPERATION_FAILED_FOR_EMPTY_PARAMS']);
        }

        $theme = new \Cx\Core\View\Model\Entity\Theme();
        $themeFolder = $theme->getFileByPath($themeName . $filePath);
        if (
            !$themeFolder->getFileSystem()->fileExists($themeFolder) &&
            \Cx\Core\ViewManager\Controller\ViewManager::isFileTypeComponent($filePath)
        ) { // resolve the component file
            $componentFilePath = \Cx\Core\ViewManager\Controller\ViewManager::getComponentFilePath($filePath, false);
            $componentFile = $theme->getFileByPath($themeName . '/' . $componentFilePath);
            if ($componentFilePath && $componentFile->getFileSystem()->fileExists($componentFile)) { // file exists
                $filePath = $componentFilePath;
            } else { // file not exists may be a folder
                $filePath = \Cx\Core\ViewManager\Controller\ViewManager::replaceComponentFolderByItsType($filePath);
            }
        }

        $themeFile = $theme->getFileByPath($themeName . '/' . $filePath);
        if ($themeFile->getFileSystem()->fileExists($themeFile)) {
            $status = $themeFile->getFileSystem()->removeFile($themeFile);
            if ($themeFile->getFileSystem()->isDirectory($themeFile)) {
                $succesMessage = sprintf($_ARRAYLANG['TXT_THEME_FOLDER_'. $operation .'_SUCCESS'], contrexx_input2xhtml($pathStripped));
            } else {
                $succesMessage = sprintf($_ARRAYLANG['TXT_THEME_FILE_'. $operation .'_SUCCESS'], contrexx_input2xhtml($pathStripped));
            }

            if (!$status) {
                return array('status' => 'error', 'reload' => false, 'message' => $_ARRAYLANG['TXT_THEME_'. $operation .'_FAILED']);
            }
            return array('status' => 'success', 'reload' => true, 'message' =>  $succesMessage);
        }

        return array('status' => 'error', 'reload' => false, 'message' => sprintf($_ARRAYLANG['TXT_THEME_OPERATION_FAILED_FOR_FILE_NOT_EXITS'], contrexx_input2xhtml($filePath)));
    }

    /**
     * rename the file or directory
     *
     * @param array $params supplied arguments from JsonData-request
     * @return string
     */
    public function rename($params)
    {
        global $_ARRAYLANG, $objInit;

        $_ARRAYLANG = $objInit->loadLanguageData('ViewManager');
        if (empty($params['post']['theme']) || empty($params['post']['oldName']) || empty($params['post']['newName'])) {
            return array('status' => 'error', 'message' => $_ARRAYLANG['TXT_THEME_OPERATION_FAILED_FOR_EMPTY_NAME']);
        }
        if ($params['post']['isFolder'] && preg_match('/^\./', trim($params['post']['newName']))) { // folder name should not start with dot(.)
            return array('status' => 'error', 'reload' => false, 'message' => sprintf($_ARRAYLANG['TXT_THEME_FOLDER_NAME_NOT_ALLOWED'], contrexx_input2xhtml($params['post']['newName'])));
        }

        $matches = null;
        preg_match('@{([0-9A-Za-z._-]+)(:([_a-zA-Z][A-Za-z_0-9]*))?}@sm', $params['post']['newName'], $matches);
        if (!empty($matches)) {
            return array('status' => 'error', 'reload' => false, 'message' => sprintf($_ARRAYLANG['TXT_THEME_NAME_NOT_ALLOWED'], contrexx_input2xhtml($params['post']['newName'])));
        }

        $themeName          = '/' . contrexx_input2raw($params['post']['theme']);
        $currentThemeFolder = \Env::get('cx')->getWebsiteThemesPath() . $themeName;
        $oldFilePath        = $params['post']['oldName'];
        $newFileName        = $params['post']['newName'];
        $isFolder           = $params['post']['isFolder'] ?: 0;
        $newFilePath        = \Cx\Lib\FileSystem\FileSystem::replaceCharacters($newFileName);

        // Cannot rename the virtual directory
        $virtualDirs = array(
            '/'. \Cx\Core\Core\Model\Entity\SystemComponent::TYPE_CORE_MODULE,
            '/'. \Cx\Core\Core\Model\Entity\SystemComponent::TYPE_MODULE,
            '/'. \Cx\Core\Core\Model\Entity\SystemComponent::TYPE_CORE
        );
        if (in_array($oldFilePath, $virtualDirs) || in_array('/'.$newFilePath, $virtualDirs)) {
            return array('status' => 'error', 'reload' => false, 'message' => $_ARRAYLANG['TXT_THEME_OPERATION_FAILED_FOR_RENAME_VIRTUAL_FOLDER']);
        }

        $theme   = new \Cx\Core\View\Model\Entity\Theme();
        $oldFile = $theme->getFileByPath($themeName . $oldFilePath);
        if (
            !$oldFile->getFileSystem()->fileExists($oldFile) &&
            \Cx\Core\ViewManager\Controller\ViewManager::isFileTypeComponent($oldFilePath)
        ) { // resolve the component file
            $componentFilePath = \Cx\Core\ViewManager\Controller\ViewManager::getComponentFilePath($oldFilePath, false);
            $componentFile = $theme->getFileByPath($themeName . $componentFilePath);
            if ($componentFilePath && $componentFile->getFileSystem()->fileExists($componentFile)) { // file exists
                $oldFilePath = $componentFilePath;
            } else { // file not exists may be a folder
                $oldFilePath = \Cx\Core\ViewManager\Controller\ViewManager::replaceComponentFolderByItsType($oldFilePath);
            }
        }

        $sourceFile = $theme->getFileByPath($themeName . $oldFilePath);
        $destFile   = $theme->getFileByPath($sourceFile->getPath() . '/' . $newFilePath);
        if ($sourceFile->getFileSystem()->moveFile($sourceFile, $destFile->getName())) {
            $path     = preg_replace(
                '#' . $currentThemeFolder . '#',
                 '',
                 $destFile->getFileSystem()->getFullPath($destFile) .
                 $destFile->getName() . '.' . $sourceFile->getExtension()
            );
            $message = $isFolder ? $_ARRAYLANG['TXT_THEME_FOLDER_RENAME_SUCCESS'] : $_ARRAYLANG['TXT_THEME_FILE_RENAME_SUCCESS'];
            return array('status' => 'success', 'reload' => true, 'path' => \Cx\Core\ViewManager\Controller\ViewManager::getThemeRelativePath($path), 'message' =>  $message);
        }

        return array('status' => 'error', 'reload' => false, 'message' => $_ARRAYLANG['TXT_THEME_RENAME_FAILED']);
    }

    /**
     * create new file or folder
     *
     * @param array $params supplied arguments from JsonData-request
     * @return string
     */
    public function newWithin($params)
    {
        global $_ARRAYLANG, $objInit;

        $_ARRAYLANG = $objInit->loadLanguageData('ViewManager');
        if (empty($params['post']['theme']) || empty($params['post']['name'])) {
            return array('status' => 'error', 'message' => $_ARRAYLANG['TXT_THEME_OPERATION_FAILED_FOR_EMPTY_NAME']);
        }

        if ($params['post']['isFolder'] && preg_match('/^\./', trim($params['post']['name']))) { // folder name should not start with dot(.)
            return array('status' => 'error', 'reload' => false, 'message' => sprintf($_ARRAYLANG['TXT_THEME_FOLDER_NAME_NOT_ALLOWED'], contrexx_input2xhtml($params['post']['name'])));
        }

        $matches = null;
        preg_match('@{([0-9A-Za-z._-]+)(:([_a-zA-Z][A-Za-z_0-9]*))?}@sm', $params['post']['name'], $matches);
        if (!empty($matches)) {
            return array('status' => 'error', 'reload' => false, 'message' => sprintf($_ARRAYLANG['TXT_THEME_NAME_NOT_ALLOWED'], contrexx_input2xhtml($params['post']['newName'])));
        }

        // Cannot rename the virtual directory
        $virtualDirs = array(
            '/'. \Cx\Core\Core\Model\Entity\SystemComponent::TYPE_CORE_MODULE,
            '/'. \Cx\Core\Core\Model\Entity\SystemComponent::TYPE_MODULE,
            '/'. \Cx\Core\Core\Model\Entity\SystemComponent::TYPE_CORE
        );

        $theme       = new \Cx\Core\View\Model\Entity\Theme();
        $themeName   = contrexx_input2raw($params['post']['theme']);
        $themeFolder = $theme->getFileByPath('/' . $themeName);
        $currentThemeFolderDirPath = \Env::get('cx')->getWebsiteThemesPath() . '/'. $themeName . '/';
        // Create the theme folder, if it does not exist
        if (
            !$themeFolder->getFileSystem()->fileExists($themeFolder) &&
            !$themeFolder->getFileSystem()->createDirectory($themeName)
        ) {
            return array('status' => 'error', 'reload' => false, 'message' => $_ARRAYLANG['TXT_THEME_NEWFILE_FAILED']);
        }

        $newFileName               = \Cx\Lib\FileSystem\FileSystem::replaceCharacters($params['post']['name']);

        if (!\FWValidator::is_file_ending_harmless($newFileName)) {
            return array('status' => 'error', 'reload' => false, 'message' => sprintf($_ARRAYLANG['TXT_THEME_FILE_EXTENSION_NOT_ALLOWED'], contrexx_input2xhtml($newFileName)));
        }

        if (in_array('/'.$newFileName, $virtualDirs)) {
            return array('status' => 'error', 'reload' => false, 'message' => $_ARRAYLANG['TXT_THEME_OPERATION_FAILED_FOR_VIRTUAL_FOLDER']);
        }

        $file = $theme->getFileByPath('/' . $themeName . '/' . $newFileName);
        if (!$file->getFileSystem()->fileExists($file)) {
            if ($params['post']['isFolder']) {
                $status = $file->getFileSystem()->createDirectory($themeName . '/' . $newFileName);
                $succesMessage = sprintf($_ARRAYLANG['TXT_THEME_FOLDER_CREATE_SUCCESS'], contrexx_input2xhtml($newFileName));
            } else {
                $status = $file->getFileSystem()->writeFile($file, '');
                $succesMessage = sprintf($_ARRAYLANG['TXT_THEME_FILE_CREATE_SUCCESS'], contrexx_input2xhtml($newFileName));
            }

            if ($status === false) {
                return array('status' => 'error', 'message' => $_ARRAYLANG['TXT_THEME_NEWFILE_FAILED']);
            }
            return array('status' => 'success', 'reload' => true, 'message' => $succesMessage, 'path' => '/' .$newFileName);
        }
        return array('status' => 'error', 'message' => sprintf($_ARRAYLANG['TXT_THEME_OPERATION_FAILED_FOR_FILE_ALREADY_EXITS'], contrexx_input2xhtml($newFileName)));
    }

}
