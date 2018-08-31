<?php

namespace Cx\Modules\Shop\Model\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Orders
 */
class Orders extends \Cx\Model\Base\EntityBase {
    /**
     * @var integer
     */
    protected $id;

    /**
     * @var integer
     */
    protected $customerId;

    /**
     * @var integer
     */
    protected $currencyId;

    /**
     * @var string
     */
    protected $sum;

    /**
     * @var \DateTime
     */
    protected $dateTime;

    /**
     * @var boolean
     */
    protected $status;

    /**
     * @var string
     */
    protected $gender;

    /**
     * @var string
     */
    protected $company;

    /**
     * @var string
     */
    protected $firstname;

    /**
     * @var string
     */
    protected $lastname;

    /**
     * @var string
     */
    protected $address;

    /**
     * @var string
     */
    protected $city;

    /**
     * @var string
     */
    protected $zip;

    /**
     * @var integer
     */
    protected $countryId;

    /**
     * @var string
     */
    protected $phone;

    /**
     * @var string
     */
    protected $vatAmount;

    /**
     * @var string
     */
    protected $shipmentAmount;

    /**
     * @var integer
     */
    protected $shipmentId;

    /**
     * @var integer
     */
    protected $paymentId;

    /**
     * @var string
     */
    protected $paymentAmount;

    /**
     * @var string
     */
    protected $ip;

    /**
     * @var integer
     */
    protected $langId;

    /**
     * @var string
     */
    protected $note;

    /**
     * @var \DateTime
     */
    protected $modifiedOn;

    /**
     * @var string
     */
    protected $modifiedBy;

    /**
     * @var string
     */
    protected $billingGender;

    /**
     * @var string
     */
    protected $billingCompany;

    /**
     * @var string
     */
    protected $billingFirstname;

    /**
     * @var string
     */
    protected $billingLastname;

    /**
     * @var string
     */
    protected $billingAddress;

    /**
     * @var string
     */
    protected $billingCity;

    /**
     * @var string
     */
    protected $billingZip;

    /**
     * @var integer
     */
    protected $billingCountryId;

    /**
     * @var string
     */
    protected $billingPhone;

    /**
     * @var string
     */
    protected $billingFax;

    /**
     * @var string
     */
    protected $billingEmail;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    protected $lsvs;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    protected $orderItems;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    protected $relCustomerCoupons;

    /**
     * @var \Cx\Core\Locale\Model\Entity\Locale
     */
    protected $lang;

    /**
     * @var \Cx\Modules\Shop\Model\Entity\Currencies
     */
    protected $currencies;

    /**
     * @var \Cx\Modules\Shop\Model\Entity\Shipper
     */
    protected $shipper;

    /**
     * @var \Cx\Modules\Shop\Model\Entity\Payment
     */
    protected $payment;

    /**
     * @var \Cx\Core\User\Model\Entity\User
     */
    protected $customer;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->lsvs = new \Doctrine\Common\Collections\ArrayCollection();
        $this->orderItems = new \Doctrine\Common\Collections\ArrayCollection();
        $this->relCustomerCoupons = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set customerId
     *
     * @param integer $customerId
     * @return Orders
     */
    public function setCustomerId($customerId)
    {
        $this->customerId = $customerId;

        return $this;
    }

    /**
     * Get customerId
     *
     * @return integer 
     */
    public function getCustomerId()
    {
        return $this->customerId;
    }

    /**
     * Set currencyId
     *
     * @param integer $currencyId
     * @return Orders
     */
    public function setCurrencyId($currencyId)
    {
        $this->currencyId = $currencyId;

        return $this;
    }

    /**
     * Get currencyId
     *
     * @return integer 
     */
    public function getCurrencyId()
    {
        return $this->currencyId;
    }

    /**
     * Set sum
     *
     * @param string $sum
     * @return Orders
     */
    public function setSum($sum)
    {
        $this->sum = $sum;

        return $this;
    }

    /**
     * Get sum
     *
     * @return string 
     */
    public function getSum()
    {
        return $this->sum;
    }

    /**
     * Set dateTime
     *
     * @param \DateTime $dateTime
     * @return Orders
     */
    public function setDateTime($dateTime)
    {
        $this->dateTime = $dateTime;

        return $this;
    }

    /**
     * Get dateTime
     *
     * @return \DateTime 
     */
    public function getDateTime()
    {
        return $this->dateTime;
    }

    /**
     * Set status
     *
     * @param boolean $status
     * @return Orders
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return boolean 
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set gender
     *
     * @param string $gender
     * @return Orders
     */
    public function setGender($gender)
    {
        $this->gender = $gender;

        return $this;
    }

    /**
     * Get gender
     *
     * @return string 
     */
    public function getGender()
    {
        return $this->gender;
    }

    /**
     * Set company
     *
     * @param string $company
     * @return Orders
     */
    public function setCompany($company)
    {
        $this->company = $company;

        return $this;
    }

    /**
     * Get company
     *
     * @return string 
     */
    public function getCompany()
    {
        return $this->company;
    }

    /**
     * Set firstname
     *
     * @param string $firstname
     * @return Orders
     */
    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;

        return $this;
    }

    /**
     * Get firstname
     *
     * @return string 
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * Set lastname
     *
     * @param string $lastname
     * @return Orders
     */
    public function setLastname($lastname)
    {
        $this->lastname = $lastname;

        return $this;
    }

    /**
     * Get lastname
     *
     * @return string 
     */
    public function getLastname()
    {
        return $this->lastname;
    }

    /**
     * Set address
     *
     * @param string $address
     * @return Orders
     */
    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Get address
     *
     * @return string 
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Set city
     *
     * @param string $city
     * @return Orders
     */
    public function setCity($city)
    {
        $this->city = $city;

        return $this;
    }

    /**
     * Get city
     *
     * @return string 
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Set zip
     *
     * @param string $zip
     * @return Orders
     */
    public function setZip($zip)
    {
        $this->zip = $zip;

        return $this;
    }

    /**
     * Get zip
     *
     * @return string 
     */
    public function getZip()
    {
        return $this->zip;
    }

    /**
     * Set countryId
     *
     * @param integer $countryId
     * @return Orders
     */
    public function setCountryId($countryId)
    {
        $this->countryId = $countryId;

        return $this;
    }

    /**
     * Get countryId
     *
     * @return integer 
     */
    public function getCountryId()
    {
        return $this->countryId;
    }

    /**
     * Set phone
     *
     * @param string $phone
     * @return Orders
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get phone
     *
     * @return string 
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Set vatAmount
     *
     * @param string $vatAmount
     * @return Orders
     */
    public function setVatAmount($vatAmount)
    {
        $this->vatAmount = $vatAmount;

        return $this;
    }

    /**
     * Get vatAmount
     *
     * @return string 
     */
    public function getVatAmount()
    {
        return $this->vatAmount;
    }

    /**
     * Set shipmentAmount
     *
     * @param string $shipmentAmount
     * @return Orders
     */
    public function setShipmentAmount($shipmentAmount)
    {
        $this->shipmentAmount = $shipmentAmount;

        return $this;
    }

    /**
     * Get shipmentAmount
     *
     * @return string 
     */
    public function getShipmentAmount()
    {
        return $this->shipmentAmount;
    }

    /**
     * Set shipmentId
     *
     * @param integer $shipmentId
     * @return Orders
     */
    public function setShipmentId($shipmentId)
    {
        $this->shipmentId = $shipmentId;

        return $this;
    }

    /**
     * Get shipmentId
     *
     * @return integer 
     */
    public function getShipmentId()
    {
        return $this->shipmentId;
    }

    /**
     * Set paymentId
     *
     * @param integer $paymentId
     * @return Orders
     */
    public function setPaymentId($paymentId)
    {
        $this->paymentId = $paymentId;

        return $this;
    }

    /**
     * Get paymentId
     *
     * @return integer 
     */
    public function getPaymentId()
    {
        return $this->paymentId;
    }

    /**
     * Set paymentAmount
     *
     * @param string $paymentAmount
     * @return Orders
     */
    public function setPaymentAmount($paymentAmount)
    {
        $this->paymentAmount = $paymentAmount;

        return $this;
    }

    /**
     * Get paymentAmount
     *
     * @return string 
     */
    public function getPaymentAmount()
    {
        return $this->paymentAmount;
    }

    /**
     * Set ip
     *
     * @param string $ip
     * @return Orders
     */
    public function setIp($ip)
    {
        $this->ip = $ip;

        return $this;
    }

    /**
     * Get ip
     *
     * @return string 
     */
    public function getIp()
    {
        return $this->ip;
    }

    /**
     * Set langId
     *
     * @param integer $langId
     * @return Orders
     */
    public function setLangId($langId)
    {
        $this->langId = $langId;

        return $this;
    }

    /**
     * Get langId
     *
     * @return integer 
     */
    public function getLangId()
    {
        return $this->langId;
    }

    /**
     * Set note
     *
     * @param string $note
     * @return Orders
     */
    public function setNote($note)
    {
        $this->note = $note;

        return $this;
    }

    /**
     * Get note
     *
     * @return string 
     */
    public function getNote()
    {
        return $this->note;
    }

    /**
     * Set modifiedOn
     *
     * @param \DateTime $modifiedOn
     * @return Orders
     */
    public function setModifiedOn($modifiedOn)
    {
        $this->modifiedOn = $modifiedOn;

        return $this;
    }

    /**
     * Get modifiedOn
     *
     * @return \DateTime 
     */
    public function getModifiedOn()
    {
        return $this->modifiedOn;
    }

    /**
     * Set modifiedBy
     *
     * @param string $modifiedBy
     * @return Orders
     */
    public function setModifiedBy($modifiedBy)
    {
        $this->modifiedBy = $modifiedBy;

        return $this;
    }

    /**
     * Get modifiedBy
     *
     * @return string 
     */
    public function getModifiedBy()
    {
        return $this->modifiedBy;
    }

    /**
     * Set billingGender
     *
     * @param string $billingGender
     * @return Orders
     */
    public function setBillingGender($billingGender)
    {
        $this->billingGender = $billingGender;

        return $this;
    }

    /**
     * Get billingGender
     *
     * @return string 
     */
    public function getBillingGender()
    {
        return $this->billingGender;
    }

    /**
     * Set billingCompany
     *
     * @param string $billingCompany
     * @return Orders
     */
    public function setBillingCompany($billingCompany)
    {
        $this->billingCompany = $billingCompany;

        return $this;
    }

    /**
     * Get billingCompany
     *
     * @return string 
     */
    public function getBillingCompany()
    {
        return $this->billingCompany;
    }

    /**
     * Set billingFirstname
     *
     * @param string $billingFirstname
     * @return Orders
     */
    public function setBillingFirstname($billingFirstname)
    {
        $this->billingFirstname = $billingFirstname;

        return $this;
    }

    /**
     * Get billingFirstname
     *
     * @return string 
     */
    public function getBillingFirstname()
    {
        return $this->billingFirstname;
    }

    /**
     * Set billingLastname
     *
     * @param string $billingLastname
     * @return Orders
     */
    public function setBillingLastname($billingLastname)
    {
        $this->billingLastname = $billingLastname;

        return $this;
    }

    /**
     * Get billingLastname
     *
     * @return string 
     */
    public function getBillingLastname()
    {
        return $this->billingLastname;
    }

    /**
     * Set billingAddress
     *
     * @param string $billingAddress
     * @return Orders
     */
    public function setBillingAddress($billingAddress)
    {
        $this->billingAddress = $billingAddress;

        return $this;
    }

    /**
     * Get billingAddress
     *
     * @return string 
     */
    public function getBillingAddress()
    {
        return $this->billingAddress;
    }

    /**
     * Set billingCity
     *
     * @param string $billingCity
     * @return Orders
     */
    public function setBillingCity($billingCity)
    {
        $this->billingCity = $billingCity;

        return $this;
    }

    /**
     * Get billingCity
     *
     * @return string 
     */
    public function getBillingCity()
    {
        return $this->billingCity;
    }

    /**
     * Set billingZip
     *
     * @param string $billingZip
     * @return Orders
     */
    public function setBillingZip($billingZip)
    {
        $this->billingZip = $billingZip;

        return $this;
    }

    /**
     * Get billingZip
     *
     * @return string 
     */
    public function getBillingZip()
    {
        return $this->billingZip;
    }

    /**
     * Set billingCountryId
     *
     * @param integer $billingCountryId
     * @return Orders
     */
    public function setBillingCountryId($billingCountryId)
    {
        $this->billingCountryId = $billingCountryId;

        return $this;
    }

    /**
     * Get billingCountryId
     *
     * @return integer 
     */
    public function getBillingCountryId()
    {
        return $this->billingCountryId;
    }

    /**
     * Set billingPhone
     *
     * @param string $billingPhone
     * @return Orders
     */
    public function setBillingPhone($billingPhone)
    {
        $this->billingPhone = $billingPhone;

        return $this;
    }

    /**
     * Get billingPhone
     *
     * @return string 
     */
    public function getBillingPhone()
    {
        return $this->billingPhone;
    }

    /**
     * Set billingFax
     *
     * @param string $billingFax
     * @return Orders
     */
    public function setBillingFax($billingFax)
    {
        $this->billingFax = $billingFax;

        return $this;
    }

    /**
     * Get billingFax
     *
     * @return string 
     */
    public function getBillingFax()
    {
        return $this->billingFax;
    }

    /**
     * Set billingEmail
     *
     * @param string $billingEmail
     * @return Orders
     */
    public function setBillingEmail($billingEmail)
    {
        $this->billingEmail = $billingEmail;

        return $this;
    }

    /**
     * Get billingEmail
     *
     * @return string 
     */
    public function getBillingEmail()
    {
        return $this->billingEmail;
    }

    /**
     * Add lsvs
     *
     * @param \Cx\Modules\Shop\Model\Entity\Lsv $lsvs
     * @return Orders
     */
    public function addLsv(\Cx\Modules\Shop\Model\Entity\Lsv $lsvs)
    {
        $this->lsvs[] = $lsvs;

        return $this;
    }

    /**
     * Remove lsvs
     *
     * @param \Cx\Modules\Shop\Model\Entity\Lsv $lsvs
     */
    public function removeLsv(\Cx\Modules\Shop\Model\Entity\Lsv $lsvs)
    {
        $this->lsvs->removeElement($lsvs);
    }

    /**
     * Get lsvs
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getLsvs()
    {
        return $this->lsvs;
    }

    /**
     * Add orderItems
     *
     * @param \Cx\Modules\Shop\Model\Entity\OrderItems $orderItems
     * @return Orders
     */
    public function addOrderItem(\Cx\Modules\Shop\Model\Entity\OrderItems $orderItems)
    {
        $this->orderItems[] = $orderItems;

        return $this;
    }

    /**
     * Remove orderItems
     *
     * @param \Cx\Modules\Shop\Model\Entity\OrderItems $orderItems
     */
    public function removeOrderItem(\Cx\Modules\Shop\Model\Entity\OrderItems $orderItems)
    {
        $this->orderItems->removeElement($orderItems);
    }

    /**
     * Get orderItems
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getOrderItems()
    {
        return $this->orderItems;
    }

    /**
     * Add relCustomerCoupons
     *
     * @param \Cx\Modules\Shop\Model\Entity\RelCustomerCoupon $relCustomerCoupons
     * @return Orders
     */
    public function addRelCustomerCoupon(\Cx\Modules\Shop\Model\Entity\RelCustomerCoupon $relCustomerCoupons)
    {
        $this->relCustomerCoupons[] = $relCustomerCoupons;

        return $this;
    }

    /**
     * Remove relCustomerCoupons
     *
     * @param \Cx\Modules\Shop\Model\Entity\RelCustomerCoupon $relCustomerCoupons
     */
    public function removeRelCustomerCoupon(\Cx\Modules\Shop\Model\Entity\RelCustomerCoupon $relCustomerCoupons)
    {
        $this->relCustomerCoupons->removeElement($relCustomerCoupons);
    }

    /**
     * Get relCustomerCoupons
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getRelCustomerCoupons()
    {
        return $this->relCustomerCoupons;
    }

    /**
     * Set currencies
     *
     * @param \Cx\Core\Locale\Model\Entity\Locale $lang
     * @return Orders
     */
    public function setLang(\Cx\Core\Locale\Model\Entity\Locale $lang = null)
    {
        $this->currencies = $lang;

        return $this;
    }

    /**
     * Get lang
     *
     * @return \Cx\Core\Locale\Model\Entity\Locale
     */
    public function getLang()
    {
        return $this->lang;
    }

    /**
     * Set currencies
     *
     * @param \Cx\Modules\Shop\Model\Entity\Currencies $currencies
     * @return Orders
     */
    public function setCurrencies(\Cx\Modules\Shop\Model\Entity\Currencies $currencies = null)
    {
        $this->currencies = $currencies;

        return $this;
    }

    /**
     * Get currencies
     *
     * @return \Cx\Modules\Shop\Model\Entity\Currencies 
     */
    public function getCurrencies()
    {
        return $this->currencies;
    }

    /**
     * Set shipper
     *
     * @param \Cx\Modules\Shop\Model\Entity\Shipper $shipper
     * @return Orders
     */
    public function setShipper(\Cx\Modules\Shop\Model\Entity\Shipper $shipper = null)
    {
        $this->shipper = $shipper;

        return $this;
    }

    /**
     * Get shipper
     *
     * @return \Cx\Modules\Shop\Model\Entity\Shipper 
     */
    public function getShipper()
    {
        return $this->shipper;
    }

    /**
     * Set payment
     *
     * @param \Cx\Modules\Shop\Model\Entity\Payment $payment
     * @return Orders
     */
    public function setPayment(\Cx\Modules\Shop\Model\Entity\Payment $payment = null)
    {
        $this->payment = $payment;

        return $this;
    }

    /**
     * Get payment
     *
     * @return \Cx\Modules\Shop\Model\Entity\Payment 
     */
    public function getPayment()
    {
        return $this->payment;
    }

    /**
     * Set customer
     *
     * @param \Cx\Core\User\Model\Entity\User $customer
     * @return Orders
     */
    public function setCustomer(\Cx\Core\User\Model\Entity\User $customer = null)
    {
        $this->customer = $customer;

        return $this;
    }

    /**
     * Get customer
     *
     * @return \Cx\Core\User\Model\Entity\User
     */
    public function getCustomer()
    {
        return $this->customer;
    }
}
