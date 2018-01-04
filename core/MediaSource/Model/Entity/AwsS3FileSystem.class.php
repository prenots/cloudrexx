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
     * Remove the file
     *
     * @param File $file File object
     * @return boolean Status of the file remove
     */
    public function removeFile(File $file)
    {
        if (
            \FWValidator::isEmpty($file->getFullName()) ||
            \FWValidator::isEmpty($file->getPath()) ||
            !$this->fileExists($file)
        ) {
            return false;
        }

        $filePath = $this->getFullPath($file) . $file->getFullName();
        if ($this->isDirectory($file) && rmdir($filePath)) {
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
     * Move the file
     *
     * @param File   $fromFile   File object
     * @param string $toFilePath Destination file path
     * @return boolean status of file/directory move
     */
    public function moveFile(File $fromFile, $toFilePath)
    {
        if (
            !$this->fileExists($fromFile) ||
            empty($toFilePath) ||
            !\FWValidator::is_file_ending_harmless($toFilePath)
        ) {
            return false;
        }

        $toFile       = new LocalFile($toFilePath, $fromFile->getFileSystem());
        $fromFileName = $this->getFullPath($fromFile) . $fromFile->getFullName();
        $toFileName   = $this->getFullPath($toFile);
        if (!$this->isDirectory($fromFile)) {
            $toFileName = $toFileName . $toFile->getName() . '.' . $fromFile->getExtension();
        } else {
            $fromFileName = $fromFileName . '/';
            $toFileName = $toFileName . $toFile->getFullName() . '/';
        }

        // If the source and destination file path are same then return success message
        if ($fromFileName == $toFileName) {
            return true;
        }

        // Move the file/directory using FileSystem
        $toFileKey   = substr($toFileName, strlen($this->documentPath) + 1);
        $fromFileKey = substr($fromFileName, strlen($this->documentPath) + 1);
        try {
            // Copy the file/directory from source to destination
            $isDirectory = $this->isDirectory($fromFile);
            if ($isDirectory) {
                $iterator = new \RecursiveIteratorIterator(
                    new \RecursiveDirectoryIterator(
                        $fromFileName
                    ),
                    \RecursiveIteratorIterator::SELF_FIRST
                );

                $hasChild = false;
                foreach ($iterator as $file) {
                    $hasChild = true;
                    $filePath = $file->getPath() . '/' . $file->getFilename();
                    if ($file->isDir()) {
                        $filePath = $filePath . '/';
                    }
                    $copyFilePath = substr(
                        $filePath,
                        strlen($fromFileName)
                    );
                    try {
                        $this->s3Client->copyObject(array(
                            'Bucket'     => $this->bucketName,
                            'Key'        => $toFileKey . $copyFilePath,
                            'CopySource' => urlencode(
                                $this->bucketName . '/' . $fromFileKey . $copyFilePath
                            ),
                        ));

                        $this->s3Client->deleteObject(array(
                            'Bucket'     => $this->bucketName,
                            'Key'        => $fromFileKey . $copyFilePath,
                        ));
                    } catch (\Aws\S3\Exception\S3Exception $e) {
                        \DBG::log($e->getMessage());
                        continue;
                    }
                }
            }
            if (!$isDirectory || !$hasChild) {
                $this->s3Client->copyObject(array(
                    'Bucket'     => $this->bucketName,
                    'Key'        => $toFileKey,
                    'CopySource' => urlencode($this->bucketName . '/' . $fromFileKey),
                ));

                // If the move file is image then remove its thumbnail
                $this->removeThumbnails($fromFile);
            }
            // Delete the source file/directory
            $this->s3Client->deleteObject(array(
                'Bucket'     => $this->bucketName,
                'Key'        => $fromFileKey,
            ));
        } catch (\Aws\S3\Exception\S3Exception $e) {
            \DBG::log($e->getMessage());
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
                Cx\Lib\FileSystem\FileSystem::CHMOD_FOLDER,
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
}
