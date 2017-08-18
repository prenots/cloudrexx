<?php

namespace Cx\Core\Model\Model\Entity\Proxy\__CG__\Cx\Modules\Order\Model\Entity;

/**
 * DO NOT EDIT THIS FILE - IT WAS CREATED BY DOCTRINE'S PROXY GENERATOR
 */
class Subscription extends \Cx\Modules\Order\Model\Entity\Subscription implements \Doctrine\ORM\Proxy\Proxy
{
    /**
     * @var \Closure the callback responsible for loading properties in the proxy object. This callback is called with
     *      three parameters, being respectively the proxy object to be initialized, the method that triggered the
     *      initialization process and an array of ordered parameters that were passed to that method.
     *
     * @see \Doctrine\Common\Persistence\Proxy::__setInitializer
     */
    public $__initializer__;

    /**
     * @var \Closure the callback responsible of loading properties that need to be copied in the cloned object
     *
     * @see \Doctrine\Common\Persistence\Proxy::__setCloner
     */
    public $__cloner__;

    /**
     * @var boolean flag indicating if this object was already initialized
     *
     * @see \Doctrine\Common\Persistence\Proxy::__isInitialized
     */
    public $__isInitialized__ = false;

    /**
     * @var array properties to be lazy loaded, with keys being the property
     *            names and values being their default values
     *
     * @see \Doctrine\Common\Persistence\Proxy::__getLazyProperties
     */
    public static $lazyPropertiesDefaults = array();



    /**
     * @param \Closure $initializer
     * @param \Closure $cloner
     */
    public function __construct($initializer = null, $cloner = null)
    {

        $this->__initializer__ = $initializer;
        $this->__cloner__      = $cloner;
    }

    /**
     * {@inheritDoc}
     * @param string $name
     */
    public function __get($name)
    {
        $this->__initializer__ && $this->__initializer__->__invoke($this, '__get', array($name));

        return parent::__get($name);
    }





    /**
     * 
     * @return array
     */
    public function __sleep()
    {
        if ($this->__isInitialized__) {
            return array('__isInitialized__', 'id', 'order', 'product', 'subscriptionDate', 'expirationDate', 'productEntityId', 'productEntity', 'paymentAmount', 'paymentState', 'renewalUnit', 'renewalQuantifier', 'renewalDate', 'externalSubscriptionId', 'description', 'note', 'state', 'terminationDate', 'validators', 'virtual');
        }

        return array('__isInitialized__', 'id', 'order', 'product', 'subscriptionDate', 'expirationDate', 'productEntityId', 'productEntity', 'paymentAmount', 'paymentState', 'renewalUnit', 'renewalQuantifier', 'renewalDate', 'externalSubscriptionId', 'description', 'note', 'state', 'terminationDate', 'validators', 'virtual');
    }

    /**
     * 
     */
    public function __wakeup()
    {
        if ( ! $this->__isInitialized__) {
            $this->__initializer__ = function (Subscription $proxy) {
                $proxy->__setInitializer(null);
                $proxy->__setCloner(null);

                $existingProperties = get_object_vars($proxy);

                foreach ($proxy->__getLazyProperties() as $property => $defaultValue) {
                    if ( ! array_key_exists($property, $existingProperties)) {
                        $proxy->$property = $defaultValue;
                    }
                }
            };

        }
    }

    /**
     * 
     */
    public function __clone()
    {
        $this->__cloner__ && $this->__cloner__->__invoke($this, '__clone', array());
    }

    /**
     * Forces initialization of the proxy
     */
    public function __load()
    {
        $this->__initializer__ && $this->__initializer__->__invoke($this, '__load', array());
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific loading logic
     */
    public function __isInitialized()
    {
        return $this->__isInitialized__;
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific loading logic
     */
    public function __setInitialized($initialized)
    {
        $this->__isInitialized__ = $initialized;
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific loading logic
     */
    public function __setInitializer(\Closure $initializer = null)
    {
        $this->__initializer__ = $initializer;
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific loading logic
     */
    public function __getInitializer()
    {
        return $this->__initializer__;
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific loading logic
     */
    public function __setCloner(\Closure $cloner = null)
    {
        $this->__cloner__ = $cloner;
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific cloning logic
     */
    public function __getCloner()
    {
        return $this->__cloner__;
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific loading logic
     * @static
     */
    public function __getLazyProperties()
    {
        return self::$lazyPropertiesDefaults;
    }

    
    /**
     * {@inheritDoc}
     */
    public function getId()
    {
        if ($this->__isInitialized__ === false) {
            return (int)  parent::getId();
        }


        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getId', array());

        return parent::getId();
    }

    /**
     * {@inheritDoc}
     */
    public function setId($id)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setId', array($id));

        return parent::setId($id);
    }

    /**
     * {@inheritDoc}
     */
    public function setOrder(\Cx\Modules\Order\Model\Entity\Order $order, $updatePaymentAmount = false)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setOrder', array($order, $updatePaymentAmount));

        return parent::setOrder($order, $updatePaymentAmount);
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getOrder', array());

        return parent::getOrder();
    }

    /**
     * {@inheritDoc}
     */
    public function setProduct($product)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setProduct', array($product));

        return parent::setProduct($product);
    }

    /**
     * {@inheritDoc}
     */
    public function getProduct()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getProduct', array());

        return parent::getProduct();
    }

    /**
     * {@inheritDoc}
     */
    public function getExpirationDate()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getExpirationDate', array());

        return parent::getExpirationDate();
    }

    /**
     * {@inheritDoc}
     */
    public function setExpirationDate($expirationDate)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setExpirationDate', array($expirationDate));

        return parent::setExpirationDate($expirationDate);
    }

    /**
     * {@inheritDoc}
     */
    public function getProductEntityId()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getProductEntityId', array());

        return parent::getProductEntityId();
    }

    /**
     * {@inheritDoc}
     */
    public function setProductEntityId($productEntityId)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setProductEntityId', array($productEntityId));

        return parent::setProductEntityId($productEntityId);
    }

    /**
     * {@inheritDoc}
     */
    public function getProductEntity()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getProductEntity', array());

        return parent::getProductEntity();
    }

    /**
     * {@inheritDoc}
     */
    public function setProductEntity($productEntity)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setProductEntity', array($productEntity));

        return parent::setProductEntity($productEntity);
    }

    /**
     * {@inheritDoc}
     */
    public function getPaymentAmount()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getPaymentAmount', array());

        return parent::getPaymentAmount();
    }

    /**
     * {@inheritDoc}
     */
    public function setPaymentAmount($paymentAmount)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setPaymentAmount', array($paymentAmount));

        return parent::setPaymentAmount($paymentAmount);
    }

    /**
     * {@inheritDoc}
     */
    public function getPaymentState()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getPaymentState', array());

        return parent::getPaymentState();
    }

    /**
     * {@inheritDoc}
     */
    public function setPaymentState($paymentState)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setPaymentState', array($paymentState));

        return parent::setPaymentState($paymentState);
    }

    /**
     * {@inheritDoc}
     */
    public function getRenewalUnit()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getRenewalUnit', array());

        return parent::getRenewalUnit();
    }

    /**
     * {@inheritDoc}
     */
    public function setRenewalUnit($renewalUnit)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setRenewalUnit', array($renewalUnit));

        return parent::setRenewalUnit($renewalUnit);
    }

    /**
     * {@inheritDoc}
     */
    public function getRenewalQuantifier()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getRenewalQuantifier', array());

        return parent::getRenewalQuantifier();
    }

    /**
     * {@inheritDoc}
     */
    public function setRenewalQuantifier($renewalQuantifier)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setRenewalQuantifier', array($renewalQuantifier));

        return parent::setRenewalQuantifier($renewalQuantifier);
    }

    /**
     * {@inheritDoc}
     */
    public function getRenewalDate()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getRenewalDate', array());

        return parent::getRenewalDate();
    }

    /**
     * {@inheritDoc}
     */
    public function setRenewalDate($renewalDate)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setRenewalDate', array($renewalDate));

        return parent::setRenewalDate($renewalDate);
    }

    /**
     * {@inheritDoc}
     */
    public function getDescription()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getDescription', array());

        return parent::getDescription();
    }

    /**
     * {@inheritDoc}
     */
    public function setDescription($description)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setDescription', array($description));

        return parent::setDescription($description);
    }

    /**
     * {@inheritDoc}
     */
    public function getNote()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getNote', array());

        return parent::getNote();
    }

    /**
     * {@inheritDoc}
     */
    public function setNote($note)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setNote', array($note));

        return parent::setNote($note);
    }

    /**
     * {@inheritDoc}
     */
    public function getExternalSubscriptionId()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getExternalSubscriptionId', array());

        return parent::getExternalSubscriptionId();
    }

    /**
     * {@inheritDoc}
     */
    public function setExternalSubscriptionId($externalSubscriptionId)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setExternalSubscriptionId', array($externalSubscriptionId));

        return parent::setExternalSubscriptionId($externalSubscriptionId);
    }

    /**
     * {@inheritDoc}
     */
    public function payComplete()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'payComplete', array());

        return parent::payComplete();
    }

    /**
     * {@inheritDoc}
     */
    public function getSubscriptionDate()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getSubscriptionDate', array());

        return parent::getSubscriptionDate();
    }

    /**
     * {@inheritDoc}
     */
    public function setSubscriptionDate($subscriptionDate)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setSubscriptionDate', array($subscriptionDate));

        return parent::setSubscriptionDate($subscriptionDate);
    }

    /**
     * {@inheritDoc}
     */
    public function getState()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getState', array());

        return parent::getState();
    }

    /**
     * {@inheritDoc}
     */
    public function setState($state)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setState', array($state));

        return parent::setState($state);
    }

    /**
     * {@inheritDoc}
     */
    public function getTerminationDate()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getTerminationDate', array());

        return parent::getTerminationDate();
    }

    /**
     * {@inheritDoc}
     */
    public function setTerminationDate($terminationDate)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setTerminationDate', array($terminationDate));

        return parent::setTerminationDate($terminationDate);
    }

    /**
     * {@inheritDoc}
     */
    public function terminate()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'terminate', array());

        return parent::terminate();
    }

    /**
     * {@inheritDoc}
     */
    public function getComponentController()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getComponentController', array());

        return parent::getComponentController();
    }

    /**
     * {@inheritDoc}
     */
    public function setVirtual($virtual)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setVirtual', array($virtual));

        return parent::setVirtual($virtual);
    }

    /**
     * {@inheritDoc}
     */
    public function isVirtual()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'isVirtual', array());

        return parent::isVirtual();
    }

    /**
     * {@inheritDoc}
     */
    public function validate()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'validate', array());

        return parent::validate();
    }

    /**
     * {@inheritDoc}
     */
    public function __call($methodName, $arguments)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, '__call', array($methodName, $arguments));

        return parent::__call($methodName, $arguments);
    }

    /**
     * {@inheritDoc}
     */
    public function __toString()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, '__toString', array());

        return parent::__toString();
    }

}
