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
 * AwsS3FileSystem
 *
 * @copyright   Cloudrexx AG
 * @author      Project Team SS4U <info@cloudrexx.com>
 * @package     cloudrexx
 * @subpackage  core_mediasource
 */

namespace Cx\Core\MediaSource\Model\Entity;

/**
 * AwsS3FileSystemException
 *
 * @copyright   Cloudrexx AG
 * @author      Project Team SS4U <info@cloudrexx.com>
 * @package     cloudrexx
 * @subpackage  core_mediasource
 */

class AwsS3FileSystemException extends \Exception {}

/**
 * AwsS3FileSystem
 *
 * @copyright   Cloudrexx AG
 * @author      Project Team SS4U <info@cloudrexx.com>
 * @package     cloudrexx
 * @subpackage  core_mediasource
 */

class AwsS3FileSystem extends \Cx\Model\Base\EntityBase implements FileSystem {
    /**
     * Bucket name
     *
     * @var string
     */
    protected $bucketName;

    /**
     * Directory key
     *
     * @var string
     */
    protected $directoryKey;

    /**
      * Region
      *
      * @var string
      */
     protected $region;

     /**
      * AWS access key ID
      *
      * @var string
      */
     protected $credentialsKey;

     /**
      * AWS secret access key
      *
      * @var string
      */
     protected $credentialsSecret;

     /**
      * AWS version
      *
      * @var string
      */
     protected $version;

    /**
     * RootPath
     *
     * @var string
     */
    protected $rootPath;

    /**
     * Directory prefix with S3 protocol
     *
     * @var string
     */
    protected $directoryPrefix;

    /**
     * List of cached files
     *
     * @var array
     */
    protected $fileListCache;

    /**
     * Constructor
     *
     * @param string $bucketName        Bucket name
     * @param string $directoryKey      Directory key
     * @param string $credentialsKey    AWS access key ID
     * @param string $credentialsSecret AWS secret access key
     * @param string $region            AWS region
     * @param string $version           AWS version
     */
    public function __construct(
        $bucketName,
        $directoryKey,
        $credentialsKey,
        $credentialsSecret,
        $region,
        $version
    ) {
        if (empty($bucketName)) {
            throw new AwsS3FileSystemException('Bucket name is missing!.');
        }

        // Load the Aws SDK
        $this->cx->getClassLoader()->loadFile(
            $this->cx->getCodeBaseLibraryPath() . '/Aws/aws.phar'
        );

        $this->rootPath     = rtrim($directoryKey, '/');
        $this->bucketName   = $bucketName;
        $this->directoryKey = $directoryKey;
        $this->region            = $region;
        $this->credentialsKey    = $credentialsKey;
        $this->credentialsSecret = $credentialsSecret;
        $this->version           = $version;
        $this->directoryPrefix   = 's3://' . $this->bucketName . '/';
        // Initialize the S3 Client object
        $this->initS3Client();
    }

    /**
     * Initialize the AWS S3 Client and Register the stream wrapper
     *
     * @throws AwsS3FileSystemException
     */
    protected function initS3Client()
    {
        try {
            $clientInstance = new \Aws\S3\S3Client(array(
                'version'     => $this->version,
                'region'      => $this->region,
                'credentials' => array(
                    'key'    => $this->credentialsKey,
                    'secret' => $this->credentialsSecret
                )
            ));
            $clientInstance->registerStreamWrapper();
        } catch (\Aws\S3\Exception\S3Exception $e) {
            throw new AwsS3FileSystemException(
                'Error in creating AWS S3 Client.',
                '',
                $e->getMessage()
            );
        }
    }

    public function getFileList($directory, $recursive = true) {}

    /**
     * Check whether the file exists in the filesytem
     *
     * @param File $file LocalFile object
     * @return boolean true if the file or directory specified by
     *                 filename exists otherwise false
     */
    public function fileExists(File $file)
    {
        return file_exists(
            $this->directoryPrefix . $this->getFullPath($file) .
            $file->getFullName()
        );
    }

    /**
     * Remove the file
     *
     * @param LocalFile $file File object
     * @return string status message of the file remove
     */
    public function removeFile(File $file)
    {
        global $_ARRAYLANG;

        $filename = $file->getFullName();
        $strPath  = $file->getPath();
        if (empty($filename) || empty($strPath)) {
            return sprintf(
                $_ARRAYLANG['TXT_FILEBROWSER_FILE_UNSUCCESSFULLY_REMOVED'],
                $filename
            );
        }

        $directoryPath = $this->directoryPrefix . $this->getFullPath($file) . $filename;
        if (is_dir($directoryPath)) {
            if (rmdir($directoryPath)) {
                return sprintf(
                    $_ARRAYLANG['TXT_FILEBROWSER_DIRECTORY_SUCCESSFULLY_REMOVED'],
                    $filename
                );
            } else {
                return sprintf(
                    $_ARRAYLANG['TXT_FILEBROWSER_DIRECTORY_UNSUCCESSFULLY_REMOVED'],
                    $filename
                );
            }
        } else {
            if (unlink($directoryPath)) {
                // If the removing file is image then remove its thumbnail files
                $this->removeThumbnails($file);
                return sprintf(
                    $_ARRAYLANG['TXT_FILEBROWSER_FILE_SUCCESSFULLY_REMOVED'],
                    $filename
                );
            } else {
                return sprintf(
                    $_ARRAYLANG['TXT_FILEBROWSER_FILE_UNSUCCESSFULLY_REMOVED'],
                    $filename
                );
            }
        }
    }

    /**
     * Remove thumbnails
     *
     * @param File $file File object
     */
    public function removeThumbnails(File $file)
    {
        if (!$this->isImage($file->getExtension())) {
            return;
        }

        $iterator = new \RegexIterator(
            new \DirectoryIterator(
                $this->directoryPrefix . $this->getFullPath($file)
            ),
            '/' . preg_quote($file->getName(), '/') . '.thumb_[a-z]+/'
        );
        foreach ($iterator as $thumbnail) {
            unlink(
                $this->directoryPrefix . $this->getFullPath($file) .
                '/' . $thumbnail
            );
        }
    }

    /**
     * Check the given argument $extension as image extension
     *
     * @param string $extension File extenstion
     * @return boolean true if the $extension is image extension otherwise false
     */
    public function isImage($extension)
    {
        return preg_match("/(jpg|jpeg|gif|png)/i", $extension);
    }

    /**
     * Get the file full path
     *
     * @param File $file File object
     * @return string full path of the file
     */
    public function getFullPath(File $file)
    {
        return $this->rootPath . ltrim($file->getPath(), '.') . '/';
    }

    public function moveFile(File $file, $destination) {}

    /**
     * Write the File
     *
     * @param File $file File object
     */
    public function writeFile(File $file, $content)
    {
        file_put_contents(
            $this->directoryPrefix . $this->getFullPath($file) .
            $file->getFullName(),
            $content
        );
    }

    /**
     * Read the File
     *
     * @param File $file File object
     * @return string content of the file
     */
    public function readFile(File $file)
    {
        return file_get_contents(
            $this->directoryPrefix . $this->getFullPath($file) .
            $file->getFullName()
        );
    }

    /**
     * Check whether the $file is directory or not
     *
     * @param File $file File object
     * @return boolean true if the $file is a directory otherwise false
     */
    public function isDirectory(File $file)
    {
        return is_dir(
            $this->directoryPrefix . $this->getFullPath($file) .
            $file->getFullName()
        );
    }

    /**
     * Check whether the $file is file or not
     *
     * @param File $file File object
     * @return boolean true if the $file is a file otherwise false
     */
    public function isFile(File $file)
    {
        return is_file(
            $this->directoryPrefix . $this->getFullPath($file) .
            $file->getFullName()
        );
    }

    public function getLink(File $file) {}

    /**
     * Create a directory
     *
     * @param string $path      Directory path
     * @param string $directory Directory name
     * @return string status message
     */
    public function createDirectory($path, $directory)
    {
        global $_ARRAYLANG;

        \Env::get('init')->loadLanguageData('MediaBrowser');
        if (!mkdir($this->directoryPrefix . $path . '/' . $directory)) {
            return sprintf(
                $_ARRAYLANG['TXT_FILEBROWSER_UNABLE_TO_CREATE_FOLDER'],
                $directory
            );
        } else {
            return sprintf(
                $_ARRAYLANG['TXT_FILEBROWSER_DIRECTORY_SUCCESSFULLY_CREATED'],
                $directory
            );
        }
    }

    public function getFileFromPath($path) {}
}