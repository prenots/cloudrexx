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
 * Database access function(s)
 * @copyright    CLOUDREXX CMS - CLOUDREXX AG
 * @author        Cloudrexx Development Team <info@cloudrexx.com>
 * @package     cloudrexx
 * @subpackage  core
 * @version        1.0.0
 */

/**
 * @ignore
 */
require_once UPDATE_LIB.'/adodb/adodb.inc.php';
/**
 * @ignore
 */
require_once UPDATE_LIB.'/adodb/drivers/adodb-pdo.inc.php';
/**
 * @ignore
 */
require_once(UPDATE_CORE . '/CustomAdodbPdo.class.php');

/**
 * Returns the database object.
 *
 * If none was created before, or if {link $newInstance} is true,
 * creates a new database object first.
 * In case of an error, the reference argument $errorMsg is set
 * to the error message.
 * @author  Cloudrexx Development Team <info@cloudrexx.com>
 * @access  public
 * @version 1.0.0
 * @param   string  $errorMsg       Error message
 * @param   boolean $newInstance    Force new instance
 * @global  array                   Language array
 * @global  array                   Database configuration
 * @global  integer                 ADODB fetch mode
 * @return  boolean                 True on success, false on failure
 */
function getDatabaseObject(&$errorMsg, $newInstance = false)
{
    global $_DBCONFIG, $_CONFIG, $pdoConnectionUpdate;
    static $objDatabase;

    if (is_object($objDatabase) && !$newInstance) {
        return $objDatabase;
    } else {
        if (!isset($pdoConnectionUpdate)) {
            $pdoConnectionUpdate = new \PDO(
                'mysql:dbname=' . $_DBCONFIG['database'] . ';' . (!empty($_DBCONFIG['charset']) ? 'charset=' . $_DBCONFIG['charset'] . ';' : '') . 'host='.$_DBCONFIG['host'],
                $_DBCONFIG['user'],
                $_DBCONFIG['password'],
                array(
                    // Setting the connection character set in the DSN (see below new \PDO()) prior to PHP 5.3.6 did not work.
                    // We will have to manually do it by executing the SET NAMES query when connection to the database.
                    \PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES '.$_DBCONFIG['charset'],
                )
            );
            $pdoConnectionUpdate->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
        }

        global $ADODB_FETCH_MODE, $ADODB_NEWCONNECTION;

        // open db connection
        $ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
        $ADODB_NEWCONNECTION = 'cxAdodbPdoConnectionFactoryUpdate';
        $objDb = \ADONewConnection('pdo');

        $errorNo = $objDb->ErrorNo();
        if ($errorNo != 0) {
            if ($errorNo == 1049) {
                $errorMsg = 'The database is unavailable';
            } else {
                $errorMsg =  $objDb->ErrorMsg()."<br />";
            }
            unset($objDb);
            return false;
        }
        // Disable STRICT_TRANS_TABLES mode:
        $res = $objDb->Execute('SELECT @@sql_mode');
        if ($res->EOF) {
            $errorMsg = 'Database mode error';
            return;
        }
        $sqlModes = explode(',', $res->fields['@@sql_mode']);
        array_walk($sqlModes, 'trim');
        if (($index = array_search('STRICT_TRANS_TABLES', $sqlModes)) !== false) {
            unset($sqlModes[$index]);
        }
        $objDb->Execute('SET sql_mode = \'' . implode(',', $sqlModes) . '\'');

        if ($objDb) {
            if ($newInstance) {
                return $objDb;
            } else {
                $objDatabase = $objDb;
                return $objDb;
            }
        } else {
            $errorMsg = 'Cannot connect to database server<i>&nbsp;('.$objDb->ErrorMsg().')</i>';
            unset($objDb);
        }
        return false;
    }
}

function cxAdodbPdoConnectionFactoryUpdate() {
    global $pdoConnectionUpdate;
    return new \Cx\Core\Model\CustomAdodbPdoUpdate($pdoConnectionUpdate);
}

?>
