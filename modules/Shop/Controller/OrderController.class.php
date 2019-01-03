<?php
/**
 * Created by PhpStorm.
 * User: sam
 * Date: 03.01.19
 * Time: 12:59
 */

namespace Cx\Modules\Shop\Controller;


class OrderController extends \Cx\Core\Core\Model\Entity\Controller
{
    public function getViewGeneratorOptions($options)
    {
        global $_ARRAYLANG;

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
                        return $this->getStatusMenu($value);
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
                )
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
            )
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
    protected function getStatusMenu($value, $name = '', $formName = '')
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

        $wrapper->addChild($title);
        $wrapper->addChild($input);
        $wrapper->addChild($addition);

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
                $td->addChild($addition);
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
            $td->addChild($addition);
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
        $tdWeightTitle->setAttribute('style', 'text-align: right;');
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
        $tdNetpriceTitle->setAttribute('style', 'text-align: right;');
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
        $tdNetpriceInput->addChild($additionChf);

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
}