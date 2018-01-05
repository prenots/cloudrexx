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
 * @copyright   Cloudrexx AG
 * @author Robin Glauser <robin.glauser@comvation.com>
 * @package     cloudrexx
 */

namespace Cx\Core\MediaSource\Model\Entity;

interface FileSystem {
    public function getFileList($directory, $recursive = true);
    public function removeFile(File $file);
    public function moveFile(File $file, $destination);
    public function writeFile(File $file, $content);
    public function readFile(File $file);
    public function isDirectory(File $file);
    public function isFile(File $file);
    public function getLink(File $file);
    public function createDirectory($path, $directory);
    public function getFileFromPath($path);

    /**
     * Check whether file exists in the filesytem
     *
     * @param File $file
     * @return boolean True when exists, false otherwise
     */
    public function fileExists(File $file);

    /**
     * Make a File writable
     *
     * @param File $file
     */
    public function makeWritable(File $file);

    /**
     * Copy the file
     *
     * @param File    $file         Source file object
     * @param string  $destination  Destination file path
     * @param boolean $ignoreExists True, if the destination file exists it will be overwritten
     *                              otherwise file will be created with new name
     * @return string Name of the copy file
     */
    public function copyFile(File $file, $destination, $ignoreExists = false);

    /**
     * Get the file web path
     *
     * @param File $file File object
     * @return string Returns the file web path without filename
     */
    public function getWebPath(File $file);
}
