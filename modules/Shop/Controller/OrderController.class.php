<?php
/**
 * Cloudrexx
 *
 * @link      http://www.cloudrexx.com
 * @copyright Cloudrexx AG 2007-2018
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
 * OrderController to handle orders
 *
 * @copyright   Cloudrexx AG
 * @author      Sam Hawkes <info@cloudrexx.com>
 * @package     cloudrexx
 * @subpackage  module_shop
 */
namespace Cx\Modules\Shop\Controller;

/**
 * OrderController to handle orders
 *
 * @copyright   Cloudrexx AG
 * @author      Sam Hawkes <info@cloudrexx.com>
 * @package     cloudrexx
 * @subpackage  module_shop
 */
class OrderController extends \Cx\Core\Core\Model\Entity\Controller
{
    /**
     * Get ViewGenerator options for Manufacturer entity
     *
     * @param $options array predefined ViewGenerator options
     * @throws \Exception
     * @return array includes ViewGenerator options for Manufacturer entity
     */
    public function getViewGeneratorOptions($options)
    {
        global $_ARRAYLANG;

        // Until we know how to get the editId without the $_GET param
        if ($this->cx->getRequest()->hasParam('editid')) {
            $this->orderId = explode(
                '}',
                explode(
                    ',',
                    $this->cx->getRequest()->getParam('editid')
                )[1]
            )[0];
        }

        $options['functions']['filtering'] = true;
        $options['functions']['searching'] = true;
        $options['functions']['show'] = true;
        $options['functions']['editable'] = true;
        $options['functions']['paging'] = true;
        //$options['functions']['add'] = false;
        $options['functions']['onclick']['delete'] = 'deleteOrder';
        $options['functions']['order']['id'] = SORT_DESC;
        $options['functions']['searchCallback'] = function(
            $qb,
            $field,
            $crit,
            $i
        ) {
            if ($field == 'customer') {
                $qb->join(
                    '\Cx\Core\User\Model\Entity\User',
                    'u', 'WITH', 'u.id = x.customerId'
                );
                $qb->andWhere('?'.$i.' MEMBER OF u.group');
                $qb->setParameter($i, $crit);
            } else {
                $qb->andWhere($qb->expr()->eq('x.' . $field, '?' . $i));
                $qb->setParameter($i, $crit);
            }
            return $qb;
        };
        $options['multiActions']['delete'] = array(
            'title' => $_ARRAYLANG['TXT_DELETE'],
            'jsEvent' => 'delete:order'
        );

        // Delete Event
        $scope = 'order';
        \ContrexxJavascript::getInstance()->setVariable(
            'CSRF_PARAM',
            \Cx\Core\Csrf\Controller\Csrf::code(),
            $scope
        );
        \ContrexxJavascript::getInstance()->setVariable(
            'TXT_CONFIRM_DELETE_ORDER',
            $_ARRAYLANG['TXT_CONFIRM_DELETE_ORDER'],
            $scope
        );
        \ContrexxJavascript::getInstance()->setVariable(
            'TXT_ACTION_IS_IRREVERSIBLE',
            $_ARRAYLANG['TXT_ACTION_IS_IRREVERSIBLE'],
            $scope
        );
        \ContrexxJavascript::getInstance()->setVariable(
            'TXT_SHOP_CONFIRM_RESET_STOCK',
            $_ARRAYLANG['TXT_SHOP_CONFIRM_RESET_STOCK'],
            $scope
        );

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
                'titleAddress',
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
                'titlePaymentInfos',
                'payment',
                'lsvs',
                'titleBill',
                'orderItems',
                'vatAmount',
                'emptyField',
                'shipmentAmount',
                'paymentAmount',
                'sum',
                'titleNote',
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
                'table' => array(
                    'attributes' => array(
                        'class' => 'order-id',
                    ),
                ),
                'attributes' => array(
                    'class' => 'readonly',
                ),
                'readonly' => true,
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
                'table' => array(
                    'attributes' => array(
                        'class' => 'order-sum',
                    ),
                ),
                'formfield' => function (
                    $fieldname, $fieldtype, $fieldlength,
                    $fieldvalue, $fieldoptions
                ) {
                    return $this->getCustomInputFields(
                        $fieldname,
                        $fieldvalue
                    );
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
                    'attributes' => array(
                        'class' => 'order-date-time',
                    ),
                ),
                'attributes' => array(
                    'class' => 'readonly',
                ),
                'readonly' => true,
                'type' => 'input',
            ),
            'status' => array(
                'showOverview' => true,
                'sorting' => false,
                'searchCheckbox' => 0,
                'formtext' => $_ARRAYLANG['DETAIL_STATUS'],
                'table' => array (
                    'parse' => function ($value, $rowData) {
                        return $this->getStatusMenu($value, '', $rowData['id']);
                    },
                    'attributes' => array(
                        'class' => 'order-status',
                    ),
                ),
                'formfield' => function (
                    $fieldname, $fieldtype, $fieldlength,
                    $fieldvalue, $fieldoptions
                ) {
                    return $this->getDetailStatusMenu(
                        $fieldvalue,
                        $fieldname
                    );
                },
                'filterOptionsField' => function (
                    $parseObject, $fieldName, $elementName, $formName
                ) {
                    return $this->getStatusMenu(
                        '',
                        $elementName,
                        0,
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
                    return $this->getCustomInputFields(
                        $fieldname,
                        $fieldvalue
                    );
                }
            ),
            'shipmentAmount' => array(
                'showOverview' => false,
                'allowFiltering' => false,
                'formfield' => function (
                    $fieldname, $fieldtype, $fieldlength,
                    $fieldvalue, $fieldoptions
                ) {
                    return $this->getCustomInputFields(
                        $fieldname,
                        $fieldvalue
                    );
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
                    return $this->getCustomInputFields(
                        $fieldname,
                        $fieldvalue
                    );
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
                'type' => 'div',
                'table' => array(
                    'parse' => function($value, $rowData) {
                        return $this->getNoteToolTip($value);
                    },
                    'attributes' => array(
                        'class' => 'order-note',
                    ),
                ),
                'formfield' => function($name, $type, $length, $value) {
                    return $this->getDivWrapper($value);
                }
            ),
            'modifiedOn' => array(
                'showOverview' => false,
                'allowFiltering' => false,
                'attributes' => array(
                    'class' => 'readonly',
                    'readonly' => 'readonly'
                ),
                'formfield' => function (
                    $fieldname, $fieldtype, $fieldlength, $fieldvalue,
                    $fieldoptions
                ) {
                    global $_ARRAYLANG;
                    if (empty($fieldvalue)) {
                        $field = new \Cx\Core\Html\Model\Entity\TextElement(
                            $_ARRAYLANG['TXT_ORDER_WASNT_YET_EDITED']
                        );
                        return $field;
                    }

                    $field = new \Cx\Core\Html\Model\Entity\DataElement(
                        $fieldname,
                        $fieldvalue,
                        'input'
                    );
                    $field->setAttributes($fieldoptions['attributes']);
                    return $field;
                },
                'storecallback' => function($value) {
                    $date = new \DateTime('now');
                    return $date->format('Y-m-d H:i:s');
                }

            ),
            'modifiedBy' => array(
                'showOverview' => false,
                'allowFiltering' => false,
                'attributes' => array(
                    'class' => 'readonly',
                    'readonly' => 'readonly',
                ),
                'storecallback' => function($value) {
                    return $objFWUser = \FWUser::getFWUserObject()->objUser->getEmail();
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
                        'gender_undefined' => $_ARRAYLANG[
                        'TXT_SHOP_GENDER_UNDEFINED'
                        ],
                        'gender_male' => $_ARRAYLANG[
                        'TXT_SHOP_GENDER_MALE'
                        ],
                        'gender_female' => $_ARRAYLANG[
                        'TXT_SHOP_GENDER_FEMALE'
                        ]
                    );

                    $genderDropdown = new \Cx\Core\Html\Model\Entity\DataElement(
                        $fieldname,
                        $fieldvalue,
                        'select',
                        null,
                        $validData
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
            'orderItems' => array(
                'showOverview' => false,
                'allowFiltering' => false,
                'formfield' => function (
                    $fieldname, $fieldtype, $fieldlength,
                    $fieldvalue, $fieldoptions
                ) {
                    return $this->generateOrderItemView();
                },
                'storecallback' => function($value, $entity) {
                    $this->cx->getDb()->getEntityManager()->getRepository(
                        'Cx\Modules\Shop\Model\Entity\OrderItem'
                    )->save($value, $entity);
                },
            ),
            'relCustomerCoupons' => array(
                'showOverview' => false,
                'showDetail' => true,
                'mode' => 'associate',
                'type' => 'hidden',
                'allowFiltering' => false,
            ),
            'lang' => array(
                'showOverview' => false,
                'allowFiltering' => false,
                'attributes' => array(
                    'class' => 'readonly',
                ),
                'readonly' => true,
                'type' => 'input',
            ),
            'currency' => array(
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
                'attributes' => array(
                    'class' => 'readonly',
                ),
                'readonly' => true,
                'type' => 'input',
            ),
            'customer' => array(
                'showOverview' => true,
                'showDetail' => false,
                'sorting' => false,
                'table' => array (
                    'parse' => function ($value, $rowData) {
                        return $this->getCustomerLink($value, $rowData);
                    },
                    'attributes' => array(
                        'class' => 'order-customer',
                    ),
                ),
                'filterOptionsField' => function (
                    $parseObject, $fieldName, $elementName, $formName
                ) {
                    return $this->getCustomerGroupMenu($elementName, $formName);
                },
            ),
            'titleAddress' => array(
                'custom' => true,
                'showOverview' => false,
                'allowFiltering' => false,
                'formfield' => function() {
                    global $_ARRAYLANG;
                    return $this->getTitleRow(
                        array(
                            $_ARRAYLANG['TXT_BILLING_ADDRESS'],
                            $_ARRAYLANG['TXT_SHIPPING_ADDRESS']
                        )
                    );
                }
            ),
            'titlePaymentInfos' => array(
                'custom' => true,
                'showOverview' => false,
                'allowFiltering' => false,
                'formfield' => function() {
                    global $_ARRAYLANG;
                    return $this->getTitleRow(
                        array($_ARRAYLANG['TXT_PAYMENT_INFORMATIONS'])
                    );
                }
            ),
            'titleBill' => array(
                'custom' => true,
                'showOverview' => false,
                'allowFiltering' => false,
                'formfield' => function() {
                    global $_ARRAYLANG;
                    return $this->getTitleRow(
                        array($_ARRAYLANG['TXT_BILL'])
                    );
                }
            ),
            'titleNote' => array(
                'custom' => true,
                'showOverview' => false,
                'allowFiltering' => false,
                'formfield' => function() {
                    global $_ARRAYLANG;
                    return $this->getTitleRow(
                        array($_ARRAYLANG['TXT_CUSTOMER_REMARKS'])
                    );
                }
            ),
            'emptyField' => array(
                'custom' => true,
                'formfield' => function() {
                    return $this->getDivWrapper('');
                },
                'showOverview' => false,
            ),
        );
        $order = new \Cx\Modules\Shop\Model\Entity\Order();
        if (!empty($this->orderId)) {
            $order = $this->cx->getDb()->getEntityManager()->getRepository(
                '\Cx\Modules\Shop\Model\Entity\Order'
            )->findOneBy(array('id' => $this->orderId));
        }
        if (!empty($order) && count($order->getLsvs()) > 0) {
            $options['fields']['lsvs'] = array(
                'showOverview' => false,
                'allowFiltering' => false,
                'formfield' => function (
                    $fieldname, $fieldtype, $fieldlength, $fieldvalue,
                    $fieldoptions
                ) {
                    return $this->generateLsvs($fieldvalue);
                },
                'storecallback' => function($value, $entity) {
                    $repo = $this->cx->getDb()->getEntityManager()
                        ->getRepository(
                            '\Cx\Modules\Shop\Model\Entity\Lsv'
                        );
                    $repo->save($value, $entity->getId());
                }
            );
        } else {
            $options['fields']['lsvs'] = array(
                'showOverview' => false,
                'allowFiltering' => false,
                'showDetail' => false,
            );
        }
        return $options;
    }

    protected function getCustomerLink($value, $rowData)
    {
        $objUser = \FWUser::getFWUserObject()->objUser->getUser(
            $value->getId()
        );

        $name = $objUser->getProfileAttribute(
                'lastname'
            ) . ' ' .$objUser->getProfileAttribute(
                'firstname'
            );

        $link = new \Cx\Core\Html\Model\Entity\HtmlElement('a');
        $nameElement = new \Cx\Core\Html\Model\Entity\TextElement($name);
        $link->addChild($nameElement);
        // Use this when Ticket CLX-2296 is merged into master
        // \Cx\Core\Html\Controller\ViewGenerator::getVgShowUrl(0, $rowData['id']);
        $showUrl = '';
        $link->setAttribute('href', $showUrl);

        return $link;
    }

    /**
     * Get dropdown to search for customer groups.
     *
     * @param string $elementName name of element
     * @return \Cx\Core\Html\Model\Entity\DataElement
     */
    protected function getCustomerGroupMenu($elementName, $formName)
    {
        global $_ARRAYLANG;
        \Cx\Core\Setting\Controller\Setting::init('Shop', 'config');
        $resellerGroup = \Cx\Core\Setting\Controller\Setting::getValue(
            'usergroup_id_reseller',
            'Shop'
        );

        $customerGroup = \Cx\Core\Setting\Controller\Setting::getValue(
            'usergroup_id_customer',
            'Shop'
        );

        $validValues = array(
            '' => $_ARRAYLANG['TXT_SHOP_ORDER_CUSTOMER_GROUP_PLEASE_CHOOSE'],
            $resellerGroup => $_ARRAYLANG['TXT_CUSTOMER'],
            $customerGroup => $_ARRAYLANG['TXT_RESELLER'],
        );
        $searchField = new \Cx\Core\Html\Model\Entity\DataElement(
            $elementName,
            '',
            'select',
            null,
            $validValues
        );

        $searchField->setAttributes(
            array(
                'form' => $formName,
                'data-vg-attrgroup' => 'search',
                'data-vg-field' => 'customer',
                'class' => 'vg-encode'
            )
        );
        return $searchField;
    }

    /**
     * Get a dropdown with all status values.
     *
     * @param string $value    value of field
     * @param string $name     name of field
     * @param string $formName name of form
     * @return \Cx\Core\Html\Model\Entity\DataElement
     * @throws \Doctrine\ORM\ORMException
     */
    protected function getStatusMenu($value, $name = '', $id = 0, $formName = '')
    {
        global $_ARRAYLANG;

        $validValues = array();
        $statusValues = $this->cx->getDb()
            ->getEntityManager()->getRepository(
                $this->getNamespace()
                . '\\Model\Entity\Order'
            )->getStatusValues();
        if (!empty($formName)) {
            $validValues = array(
                '' => $_ARRAYLANG['TXT_SHOP_ORDER_STATUS_PLEASE_CHOOSE'],
            );
        }
        $validValues = array_merge($validValues, $statusValues);

        if (empty($name)) {
            $name = 'status';
        }

        if (!empty($id)) {
            $name = $name . '-' . $id;
        }
        $statusField = new \Cx\Core\Html\Model\Entity\DataElement(
            $name,
            $value,
            'select',
            null,
            $validValues
        );

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

    /**
     * Get status menu for detail view. It has a custom field to send a mail.
     *
     * @param string $fieldvalue value of field
     * @param string $fieldname  name of field
     * @return \Cx\Core\Html\Model\Entity\HtmlElement
     * @throws \Doctrine\ORM\ORMException
     */
    protected function getDetailStatusMenu($fieldvalue, $fieldname)
    {
        global $_ARRAYLANG;

        $wrapper = new \Cx\Core\Html\Model\Entity\HtmlElement('div');
        $statusMenu = $this->getStatusMenu($fieldvalue, $fieldname);

        $wrapperEmail = new \Cx\Core\Html\Model\Entity\HtmlElement('div');
        $textEmail = new \Cx\Core\Html\Model\Entity\TextElement(
            $_ARRAYLANG['TXT_SEND_MAIL']
        );
        $labelEmail = new \Cx\Core\Html\Model\Entity\HtmlElement('label');
        $inputEmail = new \Cx\Core\Html\Model\Entity\DataElement(
            'sendMail',
            '1',
            'input'
        );

        $wrapperEmail->setAttribute('id', 'sendMailDiv');
        $labelEmail->setAttribute('for', 'sendMail');
        $inputEmail->setAttributes(
            array(
                'type' => 'checkbox',
                'id' => 'sendMail',
                'onclick' => 'swapSendToStatus();',
                'checked' => 'checked'
            )
        );

        $labelEmail->addChild($textEmail);
        $wrapperEmail->addChild($inputEmail);
        $wrapperEmail->addChild($labelEmail);
        $wrapper->addChild($statusMenu);
        $wrapper->addChild($wrapperEmail);

        return $wrapper;
    }

    /**
     * Get custom input fields to align them on the right side.
     *
     * @param string $fieldname  name of field
     * @param string $fieldvalue value of field
     * @return \Cx\Core\Html\Model\Entity\HtmlElement
     */
    protected function getCustomInputFields($fieldname, $fieldvalue)
    {
        global $_ARRAYLANG;

        $wrapper = new \Cx\Core\Html\Model\Entity\HtmlElement('div');
        $wrapper->addClass('custom-input');

        $title = new \Cx\Core\Html\Model\Entity\TextElement(
            $_ARRAYLANG[$fieldname]
        );
        $input = new \Cx\Core\Html\Model\Entity\DataElement(
            $fieldname,
            $fieldvalue,
            'input'
        );
        $addition = new \Cx\Core\Html\Model\Entity\TextElement('CHF');
        $spanWrapper = new \Cx\Core\Html\Model\Entity\HtmlElement('span');
        $spanWrapper->addChild($addition);

        $wrapper->addChild($title);
        $wrapper->addChild($input);
        $wrapper->addChild($spanWrapper);

        return $wrapper;
    }

    /**
     * Return custom lsv edit field.
     *
     * @param \Cx\Modules\Shop\Model\Entity\Lsv $entity lsv entity
     * @return \Cx\Core\Html\Model\Entity\HtmlElement
     */
    protected function generateLsvs($entity)
    {
        global $_ARRAYLANG;

        $entity = $this->cx->getDb()->getEntityManager()->getRepository(
            '\Cx\Modules\Shop\Model\Entity\Lsv'
        )->findOneBy(array('orderId' => $this->orderId));

        if (empty($entity)) {
            $empty = new \Cx\Core\Html\Model\Entity\TextElement('');
            return $empty;
        }

        $em = \Cx\Core\Core\Controller\Cx::instanciate()->getDb()
            ->getEntityManager();
        $meta = $em->getClassMetadata('\Cx\Modules\Shop\Model\Entity\Lsv');
        $attributes = $meta->getFieldNames();
        $wrapper = new \Cx\Core\Html\Model\Entity\HtmlElement('div');

        $doNotShow = array('orderId');

        foreach ($attributes as $attribute) {

            if (in_array($attribute, $doNotShow)) {
                continue;
            }

            $divGroup = new \Cx\Core\Html\Model\Entity\HtmlElement('div');
            $label = new \Cx\Core\Html\Model\Entity\HtmlElement('label');
            $title = new \Cx\Core\Html\Model\Entity\TextElement(
                $_ARRAYLANG[$attribute]
            );
            $divControls = new \Cx\Core\Html\Model\Entity\HtmlElement('div');
            $input = new \Cx\Core\Html\Model\Entity\HtmlElement('input');

            $getter = 'get' . ucfirst($attribute);

            $divGroup->addClass('group');
            $label->setAttribute('for', 'form-0-' . $attribute);
            $divControls->addClass('controls');
            $input->setAttributes(
                array(
                    'name' => $attribute,
                    'value' => $entity->$getter(),
                    'type' => 'text',
                    'id' => 'form-0-'.$attribute,
                    'onkeyup' => 'return true;',
                    'class' => 'form-control'
                )
            );

            $label->addChild($title);
            $divGroup->addChild($label);
            $divGroup->addChild($divControls);
            $divControls->addChild($input);
            $wrapper->addChild($divGroup);
        }

        return $wrapper;
    }

    /**
     * Get the order item table.
     *
     * @return \Cx\Core\Html\Model\Entity\HtmlElement
     * @throws \Cx\Core\Setting\Controller\SettingException
     * @throws \Doctrine\ORM\ORMException
     */
    protected function generateOrderItemView()
    {
        global $_ARRAYLANG;

        if (empty($this->orderId)) {
            return;
        }

        $tableConfig['entity'] = '\Cx\Modules\Shop\Model\Entity\OrderItem';
        $tableConfig['criteria'] = array('orderId' => $this->orderId);

        $orderItems = $this->cx->getDb()->getEntityManager()->getRepository(
            $tableConfig['entity']
        )->findBy($tableConfig['criteria']);

        $order = $this->cx->getDb()->getEntityManager()->getRepository(
            '\Cx\Modules\Shop\Model\Entity\Order'
        )->findOneBy(array('id' => $this->orderId));

        $currency = $order->getCurrency()->getCode();

        $tableConfig['header'] = array(
            'quantity' => array(
                'type' => 'input',
            ),
            'product_name' => array(
                'type' => 'text',
            ),
            'weight' => array(
                'type' => 'input',
            ),
            'price' => array(
                'type' => 'input',
                'addition' => $currency,
            ),
            'vat_rate' => array(
                'type' => 'input',
                'addition' => '%',
            ),
            'sum' => array(
                'type' => 'input',
                'addition' => $currency,
            ),
        );

        $table = new \Cx\Core\Html\Model\Entity\HtmlElement('table');
        $tableBody = new \Cx\Core\Html\Model\Entity\HtmlElement('tbody');;
        $headerTr = new \Cx\Core\Html\Model\Entity\HtmlElement('tr');

        $table->addChild($tableBody);
        $tableBody->addChild($headerTr);

        foreach ($tableConfig['header'] as $key => $header) {
            $th = new \Cx\Core\Html\Model\Entity\HtmlElement('th');
            $title = $_ARRAYLANG[$key];
            $title = new \Cx\Core\Html\Model\Entity\TextElement($title);
            $th->addChild($title);
            $th->setAttributes(
                array(
                    'id' => $key,
                    'name' => $key,
                )
            );
            $headerTr->addChild($th);
        }
        $cols = $this->cx->getDb()->getEntityManager()->getClassMetadata(
            $tableConfig['entity']
        )->getColumnNames();

        foreach ($orderItems as $orderItem) {
            $tr = new \Cx\Core\Html\Model\Entity\HtmlElement('tr');
            $id = $orderItem->getId();

            foreach ($tableConfig['header'] as $key => $header) {
                $td = new \Cx\Core\Html\Model\Entity\HtmlElement('td');

                // Replace _ and set new word to uppercase, to get the getter
                // name
                $methodName = str_replace(
                    " ",
                    "",
                    mb_convert_case(
                        str_replace(
                            "_",
                            " ",
                            $key
                        ),
                        MB_CASE_TITLE
                    )
                );

                $getter = 'get' . ucfirst($methodName);
                $value = '';
                if (in_array($key, $cols)) {
                    $value = $orderItem->$getter();
                }

                if ($header['type'] == 'input') {
                    $field = new \Cx\Core\Html\Model\Entity\DataElement(
                        'product_' . $key .'-'. $id,
                        $value,
                        'input'
                    );
                    $field->setAttributes(
                        array(
                            'onchange' => 'calcPrice(' . $id . ')',
                            'id' => 'product_' . $key .'-'. $id
                        )
                    );
                } else {
                    $field = new \Cx\Core\Html\Model\Entity\HtmlElement(
                        'label'
                    );
                    $text = new \Cx\Core\Html\Model\Entity\TextElement(
                        $value
                    );
                    $field->setAttributes(
                        array(
                            'name' => 'product_' . $key .'-'. $id,
                            'id' => 'product_' . $key .'-'. $id,
                            'class' => 'product',
                        )
                    );
                    $field->addChild($text);
                    $hiddenField = new \Cx\Core\Html\Model\Entity\DataElement(
                        'product_product_id-'. $id,
                        $orderItem->getProductId(),
                        'input'
                    );
                    $hiddenField->setAttributes(
                        array(
                            'id' => 'product_product_id-'. $id,
                            'class' => 'product_ids',
                            'type' => 'hidden'
                        )
                    );
                    $td->addChild($hiddenField);
                }

                if ($key == 'sum') {
                    $field->setAttribute('readonly', 'readonly');
                }

                $td->addChild($field);
                $tr->addChild($td);

                if (empty($header['addition'])) {
                    continue;
                }
                $addition = new \Cx\Core\Html\Model\Entity\TextElement(
                    $header['addition']
                );
                $spanWrapper = new \Cx\Core\Html\Model\Entity\HtmlElement('span');
                $spanWrapper->addChild($addition);
                $td->addChild($spanWrapper);
            }
            $tableBody->addChild($tr);
        }

        // add new empty order item
        $trEmpty = new \Cx\Core\Html\Model\Entity\HtmlElement('tr');

        foreach ($tableConfig['header'] as $key => $header) {
            $td = new \Cx\Core\Html\Model\Entity\HtmlElement('td');
            $value = '0';

            if ($key == 'product_name') {
                $validValues[0] = '-';
                $products = $this->cx->getDb()->getEntityManager()
                    ->getRepository(
                        '\Cx\Modules\Shop\Model\Entity\Product'
                    )->findAll();

                foreach ($products as $product) {
                    $validValues[$product->getId()] = $product->getName();
                }

                $field = new \Cx\Core\Html\Model\Entity\DataElement(
                    'product_' . $key .'-0',
                    0,
                    'select',
                    null,
                    $validValues
                );
                $field->setAttributes(
                    array(
                        'onchange' =>'changeProduct(0,this.value);',
                        'id' => 'product_' . $key .'-0',
                        'class' => 'product',
                    )
                );
                $hiddenField = new \Cx\Core\Html\Model\Entity\DataElement(
                    'product_product_id-0', '0', 'input'
                );
                $hiddenField->setAttributes(
                    array(
                        'id' => 'product_product_id-0',
                        'class' => 'product_ids',
                        'type' => 'hidden'
                    )
                );

                $td->addChild($hiddenField);
            } else if ($header['type'] == 'input') {
                $field = new \Cx\Core\Html\Model\Entity\DataElement(
                    'product_' . $key .'-0',
                    $value,
                    'input'
                );
                $field->setAttributes(
                    array(
                        'onchange' => 'calcPrice(0)',
                        'id' => 'product_' . $key .'-0',
                    )
                );
            } else {
                $field = new \Cx\Core\Html\Model\Entity\TextElement(
                    $value
                );
                $field->setAttribute('name', 'product' . $key .'-0');
            }

            if ($key == 'sum') {
                $field->setAttribute('readonly', 'readonly');
            }

            $td->addChild($field);
            $trEmpty->addChild($td);

            if (empty($header['addition'])) {
                continue;
            }
            $addition = new \Cx\Core\Html\Model\Entity\TextElement(
                $header['addition']
            );
            $spanWrapper = new \Cx\Core\Html\Model\Entity\HtmlElement(
                'span'
            );
            $spanWrapper->addChild($addition);
            $td->addChild($spanWrapper);
        }

        $tableBody->addChild($trEmpty);

        // add coupon
        $couponRel = $this->cx->getDb()->getEntityManager()->getRepository(
            '\Cx\Modules\Shop\Model\Entity\RelCustomerCoupon'
        )->findOneBy(array('orderId' => $this->orderId));

        if (!empty($couponRel)) {
            $trCoupon = new \Cx\Core\Html\Model\Entity\HtmlElement('tr');
            $tdEmpty = new \Cx\Core\Html\Model\Entity\HtmlElement('td');
            $trCoupon->addChild($tdEmpty);

            $tdName = new \Cx\Core\Html\Model\Entity\HtmlElement('td');
            $text = new \Cx\Core\Html\Model\Entity\TextElement(
                $_ARRAYLANG['TXT_SHOP_DISCOUNT_COUPON_CODE'] . ' ' .
                $couponRel->getCode()
            );
            $tdName->addChild($text);
            $trCoupon->addChild($tdName);

            $tdEmpty = new \Cx\Core\Html\Model\Entity\HtmlElement('td');
            $tdEmpty->setAttribute('colspan', 3);
            $trCoupon->addChild($tdEmpty);

            $tdAmount = new \Cx\Core\Html\Model\Entity\HtmlElement('td');
            $input = new \Cx\Core\Html\Model\Entity\DataElement(
                'amount',
                '-' . $couponRel->getAmount(),
                'input'
            );
            $discountCoupon = $this->cx->getDb()->getEntityManager()->getRepository(
                '\Cx\Modules\Shop\Model\Entity\DiscountCoupon'
            )->findOneBy(
                array(
                    'code' => $couponRel->getCode(),
                )
            );
            $input->setAttributes(
                array(
                    'id' => 'coupon-amount',
                    'data-rate' => $discountCoupon->getDiscountRate(),
                    'readonly' => 'readonly'
                )
            );
            $tdAmount->addChild($input);
            $trCoupon->addChild($tdAmount);

            $tableBody->addChild($trCoupon);
        }

        // add weight and netprice
        $trCustom = new \Cx\Core\Html\Model\Entity\HtmlElement('tr');
        $tdEmpty = new \Cx\Core\Html\Model\Entity\HtmlElement('td');
        $trCustom->addChild($tdEmpty);

        $tdWeightTitle = new \Cx\Core\Html\Model\Entity\HtmlElement('td');
        $weightTitle = new \Cx\Core\Html\Model\Entity\TextElement(
            $_ARRAYLANG['TXT_TOTAL_WEIGHT']
        );
        $tdWeightTitle->addClass('shop-order-info');
        $tdWeightTitle->addChild($weightTitle);

        $tdWeightInput = new \Cx\Core\Html\Model\Entity\HtmlElement('td');
        $weightInput = new \Cx\Core\Html\Model\Entity\DataElement(
            'total-weight',
            '',
            'input'
        );

        $weightInput->setAttributes(
            array(
                'id' => 'total-weight',
                'readonly' => 'readonly',
            )
        );

        $tdWeightInput->addChild($weightInput);

        $trCustom->addChild($tdWeightTitle);
        $trCustom->addChild($tdWeightInput);

        $trCustom->addChild($tdEmpty);

        $tdNetpriceTitle = new \Cx\Core\Html\Model\Entity\HtmlElement('td');
        $netpriceTitle = new \Cx\Core\Html\Model\Entity\TextElement(
            $_ARRAYLANG['TXT_SHOP_DETAIL_NETPRICE']
        );
        $tdNetpriceTitle->addClass('shop-order-info');
        $tdNetpriceTitle->addChild($netpriceTitle);

        $tdNetpriceInput = new \Cx\Core\Html\Model\Entity\HtmlElement('td');
        $netpriceInput = new \Cx\Core\Html\Model\Entity\DataElement(
            'netprice',
            '',
            'input'
        );

        $netpriceInput->setAttributes(
            array(
                'id' => 'netprice',
                'readonly' => 'readonly'
            )
        );

        $additionChf = new \Cx\Core\Html\Model\Entity\TextElement('CHF');

        $tdNetpriceInput->addChild($netpriceInput);
        $tdNetpriceInput->addChild($spanWrapper);

        $trCustom->addChild($tdNetpriceTitle);
        $trCustom->addChild($tdNetpriceInput);
        $tableBody->addChild($trCustom);

        $customerId = $order->getCustomerId();
        $this->defineJsVariables($customerId);

        // Load custom Js File for order edit view
        \JS::registerJS('modules/Shop/View/Script/EditOrder.js');

        return $table;
    }

    /**
     * Defines variables that are used in the javascript file EditOrder.js.
     *
     * @global array $_ARRAYLANG array containing the language variables
     * @param  int   $customerId Id of customer
     * @throws \Doctrine\ORM\ORMException
     * @throws \Cx\Core\Setting\Controller\SettingException
     */
    protected function defineJsVariables($customerId)
    {
        global $_ARRAYLANG;

        $shipper = new \Cx\Modules\Shop\Model\Entity\Shipper();
        $products = new \Cx\Modules\Shop\Model\Entity\Product();
        $customer = \Cx\Modules\Shop\Controller\Customer::getById($customerId);

        $isReseller = $customer->isReseller();
        $groupId = $customer->getGroupId();
        $productsJsArr = $products->getJsArray($groupId, $isReseller);

        $shipmentCostJsArr = $shipper->getJsArray();

        $jsVariables = array(
            array(
                'name' => 'SHIPPER_INFORMATION',
                'content' => $shipmentCostJsArr,
            ),
            array(
                'name' => 'VAT_INCLUDED',
                'content' => \Cx\Modules\Shop\Model\Entity\Vat::isIncluded(),
            ),
            array(
                'name' => 'PRODUCT_LIST',
                'content' => $productsJsArr,
            ),
            array(
                'name' => 'TXT_WARNING_SHIPPER_WEIGHT',
                'content' => $_ARRAYLANG['TXT_WARNING_SHIPPER_WEIGHT'],
            ),
            array(
                'name' => 'TXT_PRODUCT_ALREADY_PRESENT',
                'content' => $_ARRAYLANG['TXT_PRODUCT_ALREADY_PRESENT'],
            ),
        );

        $scope = 'order';
        foreach ($jsVariables as $jsVariable) {
            \ContrexxJavascript::getInstance()->setVariable(
                $jsVariable['name'],
                $jsVariable['content'],
                $scope
            );
        }
    }

    /**
     * Return a tooltip containing the note of the order.
     *
     * @param string $value order message
     * @return \Cx\Core\Html\Model\Entity\HtmlElement
     */
    protected function getNoteToolTip($value)
    {
        $wrapper = new \Cx\Core\Html\Model\Entity\HtmlElement('div');
        $wrapper->addClass('tooltip-wrapper');

        if (empty($value) || $value === ' ') {
            return $wrapper;
        }

        $tooltipTrigger = new \Cx\Core\Html\Model\Entity\HtmlElement('span');
        $tooltipTrigger->setAttribute(
            'class',
            'icon-info tooltip-trigger icon-comment'
        );
        $tooltipTrigger->allowDirectClose(false);

        $tooltipMessage = new \Cx\Core\Html\Model\Entity\HtmlElement('span');
        $tooltipMessage->setAttribute('class', 'tooltip-message');
        $tooltipMessage->addChild(
            new \Cx\Core\Html\Model\Entity\TextElement($value)
        );

        $wrapper->addChild($tooltipTrigger);
        $wrapper->addChild($tooltipMessage);
        return $wrapper;
    }

    /**
     * Sets up the Order statistics
     * @param   \Cx\Core\Html\Sigma     $objTemplate  The optional Template,
     *                                                by reference
     * @global  ADONewConnection        $objDatabase
     * @global  array                   $_ARRAYLANG
     * @todo    Rewrite the statistics in a seperate class, extending Order
     * @static
     */
    static function view_statistics(&$objTemplate=null)
    {
        global $_ARRAYLANG;
        $cx = \Cx\Core\Core\Controller\Cx::instanciate();

        if (!$objTemplate || !$objTemplate->blockExists('no_order')) {
            $objTemplate = new \Cx\Core\Html\Sigma(
                \Cx\Core\Core\Controller\Cx::instanciate()->getCodeBaseModulePath() . '/Shop/View/Template/Backend');
            $objTemplate->loadTemplateFile('module_shop_statistic.html');
        }
        $objTemplate->setGlobalVariable($_ARRAYLANG);
        // Get the first order date; if its empty, no order has been placed yet
        $firstOrder = $cx->getDb()->getEntityManager()->getRepository(
            'Cx\Modules\Shop\Model\Entity\Order'
        )->getFirstOrder();
        if (empty($firstOrder)) {
            $objTemplate->touchBlock('no_order');
            return $objTemplate;
        }
        $year_first_order = $firstOrder->getDateTime()->format('Y');
        $month_first_order = $firstOrder->getDateTime()->format('m');
        $start_month = $end_month = $start_year = $end_year = NULL;
        if (isset($_REQUEST['submitdate'])) {
            // A range is requested
            $start_month = intval($_REQUEST['startmonth']);
            $end_month = intval($_REQUEST['stopmonth']);
            $start_year = intval($_REQUEST['startyear']);
            $end_year = intval($_REQUEST['stopyear']);
        } else {
            // Default range to one year, or back to the first order if less
            $start_month = $month_first_order;
            $end_month = Date('m');
            $start_year = $end_year = Date('Y');
            if ($year_first_order < $start_year) {
                $start_year -= 1;
                if (   $year_first_order < $start_year
                    || $month_first_order < $start_month) {
                    $start_month = $end_month;
                }
            }
        }
        $objTemplate->setVariable(array(
            'SHOP_START_MONTH' =>
                Shopmanager::getMonthDropdownMenu($start_month),
            'SHOP_END_MONTH' =>
                Shopmanager::getMonthDropdownMenu($end_month),
            'SHOP_START_YEAR' =>
                Shopmanager::getYearDropdownMenu(
                    $start_year, $year_first_order),
            'SHOP_END_YEAR' =>
                Shopmanager::getYearDropdownMenu(
                    $end_year, $year_first_order),
        ));
        $start_date = date(ASCMS_DATE_FORMAT_INTERNATIONAL_DATETIME,
            mktime(0, 0, 0, $start_month, 1, $start_year));
        // mktime() will fix the month from 13 to 01, see example 2
        // on http://php.net/manual/de/function.mktime.php.
        // Mind that this is exclusive and only used in the queries below
        // so that Order date < $end_date!
        $end_date = date(ASCMS_DATE_FORMAT_INTERNATIONAL_DATETIME,
            mktime(0, 0, 0, $end_month+1, 1, $end_year));
        $qb = $cx->getDb()->getEntityManager()->createQueryBuilder();

        $selectedStat = (isset($_REQUEST['selectstats'])
            ? intval($_REQUEST['selectstats']) : 0);
        if ($selectedStat == 2) {
            // Product statistic
            $objTemplate->setVariable(array(
                'TXT_COLUMN_1_DESC' => $_ARRAYLANG['TXT_PRODUCT_NAME'],
                'TXT_COLUMN_2_DESC' => $_ARRAYLANG['TXT_COUNT_ARTICLES'],
                'TXT_COLUMN_3_DESC' => $_ARRAYLANG['TXT_STOCK'],
                'SHOP_ORDERS_SELECTED' => '',
                'SHOP_ARTICLES_SELECTED' => \Html::ATTRIBUTE_SELECTED,
                'SHOP_CUSTOMERS_SELECTED' => '',
            ));
             $query = $qb->select(
                array(
                    'A.productId AS id', 'A.quantity AS shopColumn2',
                    'A.price AS total', 'B.stock AS shopColumn3', 'C.currencyId',
                    'B.name AS title'
                )
            )->from('Cx\Modules\Shop\Model\Entity\OrderItem', 'A')
            ->join(
                'A.order', 'C', 'WITH',
                $qb->expr()->eq('A.orderId', 'C.id')
            )->join(
                'A.product', 'B', 'WITH',
                $qb->expr()->eq('A.productId', 'B.id')
            )->where(
                $qb->expr()->andX(
                    'C.dateTime >= ?1',
                    'C.dateTime < ?2',
                    $qb->expr()->orX(
                        $qb->expr()->eq('C.status', '?3'),
                        $qb->expr()->eq('C.status', '?4')
                    )
                )
            )->orderBy('shopColumn2', 'DESC')->setParameters(
                 array(
                     1 => $start_date,
                     2 => $end_date,
                     3 => Order::STATUS_CONFIRMED,
                     4 => Order::STATUS_COMPLETED
                 )
            )->getQuery();
        } elseif ($selectedStat == 3) {
            // Customer statistic
            $objTemplate->setVariable(array(
                'TXT_COLUMN_1_DESC' => $_ARRAYLANG['TXT_NAME'],
                'TXT_COLUMN_2_DESC' => $_ARRAYLANG['TXT_COMPANY'],
                'TXT_COLUMN_3_DESC' => $_ARRAYLANG['TXT_COUNT_ARTICLES'],
                'SHOP_ORDERS_SELECTED' => '',
                'SHOP_ARTICLES_SELECTED' => '',
                'SHOP_CUSTOMERS_SELECTED' => \Html::ATTRIBUTE_SELECTED,
            ));
            $query = $qb->select(
                array(
                    'A.sum AS total ', 'A.currencyId', 'SUM(B.quantity) AS shopColumn3', 'A.customerId'
                )
            )->from(
                'Cx\Modules\Shop\Model\Entity\Order', 'A'
            )->join(
                'A.orderItems', 'B', 'WITH',
                $qb->expr()->eq('A.id', 'B.orderId')
            )->where(
                $qb->expr()->andX(
                    'A.dateTime >= ?1',
                    'A.dateTime < ?2',
                    $qb->expr()->orX(
                        $qb->expr()->eq('A.status', '?3'),
                        $qb->expr()->eq('A.status', '?4')
                    )
                )
            )->groupBy('B.orderId')->orderBy('A.sum', 'DESC')
             ->setParameters(
                 array(
                    1 => $start_date,
                    2 => $end_date,
                    3 => Order::STATUS_CONFIRMED,
                    4 => Order::STATUS_COMPLETED
                 )
             )->getQuery();
        } else {
            // Order statistic (default); sales per month
            $objTemplate->setVariable(array(
                'TXT_COLUMN_1_DESC' => $_ARRAYLANG['TXT_DATE'],
                'TXT_COLUMN_2_DESC' => $_ARRAYLANG['TXT_COUNT_ORDERS'],
                'TXT_COLUMN_3_DESC' => $_ARRAYLANG['TXT_COUNT_ARTICLES'],
                'SHOP_ORDERS_SELECTED' => \Html::ATTRIBUTE_SELECTED,
                'SHOP_ARTICLES_SELECTED' => '',
                'SHOP_CUSTOMERS_SELECTED' => '',
            ));

            $query = $qb->select(
                array(
                    'SUM(A.quantity) AS shopColumn3', 'COUNT(A.orderId) AS shopColumn2',
                    'B.currencyId', 'B.sum AS total', 'B.dateTime'
                )
            )->from('Cx\Modules\Shop\Model\Entity\OrderItem', 'A')
            ->join(
                'A.order', 'B', 'WITH',
                $qb->expr()->eq('A.orderId', 'B.id')
            )->where(
                $qb->expr()->andX(
                    'B.dateTime >= ?1',
                    'B.dateTime < ?2',
                    $qb->expr()->orX(
                        $qb->expr()->eq('B.status', '?3'),
                        $qb->expr()->eq('B.status', '?4')
                    )
                )
            )->groupBy('A.id')->orderBy('B.dateTime', 'DESC')
            ->setParameters(
                array(
                    1 => $start_date,
                    2 => $end_date,
                    3 => Order::STATUS_CONFIRMED,
                    4 => Order::STATUS_COMPLETED
                )
            )->getQuery();
        }
        $arrayResults = array();
        $results = $query->getArrayResult();

        $sumColumn3 = $sumColumn4 = 0;
        $sumColumn2 = '';

        $defaultCurrency = $cx->getDb()->getEntityManager()->getRepository(
            '\Cx\Modules\Shop\Model\Entity\Currency'
        )->getDefaultCurrency();
        if ($selectedStat == 2) {
            // Product statistc
            foreach ($results as $result) {
                // set currency id
                \Cx\Modules\Shop\Controller\CurrencyController::setActiveCurrencyId($result['currencyId']);
                $key = $result['id'];
                if (!isset($arrayResults[$key])) {
                    $arrayResults[$key] = array(
                        'column1' =>
                            '<a href="index.php?cmd=Shop'.MODULE_INDEX.
                            '&amp;act=products&amp;tpl=manage&amp;id='.
                            $result['id'].
                            '" title="'.$result['title'].'">'.
                            $result['title'].'</a>',
                        'column2' => 0,
                        'column3' => $result['shopColumn3'],
                        'column4' => 0,
                    );
                }
                $arrayResults[$key]['column2'] +=
                    + $result['shopColumn2'];
                $arrayResults[$key]['column4'] +=
                    + $result['shopColumn2']
                    * \Cx\Modules\Shop\Controller\CurrencyController::getDefaultCurrencyPrice($result['total']);
            }
            if (is_array($arrayResults)) {
                foreach ($arrayResults AS $entry) {
                    $sumColumn2 = $sumColumn2 + $entry['column2'];
                    $sumColumn3 = $sumColumn3 + $entry['column3'];
                    $sumColumn4 = $sumColumn4 + $entry['column4'];
                }
                rsort($arrayResults);
            }
        } elseif ($selectedStat == 3) {
            // Customer statistic
            foreach ($results as $result) {
                \Cx\Modules\Shop\Controller\CurrencyController::setActiveCurrencyId($result['currencyId']);
                $key = $result['customerId'];
                if (!isset($arrayResults[$key])) {
                    $objUser = \FWUser::getFWUserObject()->objUser;
                    $objUser = $objUser->getUser($key);
                    $company = '';
                    $name = $_ARRAYLANG['TXT_SHOP_CUSTOMER_NOT_FOUND'];
                    if ($objUser) {
                        $company = $objUser->getProfileAttribute('company');
                        $name =
                            $objUser->getProfileAttribute('firstname').' '.
                            $objUser->getProfileAttribute('lastname');
                    }
                    $arrayResults[$key] = array(
                        'column1' =>
                            '<a href="index.php?cmd=Shop'.MODULE_INDEX.
                            '&amp;act=customerdetails&amp;customer_id='.
                            $result->fields['id'].'">'.$name.'</a>',
                        'column2' => $company,
                        'column3' => 0,
                        'column4' => 0,
                    );
                }
                $arrayResults[$key]['column3'] += $result['shopColumn3'];
                $arrayResults[$key]['column4'] += \Cx\Modules\Shop\Controller\CurrencyController::getDefaultCurrencyPrice($result['total']);
                $sumColumn3 += $result['shopColumn3'];
                $sumColumn4 += \Cx\Modules\Shop\Controller\CurrencyController::getDefaultCurrencyPrice($result['total']);
            }
        } else {
            // Order statistic (default)
            $arrayMonths = explode(',', $_ARRAYLANG['TXT_MONTH_ARRAY']);
            foreach ($results as $result) {
                $key = $result['dateTime']->format('Y').'.'.$result['dateTime']->format('M');
                if (!isset($arrayResults[$key])) {
                    $arrayResults[$key] = array(
                        'column1' => '',
                        'column2' => 0,
                        'column3' => 0,
                        'column4' => 0,
                    );
                }
                $arrayResults[$key]['column1'] = $arrayMonths[intval($result['dateTime']->format('m'))-1].' '.$result['dateTime']->format('Y');
                $arrayResults[$key]['column2'] = $arrayResults[$key]['column2'] + 1;
                $arrayResults[$key]['column3'] = $arrayResults[$key]['column3'] + $result['shopColumn3'];
                $arrayResults[$key]['column4'] = $arrayResults[$key]['column4'] + \Cx\Modules\Shop\Controller\CurrencyController::getDefaultCurrencyPrice($result['total']);
                $sumColumn2 = $sumColumn2 + 1;
                $sumColumn3 = $sumColumn3 + $result['shopColumn3'];
                $sumColumn4 = $sumColumn4 + \Cx\Modules\Shop\Controller\CurrencyController::getDefaultCurrencyPrice($result['total']);
            }
            krsort($arrayResults, SORT_NUMERIC);
        }
        $objTemplate->setCurrentBlock('statisticRow');
        $i = 0;
        if (is_array($arrayResults)) {
            foreach ($arrayResults as $entry) {
                $objTemplate->setVariable(array(
                    'SHOP_ROWCLASS' => 'row'.(++$i % 2 + 1),
                    'SHOP_COLUMN_1' => $entry['column1'],
                    'SHOP_COLUMN_2' => $entry['column2'],
                    'SHOP_COLUMN_3' => $entry['column3'],
                    'SHOP_COLUMN_4' =>
                        \Cx\Modules\Shop\Controller\CurrencyController::formatPrice($entry['column4']).' '.
                        $defaultCurrency->getSymbol(),
                ));
                $objTemplate->parse('statisticRow');
            }
        }
        $qbCurrency = $cx->getDb()->getEntityManager()->createQueryBuilder();
        $queryCurrency = $qbCurrency->select(
            array(
                'A.currencyId', 'A.sum', 'A.dateTime',
            )
        )->from(
            'Cx\Modules\Shop\Model\Entity\Order', 'A'
        )->where(
            $qbCurrency->expr()->orX(
                $qbCurrency->expr()->eq('A.status', '?1'),
                $qbCurrency->expr()->eq('A.status', '?2')
            )
        )->orderBy('A.dateTime', 'DESC')->setParameters(
            array(
                1 => Order::STATUS_CONFIRMED,
                2 => Order::STATUS_COMPLETED
            )
        )->getQuery();

        $resultsCurrency = $queryCurrency->getArrayResult();

        if (!$resultsCurrency) {
            return Order::errorHandler();
        }
        $totalSoldProducts = 0;

        $qbTotal = $cx->getDb()->getEntityManager()->createQueryBuilder();
        $queryTotalProducts = $qbTotal->select(
            'SUM(B.quantity) AS shopTotalSoldProducts'
        )->from('Cx\Modules\Shop\Model\Entity\OrderItem', 'B')
        ->join(
            'B.order', 'A', 'WITH',
            $qbTotal->expr()->eq('A.id', 'B.orderId')
        )->where(
                $qbTotal->expr()->orX(
                $qbTotal->expr()->eq('A.status', '?1'),
                $qbTotal->expr()->eq('A.status', '?2')
            )
        )->setParameters(
            array(
                1 => Order::STATUS_CONFIRMED,
                2 => Order::STATUS_COMPLETED
            )
        )->getQuery();
        $resultTotal = $queryTotalProducts->getSingleResult();

        if ($resultTotal) {
            $totalSoldProducts = $resultTotal['shopTotalSoldProducts'];
        }
        $totalOrderSum = 0;
        $totalOrders = 0;
        $bestMonthSum = 0;
        $bestMonthDate = '';
        $arrShopMonthSum = array();
        foreach ($results as $result) {
            $orderSum = \Cx\Modules\Shop\Controller\CurrencyController::getDefaultCurrencyPrice($result['total']);
            $date = new \DateTime($resultsCurrency['dateTime']);
            if (!isset($arrShopMonthSum[$date->format('Y')][$date->format('m')])) {
                $arrShopMonthSum[$date->format('Y')][$date->format('m')] = 0;
            }
            $arrShopMonthSum[$date->format('Y')][$date->format('m')] += $orderSum;
            $totalOrderSum += $orderSum;
            $totalOrders++;
        }
        $months = explode(',', $_ARRAYLANG['TXT_MONTH_ARRAY']);
        foreach ($arrShopMonthSum as $year => $arrMonth) {
            foreach ($arrMonth as $month => $sum) {
                if ($bestMonthSum < $sum) {
                    $bestMonthSum = $sum;
                    $bestMonthDate = $months[$month-1].' '.$year;
                }
            }
        }
        $objTemplate->setVariable(array(
            'SHOP_ROWCLASS' => 'row'.(++$i % 2 + 1),
            'SHOP_TOTAL_SUM' =>
                \Cx\Modules\Shop\Controller\CurrencyController::formatPrice($totalOrderSum).' '.
                $defaultCurrency->getSymbol(),
            'SHOP_MONTH' => $bestMonthDate,
            'SHOP_MONTH_SUM' =>
                \Cx\Modules\Shop\Controller\CurrencyController::formatPrice($bestMonthSum).' '.
                $defaultCurrency->getSymbol(),
            'SHOP_TOTAL_ORDERS' => $totalOrders,
            'SHOP_SOLD_ARTICLES' => $totalSoldProducts,
            'SHOP_SUM_COLUMN_2' => $sumColumn2,
            'SHOP_SUM_COLUMN_3' => $sumColumn3,
            'SHOP_SUM_COLUMN_4' =>
                \Cx\Modules\Shop\Controller\CurrencyController::formatPrice($sumColumn4).' '.
                $defaultCurrency->getSymbol(),
        ));
        return true;
    }

    protected function getTitleRow($titles)
    {
        $table = new \Cx\Core\Html\Model\Entity\HtmlElement('table');
        $tr = new \Cx\Core\Html\Model\Entity\HtmlElement('tr');

        foreach ($titles as $title) {
            $th = new \Cx\Core\Html\Model\Entity\HtmlElement('th');
            $title = new \Cx\Core\Html\Model\Entity\TextElement($title);
            $th->addChild($title);
            $tr->addChild($th);
        }
        $table->addChild($tr);
        $table->addClass('adminlist title-table');
        return $table;
    }

    protected function getDivWrapper($value)
    {
        $wrapper = new \Cx\Core\Html\Model\Entity\HtmlElement('div');
        $value = new \Cx\Core\Html\Model\Entity\TextElement($value);
        $wrapper->addChild($value);
        return $wrapper;
    }
}