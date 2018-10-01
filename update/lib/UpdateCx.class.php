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

class UpdateCx extends \Cx\Core\Core\Controller\Cx {
    /**
     * System mode
     * @var string Mode as string
     * @access protected
     */
    protected $mode = 'minimal';

    /**
     * Initializes the UpdateCx class
     * This includes all class variables which are needed for the update system.
     * @global  array   $_PATHCONFIG
     */
    public function __construct() {
        global $_PATHCONFIG;

        $this->setCodeBaseRepository($_PATHCONFIG['ascms_installation_root'], $_PATHCONFIG['ascms_installation_offset']);
        $this->setWebsiteRepository($_PATHCONFIG['ascms_root'], $_PATHCONFIG['ascms_root_offset']);
    }

    /**
     * Loading EventManager, DB, License
     * @global PDOConnection    $connection
     * @global array            $_DBCONFIG
     * @global array            $_CONFIG
     */
    public function minimalInit() {
        global $pdoConnectionUpdate, $objDatabase, $_DBCONFIG, $_CONFIG;

        // Set database connection details
        $objDb = new \Cx\Core\Model\Model\Entity\Db($_DBCONFIG);

        // Set database user details
        $objDbUser = new \Cx\Core\Model\Model\Entity\DbUser($_DBCONFIG);

        // Initialize database connection
        $this->db = \Cx\Core\Model\Db::fromExistingConnection($objDb, $objDbUser, $pdoConnectionUpdate, $objDatabase, \Env::get('em'));

        // initialize event manager
        $this->eventManager = new \Cx\Core\Event\Controller\EventManager($this);
        new \Cx\Core\Event\Controller\ModelEventWrapper($this);

        // initialize license
        $this->license = \Cx\Core_Modules\License\License::getCached($_CONFIG, $this->getDb()->getAdoDb());

        $this->cl = \Env::get('ClassLoader');
    }

    public function getMediaSourceManager(){
        if (!$this->mediaSourceManager){
            // register events required for MediaSourceManager initialization
            $this->getEvents()->addEvent('preComponent');
            $this->getEvents()->addEvent('postComponent');
            $this->getEvents()->addEvent('mediasource.load');
            $this->getEvents()->addEventListener(
            'mediasource.load', new \Cx\Core\ViewManager\Model\Event\ViewManagerEventListener($this)
        );

            $this->mediaSourceManager = new \Cx\Core\MediaSource\Model\Entity\MediaSourceManager($this);
        }
        return $this->mediaSourceManager;
    }

    public function getComponent($component) {
        switch ($component) {
            case 'Cache':
                return new class {
                    public function clearCache() {}
                    public function delete() {}
                    public function fetch() {}
                    public function save() {}
                    public function getCacheDriver() {
                        return new \Doctrine\Common\Cache\ArrayCache();
                    }
                };
                break;

            case 'Model':
                return new class {
                    public function slugify($string) {
                        // replace international characters
                        $string = \Cx\Core\Core\Controller\Cx::instanciate()->getComponent('LanguageManager')
                            ->replaceInternationalCharacters($string);

                        // replace spaces
                        $string = preg_replace('/\s+/', '-', $string);

                        // replace all non-url characters
                        $string = preg_replace('/[^a-zA-Z0-9-_]/', '', $string);

                        // replace duplicate occurrences (in a row) of char "-" and "_"
                        $string = preg_replace('/([-_]){2,}/', '-', $string);

                        return $string;
                    }
                };
                break;

            case 'LanguageManager':
                return new class {
                    public function replaceInternationalCharacters($text) {
                        $text = str_replace(
                            array_keys(\Cx\Core\LanguageManager\Controller\ComponentController::$REPLACEMENT_CHARLIST),
                            \Cx\Core\LanguageManager\Controller\ComponentController::$REPLACEMENT_CHARLIST,
                            $text
                        );
                        return $text;
                    }
                };
                break;

            case 'Session':
                return new class {
                    public function getSession($forceInitialization = true) {
                        $sessionObj = \cmsSession::getInstance();
                    }

                    // session is always initialized during update
                    public function isInitialized() {
                        return true;
                    }
                };
                break;

            default;
                break;
        }

        return parent::getComponent($component);
    }
}
