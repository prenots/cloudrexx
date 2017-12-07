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
     * @param string $path File path
     * @return LocalFile
     */
    public function getMediaSourceFileFromPath($path)
    {
        // If the path does not have leading backslash then add it
        if (strpos($path, '/') !== 0) {
            $path = '/' . $path;
        }

        try {
            // Get MediaSource and MediaSourceFile object
            $mediaSource     = $this->getMediaSourceByPath($path);
            $mediaSourcePath = $mediaSource->getDirectory();
            $mediaSourceFile = $mediaSource->getFileSystem()
                ->getFileFromPath(substr($path, strlen($mediaSourcePath[1])));
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
     * @return \Cx\Core\MediaSource\Model\Entity\MediaSource MediaSource object
     * @throws MediaSourceManagerException
     */
    public function getMediaSourceByPath($path)
    {
        foreach ($this->mediaTypes as $mediaSource) {
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
     * Move file from one filesystem to other filesystem
     *
     * @param string $sourcePath      Source filepath
     * @param string $destinationPath Destination filepath
     * @return boolean status of file move
     */
    public function moveFile($sourcePath, $destinationPath)
    {
        // Get source file object
        $isSourceLocalFile = true;
        $sourceFile        = $this->getMediaSourceFileFromPath($sourcePath);
        if (!$sourceFile) {
            // The source file is a local file
            $sourcePath = $this->cx->getWebsitePath() . '/' . $sourcePath;
            // Return If the source file does not exists
            if (!\Cx\Lib\FileSystem\FileSystem::exists($sourcePath)) {
                return false;
            }
            $sourceFileExtension = pathinfo($sourcePath, PATHINFO_EXTENSION);
            $isSourceDir         = is_dir($sourcePath);
        } else {
            // Return If the source file does not exists
            if (!$sourceFile->getFileSystem()->fileExists($sourceFile)) {
                return false;
            }

            // Check whether the source file is local file or S3 file
            if ($sourceFile->getFileSystem() instanceof AwsS3FileSystem) {
                $isSourceLocalFile = false;
            }

            $sourcePath =
                $sourceFile->getFileSystem()->getFullPath($sourceFile) .
                $sourceFile->getFullName();
            $sourceFileExtension = $sourceFile->getExtension();
            $isSourceDir = $sourceFile->getFileSystem()->isDirectory($sourceFile);
        }

        // Get destination file object
        if (strpos($destinationPath, '/') !== 0) {
            $destinationPath = '/' . $destinationPath;
        }

        try {
            $isDestinationLocalFile = true;
            $mediaSource     = $this->getMediaSourceByPath($destinationPath);
            $mediaSourcePath = $mediaSource->getDirectory();
            $filePath = substr($destinationPath, strlen($mediaSourcePath[1]));
            // Create destination file object by the $destinationPath
            if ($mediaSource->getFileSystem() instanceof \Cx\Core\ViewManager\Model\Entity\ViewManagerFileSystem) {
                $destinationFile = new \Cx\Core\ViewManager\Model\Entity\ViewManagerFile(
                    $filePath,
                    $mediaSource->getFileSystem()
                );
            } else {
                $destinationFile = new LocalFile($filePath, $mediaSource->getFileSystem());
            }

            // Check if the destination file directory exists otherwise
            // try to create the directory
            if (!$mediaSource->getFileSystem()->isDirectoryExists($destinationFile)) {
                $destinationFile->getFileSystem()->createDirectory(
                    ltrim($destinationFile->getPath(), '/')
                );
            }

            // Return if the destination file directory is not exists
            if (!$mediaSource->getFileSystem()->isDirectoryExists($destinationFile)) {
                return false;
            }

            // Check whether the destination file is local file or S3 file
            if ($destinationFile->getFileSystem() instanceof AwsS3FileSystem) {
                $isDestinationLocalFile = false;
            }

            $destinationFileName = $destinationFile->getFullName();
            if (!$isSourceDir) {
                $destinationFileName =
                    $destinationFile->getName() . '.' . $sourceFileExtension;
            }
            $destinationPath =
                $destinationFile->getFileSystem()->getFullPath($destinationFile) .
                $destinationFileName;
        } catch(MediaSourceManagerException $e) {
            // The Destination file is a local file
            $destinationPath = $this->cx->getWebsitePath() . $destinationPath;
            $dirPath         = pathinfo($destinationPath, PATHINFO_DIRNAME);
            if (!is_dir($sourcePath)) {
                $destinationPath = $dirPath . '/' .
                    pathinfo($destinationPath, PATHINFO_FILENAME) .
                    '.' . $sourceFileExtension;
            }
            // Check if the destination file directory exists otherwise
            // try to create the directory if does not then call return.
            if (!\Cx\Lib\FileSystem\FileSystem::exists($dirPath)) {
                if (!\Cx\Lib\FileSystem\FileSystem::make_folder($dirPath)) {
                    return false;
                }
            }
        }

        // Move local File to local File
        if ($isSourceLocalFile && $isDestinationLocalFile) {
            return \Cx\Lib\FileSystem\FileSystem::move(
                $sourcePath,
                $destinationPath,
                false
            );
        }

        // Move s3 File to s3 File
        if (!$isSourceLocalFile && !$isDestinationLocalFile) {
            return rename(
                $sourceFile->getFileSystem()->getDirectoryPrefix() . $sourcePath,
                $destinationFile->getFileSystem()->getDirectoryPrefix() . $destinationPath
            );
        }

        // Move local File to s3 File
        if ($isSourceLocalFile && !$isDestinationLocalFile) {
            return $this->uploadFileToS3($destinationFile, $sourcePath);
        }

        // Move s3 File to local FIle
        if (!$isSourceLocalFile && $isDestinationLocalFile) {
            return $this->downloadFileFromS3($sourceFile, $destinationPath);
        }
    }

    /**
     * Upload a local file into S3
     *
     * @param File   $destinationFile Destination file object
     * @param string $sourcePath      Source file path
     * @return boolean status of file upload
     */
    public function uploadFileToS3(File $destinationFile, $sourcePath)
    {
        $s3Client = $destinationFile->getFileSystem()->getS3Client();
        try {
            $s3Client->putObject(array(
                'Bucket' => $destinationFile->getFileSystem()->getBucketName(),
                'Key'    => $destinationFile->getFileSystem()->getFullPath($destinationFile) .
                    $destinationFile->getFullName(),
                'SourceFile' => $sourcePath
            ));
            return \Cx\Lib\FileSystem\FileSystem::delete_file($sourcePath);
        } catch (\Aws\S3\Exception\S3Exception $e) {
            \DBG::log($e->getMessage());
            return false;
        }
    }

    /**
     * Download File from S3 to Local
     *
     * @param File   $sourceFile      Source file object
     * @param string $destinationPath Destination file path
     * @return boolean status of download
     */
    public function downloadFileFromS3(File $sourceFile, $destinationPath)
    {
        $s3Client = $sourceFile->getFileSystem()->getS3Client();
        try {
            $s3Client->getObject(array(
                'Bucket' => $sourceFile->getFileSystem()->getBucketName(),
                'Key'    => $sourceFile->getFileSystem()->getFullPath($sourceFile) .
                    $sourceFile->getFullName(),
                'SaveAs' => $destinationPath
            ));
            return true;
        } catch (\Aws\S3\Exception\S3Exception $e) {
            \DBG::log($e->getMessage());
            return false;
        }
    }
}
