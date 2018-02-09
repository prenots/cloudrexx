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
 * class MediaSourceManager
 *
 * @copyright   Cloudrexx AG
 * @author      Tobias Schmoker <tobias.schmoker@comvation.com>
 *              Robin Glauser <robin.glauser@comvation.com>
 * @package     cloudrexx
 * @subpackage  core_mediasource
 */

namespace Cx\Core\MediaSource\Model\Entity;

use Cx\Core\Core\Controller\Cx;
use Cx\Model\Base\EntityBase;

/**
 * Class MediaSourceManagerException
 *
 * @copyright   Cloudrexx AG
 * @author      Thomas Däppen <thomas.daeppen@cloudrexx.com>
 * @package     cloudrexx
 * @subpackage  core_mediasource
 */
class MediaSourceManagerException extends \Exception {}

/**
 * Class MediaSourceManager
 *
 * @copyright   Cloudrexx AG
 * @author      Tobias Schmoker <tobias.schmoker@comvation.com>
 *              Robin Glauser <robin.glauser@comvation.com>
 * @package     cloudrexx
 * @subpackage  core_mediasource
 */
class MediaSourceManager extends EntityBase
{

    /**
     * @var \Cx\Core\Core\Controller\Cx
     */
    protected $cx;

    protected $mediaTypes = array();

    /**
     * @var array
     */
    protected $mediaTypePaths;

    /**
     * @var MediaSource[]
     */
    protected $allMediaTypePaths = array();

    /**
     * @var ThumbnailGenerator
     */
    protected $thumbnailGenerator;

    /**
     * @param $cx Cx
     *
     * @throws \Cx\Core\Event\Controller\EventManagerException
     */
    public function __construct($cx) {
        $this->cx             = $cx;
        $eventHandlerInstance = $this->cx->getEvents();

        /**
         * Loads all mediatypes into $this->allMediaTypePaths
         */
        $eventHandlerInstance->triggerEvent('mediasource.load', array($this));

        ksort($this->allMediaTypePaths);
        foreach ($this->allMediaTypePaths as $mediaSource) {
            /**
             * @var $mediaSource MediaSource
             */
            if ($mediaSource->checkAccess()) {
                $this->mediaTypePaths[$mediaSource->getName()] = $mediaSource->getDirectory();
                $this->mediaTypes[$mediaSource->getName()] = $mediaSource;
            }
        }
    }

    /**
     * Get the absolute path from the virtual path.
     * If the path is already absolute nothing will happen to it.
     *
     * @param $virtualPath string The virtual Path
     *
     * @return string The absolute Path
     */
    public static function getAbsolutePath($virtualPath) {
        if (self::isVirtualPath(
            $virtualPath
        )
        ) {
            $pathArray = explode('/', $virtualPath);
            return realpath(Cx::instanciate()->getMediaSourceManager()
                ->getMediaTypePathsbyNameAndOffset(array_shift($pathArray), 0)
            . '/' . join(
                '/', $pathArray
            ));
        }
        return $virtualPath;
    }

    /**
     * Checks if $subdirectory is a subdirectory of $path.
     * You can use a virtual path as a parameter.
     *
     * @param $path
     * @param $subdirectory
     *
     * @return boolean
     */
    public static function isSubdirectory($path, $subdirectory) {
        $absolutePath = self::getAbsolutePath($path);
        $absoluteSubdirectory = self::getAbsolutePath($subdirectory);
        return (boolean)preg_match(
            '#^' . preg_quote($absolutePath, '#') . '#', $absoluteSubdirectory
        );
    }

    /**
     * Checks permission
     *
     * @param $path
     *
     * @return bool
     */
    public static function checkPermissions($path) {
        foreach (
            Cx::instanciate()->getMediaSourceManager()->getMediaTypePaths() as
            $virtualPathName => $mediatype
        ) {
            if (self::isSubdirectory($virtualPathName, $path)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Check if a path is virtual or real.
     *
     * ``` php
     * \Cx\Core_Modules\MediaBrowser\Model\FileSystem::isVirtualPath('files/Movies'); // Returns true
     * ```
     *
     * @param $path
     *
     * @return bool
     */
    public static function isVirtualPath($path) {
        return !(strpos($path, '/') === 0);
    }

    public function addMediaType(MediaSource $mediaType) {
        $this->allMediaTypePaths[$mediaType->getPosition()
        . $mediaType->getName()] = $mediaType;
    }



    /**
     * @return MediaSource[]
     */
    public function getMediaTypes() {
        return $this->mediaTypes;
    }


    /**
     * @param $name string
     *
     * @return MediaSource
     * @throws MediaSourceManagerException
     */
    public function getMediaType($name) {
        if(!isset($this->mediaTypes[$name])){
            throw new MediaSourceManagerException("No such mediatype available");
        }
        return $this->mediaTypes[$name];
    }

    /**
     * @return array
     */
    public function getMediaTypePaths() {
        return $this->mediaTypePaths;
    }

    /**
     * @param $name
     *
     * @return array
     */
    public function getMediaTypePathsbyName($name) {
        return $this->mediaTypePaths[$name];
    }

    /**
     * @param $name
     * @param $offset
     *
     * @return array
     */
    public function getMediaTypePathsbyNameAndOffset($name, $offset) {
        return $this->mediaTypePaths[$name][$offset];
    }

    public function getAllMediaTypePaths() {
        return $this->allMediaTypePaths;
    }

    /**
     * @return ThumbnailGenerator
     */
    public function getThumbnailGenerator(){
        if (!$this->thumbnailGenerator){
            $this->thumbnailGenerator = new ThumbnailGenerator($this->cx,$this);
        }
        return $this->thumbnailGenerator;
    }

    /**
     * Get MediaSourceFile from the given path
     *
     * @param string  $path  File path
     * @param boolean $force True, return the File object if the file exists or not
     *                       False, return the file object only if the file exists
     * @return LocalFile File object
     */
    public function getMediaSourceFileFromPath($path, $force = false)
    {
        // If the path does not have leading backslash then add it
        if (strpos($path, '/') !== 0) {
            $path = '/' . $path;
        }

        try {
            // Get MediaSource and MediaSourceFile object
            $mediaSource     = $this->getMediaSourceByPath($path);
            $mediaSourcePath = $mediaSource->getDirectory();
            $mediaSourceFile = $mediaSource->getFileSystem()->getFileFromPath(
                substr($path, strlen($mediaSourcePath[1])),
                $force
            );
        } catch (MediaSourceManagerException $e) {
            \DBG::log($e->getMessage());
            return;
        }

        return $mediaSourceFile;
    }

    /**
     * Get MediaSource by given component
     *
     * @param \Cx\Core\Core\Model\Entity\SystemComponentController $component Component to look up for a MediaSource
     *
     * @return MediaSource  if a MediaSource of the given Component does exist
     *                              returns MediaSource, otherwise NULL 
     */
    public function getMediaSourceByComponent($component)
    {
        foreach ($this->mediaTypes as $mediaSource) {
            $mediaSourceComponent = $mediaSource->getSystemComponentController();
            if ($component == $mediaSourceComponent) {
                return $mediaSource;
            }
        }
        return null;
    }

    /**
     * Get MediaSource by the given path
     *
     * @param  string $path File path
     * @param boolean $ignorePermissions (optional) Defaults to false
     * @return \Cx\Core\MediaSource\Model\Entity\MediaSource MediaSource object
     * @throws MediaSourceManagerException
     */
    public function getMediaSourceByPath($path, $ignorePermissions = false)
    {
        $mediaSources = $this->mediaTypes;
        if ($ignorePermissions) {
            $this->allMediaTypePaths;
        }
        foreach ($mediaSources as $mediaSource) {
            $mediaSourcePath = $mediaSource->getDirectory();
            if (strpos($path, $mediaSourcePath[1]) === 0) {
                return $mediaSource;
            }
        }
        throw new MediaSourceManagerException(
            'No MediaSource found for: '. $path
        );
    }

    /**
     * Move the file/directory from one filesystem to other filesystem
     * The arguments $sourcepath and DestinationPath must be a relative path.
     * ie: /images/Access/photo/0_no_picture.gif,
     *     /media/archive1/preisliste_contrexx_2012.pdf,
     *     /themes/standard_4_0/text.css
     *     /tmp/session_hm6c0mte40hjsm802ipecn0176
     *     /images/Access/photo
     *     /media/archive1/logos
     *
     * @param string  $sourcePath      Source filepath
     * @param string  $destinationPath Destination filepath
     * @param boolean $ignoreExists    True, if the destination file/directory exists it will be overwritten
     *                                 otherwise file/directory will be created with new name
     * @return boolean status of moved file/directory
     */
    public function move($sourcePath, $destinationPath, $ignoreExists = false)
    {
        // Copy the file/directory from source to destination
        $status = $this->copy($sourcePath, $destinationPath, $ignoreExists);
        if ($status == 'error') {
            return 'error';
        }

        // Delete the source file
        $sourceFile = $this->getMediaSourceFileFromPath($sourcePath);
        if (!$sourceFile) {
            if (!\Cx\Lib\FileSystem\FileSystem::delete_file($sourcePath)) {
                $status = 'error';
            }
        } else {
            if (!$sourceFile->remove()) {
                $status = 'error';
            }
        }

        return $status;
    }

    /**
     * Copy the file/directory from one filesystem to other filesystem
     * The arguments $sourcepath and DestinationPath must be a relative path.
     * ie: /images/Access/photo/0_no_picture.gif,
     *     /media/archive1/preisliste_contrexx_2012.pdf,
     *     /themes/standard_4_0/text.css
     *     /tmp/session_hm6c0mte40hjsm802ipecn0176
     *     /images/Access/photo
     *     /media/archive1/logos
     *
     * @param string  $sourcePath      Source filepath
     * @param string  $destinationPath Destination filepath
     * @param boolean $ignoreExists    True, if the destination file/directory exists it will be overwritten
     *                                 otherwise file/directory will be created with new name
     * @return string Name of the copied file/directory
     */
    public function copy($fromPath, $toPath, $ignoreExists = false)
    {
        if (empty($fromPath) || empty($toPath)) {
            return 'error';
        }

        // Get source file object by the given $fromPath
        $sourceFile = $this->getMediaSourceFileFromPath($fromPath);
        if (!$sourceFile) {
            $sourceFile = new \Cx\Lib\FileSystem\File($fromPath);
        }

        if ($sourceFile->isFile()) {
            $toPath = pathinfo($toPath, PATHINFO_DIRNAME) . '/' .
                pathinfo($toPath, PATHINFO_FILENAME) . '.' .
                pathinfo($fromPath, PATHINFO_EXTENSION);
        }

        $destFile = $this->getMediaSourceFileFromPath($toPath, true);
        if (!$destFile) {
            $destFile = new \Cx\Lib\FileSystem\File($toPath);
        }

        if (!$ignoreExists) {
            $destFile = $this->getNewFileIfExists($destFile);
        }

        if (!$this->createFilePath($destFile, $sourceFile->isDirectory())) {
            return 'error';
        }

        $newFileName = $destFile->getFullName();
        if ($sourceFile->isFile() && !$this->copyFile($sourceFile, $destFile)) {
            $newFileName = 'error';
        }

        if ($sourceFile->isDirectory() && !$this->copyDir($sourceFile, $destFile)) {
            $newFileName = 'error';
        }

        return $newFileName;
    }

    /**
     * Copy the file from source to destination
     *
     * @param File|Cx\Lib\FileSystem $fromFile Source file object
     * @param File|Cx\Lib\FileSystem $toFile   Destination file object
     * @return boolean Status of file copy
     */
    public function copyFile($fromFile, $toFile)
    {
        if (!$fromFile || !$toFile) {
            return false;
        }

        if (
            stream_copy_to_stream(
                $fromFile->getStream('r'),
                $toFile->getStream('w')
            ) === false
        ) {
            return false;
        }

        return true;
    }

    /**
     * Copy the directory from source to destination
     *
     * @param File|Cx\Lib\FileSystem $fromFile Source file object
     * @param File|Cx\Lib\FileSystem $toFile   Destination file object
     * @return boolean Status of directory copy
     */
    public function copyDir($fromFile, $toFile)
    {
        if (!$fromFile || !$toFile) {
            return false;
        }

        $iterator = new \RegexIterator(
            new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator(
                    $fromFile->getAbsolutePath()
                ),
                \RecursiveIteratorIterator::SELF_FIRST
            ),
            '/^((?!thumb(_[a-z]+)?).)*$/'
        );
        foreach ($iterator as $file) {
            // filters
            if (
                $file->getFilename() == '.' ||
                $file->getFilename() == 'index.php' ||
                strpos($file->getFilename(), '.') === 0
            ) {
                continue;
            }

            $filePath = $file->getPath() . '/' . $file->getFilename();
            $copyPath = substr($filePath, strlen($fromFile->getAbsolutePath()));
            if ($file->isDir()) {
                if (!$this->createDirectory($toFile, $copyPath)) {
                    return false;
                }
            } else {
                if ($fromFile instanceof File) {
                    $fileSystem = $fromFile->getFileSystem();
                    $srcFile = $fileSystem->getFileFromPath(
                        substr($filePath, strlen($fileSystem->getRootPath())),
                        true
                    );
                } else {
                    $srcFile = new \Cx\Lib\FileSystem\File($filePath);
                }

                $toPath = $toFile->getAbsolutePath() . $copyPath;
                if ($toFile instanceof File) {
                    $fileSystem = $toFile->getFileSystem();
                    $destFile   = $fileSystem->getFileFromPath(
                        substr($toPath, strlen($fileSystem->getRootPath())),
                        true
                    );
                } else {
                    $destFile = new \Cx\Lib\FileSystem\File($toPath);
                }

                if (!$this->copyFile($srcFile, $destFile)) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Create the directory
     *
     * @param File|Cx\Lib\FileSystem $file      File object
     * @param string                 $directory Directory name
     * @return boolean Status of directory creation
     */
    protected function createDirectory($file, $directory)
    {
        if (empty($directory)) {
            return false;
        }

        $dirPath = $file->getAbsolutePath() . $directory;
        if ($file instanceof File) {
            $fileSystem = $file->getFileSystem();
            return $fileSystem->createDirectory(
                substr($dirPath, strlen($fileSystem->getRootPath()) + 1),
                '',
                true
            );
        } else {
            return \Cx\Lib\FileSystem\FileSystem::make_folder($dirPath);
        }
    }

    /**
     * Create directory if not exists
     *
     * @param File|Cx\Lib\FileSystem $file File object
     * @return boolean Status of created directory
     */
    protected function createFilePath($file, $isDirectory)
    {
        if ($file instanceof File) {
            $dirPath    = '';
            $fileSystem = $file->getFileSystem();
            if ($isDirectory && !$fileSystem->fileExists($file)) {
                $dirPath = substr(
                    $file->getAbsolutePath(),
                    strlen($fileSystem->getRootPath()) + 1
                );
            } else {
                $directory = $fileSystem->getFileFromPath($file->getPath(), true);
                if (!$fileSystem->fileExists($directory)) {
                    $dirPath = ltrim($file->getPath(), '/');
                }
            }

            if (
                !empty($dirPath) &&
                !$fileSystem->createDirectory($dirPath, '', true)
            ) {
                return false;
            }
        } else {
            if (
                $isDirectory &&
                !\Cx\Lib\FileSystem\FileSystem::exists($file->getAbsolutePath())
            ) {
                $dirPath = $file->getAbsolutePath();
            } else {
                $dirPath = pathinfo($file->getAbsolutePath(), PATHINFO_DIRNAME);
            }

            if (
                !\Cx\Lib\FileSystem\FileSystem::exists($dirPath) &&
                !\Cx\Lib\FileSystem\FileSystem::make_folder($dirPath)
            ) {
                return false;
            }
        }

        return true;
    }

    /**
     * Rename the file if already exists
     *
     * @param File|Cx\Lib\FileSystem $file File object
     * @return File|Cx\Lib\FileSystem File object
     */
    protected function getNewFileIfExists($file)
    {
        if ($file instanceof File) {
            $fileSystem = $file->getFileSystem();
            $fileName   = $file->getName();
            while ($fileSystem->fileExists($file)) {
                $filePath = rtrim($file->getPath(), '/') . '/' . $fileName .
                    '_' . time();
                if ($file->isFile()) {
                    $filePath .= '.' . $file->getExtension();
                }
                $file = $fileSystem->getFileFromPath($filePath, true);
            }
        } else {
            $filePath = $file->getAbsolutePath();
            $fileName = pathinfo($filePath, PATHINFO_FILENAME);
            while (\Cx\Lib\FileSystem\FileSystem::exists($filePath)) {
                $filePath = pathinfo($filePath, PATHINFO_DIRNAME) . '/' .
                    $fileName . '_' . time();
                if ($file->isFile()) {
                    $filePath .= '.' . pathinfo($file->getAbsolutePath(), PATHINFO_EXTENSION);
                }
            }
            $file = new \Cx\Lib\FileSystem\File($filePath);
        }

        return $file;
    }
}
