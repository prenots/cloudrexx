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
 * Specific BackendController for this Component. Use this to easily create a backend view
 *
 * @copyright   Cloudrexx AG
 * @author      Sam Hawkes <info@cloudrexx.com>
 * @package     cloudrexx
 * @subpackage  coremodule_shop
 */

namespace Cx\Modules\Shop\Controller;


/**
 * Specific BackendController for this Component. Use this to easily create a backend view
 *
 * @copyright   Cloudrexx AG
 * @author      Sam Hawkes <info@cloudrexx.com>
 * @package     cloudrexx
 * @subpackage  coremodule_shop
 */
class BackendController extends \Cx\Core\Core\Model\Entity\SystemComponentBackendController
{
    /**
     * This is called by the ComponentController and does all the repeating work
     *
     * This loads the ShopManager and call getPage() from it. Only temporary,
     * since the entities are migrated individually
     *
     * @global array $_CORELANG Language data
     * @global array $subMenuTitle Submenu title
     * @global array $intAccessIdOffset access id offset
     * @global array $objTemplate object template
     *
     * @param \Cx\Core\ContentManager\Model\Entity\Page $page Resolved page
     */
    public function getPage(
        \Cx\Core\ContentManager\Model\Entity\Page $page
    ) {
        global $_CORELANG, $subMenuTitle, $intAccessIdOffset, $objTemplate;

        $mappedEntities = array(
            'categories' => 'category',
        );
        if (array_key_exists(lcfirst($_GET['act']), $mappedEntities)) {
            $_GET['act'] = $mappedEntities[lcfirst($_GET['act'])];
        }

        switch(lcfirst($_GET['act']))  {
            case 'category':
            case 'manufacturer':
                $_GET['act'] = ucfirst($_GET['act']);
                parent::getPage($page);
                return;
            default:
                break;
        }


        $this->cx->getTemplate()->addBlockfile(
            'CONTENT_OUTPUT',
            'content_master',
            'LegacyContentMaster.html'
        );
        $objTemplate = $this->cx->getTemplate();

        \Permission::checkAccess($intAccessIdOffset+13, 'static');
        $subMenuTitle = $_CORELANG['TXT_SHOP_ADMINISTRATION'];
        $objShopManager = new ShopManager();
        $objShopManager->getPage();
    }

    /**
     * Returns a list of available commands (?act=XY)
     * @return array List of acts
     */
    public function getCommands()
    {
        return array(
            'orders',
            'category',
            'products',
            'manufacturer',
            'customers',
            'statistics',
            'import',
            'settings'
        );
    }

    /**
     * Return true here if you want the first tab to be an entity view
     * @return boolean True if overview should be shown, false otherwise
     */
    protected function showOverviewPage()
    {
        return false;
    }

    /**
     * This function returns the ViewGeneration options for a given entityClass
     *
     * @access protected
     * @global $_ARRAYLANG
     * @param $entityClassName contains the FQCN from entity
     * @param $dataSetIdentifier if $entityClassName is DataSet, this is used
     *                           for better partition
     * @return array with options
     */
    protected function getViewGeneratorOptions($entityClassName, $dataSetIdentifier = '')
    {
        global $_ARRAYLANG;

        $options = parent::getViewGeneratorOptions(
            $entityClassName,
            $dataSetIdentifier
        );

        switch ($entityClassName) {
            case 'Cx\Modules\Shop\Model\Entity\Manufacturer':
                $options['order']['overview'] = array(
                    'id',
                    'name',
                    'uri'
                );
                $options['order']['form'] = array(
                    'name',
                    'uri'
                );

                $options['multiActions']['delete'] = array(
                    'title' => $_ARRAYLANG['TXT_DELETE'],
                    'jsEvent' => 'delete:manufacturer'
                );

                // Delete Event
                $scope = 'order';
                \ContrexxJavascript::getInstance()->setVariable(
                    'CSRF_PARAM',
                    \Cx\Core\Csrf\Controller\Csrf::code(),
                    $scope
                );
                \ContrexxJavascript::getInstance()->setVariable(
                    'TXT_CONFIRM_DELETE_MANUFACTURER',
                    $_ARRAYLANG['TXT_CONFIRM_DELETE_MANUFACTURER'],
                    $scope
                );
                \ContrexxJavascript::getInstance()->setVariable(
                    'TXT_ACTION_IS_IRREVERSIBLE',
                    $_ARRAYLANG['TXT_ACTION_IS_IRREVERSIBLE'],
                    $scope
                );

                $options['fields'] = array(
                    'id' => array(
                        'table' => array(
                            'attributes' => array(
                                'class' => 'manufacturer-id',
                            ),
                        ),
                    ),
                    'name' => array(
                        'table' => array(
                            'attributes' => array(
                                'class' => 'manufacturer-name',
                            ),
                        ),
                        'sorting' => false,
                    ),
                    'uri' => array(
                        'table' => array(
                            'attributes' => array(
                                'class' => 'manufacturer-uri',
                            ),
                        ),
                        'sorting' => false,
                    ),
                    'products' => array(
                        'showOverview' => false,
                        'showDetail' => false,
                    ),
                );
            break;
            case 'Cx\Modules\Shop\Model\Entity\Category':
                $options['order']['overview'] = array(
                    'id',
                    'active',
                    'name'
                );
                $options['order']['form'] = array(
                    'name',
                    'parentCategory',
                    'active',
                    'picture',
                    'shortDescription',
                    'description'
                );

                $options['fields'] = array(
                    'id' => array(
                        'table' => array(
                            'attributes' => array(
                                'class' => 'category-id',
                            ),
                        ),
                        'sorting' => false,
                    ),
                    'active' => array(
                        'table' => array(
                            'attributes' => array(
                                'class' => 'category-active',
                            ),
                        ),
                        'sorting' => false,
                    ),
                    'name' => array(
                        'table' => array(
                            'attributes' => array(
                                'class' => 'category-name',
                            ),
                        ),
                        'sorting' => false,
                    ),
                    'parentCategory' => array(
                        'showOverview' => false,
                    ),
                    'parentId' => array(
                        'showOverview' => false,
                        'showDetail' => false,
                    ),
                    'picture' => array(
                        'showOverview' => false,
                    ),
                    'shortDescription' => array(
                        'showOverview' => false,
                    ),
                    'description' => array(
                        'showOverview' => false,
                    ),
                    'ord' => array(
                        'showOverview' => false,
                        'showDetail' => false,
                    ),
                    'flags' => array(
                        'showOverview' => false,
                        'showDetail' => false,
                    ),
                    'children' => array(
                        'showOverview' => false,
                        'showDetail' => false,
                    ),
                    'pricelists' => array(
                        'showOverview' => false,
                        'showDetail' => false,
                    ),
                    'products' => array(
                        'showOverview' => false,
                        'showDetail' => false,
                    ),
                );
            break;
        }
        return $options;
    }
}
