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
        $_GET['act']= lcfirst($_GET['act']);
        switch($_GET['act'])  {
            case 'categories':
            case 'products':
            case 'manufacturer':
            case 'customers':
            case 'statistics':
            case 'import':
            case 'settings':
                break;
            case 'orders':
            default:
                $_GET['act']= ucfirst($_GET['act']);
                parent::getPage($page);
                return;
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
            'Orders',
            'Categories',
            'Products',
            'Manufacturer',
            'Customers',
            'Statistics',
            'Import',
            'Settings'
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
     * @param $dataSetIdentifier if $entityClassName is DataSet, this is used for better partition
     * @return array with options
     */
    protected function getViewGeneratorOptions($entityClassName, $dataSetIdentifier = '')
    {
        global $_ARRAYLANG;

        $options = parent::getViewGeneratorOptions($entityClassName, $dataSetIdentifier);

        switch ($entityClassName) {
            case 'Cx\Modules\Shop\Model\Entity\Orders':
                $options['functions']['filtering'] = true;
                $options['functions']['searching'] = true;
                $options['functions']['show'] = true;
                $options['functions']['editable'] = true;
                $options['functions']['paging'] = true;
                $options['functions']['add'] = false;

                $options['order'] = array(
                    'overview' => array(
                        'id',
                        'dateTime',
                        'status',
                        'customer',
                        'note',
                        'sum'
                    ),
                    'form' => array(
                        'id',
                        'dateTime',
                        'status',
                        'modifiedOn',
                        'modifiedBy',
                        'lang',
                        'billingCompany',
                        'billingGender',
                        'billingLastname',
                        'billingFirstname',
                        'billingAddress',
                        'billingZip',
                        'billingCity',
                        'billingCountryId',
                        'billingPhone',
                        'billingFax',
                        'billingEmail',
                        'company',
                        'gender',
                        'lastname',
                        'firstname',
                        'address',
                        'zip',
                        'city',
                        'country',
                        'phone',
                        'shipper',
                        'payment',
                        'lsvs',
                        'orderItems',
                        'vatAmount',
                        'shipmentAmount',
                        'paymentAmount',
                        'sum',
                        'note'
                    )
                );

                $options['fields'] = array(
                    'id' => array(
                        'showOverview' => true,
                        'showDetail' => true,
                        'formtext' => $_ARRAYLANG['DETAIL_ID'],
                    ),
                    'customerId' => array(
                        'showOverview' => false,
                        'showDetail' => false,
                    ),
                    'currencyId' => array(
                        'showOverview' => false,
                        'showDetail' => false,
                    ),
                    'sum' => array(
                        'showOverview' => true,
                    ),
                    'dateTime' => array(
                        'showOverview' => true,
                        'formtext' => $_ARRAYLANG['DETAIL_DATETIME'],
                    ),
                    'status' => array(
                        'showOverview' => true,
                        'formtext' => $_ARRAYLANG['DETAIL_STATUS'],
                    ),
                    'gender' => array(
                        'showOverview' => false,
                        'showDetail' => false,
                    ),
                    'company' => array(
                        'showOverview' => false,
                    ),
                    'firstname' => array(
                        'showOverview' => false,
                    ),
                    'lastname' => array(
                        'showOverview' => false,
                    ),
                    'address' => array(
                        'showOverview' => false,
                    ),
                    'city' => array(
                        'showOverview' => false,
                    ),
                    'zip' => array(
                        'showOverview' => false,
                        'formtext' => $_ARRAYLANG['DETAIL_ZIP_CITY'],
                    ),
                    'countryId' => array(
                        'showOverview' => false,
                        'showDetail' => false,
                    ),
                    'phone' => array(
                        'showOverview' => false,
                    ),
                    'vatAmount' => array(
                        'showOverview' => false,
                    ),
                    'shipmentAmount' => array(
                        'showOverview' => false,
                    ),
                    'shipmentId' => array(
                        'showOverview' => false,
                        'showDetail' => false,
                    ),
                    'paymentId' => array(
                        'showOverview' => false,
                        'showDetail' => false,
                    ),
                    'paymentAmount' => array(
                        'showOverview' => false,
                    ),
                    'ip' => array(
                        'showOverview' => false,
                        'showDetail' => false,
                    ),
                    'langId' => array(
                        'showOverview' => false,
                        'showDetail' => false,
                    ),
                    'note' => array(
                        'showOverview' => true,
                    ),
                    'modifiedOn' => array(
                        'showOverview' => false,
                    ),
                    'modifiedBy' => array(
                        'showOverview' => false,
                    ),
                    'billingGender' => array(
                        'showOverview' => false,
                    ),
                    'billingCompany' => array(
                        'showOverview' => false,
                    ),
                    'billingFirstname' => array(
                        'showOverview' => false,
                    ),
                    'billingLastname' => array(
                        'showOverview' => false,
                    ),
                    'billingAddress' => array(
                        'showOverview' => false,
                    ),
                    'billingCity' => array(
                        'showOverview' => false,
                    ),
                    'billingZip' => array(
                        'showOverview' => false,
                        'formtext' => $_ARRAYLANG['DETAIL_ZIP_CITY'],
                    ),
                    'billingCountryId' => array(
                        'showOverview' => false,
                    ),
                    'billingPhone' => array(
                        'showOverview' => false,
                    ),
                    'billingFax' => array(
                        'showOverview' => false,
                    ),
                    'billingEmail' => array(
                        'showOverview' => false,
                    ),
                    'lsvs' => array(
                        'showOverview' => false,
                    ),
                    'orderItems' => array(
                        'showOverview' => false,
                    ),
                    'relCustomerCoupons' => array(
                        'showOverview' => false,
                        'showDetail' => false,
                    ),
                    'lang' => array(
                        'showOverview' => false,
                    ),
                    'currencies' => array(
                        'showOverview' => false,
                        'showDetail' => false,
                    ),
                    'shipper' => array(
                        'showOverview' => false,
                    ),
                    'payment' => array(
                        'showOverview' => false,
                    ),
                    'customer' => array(
                        'showOverview' => true,
                        'showDetail' => false,
                    ),
                );
                break;
        }
        return $options;
    }
}