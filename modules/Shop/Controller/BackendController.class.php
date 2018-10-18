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
                        'allowSearching' => true,
                        'allowFiltering' => false,
                        'formtext' => $_ARRAYLANG['DETAIL_ID'],
                        'formfield' => function (
                            $fieldname, $fieldtype, $fieldlength,
                            $fieldvalue, $fieldoptions
                        ) {
                            return $this->getTextElement($fieldname, $fieldvalue);
                        },
                        'sorting' => true,
                    ),
                    'customerId' => array(
                        'showOverview' => false,
                        'showDetail' => false,
                        'allowFiltering' => false,
                    ),
                    'currencyId' => array(
                        'showOverview' => false,
                        'showDetail' => false,
                        'allowFiltering' => false,
                    ),
                    'sum' => array(
                        'showOverview' => true,
                        'allowFiltering' => false,
                        'sorting' => false,
                        'formfield' => function (
                            $fieldname, $fieldtype, $fieldlength,
                            $fieldvalue, $fieldoptions
                        ) {
                            return $this->getCustomInputFields($fieldname, $fieldvalue);
                        }
                    ),
                    'dateTime' => array(
                        'showOverview' => true,
                        'allowFiltering' => false,
                        'sorting' => false,
                        'formtext' => $_ARRAYLANG['DETAIL_DATETIME'],
                        'table' => array (
                            'parse' => function ($value, $rowData) {
                                $date = new \DateTime($value);
                                $fieldvalue = $date->format('d.m.Y h:m:s');
                                return $fieldvalue;
                            },
                        ),
                        'formfield' => function (
                            $fieldname, $fieldtype, $fieldlength,
                            $fieldvalue, $fieldoptions
                        ) {
                            $date = new \DateTime($fieldvalue);
                            $fieldvalue = $date->format('d-m-Y h:m:s');

                            return $this->getTextElement($fieldname, $fieldvalue);
                        }
                    ),
                    'status' => array(
                        'showOverview' => true,
                        'sorting' => false,
                        'formtext' => $_ARRAYLANG['DETAIL_STATUS'],
                        'table' => array (
                            'parse' => function ($value, $rowData) {
                                return $this->getStatusMenu($value);
                            },
                        ),
                        'formfield' => function (
                            $fieldname, $fieldtype, $fieldlength,
                            $fieldvalue, $fieldoptions
                        ) {
                            return $this->getStatusMenu($fieldvalue, $fieldname);
                        },
                        'filterOptionsField' => function (
                            $parseObject, $fieldName, $elementName, $formName
                        ) {
                            return $this->getStatusMenu(
                                '',
                                $elementName,
                                $formName
                            );
                        },
                    ),
                    'gender' => array(
                        'showOverview' => false,
                        'showDetail' => false,
                        'allowFiltering' => false,
                    ),
                    'company' => array(
                        'showOverview' => false,
                        'allowFiltering' => false,
                    ),
                    'firstname' => array(
                        'showOverview' => false,
                        'allowFiltering' => false,
                    ),
                    'lastname' => array(
                        'showOverview' => false,
                        'allowFiltering' => false,
                    ),
                    'address' => array(
                        'showOverview' => false,
                        'allowFiltering' => false,
                    ),
                    'city' => array(
                        'showOverview' => false,
                        'allowFiltering' => false,
                    ),
                    'zip' => array(
                        'showOverview' => false,
                        'allowFiltering' => false,
                        'formtext' => $_ARRAYLANG['DETAIL_ZIP_CITY'],
                    ),
                    'countryId' => array(
                        'showOverview' => false,
                        'showDetail' => false,
                        'allowFiltering' => false,
                    ),
                    'phone' => array(
                        'showOverview' => false,
                        'allowFiltering' => false,
                    ),
                    'vatAmount' => array(
                        'showOverview' => false,
                        'allowFiltering' => false,
                        'formfield' => function (
                            $fieldname, $fieldtype, $fieldlength,
                            $fieldvalue, $fieldoptions
                        ) {
                            return $this->getCustomInputFields($fieldname, $fieldvalue);
                        }
                    ),
                    'shipmentAmount' => array(
                        'showOverview' => false,
                        'allowFiltering' => false,
                        'formfield' => function (
                            $fieldname, $fieldtype, $fieldlength,
                            $fieldvalue, $fieldoptions
                        ) {
                            return $this->getCustomInputFields($fieldname, $fieldvalue);
                        }
                    ),
                    'shipmentId' => array(
                        'showOverview' => false,
                        'showDetail' => false,
                        'allowFiltering' => false,
                    ),
                    'paymentId' => array(
                        'showOverview' => false,
                        'showDetail' => false,
                        'allowFiltering' => false,
                    ),
                    'paymentAmount' => array(
                        'showOverview' => false,
                        'allowFiltering' => false,
                        'formfield' => function (
                            $fieldname, $fieldtype, $fieldlength,
                            $fieldvalue, $fieldoptions
                        ) {
                            return $this->getCustomInputFields($fieldname, $fieldvalue);
                        }
                    ),
                    'ip' => array(
                        'showOverview' => false,
                        'showDetail' => false,
                        'allowFiltering' => false,
                    ),
                    'langId' => array(
                        'showOverview' => false,
                        'showDetail' => false,
                        'allowFiltering' => false,
                    ),
                    'note' => array(
                        'showOverview' => true,
                        'allowFiltering' => false,
                        'sorting' => false,
                        'formfield' => function (
                            $fieldname, $fieldtype, $fieldlength,
                            $fieldvalue, $fieldoptions
                        ) {
                            return $this->getTextElement($fieldname, $fieldvalue);
                        },
                    ),
                    'modifiedOn' => array(
                        'showOverview' => false,
                        'allowFiltering' => false,
                        'formfield' => function (
                            $fieldname, $fieldtype, $fieldlength,
                            $fieldvalue, $fieldoptions
                        ) {
                            $date = new \DateTime($fieldvalue);
                            $fieldvalue = $date->format('d-m-Y h:m:s');

                            return $this->getTextElement($fieldname, $fieldvalue);
                        }
                    ),
                    'modifiedBy' => array(
                        'showOverview' => false,
                        'allowFiltering' => false,
                        'formfield' => function (
                            $fieldname, $fieldtype, $fieldlength,
                            $fieldvalue, $fieldoptions
                        ) {
                            return $this->getTextElement($fieldname, $fieldvalue);
                        }
                    ),
                    'billingGender' => array(
                        'showOverview' => false,
                        'allowFiltering' => false,
                        'formfield' => function (
                            $fieldname, $fieldtype, $fieldlength,
                            $fieldvalue, $fieldoptions
                        ) {
                            global $_ARRAYLANG;

                            $validData = array(
                                'gender_undefined' => $_ARRAYLANG['TXT_SHOP_GENDER_UNDEFINED'],
                                'gender_male' => $_ARRAYLANG['TXT_SHOP_GENDER_MALE'],
                                'gender_female' => $_ARRAYLANG['TXT_SHOP_GENDER_FEMALE']
                            );

                            $genderDropdown = new \Cx\Core\Html\Model\Entity\DataElement(
                                $fieldname, $fieldvalue, 'select', null, $validData
                            );

                            return $genderDropdown;
                        }
                    ),
                    'billingCompany' => array(
                        'showOverview' => false,
                        'allowFiltering' => false,
                    ),
                    'billingFirstname' => array(
                        'showOverview' => false,
                        'allowFiltering' => false,
                    ),
                    'billingLastname' => array(
                        'showOverview' => false,
                        'allowFiltering' => false,
                    ),
                    'billingAddress' => array(
                        'showOverview' => false,
                        'allowFiltering' => false,
                    ),
                    'billingCity' => array(
                        'showOverview' => false,
                        'allowFiltering' => false,
                    ),
                    'billingZip' => array(
                        'showOverview' => false,
                        'allowFiltering' => false,
                        'formtext' => $_ARRAYLANG['DETAIL_ZIP_CITY'],
                    ),
                    'billingCountryId' => array(
                        'showOverview' => false,
                        'allowFiltering' => false,
                    ),
                    'billingPhone' => array(
                        'showOverview' => false,
                        'allowFiltering' => false,
                    ),
                    'billingFax' => array(
                        'showOverview' => false,
                        'allowFiltering' => false,
                    ),
                    'billingEmail' => array(
                        'showOverview' => false,
                        'allowFiltering' => false,
                    ),
                    'lsvs' => array(
                        'showOverview' => false,
                        'allowFiltering' => false,
                    ),
                    'orderItems' => array(
                        'showOverview' => false,
                        'allowFiltering' => false,
                    ),
                    'relCustomerCoupons' => array(
                        'showOverview' => false,
                        'showDetail' => false,
                        'allowFiltering' => false,
                    ),
                    'lang' => array(
                        'showOverview' => false,
                        'allowFiltering' => false,
                        'formfield' => function (
                            $fieldname, $fieldtype, $fieldlength,
                            $fieldvalue, $fieldoptions
                        ) {
                            return $this->getTextElement($fieldname, $fieldvalue);
                        }
                    ),
                    'currencies' => array(
                        'showOverview' => false,
                        'showDetail' => false,
                        'allowFiltering' => false,
                    ),
                    'shipper' => array(
                        'showOverview' => false,
                        'allowFiltering' => false,
                    ),
                    'payment' => array(
                        'showOverview' => false,
                        'allowFiltering' => false,
                        'formfield' => function (
                            $fieldname, $fieldtype, $fieldlength,
                            $fieldvalue, $fieldoptions
                        ) {
                            return $this->getTextElement($fieldname, $fieldvalue);
                        }
                    ),
                    'customer' => array(
                        'showOverview' => true,
                        'showDetail' => false,
                        'sorting' => false,
                        'table' => array (
                            'parse' => function ($value, $rowData) {
                                $objUser = \FWUser::getFWUserObject()->objUser
                                    ->getUser($id = (int)$value);
                                ;

                                return $objUser->getProfileAttribute(
                                        'lastname'
                                    ) . ' ' .$objUser->getProfileAttribute(
                                        'firstname'
                                    );
                            },
                        ),
                        'filterOptionsField' => function (
                            $parseObject, $fieldName, $elementName, $formName
                        ) {
                            return $this->getCustomerGroupMenu($elementName);
                        },
                    ),
                );
                break;
        }
        return $options;
    }

    protected function getTextElement($fieldname, $fieldvalue)
    {
        $textField = new \Cx\Core\Html\Model\Entity\TextElement(
            $fieldvalue
        );
        $textField->setAttribute('name', $fieldname);
        $textField->setAttribute('type', 'text');
        $textField->setAttribute('id', $fieldname);
        $textField->setAttribute('class', 'form-control');

        return $textField;
    }

    protected function getCustomerGroupMenu($elementName)
    {
        global $_ARRAYLANG;

        $resellerGroup = \Cx\Core\Setting\Controller\Setting::getValue(
            'usergroup_id_reseller',
            'Shop'
        );

        $customerGroup = \Cx\Core\Setting\Controller\Setting::getValue(
            'usergroup_id_customer',
            'Shop'
        );

        //ToDo: use $resserGroup and $customerGroup for array keys
        $validValues = array(
            '' => $_ARRAYLANG['TXT_SHOP_ORDER_CUSTOMER_GROUP_PLEASE_CHOOSE'],
            6 => $_ARRAYLANG['TXT_CUSTOMER'],
            7 => $_ARRAYLANG['TXT_RESELLER'],
        );
        $searchField = new \Cx\Core\Html\Model\Entity\DataElement(
            $elementName,
            '',
            'select',
            null,
            $validValues
        );
        return $searchField;
    }

    protected function getStatusMenu($value, $name = '', $formName = '')
    {
        global $_ARRAYLANG;

        $validValues = array();
        $statusValues = $this->cx->getDb()
            ->getEntityManager()->getRepository(
                $this->getNamespace()
                . '\\Model\Entity\Orders'
            )->getStatusValues();
        if (!empty($formName)) {
            $validValues = array(
                '' => $_ARRAYLANG['TXT_SHOP_ORDER_STATUS_PLEASE_CHOOSE'],
            );
        }
        $validValues = array_merge($validValues, $statusValues);

        $statusField = new \Cx\Core\Html\Model\Entity\DataElement(
            'status',
            $value,
            'select',
            null,
            $validValues
        );

        if (!empty($name)) {
            $statusField->setAttributes(
                array(
                    'name' => $name,
                )
            );
        }

        if (!empty($formName)) {
            $statusField->setAttributes(
                array(
                    'form' => $formName,
                    'data-vg-attrgroup' => 'search',
                    'data-vg-field' => 'status',
                    'class' => 'vg-encode'
                )
            );
        }

        return $statusField;
    }

    protected function getCustomInputFields($fieldname, $fieldvalue)
    {
        global $_ARRAYLANG;

        $wrapper = new \Cx\Core\Html\Model\Entity\HtmlElement('div');
        $wrapper->addClass('custom-input');

        $title = new \Cx\Core\Html\Model\Entity\TextElement($_ARRAYLANG[$fieldname]);
        $input = new \Cx\Core\Html\Model\Entity\DataElement($fieldname, $fieldvalue, 'input');
        $addition = new \Cx\Core\Html\Model\Entity\TextElement('CHF');

        $wrapper->addChild($title);
        $wrapper->addChild($input);
        $wrapper->addChild($addition);

        return $wrapper;
    }
}