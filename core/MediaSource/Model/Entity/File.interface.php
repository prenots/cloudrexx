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


interface File {
    public function getFileSystem();
    public function getPath();
    public function getName();
    public function getFullName();
    public function getExtension();
    public function getMimeType();
    public function __toString();

    /**
     * Gets file size
     *
     * @return int the size of the file in bytes, or false
     */
    public function getSize();

    /**
     * Copy the file/directory
     *
     * @param string  $destinationPath Destination file path
     * @param boolean $ignoreExists    True, if the destination file exists it will be overwritten
     *                                 otherwise file will be created with new name
     * @return string Name of the copy file
     */
    public function copy($destinationPath, $ignoreExists = false);

    /**
     * Move the file/directory
     *
     * @param string  $destinationPath Destination file path
     * @param boolean $ignoreExists    True, if the destination file exists it will be overwritten
     *                                 otherwise file will be created with new name
     * @return string Name of the moved file/directory
     */
    public function move($destinationPath, $ignoreExists = false);

    /**
     * Remove the file/directory
     *
     * @return boolean Status of the file/directory remove
     */
    public function remove();

    /**
     * Tells whether the filename is a regular file
     *
     * @return boolean True if the filename exists and is a regular file, false otherwise
     */
    public function isFile();

    /**
     * Tells whether the filename is a directory
     *
     * @return boolean True if the filename exists and is a directory, false otherwise
     */
    public function isDirectory();

    /**
     * Get the absolute path of the file
     *
     * @return string Absolute path of the file
     */
    public function getAbsolutePath();

    /**
     * Get File stream
     *
     * @param string $mode Type of access require to the stream
     * @return resource a file handle resource on success or false on failure
     */
    public function getStream($mode);
}
