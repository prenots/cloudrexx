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
     * Directory key
     *
     * @var string
     */
    protected $directoryKey;

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
        if (empty($bucketName)) {
            throw new AwsS3FileSystemException('Bucket name is missing!.');
        }

        // Load the Aws SDK
        $this->cx->getClassLoader()->loadFile(
            $this->cx->getCodeBaseLibraryPath() . '/Aws/aws.phar'
        );

        $this->directoryKey    = rtrim($directoryKey, '/');
        $this->directoryPrefix = 's3://' . $bucketName . '/';

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
            $clientInstance = new \Aws\S3\S3Client(array(
                'version'     => $version,
                'region'      => $region,
                'credentials' => array(
                    'key'    => $credentialsKey,
                    'secret' => $credentialsSecret
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

    /**
     * Get the file list
     *
     * @param string  $directory Directory path
     * @param boolean $recursive If true recursively parse $directory and list all the files
     *                           otherwise list all files under the $directory
     * @return array Array of file list
     */
    public function getFileList($directory, $recursive = true)
    {
        if (isset($this->fileListCache[$directory][$recursive])) {
            return $this->fileListCache[$directory][$recursive];
        }

        if (!$this->isFileExists(new LocalFile(rtrim($directory, '/'), $this))) {
            return array();
        }

        $dirPath = $this->directoryPrefix . $this->directoryKey . rtrim($directory, '/');
        $regex   = '/^((?!thumb(_[a-z]+)?).)*$/';
        if ($recursive) {
            $iteratorIterator = new \RegexIterator(
                new \RecursiveIteratorIterator(
                    new \RecursiveDirectoryIterator($dirPath),
                    \RecursiveIteratorIterator::SELF_FIRST
                ),
                $regex
            );
        } else {
            $iteratorIterator = new \RegexIterator(
                new \IteratorIterator(new \DirectoryIterator($dirPath)),
                $regex
            );
        }

        $jsonFileArray = array();
        $thumbnailList = $this->cx->getMediaSourceManager()
            ->getThumbnailGenerator()
            ->getThumbnails();

        foreach ($iteratorIterator as $file) {
            /**
             * @var $file \SplFileInfo
             */
            $extension = 'Dir';
            if (!$file->isDir()) {
                $extension = strtolower(
                    pathinfo($file->getFilename(), PATHINFO_EXTENSION)
                );
            }

            // filters
            if (
                $file->getFilename() == '.' ||
                $file->getFilename() == 'index.php' ||
                strpos($file->getFilename(), '.') === 0
            ) {
                continue;
            }

            // set preview if image
            $preview    = 'none';
            $hasPreview = false;
            $thumbnails = array();
            if ($this->isImage($extension)) {
                $hasPreview = true;
                $thumbnails = $this->getThumbnails(
                    $thumbnailList,
                    $extension,
                    $file
                );
                $preview = current($thumbnails);
                if (
                    !$this->isFileExists(
                        new LocalFile(
                            substr($preview, strlen($this->directoryKey) + 1),
                            $this
                        )
                    )
                ) {
                    $hasPreview = false;
                }
            }

            $size      = \FWSystem::getLiteralSizeFormat($file->getSize());
            $fileInfos = array(
                'filepath'   => mb_strcut(
                    $file->getPath() . '/' . $file->getFilename(),
                    mb_strlen($this->directoryPrefix) - 1
                ),
                // preselect in mediabrowser or mark a folder
                'name'       => $file->getFilename(),
                'size'       => $size ? $size : '0 B',
                'cleansize'  => $file->getSize(),
                'extension'  => ucfirst(mb_strtolower($extension)),
                'preview'    => $preview,
                'hasPreview' => $hasPreview,
                // preselect in mediabrowser or mark a folder
                'active'     => false,
                'type'       => $file->getType(),
                'thumbnail'  => $thumbnails
            );

            // filters
            if (preg_match('/\.thumb/', $fileInfos['name'])) {
                continue;
            }

            $path = array(
                $file->getFilename() => array('datainfo' => $fileInfos)
            );

            if ($recursive) {
                for (
                    $depth = $iteratorIterator->getDepth() - 1;
                    $depth >= 0;
                    $depth--
                ) {
                    $path = array(
                        $iteratorIterator->getSubIterator($depth)->current()->getFilename() => $path
                    );
                }
            }
            $jsonFileArray = $this->array_merge_recursive($jsonFileArray, $path);
        }
        $jsonFileArray = $this->utf8EncodeArray($jsonFileArray);
        $this->fileListCache[$directory][$recursive] = $jsonFileArray;

        return $jsonFileArray;
    }

    /**
     * Applies utf8_encode() to keys and values of an array
     * From: http://stackoverflow.com/questions/7490105/array-walk-recursive-modify-both-keys-and-values
     *
     * @param array $array Array to encode
     * @return array UTF8 encoded array
     */
    protected function utf8EncodeArray($array)
    {
        $helper = array();
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $value = $this->utf8EncodeArray($value);
            } else {
                $value = utf8_encode($value);
            }
            $helper[utf8_encode($key)] = $value;
        }
        return $helper;
    }

    /**
     * \array_merge_recursive() behaves unexpected with numerical indexes
     * Fix from http://php.net/array_merge_recursive (array_merge_recursive_new)
     *
     * This method behaves differently than the original since it overwrites
     * already present keys
     *
     * @return array Recursively merged array
     */
    protected function array_merge_recursive()
    {
        $arrays = func_get_args();
        $base = array_shift($arrays);

        foreach ($arrays as $array) {
            reset($base); //important
            while (list($key, $value) = each($array)) {
                if (is_array($value) && isset($base[$key]) && is_array($base[$key])) {
                    $base[$key] = $this->array_merge_recursive($base[$key], $value);
                } else {
                    $base[$key] = $value;
                }
            }
        }

        return $base;
    }

    /**
     * Check whether the file exists in the filesytem
     *
     * @param File $file LocalFile object
     * @return boolean true if the file or directory specified by
     *                 filename exists otherwise false
     */
    public function isFileExists(File $file)
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
     * @return string Status message of the file remove
     */
    public function removeFile(File $file)
    {
        $arrLang  = \Env::get('init')->loadLanguageData('MediaBrowser');
        $filename = $file->getFullName();
        $strPath  = $file->getPath();
        if (empty($filename) || empty($strPath)) {
            return sprintf(
                $arrLang['TXT_FILEBROWSER_FILE_UNSUCCESSFULLY_REMOVED'],
                $filename
            );
        }

        $directoryPath = $this->directoryPrefix . $this->getFullPath($file) . $filename;
        if (is_dir($directoryPath)) {
            if (rmdir($directoryPath)) {
                return sprintf(
                    $arrLang['TXT_FILEBROWSER_DIRECTORY_SUCCESSFULLY_REMOVED'],
                    $filename
                );
            }
            return sprintf(
                $arrLang['TXT_FILEBROWSER_DIRECTORY_UNSUCCESSFULLY_REMOVED'],
                $filename
            );
        }

        if (unlink($directoryPath)) {
            // If the removing file is image then remove its thumbnail files
            $this->removeThumbnails($file);
            return sprintf(
                $arrLang['TXT_FILEBROWSER_FILE_SUCCESSFULLY_REMOVED'],
                $filename
            );
        }

        return sprintf(
            $arrLang['TXT_FILEBROWSER_FILE_UNSUCCESSFULLY_REMOVED'],
            $filename
        );
    }

    /**
     * Get Thumbnails
     *
     * @param array  $thumbnailList Array of thumbnails list
     * @param string $extension     File extension
     * @param object $file          File object
     * @return array Array of thumbnails
     */
    public function getThumbnails(
        $thumbnailList,
        $extension,
        $file
    ) {
        $thumbnails = array();
        foreach ($thumbnailList as $thumbnail) {
            $thumbnails[$thumbnail['size']] = preg_replace(
                '/\.' . $extension . '$/i',
                $thumbnail['value'] . '.' . strtolower($extension),
                substr(
                    $file->getPath() . '/' . $file->getFilename(),
                    strlen($this->directoryPrefix) - 1
                )
            );
        }

        return $thumbnails;
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
                $this->directoryPrefix . $this->getFullPath($file) . $thumbnail
            );
        }
    }

    /**
     * Check the given argument $extension as image extension
     *
     * @param string $extension File extenstion
     * @return boolean True if the $extension is image extension otherwise false
     */
    public function isImage($extension)
    {
        return preg_match("/(jpg|jpeg|gif|png)/i", $extension);
    }

    /**
     * Get the file full path
     *
     * @param File $file File object
     * @return string Full path of the file
     */
    public function getFullPath(File $file)
    {
        return $this->directoryKey . rtrim(ltrim($file->getPath(), '.'), '/') . '/';
    }

    /**
     * Move the file
     *
     * @param File   $fromFile   File object
     * @param string $toFilePath Destination file path
     * @return string Status message of file move
     */
    public function moveFile(File $fromFile, $toFilePath)
    {
        $arrLang  = \Env::get('init')->loadLanguageData('MediaBrowser');
        $errorMsg = $arrLang['TXT_FILEBROWSER_FILE_UNSUCCESSFULLY_RENAMED'];
        if (
            !$this->isFileExists($fromFile) ||
            empty($toFilePath) ||
            !\FWValidator::is_file_ending_harmless($toFilePath)
        ) {
            return sprintf($errorMsg, $fromFile->getFullName());
        }

        // Create the $toFile's directory if does not exists
        $toFile = new LocalFile($toFilePath, $fromFile->getFileSystem());
        if (!file_exists($this->directoryPrefix . $this->getFullPath($toFile))) {
            if (!mkdir($this->directoryPrefix . $this->getFullPath($toFile), '0777')) {
                return sprintf($errorMsg, $fromFile->getName());
            }
        }

        $destFileName = $toFile->getFullName();
        if (!$this->isDirectory($fromFile)) {
            $destFileName = $toFile->getName() . '.' . $fromFile->getExtension();
        }

        // If the source and destination file path are same then return success message
        $fromFileName = $this->getFullPath($fromFile) . $fromFile->getFullName();
        $toFileName   = $this->getFullPath($toFile) . $destFileName;
        if ($fromFileName == $toFileName) {
            return sprintf(
                $arrLang['TXT_FILEBROWSER_FILE_SUCCESSFULLY_RENAMED'],
                $fromFile->getName()
            );
        }

        // If the move file is image then remove its thumbnail
        $this->removeThumbnails($fromFile);

        // Move the file/directory using FileSystem
        if (
            !rename(
                $this->directoryPrefix . $fromFileName,
                $this->directoryPrefix . $toFileName
            )
        ) {
            return sprintf($errorMsg, $fromFile->getName());
        }

        return sprintf(
            $arrLang['TXT_FILEBROWSER_FILE_SUCCESSFULLY_RENAMED'],
            $fromFile->getName()
        );
    }

    /**
     * Write the File
     *
     * @param File $file File object
     * @throws AwsS3FileSystemException
     */
    public function writeFile(File $file, $content)
    {
        $filePath =
            $this->directoryPrefix . $this->getFullPath($file) . $file->getFullName();
        $stream = fopen($filePath, 'w');
        if (!$stream) {
            throw new AwsS3FileSystemException(
                'Unable to open file ' . $filePath . ' for writing!'
            );
        }

        // write data to file
        $writeStatus = fwrite($stream, $content);

        fclose($stream);

        if ($writeStatus === false) {
            throw new AwsS3FileSystemException(
                'Unable to write data to file ' . $filePath . '!'
            );
        }
    }

    /**
     * Read the File
     *
     * @param File $file File object
     * @throws AwsS3FileSystemException
     * @return string Content of the file
     */
    public function readFile(File $file)
    {
        $filePath = $this->directoryPrefix . $this->getFullPath($file) .
            $file->getFullName();
        if (!$this->isFileExists($file)) {
            throw new AwsS3FileSystemException(
                'Unable to read data from file ' . $filePath . '!'
            );
        }

        $content = file_get_contents($filePath);
        if ($content === false) {
            throw new AwsS3FileSystemException(
                'Unable to read data from file ' . $filePath . '!'
            );
        }

        return $content;
    }

    /**
     * Check whether the $file is directory or not
     *
     * @param File $file File object
     * @return boolean True if the $file is a directory otherwise false
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
     * @return boolean True if the $file is a file otherwise false
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
     * @return string Status message
     */
    public function createDirectory($path, $directory)
    {
        $arrLang = \Env::get('init')->loadLanguageData('MediaBrowser');
        if (
            !mkdir(
                $this->directoryPrefix . $this->directoryKey . '/' . $path .
                '/' . $directory ,
                '0777'
            )
        ) {
            return sprintf(
                $arrLang['TXT_FILEBROWSER_UNABLE_TO_CREATE_FOLDER'],
                $directory
            );
        }
        return sprintf(
            $arrLang['TXT_FILEBROWSER_DIRECTORY_SUCCESSFULLY_CREATED'],
            $directory
        );
    }

    /**
     * Get file from path
     *
     * @param string $filepath File path
     * @return LocalFile File object
     */
    public function getFileFromPath($filepath)
    {
        $fileinfo = pathinfo($filepath);
        $files    = $this->getFileList($fileinfo['dirname']);
        if (!isset($files[$fileinfo['basename']])) {
            return;
        }
        return new LocalFile($filepath, $this);
    }

    public function getWebPath(File $file) {}
}
