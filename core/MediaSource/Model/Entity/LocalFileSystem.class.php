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
 * LocalFileSystem
 *
 * @copyright   Cloudrexx AG
 * @author      Robin Glauser <robin.glauser@comvation.com>
 * @package     cloudrexx
 * @subpackage  core_mediasource
 */

namespace Cx\Core\MediaSource\Model\Entity;

/**
 * LocalFileSystem
 *
 * @copyright   Cloudrexx AG
 * @author      Robin Glauser <robin.glauser@comvation.com>
 * @package     cloudrexx
 * @subpackage  core_mediasource
 */

class LocalFileSystem extends \Cx\Model\Base\EntityBase implements FileSystem {

    /**
     * The path of the file system.
     * Without ending directory separator.
     *
     * @var string
     */
    private $rootPath;

    /**
     * List of cached files
     *
     * @var array
     */
    protected $fileListCache;

    /**
     * Document root path
     *
     * @var string
     */
    protected $documentPath;

    /**
     * Constructor
     *
     * @param string $path File path
     * @throws \InvalidArgumentException
     */
    function __construct($path)
    {
        if (!$path) {
            throw new \InvalidArgumentException(
                'Path shouldn\'t be empty: Given: ' . $path
            );
        }
        $this->rootPath     = rtrim($path, '/');
        $this->documentPath = $this->cx->getWebsiteDocumentRootPath();
    }

    /**
     * Create FileSystem object from path
     *
     * @param string $path File path
     * @return LocalFileSystem
     */
    public static function createFromPath($path)
    {
        return new self($path);
    }

    /**
     * Check whether the file exists or not
     *
     * @param File $file File object
     * @return boolean True when exists, false otherwise
     */
    public function fileExists(File $file)
    {
        return file_exists($this->getFullPath($file) . $file->getFullName());
    }

    /**
     * Get the file list
     *
     * @param File    $directory Directory path
     * @param boolean $recursive If true, recursively parse $directory and list all the files
     *                           otherwise list all files under the $directory
     * @param boolean $readonly  readOnly
     * @return array Array of file list
     */
    public function getFileList($directory, $recursive = true, $readonly = false)
    {
        $directory = $this->getFileFromPath($directory, true);
        $dirPath   = $this->getFullPath($directory) . $directory->getFullName();
        if (isset($this->fileListCache[$dirPath][$recursive][$readonly])) {
            return $this->fileListCache[$dirPath][$recursive][$readonly];
        }

        if (!$this->fileExists($directory)) {
            return array();
        }

        $regex = '/^((?!thumb(_[a-z]+)?).)*$/';
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
                $preview     = current($thumbnails);
                $previewPath = substr($this->documentPath . $preview, strlen($this->rootPath));
                if (!$this->fileExists($this->getFileFromPath($previewPath, true))) {
                    $hasPreview = false;
                }
            }

            $size      = \FWSystem::getLiteralSizeFormat($file->getSize());
            $fileInfos = array(
                'filepath'   => mb_strcut(
                    $file->getPath() . '/' . $file->getFilename(),
                    mb_strlen($this->documentPath)
                ),
                // preselect in mediabrowser or mark a folder
                'name'       => $file->getFilename(),
                'size'       => $size ? $size : '0 B',
                'cleansize'  => $file->getSize(),
                'extension'  => ucfirst(mb_strtolower($extension)),
                'preview'    => $preview,
                'hasPreview' => $hasPreview,
                'active'     => false, // preselect in mediabrowser or mark a folder
                'type'       => $file->getType(),
                'thumbnail'  => $thumbnails
            );

            if ($readonly){
                $fileInfos['readonly'] = true;
            }

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
                    $depth >= 0; $depth--
                ) {
                    $path = array(
                        $iteratorIterator->getSubIterator($depth)->current()->getFilename() => $path
                    );
                }
            }
            $jsonFileArray = $this->array_merge_recursive($jsonFileArray, $path);
        }
        $jsonFileArray = $this->utf8EncodeArray($jsonFileArray);
        $this->fileListCache[$directory][$recursive][$readonly] = $jsonFileArray;

        return $jsonFileArray;
    }

    /**
     * Applies utf8_encode() to keys and values of an array
     * From: http://stackoverflow.com/questions/7490105/array-walk-recursive-modify-both-keys-and-values
     *
     * @param array $array Array to encode
     * @return array UTF8 encoded array
     */
    public function utf8EncodeArray($array)
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
     * Check the given argument $extension as image extension
     *
     * @param string $extension File extenstion
     * @return boolean True if the $extension is image extension otherwise false
     */
    public function isImage($extension)
    {
        return preg_match('/(jpg|jpeg|gif|png)/i', $extension);
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
                    strlen($this->documentPath)
                )
            );
        }

        return $thumbnails;
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
        if (
            $this->isDirectory($file) &&
            \Cx\Lib\FileSystem\FileSystem::delete_folder($filePath, true)
        ) {
            $removeStatus = true;
        } elseif (
            $this->isFile($file) &&
            \Cx\Lib\FileSystem\FileSystem::delete_file($filePath)
        ) {
            // If the removing file is image then remove its thumbnail files
            $this->removeThumbnails($file);
            $removeStatus = true;
        } else {
            $removeStatus = false;
        }

        return $removeStatus;
    }

    /**
     * Move the file/directory
     *
     * @param File   $fromFile   Source file object
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

        // Create the $toFile's directory if does not exists
        $toFile = $this->getFileFromPath($toFilePath, true);
        if (
            !$this->fileExists(
                $this->getFileFromPath($toFile->getPath(), true)
            ) &&
            !$this->createDirectory(ltrim($toFile->getPath(), '/'), '', true)
        ) {
            return false;
        }

        $destFileName = $toFile->getFullName();
        if (!$this->isDirectory($fromFile)) {
            $destFileName = $toFile->getName() . '.' . $fromFile->getExtension();
        }

        // If the source and destination file path are same then return success message
        $fromFileName = $this->getFullPath($fromFile) . $fromFile->getFullName();
        $toFileName   = $this->getFullPath($toFile) . $destFileName;
        if ($fromFileName == $toFileName) {
            return true;
        }

        // If the move file is image then remove its thumbnail
        $this->removeThumbnails($fromFile);

        // Move the file/directory using FileSystem
        return \Cx\Lib\FileSystem\FileSystem::move(
            $fromFileName,
            $toFileName,
            false
        );
    }

    /**
     * Write the File
     *
     * @param File   $file    File object
     * @param string $content File content
     * @return boolean Status of File write
     */
    public function writeFile(File $file, $content)
    {
        return file_put_contents(
            $this->getFullPath($file) . $file->getFullName(),
            $content
        );
    }

    /**
     * Read the File
     *
     * @param File $file File object
     * @return string Content of the file
     */
    public function readFile(File $file)
    {
        return file_get_contents(
            $this->getFullPath($file) . $file->getFullName()
        );
    }

    /**
     * Check whether the $file is directory or not
     *
     * @param File $file File object
     * @return boolean True if the $file is a directory otherwise false
     */
    public function isDirectory(File $file)
    {
        return is_dir($this->getFullPath($file) . $file->getFullName());
    }

    /**
     * Check whether the $file is file or not
     *
     * @param File $file File object
     * @return boolean True if the $file is a file otherwise false
     */
    public function isFile(File $file)
    {
        return is_file($this->getFullPath($file) . $file->getFullName());
    }

    public function getLink(
        File $file
    ) {
        // TODO: Implement getLink() method.
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
            !\Cx\Lib\FileSystem\FileSystem::make_folder(
                $this->rootPath . '/' . $path . '/' . $directory,
                $recursive
            )
        ) {
            return false;
        }

        return true;
    }

    /**
     * Get the file full path
     *
     * @param File $file File object
     * @return string Returns the file full path without filename
     */
    public function getFullPath(File $file)
    {
        return $this->rootPath . rtrim(ltrim($file->getPath(), '.') , '/') . '/';
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
            ), '/' . preg_quote($file->getName(), '/') . '.thumb_[a-z]+/'
        );
        foreach ($iterator as $thumbnail) {
            \Cx\Lib\FileSystem\FileSystem::delete_file(
                $thumbnail->getRealPath()
            );
        }
    }

    /**
     * Get Root path of the filesystem
     *
     * @return string
     */
    public function getRootPath()
    {
        return $this->rootPath;
    }

    /**
     * Set root path of the filesystem
     *
     * @param string $rootPath
     */
    public function setRootPath($rootPath)
    {
        $this->rootPath = $rootPath;
    }

    /**
     * Get File object from the path
     *
     * @param string  $filepath File path
     * @param boolean $force    True, return the File object also if the given file not exists or
     *                          False, return the file object if the file exists
     * @return LocalFile File Object
     */
    public function getFileFromPath($filepath, $force = false)
    {
        if ($force) {
            return new LocalFile($filepath, $this);
        }

        $fileinfo = pathinfo($filepath);
        $files    = $this->getFileList($fileinfo['dirname']);
        if (!isset($files[$fileinfo['basename']])) {
            return;
        }

        return new LocalFile($filepath, $this);
    }

    /**
     * Make a File writable
     *
     * @param File $file
     * @return boolean True if file writable, false otherwise
     */
    public function makeWritable(File $file)
    {
        return \Cx\Lib\FileSystem\FileSystem::makeWritable(
            $this->getFullPath($file) . $file->getFullName()
        );
    }

    /**
     * Get the file web path
     *
     * @param File $file File object
     * @return string Returns the file web path without filename
     */
    public function getWebPath(File $file)
    {
        return substr(
            $this->getFullPath($file),
            strlen($this->documentPath)
        );
    }

    /**
     * Copy the file
     *
     * @param File    $fromFile     Source file object
     * @param string  $toFilePath   Destination file path
     * @param boolean $ignoreExists True, if the destination file exists it will be overwritten
     *                              otherwise file will be created with new name
     * @return string Name of the copy file
     */
    public function copyFile(
        File $fromFile,
        $toFilePath,
        $ignoreExists = false
    ) {
        if (
            !$this->fileExists($fromFile) ||
            empty($toFilePath) ||
            !\FWValidator::is_file_ending_harmless($toFilePath)
        ) {
            return false;
        }

        $toFile = $this->getFileFromPath($toFilePath, true);
        if (
            !$this->fileExists(
                $this->getFileFromPath($toFile->getPath(), true)
            ) &&
            !$this->createDirectory(ltrim($toFile->getPath(), '/'), '', true)
        ) {
            return false;
        }

        $fileSystem = new \Cx\Lib\FileSystem\FileSystem();
        return $fileSystem->copyFile(
            $this->getFullPath($fromFile),
            $fromFile->getFullName(),
            $this->getFullPath($toFile),
            $toFile->getFullName(),
            $ignoreExists
        );
    }
}
