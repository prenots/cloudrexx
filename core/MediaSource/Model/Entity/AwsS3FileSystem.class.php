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

class AwsS3FileSystem extends LocalFileSystem {

    /**
     * Object of AWS S3 client
     *
     * @var \Aws\S3\S3Client
     */
    protected $s3Client;

    /**
     * Name of the bucket
     *
     * @var string
     */
    protected $bucketName;

    /**
     * Constructor
     *
     * @param string $bucketName        Bucket name
     * @param string $directoryKey      Directory key
     * @param string $credentialsKey    AWS access key ID
     * @param string $credentialsSecret AWS secret access key
     * @param string $region            AWS region
     * @param string $version           AWS version
     * @throws AwsS3FileSystemException
     */
    public function __construct(
        $bucketName,
        $directoryKey,
        $credentialsKey,
        $credentialsSecret,
        $region,
        $version
    ) {
        if (empty($bucketName) || empty($directoryKey)) {
            throw new AwsS3FileSystemException('Bucket name is missing!.');
        }

        // Load the Aws SDK
        $this->cx->getClassLoader()->loadFile(
            $this->cx->getCodeBaseLibraryPath() . '/Aws/aws.phar'
        );

        $this->bucketName   = $bucketName;
        $this->documentPath = 's3://' . $this->bucketName;
        $this->setRootPath(
            $this->documentPath . '/' . rtrim(ltrim($directoryKey, '/'), '/')
        );

        // Initialize the S3 Client object
        $this->initS3Client($version, $region, $credentialsKey, $credentialsSecret);
    }

    /**
     * Initialize the AWS S3 Client and Register the stream wrapper
     *
     * @param string $version           AWS version
     * @param string $region            AWS region
     * @param string $credentialsKey    AWS access key ID
     * @param string $credentialsSecret AWS secret access key
     * @throws AwsS3FileSystemException
     */
    protected function initS3Client(
        $version,
        $region,
        $credentialsKey,
        $credentialsSecret
    ) {
        try {
            $this->s3Client = new \Aws\S3\S3Client(array(
                'version'     => $version,
                'region'      => $region,
                'credentials' => array(
                    'key'    => $credentialsKey,
                    'secret' => $credentialsSecret
                )
            ));
            $this->s3Client->registerStreamWrapper();
        } catch (\Aws\S3\Exception\S3Exception $e) {
            throw new AwsS3FileSystemException(
                'Error in creating AWS S3 Client.',
                '',
                $e->getMessage()
            );
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
                $this->getFullPath($file)
            ),
            '/' . preg_quote($file->getName(), '/') . '.thumb_[a-z]+/'
        );
        foreach ($iterator as $thumbnail) {
            unlink(
                $this->getFullPath($file) . $thumbnail
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function remove(File $file)
    {
        if (
            \FWValidator::isEmpty($file->getFullName()) ||
            \FWValidator::isEmpty($file->getPath()) ||
            !$this->fileExists($file)
        ) {
            return false;
        }

        $filePath = $this->getFullPath($file) . $file->getFullName();
        if ($this->isDirectory($file) && $this->removeDir($file)) {
            return true;
        } elseif ($this->isFile($file) && unlink($filePath)) {
            // If the removing file is image then remove its thumbnail files
            $this->removeThumbnails($file);
            return true;
        } else {
            return false;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function move(File $fromFile, $toFilePath, $ignoreExists = false)
    {
        // Copy the file/directory
        $status = $this->copy($fromFile, $toFilePath, $ignoreExists);

        // Delete the file/directory
        if ($status != 'error' && !$this->remove($fromFile)) {
            $status = 'error';
        }

        return $status;
    }

    /**
     * Copy the directory and its files from source to destination
     *
     * @param string $fromPath  Source file path
     * @param string $toPath    Destination file path
     * @param array  $fileLists List of files to delete(optional)
     * @return boolean Status of copy
     */
    public function copyDir($fromPath, $toPath, $fileLists = array())
    {
        if (empty($fileLists)) {
            $fileLists = $this->getFileList($fromPath);
        }

        if (!$this->fileExists($this->getFileFromPath($toPath, true))) {
            $this->createDirectory(ltrim($toPath, '/'), '');
        }

        $directoryKey = substr($this->getRootPath(), strlen($this->documentPath));
        $toFileKey    = $directoryKey . $toPath;
        $fromFileKey  = $directoryKey . $fromPath;
        foreach ($fileLists as $fileList) {
            if (!isset($fileList['datainfo'])) {
                continue;
            }
            $dataInfo     = $fileList['datainfo'];
            $copyFilePath = substr($dataInfo['filepath'], strlen($fromFileKey));
            $toFilePath   = $toFileKey . $copyFilePath;
            if ($dataInfo['extension'] == 'Dir') {
                $dirPath = substr($toFilePath, strlen($directoryKey) + 1);
                if (!$this->createDirectory($dirPath, '')) {
                    return false;
                }
                $this->copyDir($fromPath, $toPath, $fileList);
            } else {
                if (!$this->copyFile($fromFileKey . $copyFilePath, $toFilePath)) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Copy the file from source to destination path
     *
     * @param string $fromFileKey Source file key
     * @param string $toFileKey   Destination file key
     * @return boolean Status of file copy
     */
    public function copyFile($fromFileKey, $toFileKey)
    {
        if (empty($fromFileKey) || empty($toFileKey)) {
            return false;
        }

        try {
            $this->s3Client->copyObject(array(
                'Bucket'     => $this->bucketName,
                'Key'        => ltrim($toFileKey, '/'),
                'CopySource' => urlencode($this->bucketName . $fromFileKey),
            ));
            return true;
        } catch (\Aws\S3\Exception\S3Exception $e) {
            \DBG::log($e->getMessage());
            return false;
        }
    }

    /**
     * Remove the directory
     *
     * @param File  $file   File object
     * @param array $files  List of files to delete(optional)
     * @return boolean Status of the directory remove
     */
    public function removeDir(File $file, $files = array())
    {
        $removeSrcDir = false;
        if (empty($files)) {
            $files = $this->getFileList(
                rtrim($file->getPath(), '/') . '/' . $file->getFullName()
            );
            $removeSrcDir = $this->fileExists($file);
        }

        foreach ($files as $fileList) {
            if (!isset($fileList['datainfo'])) {
                continue;
            }
            $dataInfo = $fileList['datainfo'];
            if ($dataInfo['extension'] == 'Dir') {
                $this->removeDir($file, $fileList);
                if (!rmdir($this->documentPath . $dataInfo['filepath'])) {
                    return false;
                }
            } else {
                $filePath = $this->documentPath . $dataInfo['filepath'];
                if (!unlink($filePath)) {
                    return false;
                }
                // If the removing file is image then remove its thumbnail files
                $this->removeThumbnails(
                    $this->getFileFromPath(
                        substr($filePath, strlen($this->getRootPath())),
                        true
                    )
                );
            }
        }

        if (
            $removeSrcDir &&
            !rmdir($this->getFullPath($file) . $file->getFullName())
        ) {
            return false;
        }

        return true;
    }

    /**
     * Create directory
     *
     * @param string  $path      File path
     * @param string  $directory Directory name
     * @param boolean $recursive If true then create the directory recursively
     *                           otherwise not
     * @return boolean status of directory creation
     */
    public function createDirectory($path, $directory, $recursive = false)
    {
        if (
            !mkdir(
                $this->getRootPath() . '/' . $path . '/' . $directory ,
                \Cx\Lib\FileSystem\FileSystem::CHMOD_FOLDER,
                $recursive
            )
        ) {
            return false;
        }

        return true;
    }

    /**
     * Make a File writable
     *
     * @param File $file
     * @return boolean True if file writable, false otherwise
     */
    public function makeWritable(File $file) { return true; }

    /**
     * {@inheritdoc}
     */
    public function copy(File $fromFile, $toFilePath, $ignoreExists = false)
    {
        if (
            !$this->fileExists($fromFile) ||
            empty($toFilePath) ||
            !\FWValidator::is_file_ending_harmless($toFilePath)
        ) {
            return 'error';
        }

        $toFile = $this->getFileFromPath($toFilePath, true);
        if (
            $this->isFile($fromFile) &&
            $fromFile->getExtension() !== $toFile->getExtension()
        ) {
            $toFile = $this->getFileFromPath(
                rtrim($toFile->getPath(), '/') . '/' . $toFile->getName()
                . '.' . $fromFile->getExtension(),
                true
            );
        }

        if (!$ignoreExists) {
            // Rename the file/directory if already exists
            $toFileName = $toFile->getName();
            while ($this->fileExists($toFile)) {
                $filePath = rtrim($toFile->getPath(), '/') . '/' . $toFileName .
                    '_' . time();
                if (!$this->isDirectory($fromFile)) {
                    $filePath .= '.' . $toFile->getExtension();
                }
                $toFile = $this->getFileFromPath($filePath, true);
            }
        }

        // Copy the file/directory
        $status   = $toFile->getFullName();
        $fromPath = $fromFile->__toString();
        $toPath   = $toFile->__toString();
        if ($this->isDirectory($fromFile) && !$this->copyDir($fromPath, $toPath)) {
            $status = 'error';
        }

        $directoryKey = substr($this->getRootPath(), strlen($this->documentPath));
        if (
            $this->isFile($fromFile) &&
            !$this->copyFile($directoryKey . $fromPath, $directoryKey . $toPath)
        ) {
            $status = 'error';
        }

        return $status;
    }
}
