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
 * Javascript
 *
 * @author      Stefan Heinemann <sh@comvation.com>
 * @author      Michael Ritter <michael.ritter@cloudrexx.com>
 * @copyright   CLOUDREXX CMS - CLOUDREXX AG
 * @package     cloudrexx
 * @subpackage  core_javascript
 * @todo        Edit PHP DocBlocks!
 */

namespace Cx\Core\JavaScript\Controller;

/**
 * Javascript
 *
 * @author      Stefan Heinemann <sh@comvation.com>
 * @author      Michael Ritter <michael.ritter@cloudrexx.com>
 * @copyright   CLOUDREXX CMS - CLOUDREXX AG
 * @package     cloudrexx
 * @subpackage  core_javascript
 * @todo        Edit PHP DocBlocks!
 */
class JavaScript
{
    /**
     * An offset that shall be used before all paths
     *
     * When the JS files are used e.g. in the cadmin
     * section, all paths need a '../' before the path.
     * This variable holds that offset.
     * @see setOffset($offset)
     * @access protected
     * @var string
     */
    protected $offset = '';

    /**
     * The array containing all the registered stuff
     *
     * @access protected
     * @var array
     */
    protected $active = array();

    /**
     * Holding the last error
     * @access protected
     * @var string
     */
    protected $error;

    /**
     * Available JS libs
     * These JS files are per default available
     * in every Cloudrexx CMS.
     * The format is the following:
     * array(
     *      scriptname : array (
     *          jsfiles :   array of strings containing
     *                      all needed javascript files
     *          cssfiles :  array of strings containing
     *                      all needed css files
     *          dependencies :  array of strings containing
     *                          all dependencies in the right
     *                          order
     *          specialcode :   special js code to be executed
     *          loadcallback:   function that will be executed with
     *                          the options as parameter when chosen
     *                          to activate that JS library, so the
     *                          options can be parsed
     *          makecallback:   function that will be executed when
     *                          the code is generated
     *      )
     * )
     * @access protected
     * @var array
     */
    protected $available = array(
        'prototype'     => array(
            'jsfiles'       => array(
                'lib/javascript/prototype.js'
            ),
        ),
        'scriptaculous' => array(
            'jsfiles'       => array(
                'lib/javascript/scriptaculous/scriptaculous.js'
            ),
            'dependencies'  => array(
                'prototype'
            ),
        ),
        'shadowbox'     => array(
            'jsfiles'       => array(
                'lib/javascript/shadowbox/shadowbox.js'
            ),
            'dependencies'  => array(
                'cx', // depends on jquery
            ),
            'specialcode'  => '
Shadowbox.loadSkin("classic", cx.variables.get("basePath", "contrexx")+"lib/javascript/shadowbox/src/skin/");
Shadowbox.loadLanguage("en", cx.variables.get("basePath", "contrexx")+"lib/javascript/shadowbox/src/lang");
Shadowbox.loadPlayer(["flv", "html", "iframe", "img", "qt", "swf", "wmp"], cx.variables.get("basePath", "contrexx")+"lib/javascript/shadowbox/src/player");
cx.jQuery(document).ready(function(){
  Shadowbox.init();
})'
        ),
        'jquery'     => array(
            'versions' => array(
                '2.0.3' => array(
                    'jsfiles' => array(
                        'lib/javascript/jquery/2.0.3/js/jquery.min.js',
                     ),
                ),
                '2.0.2' => array(
                    'jsfiles' => array(
                        'lib/javascript/jquery/2.0.2/js/jquery.min.js',
                     ),
                ),
                '1.10.1' => array(
                    'jsfiles' => array(
                        'lib/javascript/jquery/1.10.1/js/jquery.min.js',
                     ),
                ),
                '1.9.1' => array(
                    'jsfiles' => array(
                        'lib/javascript/jquery/1.9.1/js/jquery.min.js',
                     ),
                ),
                '1.8.3' => array(
                    'jsfiles' => array(
                        'lib/javascript/jquery/1.8.3/js/jquery.min.js',
                     ),
                ),
                '1.7.3' => array(
                    'jsfiles' => array(
                        'lib/javascript/jquery/1.7.3/js/jquery.min.js',
                     ),
                ),
                '1.6.4' => array(
                    'jsfiles' => array(
                        'lib/javascript/jquery/1.6.4/js/jquery.min.js',
                     ),
                ),
                '1.6.1' => array(
                    'jsfiles'       => array(
                        'lib/javascript/jquery/1.6.1/js/jquery.min.js',
                     ),
                ),
            ),
            'specialcode' => '$J = jQuery;'
        ),
        'jquery-tools' => array(
            'jsfiles' => array(
                'lib/javascript/jquery/tools/jquery.tools.min.js',
            ),
            'dependencies' => array('jquery'),
        ),
        'jquery-imgareaselect' => array(
            'jsfiles'          => array(
                'lib/javascript/jquery/plugins/imgareaselect/jquery.imgareaselect.js',
            ),
            'cssfiles'         => array(
                'lib/javascript/jquery/plugins/imgareaselect/css/imgareaselect-animated.css',
            ),
            'dependencies' => array('jquery'),
        ),
        'jquery-jqplot' => array(
            'jsfiles'   => array(
                'lib/javascript/jquery/plugins/jqplot/jquery.jqplot.js',
                'lib/javascript/jquery/plugins/jqplot/plugins/jqplot.canvasTextRenderer.js',
                'lib/javascript/jquery/plugins/jqplot/plugins/jqplot.categoryAxisRenderer.js',
                'lib/javascript/jquery/plugins/jqplot/plugins/jqplot.barRenderer.js',
                'lib/javascript/jquery/plugins/jqplot/plugins/jqplot.highlighter.js',
                'lib/javascript/jquery/plugins/jqplot/plugins/jqplot.canvasAxisTickRenderer.js'
            ),
            'cssfiles'  => array(
                'lib/javascript/jquery/plugins/jqplot/jquery.jqplot.css',
            ),
            'dependencies' => array('jquery'),
        ),
        'jquery-bootstrap' => array(
            'jsfiles' => array(
                'lib/javascript/jquery/plugins/bootstrap/bootstrap.js',
            ),
            'cssfiles' => array(
                'lib/javascript/jquery/plugins/bootstrap/bootstrap.css',
            ),
            'dependencies' => array('jquery'),
        ),
        'ckeditor'     => array(
            'jsfiles'       => array(
                'lib/ckeditor/ckeditor.js',
            ),
            'dependencies' => array('jquery'),
        ),
        'js-cookie' => array(
            'jsfiles'       => array(
                'lib/javascript/js-cookie.min.js',
            ),
        ),
        // Required by HTML::getDatepicker() (modules/shop)!
        // (Though other versions will do just as well)
// TODO: remove & replace by cx call
        'jqueryui'     => array(
            'jsfiles'       => array(
                'lib/javascript/jquery/ui/jquery-ui-1.8.7.custom.min.js',
                'lib/javascript/jquery/ui/jquery-ui-timepicker-addon.js',
            ),
            'cssfiles'      => array(
                'lib/javascript/jquery/ui/css/jquery-ui.css'
            ),
            'dependencies'  => array(
                'cx', // depends on jquery
            ),
        ),
        //stuff to beautify forms.
        'cx-form'     => array(
            'jsfiles'       => array(
                'lib/javascript/jquery/ui/jquery.multiselect2side.js'
            ),
            'cssfiles'      => array(
                'lib/javascript/jquery/ui/css/jquery.multiselect2side.css'
            ),
            'dependencies'  => array(
                'jqueryui'
            ),
        ),

/*
Coming soon
Caution: JS/ALL files are missing. Also, this should probably be loaded through js:cx now.
        'jcrop' => array(
            'jsfiles'       => array(
                'lib/javascript/jcrop/js/jquery.Jcrop.min.js'
            ),
            'cssfiles'      => array(
                'lib/javascript/jcrop/css/jquery.Jcrop.css',
            ),
            'dependencies'  => array(
                'jquery',
            ),
            // When invoking jcrop, add code like this to create the widget:
            // cx.jQuery(window).load(function(){
            //   cx.jQuery("#my_image").Jcrop({ [option: value, ...] });
            // });
            // where option may be any of
            // aspectRatio   decimal
            //    Aspect ratio of w/h (e.g. 1 for square)
            // minSize       array [ w, h ]
            //    Minimum width/height, use 0 for unbounded dimension
            // maxSize       array [ w, h ]
            //    Maximum width/height, use 0 for unbounded dimension
            // setSelect     array [ x, y, x2, y2 ]
            //    Set an initial selection area
            // bgColor       color value
            //    Set color of background container
            // bgOpacity     decimal 0 - 1
            //    Opacity of outer image when cropping
        ),
*/
        'md5' => array(
            'jsfiles' => array(
                'lib/javascript/jquery/jquery.md5.js',
            ),
            'dependencies' => array('jquery'),
        ),
        'cx' => array(
            'jsfiles' => array(
                'lib/javascript/cx/contrexxJs.js',
                'lib/javascript/cx/contrexxJs-tools.js',
                'lib/javascript/jquery/jquery.includeMany-1.2.2.js' //to dynamically include javascript files
            ),
            'dependencies' => array(
                'md5', // depends on jquery
                'jquery-tools', // depends on jquery
            ),
            'lazyDependencies' => array('jqueryui'),
            //we insert the specialCode for the Cloudrexx-API later in getCode()
        ),
        'jstree' => array(
            'jsfiles' => array(
                'lib/javascript/jquery/jstree/jquery.jstree.js',
                'lib/javascript/jquery/hotkeys/jquery.hotkeys.js',
            ),
            'dependencies' => array('jquery', 'js-cookie'),
        ),
        'ace' => array(
            'jsfiles'  => array(
                'lib/ace/ace.js',
            ),
            'dependencies' => array('jquery'),
        ),

        // jQ UI input select enhancer. used in Content Manager 2
        'chosen' => array(
            'jsfiles' => array(
                'lib/javascript/jquery/chosen/jquery.chosen.js'
            ),
            'cssfiles' => array(
                'lib/javascript/jquery/chosen/chosen.css'
            ),
            'dependencies' => array('jquery'),
            'specialcode'  => '
                cx.jQuery(document).ready(function() {
                    if(cx.jQuery(".chzn-select").length > 0) {
                        cx.jQuery(".chzn-select").chosen({
                            disable_search: true
                        });
                    }
                });'
        ),
        'backend' => array(
            'jsfiles' => array(
                'lib/javascript/switching_content.js',
                'lib/javascript/cx_tabs.js',
                'lib/javascript/set_checkboxes.js'
            )
        ),
        'user-live-search' => array(
            'jsfiles' => array(
                'lib/javascript/user-live-search.js',
            ),
            'dependencies' => array(
                'cx', // depends on jquery
                'jqueryui',
            ),
        ),
        'bootstrapvalidator' => array(
            'jsfiles' => array(
                'lib/javascript/jquery/bootstrapvalidator/js/bootstrapValidator.min.js'
            ),
            'cssfiles' => array(
                'lib/javascript/jquery/bootstrapvalidator/css/bootstrapValidator.min.css',
            ),
            'dependencies' => array(
                'twitter-bootstrap'
            ),
        ),
        'twitter-bootstrap' => array(
            'versions' => array(
                '3.2.0' => array(
                    'jsfiles' => array(
                        'lib/javascript/twitter-bootstrap/3.2.0/js/bootstrap.min.js',
                     ),
                    'cssfiles' => array(
                        'lib/javascript/twitter-bootstrap/3.2.0/css/bootstrap.min.css',
                     ),
                    'dependencies' => array('jquery' => '^([^1]\..*|1\.[^0-8]*\..*)$'), // jquery needs to be version 1.9.0 or higher
                ),
                '3.1.0' => array(
                    'jsfiles' => array(
                        'lib/javascript/twitter-bootstrap/3.1.0/js/bootstrap.min.js',
                     ),
                    'cssfiles' => array(
                        'lib/javascript/twitter-bootstrap/3.1.0/css/bootstrap.min.css',
                     ),
                    'dependencies' => array('jquery' => '^([^1]\..*|1\.[^0-6]*\..*)$'), // jquery needs to be version 1.7.3 or higher
                ),
                '3.0.3' => array(
                    'jsfiles' => array(
                        'lib/javascript/twitter-bootstrap/3.0.3/js/bootstrap.min.js',
                     ),
                    'cssfiles' => array(
                        'lib/javascript/twitter-bootstrap/3.0.3/css/bootstrap.min.css',
                     ),
                    'dependencies' => array('jquery' => '^([^1]\..*|1\.[^0-6]*\..*)$'), // jquery needs to be version 1.7.3 or higher
                ),
                '3.0.2' => array(
                    'jsfiles' => array(
                        'lib/javascript/twitter-bootstrap/3.0.2/js/bootstrap.min.js',
                     ),
                    'cssfiles' => array(
                        'lib/javascript/twitter-bootstrap/3.0.2/css/bootstrap.min.css',
                     ),
                    'dependencies' => array('jquery' => '^([^1]\..*|1\.[^0-6]*\..*)$'), // jquery needs to be version 1.7.3 or higher
                ),
                '3.0.1' => array(
                    'jsfiles' => array(
                        'lib/javascript/twitter-bootstrap/3.0.1/js/bootstrap.min.js',
                     ),
                    'cssfiles' => array(
                        'lib/javascript/twitter-bootstrap/3.0.1/css/bootstrap.min.css',
                     ),
                    'dependencies' => array('jquery' => '^([^1]\..*|1\.[^0-6]*\..*)$'), // jquery needs to be version 1.7.3 or higher
                ),
                '3.0.0' => array(
                    'jsfiles' => array(
                        'lib/javascript/twitter-bootstrap/3.0.0/js/bootstrap.min.js',
                     ),
                    'cssfiles' => array(
                        'lib/javascript/twitter-bootstrap/3.0.0/css/bootstrap.min.css',
                     ),
                    'dependencies' => array('jquery' => '^([^1]\..*|1\.[^0-6]*\..*)$'), // jquery needs to be version 1.7.3 or higher
                ),
                '2.3.2' => array(
                    'jsfiles' => array(
                        'lib/javascript/twitter-bootstrap/2.3.2/js/bootstrap.min.js',
                     ),
                    'cssfiles' => array(
                        'lib/javascript/twitter-bootstrap/2.3.2/css/bootstrap.min.css',
                        'lib/javascript/twitter-bootstrap/2.3.2/css/bootstrap-responsive.min.css',
                     ),
                    'dependencies' => array('jquery' => '^([^1]\..*|1\.[^0-6]*\..*)$'), // jquery needs to be version 1.7.3 or higher
                ),
            ),
        ),
        'mediabrowser' => array(
            'jsfiles' => array(
                'lib/javascript/jquery/2.0.3/js/jquery.min.js',
                'lib/plupload/js/moxie.min.js?v=2',
                'lib/plupload/js/plupload.full.min.js?v=2',
                'lib/javascript/angularjs/angular.js?v=2',
                'lib/javascript/angularjs/angular-route.js?v=2',
                'lib/javascript/angularjs/angular-animate.js?v=2',
                'lib/javascript/twitter-bootstrap/3.1.0/js/bootstrap.min.js',
                'lib/javascript/angularjs/ui-bootstrap-tpls-0.11.2.min.js',
                'lib/javascript/bootbox.min.js'
            ),
            'cssfiles' => array(
                'core_modules/MediaBrowser/View/Style/MediaBrowser.css?v=2',
                'core_modules/MediaBrowser/View/Style/Frontend.css?v=2'
            ),
            'dependencies' => array(
                'cx',
                // Note: loading jQuery as a dependency does not work as it would
                //       interfere with jQuery plugins
                //'jquery'    => '^([^1]\..*|1\.[^0-8]*\..*)$', // jquery needs to be version 1.9.0 or higher
            ),
            'specialcode' => 'if (typeof cx.variables.get(\'jquery\', \'mediabrowser\') == \'undefined\'){
    cx.variables.set({"jquery": jQuery.noConflict(true)},\'mediabrowser\');
}'
        ),
        'intro.js' => array(
            'jsfiles' => array(
                'lib/javascript/intro/intro.min.js',
            )
        ),
        'schedule-publish-tooltip' => array(
            'jsfiles' => array(
                'core/Core/View/Script/ScheduledPublishing.js',
            ),
            'cssfiles' => array(
                'core/Core/View/Style/ScheduledPublishing.css'
            ),
            'loadcallback' => 'initScheduledPublishing',
            'dependencies' => array(
                'cx',
            ),
        ),
    );

    /**
     * Holds the custom JS files
     * @access protected
     * @var array
     */
    protected $customJS = array();

    /**
     * Holds the template JS files
     * @access protected
     * @var array
     */
    protected $templateJS = array();

    /**
     * The custom CSS files
     * @access protected
     * @var array
     */
    protected $customCSS = array();

    /**
     * The custom Code
     * @access protected
     * @var array
     */
    protected $customCode = array();

    /**
     * The players of the shadowbox
     * @access protected
     * @var array
     */
    protected $shadowBoxPlayers = array('img', 'swf', 'flv', 'qt', 'wmp', 'iframe','html');

    /**
     * The language of the shadowbox to be used
     * @access protected
     * @var string
     */
    protected $shadowBoxLanguage = 'en';

    /**
     * Remembers all js files already added in some way.
     *
     * @access protected
     * @var array
     */
    protected $registeredJsFiles = array();

    protected $re_name_postfix = 1;
    protected $comment_dict = array();

    /**
     * Array holding certain scripts we do not want the user to include - we provide
     * the version supplied with Cloudrexx instead.
     *
     * This was introduced to prevent the user from overriding the jQuery plugins included
     * by the Cloudrexx javascript framework.
     *
     * @see registerFromRegex()
     * @var array associative array ( '/regexstring/' => 'componentToIncludeInstead' )
     */
    protected $alternatives = array(
        '/^jquery([-_]\d\.\d(\.\d)?)?(\.custom)?(\.m(in|ax))?\.js$/i' => 'jquery',
        '/^contrexxJs\.js$/i' => 'cx',
    );

    /**
     * Set the offset parameter
     * @param string
     * @access public
     * @todo Setting the offset path could be done automatically. Implement such an algorithm
     *       and remove this method.
     */
    public function setOffset($offset)
    {
        if (!preg_match('/\/$/', $offset)) {
            $offset .= '/';
        }
        $this->offset = $offset;
    }


    /**
     * Activate an available js file
     *
     * The options parameter is specific for the chosen
     * library. The library must define callback methods for
     * the options to be used.
     * @access public
     * @param  string  $name
     * @param  array   $options
     * @param  bool    $dependencies
     * @return bool
     */
    public function activate($name, $options = null, $dependencies = true)
    {
        $name = strtolower($name);
        $index = array_search($name, $this->active);
        if ($index !== false) {
            // Move dependencies to the end of the array, so that the
            // inclusion order is maintained.
            // Note that the entire array is reversed for code generation,
            // so dependencies are loaded first!
            // See {@see getCode()} below.
            unset($this->active[$index]);
        }
        if (array_key_exists($name, $this->available) === false) {
            $this->error = $name.' is not a valid name for
                an available javascript type';
            return false;
        }
        $data = $this->available[$name];
        if (!empty($data['ref'])) {
            $name = $data['ref'];
            if (array_key_exists($name, $this->available)) {
                $data = $this->available[$name];
            } else {
                $this->error = $name.' unknown reference';
                return false;
            }
        }
        $this->active[] = $name;
        if (!empty($data['dependencies']) && $dependencies) {
            foreach ($data['dependencies'] as $dep => $depVersion) {
                if (is_string($dep)) {
                    $this->activateByVersion($dep, $depVersion, $name);
                } else {
                    // dependency does not specify a particular version of the library to load
                    // -> $depVersion contains the library name
                    $this->activate($depVersion);
                }
            }
        }
        if (isset($data['loadcallback']) && isset($options)) {
            $this->{$data['loadcallback']}($options);
        }
        return true;
    }

    /**
     * Activate a specific version of an available js file
     *
     * @param  string  $name Name of the library to load
     * @param  string  $version Specific version of the library to load.
     *                 Specified as 'x.z.y'. Also accepts PCRE wildchars.
     * @param  string  $dependencyOf is the optional name of the library
     *                 that triggered the loaded of the specific library version.
     * @return bool     TRUE if specific version of the library has been loaded. FALSE on failure
     */
    public function activateByVersion($name, $version, $dependencyOf = null) {
        // abort in case the library is unknown
        if (!isset($this->available[$name])) {
            return false;
        }

        // fetch the library meta data
        $library = $this->available[$name];

        // check if a matching library has already been loaded
        $activatedLibraries = preg_grep('/^'.$name.'-version-/', $this->active);
        // check if any of the already loaded libraries can be used as an alternativ
        foreach ($activatedLibraries as $activatedLibrary) {
            $activatedLibraryVersion = str_replace($name.'-version-', '', $activatedLibrary);
            if (!preg_match('/'.$version.'/', $activatedLibraryVersion)) {
                continue;
            }

            if ($name != 'jquery' || !$dependencyOf) {
                return true;
            }

            $libraryVersionData['specialcode'] = "cx.libs={{$name}:{'$dependencyOf': jQuery.noConflict()}};";
            $customAvailableLibrary = $name.'-version-'.$activatedLibraryVersion;
            $this->available[$customAvailableLibrary]['specialcode'] .= $libraryVersionData;

            // trigger the activate again to push the library up in the dependency chain
            $this->activate($customAvailableLibrary);
            return true;
        }

        // abort in case the library does not specify particular versions
        if (!isset($library['versions'])) {
            return false;
        }

        // try to load a matching version of the library
        foreach ($library['versions'] as $libraryVersion => $libraryVersionData) {
            if (!preg_match('/'.$version.'/', $libraryVersion)) {
                continue;
            }

            // register specific version of the library
            $customAvailableLibrary = $name.'-version-'.$libraryVersion;
            if ($name == 'jquery') {
                if ($dependencyOf) {
                    $libraryVersionData['specialcode'] = "cx.libs={{$name}:{'$dependencyOf': jQuery.noConflict()}};";
                } else {
                    $libraryVersionData['specialcode'] = "cx.libs={{$name}:{'$libraryVersion': jQuery.noConflict()}};";
                }
                // we have to load cx again as we are using cx.libs in the specialcode
                $libraryVersionData['dependencies'] = array('cx');
            }
            $this->available[$customAvailableLibrary] = $libraryVersionData;

            // activate the specific version of the library
            $this->activate($customAvailableLibrary);
            return true;
        }

        // no library by the specified version found
        return false;
    }

    /**
     * Deactivate a previously activated js file
     * @param string $name
     * @access public
     * @return bool
     */
    public function deactivate($name)
    {
        $name = strtolower($name);
        $searchResult = array_search($name, $this->active);
        if ($searchResult === false)
        {
            $this->error = $name.' is not a valid name for
                an available javascript type';
            return false;
        }
        unset($this->active[$searchResult]);
        return true;
    }


    /**
     * Register a custom JavaScript file
     *
     * Loads a new, individual JavaScript file that will be included in the page response.
     * If a file is registered that already exists as an available JavaScript library,
     * then this one will be loaded instead.
     * @param string $file The path of $file must be specified relative to the document root of the website.
     *     I.e. modules/foo/bar.js
     * @param bool $template is a javascript file which has been included from template
     *
     * External files are also suppored by providing a valid HTTP(S) URI as $file.
     * @return bool Returns TRUE if the file will be loaded, otherwiese FALSE.
     */
    public function registerJS($file, $template = false)
    {
        // check whether the script has a query string and remove it
        // this is necessary to check whether the file exists in the filesystem or not
        $fileName = $file;
        $queryStringBegin = strpos($fileName, '?');
        if ($queryStringBegin) {
            $fileName = substr($fileName, 0, $queryStringBegin);
        }

        // if it is an local javascript file
        if (!preg_match('#^https?://#', $fileName)) {
            if ($fileName[0] == '/') {
                $codeBasePath = \Cx\Core\Core\Controller\Cx::instanciate()->getCodeBasePath();
                $websitePath = \Cx\Core\Core\Controller\Cx::instanciate()->getWebsitePath();
            } else {
                $codeBasePath = \Cx\Core\Core\Controller\Cx::instanciate()->getCodeBaseDocumentRootPath();
                $websitePath = \Cx\Core\Core\Controller\Cx::instanciate()->getWebsiteDocumentRootPath();
            }
            if (   !file_exists(\Env::get('ClassLoader')->getFilePath(($codeBasePath.'/').$fileName))
                && !file_exists(\Env::get('ClassLoader')->getFilePath(($websitePath.'/').$fileName))
            ) {
                $this->error .= "The file ".$fileName." doesn't exist\n";
                return false;
            }
        }

        // add original file name with query string to custom javascripts array
        if (array_search($file, $this->customJS) !== false || array_search($file, $this->templateJS) !== false) {
            return true;
        }
        if ($template) {
            $this->templateJS[] = $file;
        } else {
            $this->customJS[] = $file;
        }
        return true;
    }

    /**
     * Register a custom css file
     *
     * Add a new, individual CSS file to the list.
     * The filename has to be relative to the document root.
     * @access public
     * @return bool
     */
    public function registerCSS($file)
    {
        if (   !file_exists(\Env::get('ClassLoader')->getFilePath(\Cx\Core\Core\Controller\Cx::instanciate()->getCodeBaseDocumentRootPath().'/'.$file))
            && !file_exists(\Env::get('ClassLoader')->getFilePath(\Cx\Core\Core\Controller\Cx::instanciate()->getWebsiteDocumentRootPath().'/'.$file))
        ) {
            $this->error = "The file ".$file." doesn't exist\n";
            return false;
        }

        if (array_search($file, $this->customCSS) === false) {
            $this->customCSS[] = $file;
        }
        return true;
    }


    /**
     * Register special code
     * Add special code to the List
     * @access public
     * @return bool
     */
    public function registerCode($code)
    {
        // try to see if this code already exists
        $code = trim($code);
        if (array_search($code, $this->customCode) === false) {
            $this->customCode[] = $code;
        }
        return true;
    }


    /**
     * Return the code for the placeholder
     * @access public
     * @return string
     */
    public function getCode()
    {
        $cssfiles = array();
// TODO: Unused
//        $jsfiles = array();
//        $specialcode = array();
        $lazyLoadingFiles = array();
        $retstring  = '';
        $jsScripts = array();
        if (count($this->active) > 0) {
            // check for lazy dependencies, if there are lazy dependencies, activate cx
            // cx provides the lazy loading mechanism
            // this should be here because the cx variable have to be set before cx is initialized
            foreach ($this->active as $name) {
                $data = $this->available[$name];
                if (!empty($data['lazyDependencies'])) {
                    foreach ($data['lazyDependencies'] as $dependency) {
                        if (!in_array($dependency, $this->active)) {
                            // if the lazy dependency is not activated so far
                            $lazyLoadingFiles = array_merge($lazyLoadingFiles, $this->available[$dependency]['jsfiles']);
                        }
                        if (!empty($this->available[$dependency]['cssfiles'])) {
                            $cssfiles = array_merge($cssfiles, $this->available[$dependency]['cssfiles']);
                        }
                    }
                }
            }
            if (!empty($lazyLoadingFiles)) {
                $this->activate('cx');
            }

            // set cx.variables with lazy loading file paths
            \ContrexxJavascript::getInstance()->setVariable('lazyLoadingFiles', $lazyLoadingFiles, 'contrexx');

            // Note the "reverse" here.  Dependencies are at the end of the
            // array, and must be loaded first!
            foreach (array_reverse($this->active) as $name) {
                $data = $this->available[$name];
                if (!isset($data['jsfiles']) && !isset($data['versions'])) {
                    $this->error = 'A JS entry should at least contain one js file...';
                    return false;
                }
                // get js files which are specified or the js files from first version
                if (!isset($data['jsfiles'])) {
                    // get data from default version and load the files from there
                    $versionData = end($data['versions']);
                    $data = array_merge($data, $versionData);
                }
                $jsScripts[] = $this->makeJSFiles($data['jsfiles']);
                if (!empty($data['cssfiles'])) {
                    $cssfiles = array_merge($cssfiles, $data['cssfiles']);
                }
                if (isset($data['specialcode']) && strlen($data['specialcode']) > 0) {
                    $jsScripts[] = $this->makeSpecialCode(array($data['specialcode']));
                }
                if (isset($data['makecallback'])) {
                    $this->{$data['makecallback']}();
                }
                // Special case cloudrexx-API: fetch specialcode if activated
                if ($name == 'cx') {
                    $jsScripts[] = $this->makeSpecialCode(
                        array(\ContrexxJavascript::getInstance()->initJs()));
                }
            }
        }

        $jsScripts[] = $this->makeJSFiles($this->customJS);

        // if jquery is activated, do a noConflict
        if (array_search('jquery', $this->active) !== false) {
            $jsScripts[] = $this->makeSpecialCode('if (typeof jQuery != "undefined") { jQuery.noConflict(); }');
        }
        $jsScripts[] = $this->makeJSFiles($this->templateJS);

        // no conflict for normal jquery version which has been included in template or by theme dependency
        $jsScripts[] = $this->makeSpecialCode('if (typeof jQuery != "undefined") { jQuery.noConflict(); }');
        $retstring .= $this->makeCSSFiles($cssfiles);
        $retstring .= $this->makeCSSFiles($this->customCSS);
        // Add javscript files
        $retstring .= implode(' ', $jsScripts);
        $retstring .= $this->makeJSFiles($this->customJS);
        $retstring .= $this->makeSpecialCode($this->customCode);
        return $retstring;
    }


    /**
     * Return the last error
     * @return string
     * @access public
     */
    public function getLastError()
    {
        return $this->error;
    }


    /**
     * Return the available libs
     * @access public
     * @return array
     */
    public function getAvailableLibs()
    {
        return $this->available;
    }


    /**
     * Make the code for the Javascript files
     * @param array $files
     * @return string
     * @access protected
     */
    protected function makeJSFiles($files)
    {
        global $_CONFIG;
        $code = '';

        foreach ($files as $file) {
            // The file has already been added to the js list
            if (array_search($file, $this->registeredJsFiles) !== false)
                continue;
            $this->registeredJsFiles[] = $file;
            $path = '';

            if (!preg_match('#^https?://#', $file)) {
                $path = $this->offset;
                if ($_CONFIG['useCustomizings'] == 'on' && file_exists(ASCMS_CUSTOMIZING_PATH.'/'.$file)) {
                    $path .= preg_replace('#'.\Cx\Core\Core\Controller\Cx::instanciate()->getCodeBaseDocumentRootPath().'/#', '', ASCMS_CUSTOMIZING_PATH) . '/';
                }
            }

            $path .= $file;
            $code .= "<script type=\"text/javascript\" src=\"".$path."\"></script>\n\t";
        }
        return $code;
    }


    /**
     * Make the code for the CSS files
     * @param array $files
     * @return string
     * @access protected
     */
    protected function makeCSSFiles($files)
    {
        global $_CONFIG;
        $code = '';
        foreach ($files as $file) {
            $path = $this->offset;
            if ($_CONFIG['useCustomizings'] == 'on' && file_exists(ASCMS_CUSTOMIZING_PATH.'/'.$file)) {
                $path .= preg_replace('#'.\Cx\Core\Core\Controller\Cx::instanciate()->getCodeBaseDocumentRootPath().'/#', '', ASCMS_CUSTOMIZING_PATH) . '/';
            }
            $path .= $file;
            $code .= "<link rel=\"stylesheet\" type=\"text/css\" href=\"".$path."\" />\n\t";
        }
        return $code;
    }


    /**
     * Make the code section for
     * @access protected
     * @param array $code
     * @return string
     */
    protected function makeSpecialCode($code)
    {
        if (empty($code)) {
            return '';
        }

        $retcode = "<script type=\"text/javascript\">\n/* <![CDATA[ */\n";
        if (is_array($code)) {
            $retcode .= implode("\r\n", $code);
        } else {
            $retcode .= $code;
        }
        $retcode .= "\n/* ]]> */\n</script>\n";
        return $retcode;
    }


    public function registerFromRegex($matchinfo)
    {
        $script = $matchinfo[1];
        $alternativeFound = false;
        //make sure we include the alternative if provided
        foreach($this->alternatives as $pattern => $alternative) {
            if(preg_match($pattern, basename($script)) > 0) {
                if ($alternative != 'jquery') {
                    $this->activate($alternative);
                    $alternativeFound = true;
                }
                break;
            }
        }
        //only register the js if we didn't activate the alternative
        if(!$alternativeFound)
            $this->registerJS($script, true);
    }


    /**
     * Finds all <script>-Tags in the passed HTML content, strips them out
     * and puts them in the internal JAVASCRIPT placeholder store.
     * You can then retreive them all-in-one with $this->getCode().
     * @param string $content - Reference to the HTML content. Note that it
     *                          WILL be modified in-place.
     */
    public function findJavascripts(&$content)
    {
        $this->grabComments($content);
        $content = preg_replace_callback('/<script .*?src=(?:"|\')([^"\']*)(?:"|\').*?\/?>(?:<\/script>)?/i', array('JS', 'registerFromRegex'), $content);
        $this->restoreComments($content);
    }

    /**
     * Finds all <link>-Tags in the passed HTML content, strips them out
     * and puts them in the internal CSS placeholder store.
     * You can then retreive them all-in-one with $this->getCode().
     * @param string $content - Reference to the HTML content. Note that it
     *                          WILL be modified in-place.
     */
    public function findCSS(&$content)
    {
        $this->grabComments($content);
        //deactivate error handling for not well formed html
        libxml_use_internal_errors(true);
        $css = array();
        $dom = new \DomDocument;
        $dom->loadHTML($content);
        libxml_clear_errors();
        foreach($dom->getElementsByTagName('link') as $element) {
            if(preg_match('/\.css(\?.*)?$/', $element->getAttribute('href'))) {
                $css[] = $element->getAttribute('href');
                $this->registerCSS($element->getAttribute('href'));
            }
        }
        $this->restoreComments($content);
        return $css;
    }

    /**
     * Get an array of libraries which are ready to load in different versions
     * @return array the libraries which are ready to configure for skin
     */
    public function getConfigurableLibraries()
    {
        $configurableLibraries = array();
        foreach ($this->available as $libraryName => $libraryInfo) {
            if (isset($libraryInfo['versions'])) {
                $configurableLibraries[$libraryName] = $libraryInfo;
            }
        }
        return $configurableLibraries;
    }


    /**
     * Grabs all comments in the given HTML and replaces them with a
     * temporary string. Modifies the given HTML in-place.
     * @param string $content
     */
    protected function grabComments(&$content)
    {
        $content = preg_replace_callback('#<!--.*?-->#ms', array($this, '_storeComment'), $content);
    }


    /**
     * Restores all grabbed comments (@see $this->grabComments()) and
     * puts them back in the given content. Modifies the given HTML in-place.
     * @param string $content
     */
    protected function restoreComments(&$content)
    {
        krsort($this->comment_dict);
        foreach ($this->comment_dict as $key => $value) {
            $content = str_replace($key, $value, $content);
        }
    }


    /**
     * Internal helper for replacing comments. @see $this->grabComments()
     */
    protected function _storeComment($re)
    {
        $name = 'saved_comment_'.$this->re_name_postfix;
        $this->comment_dict[$name] = $re[0];
        $this->re_name_postfix++;
        return $name;
    }

    /**
     * Callback function to load related cx variables for "schedule-publish-tooltip" lib
     *
     * @param array $options options array
     */
    protected function initScheduledPublishing($options)
    {
        global $_CORELANG;

        \ContrexxJavascript::getInstance()->setVariable(array(
            'active'            => $_CORELANG['TXT_CORE_ACTIVE'],
            'inactive'          => $_CORELANG['TXT_CORE_INACTIVE'],
            'scheduledActive'   => $_CORELANG['TXT_CORE_SCHEDULED_ACTIVE'],
            'scheduledInactive' => $_CORELANG['TXT_CORE_SCHEDULED_INACTIVE'],
        ), 'core/View');
    }
}
