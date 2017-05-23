<?php

namespace Cx\Model\Proxies\__CG__\Cx\Core_Modules\Sync\Model\Entity;

/**
 * DO NOT EDIT THIS FILE - IT WAS CREATED BY DOCTRINE'S PROXY GENERATOR
 */
class Host extends \Cx\Core_Modules\Sync\Model\Entity\Host implements \Doctrine\ORM\Proxy\Proxy
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
            return array('__isInitialized__', 'id', 'host', 'active', 'apiKey', 'apiVersion', 'urlTemplate', 'hostEntities', 'validators', 'virtual');
        }

        return array('__isInitialized__', 'id', 'host', 'active', 'apiKey', 'apiVersion', 'urlTemplate', 'hostEntities', 'validators', 'virtual');
    }

    /**
     * 
     */
    public function __wakeup()
    {
        if ( ! $this->__isInitialized__) {
            $this->__initializer__ = function (Host $proxy) {
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
    public function setHost($host)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setHost', array($host));

        return parent::setHost($host);
    }

    /**
     * {@inheritDoc}
     */
    public function getHost()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getHost', array());

        return parent::getHost();
    }

    /**
     * {@inheritDoc}
     */
    public function setActive($active)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setActive', array($active));

        return parent::setActive($active);
    }

    /**
     * {@inheritDoc}
     */
    public function getActive()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getActive', array());

        return parent::getActive();
    }

    /**
     * {@inheritDoc}
     */
    public function setApiKey($apiKey)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setApiKey', array($apiKey));

        return parent::setApiKey($apiKey);
    }

    /**
     * {@inheritDoc}
     */
    public function getApiKey()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getApiKey', array());

        return parent::getApiKey();
    }

    /**
     * {@inheritDoc}
     */
    public function setApiVersion($apiVersion)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setApiVersion', array($apiVersion));

        return parent::setApiVersion($apiVersion);
    }

    /**
     * {@inheritDoc}
     */
    public function getApiVersion()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getApiVersion', array());

        return parent::getApiVersion();
    }

    /**
     * {@inheritDoc}
     */
    public function setUrlTemplate($urlTemplate)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setUrlTemplate', array($urlTemplate));

        return parent::setUrlTemplate($urlTemplate);
    }

    /**
     * {@inheritDoc}
     */
    public function getUrlTemplate()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getUrlTemplate', array());

        return parent::getUrlTemplate();
    }

    /**
     * {@inheritDoc}
     */
    public function addHostEntity(\Cx\Core_Modules\Sync\Model\Entity\HostEntity $hostEntity)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'addHostEntity', array($hostEntity));

        return parent::addHostEntity($hostEntity);
    }

    /**
     * {@inheritDoc}
     */
    public function getHostEntities()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getHostEntities', array());

        return parent::getHostEntities();
    }

    /**
     * {@inheritDoc}
     */
    public function setHostEntities($hostEntities)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setHostEntities', array($hostEntities));

        return parent::setHostEntities($hostEntities);
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
