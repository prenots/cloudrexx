<?php

namespace Cx\Core\Model\Model\Entity\Proxy;

/**
 * THIS CLASS WAS GENERATED BY THE DOCTRINE ORM. DO NOT EDIT THIS FILE.
 */
class CxCoreCountryModelEntityCountryProxy extends \Cx\Core\Country\Model\Entity\Country implements \Doctrine\ORM\Proxy\Proxy
{
    private $_entityPersister;
    private $_identifier;
    public $__isInitialized__ = false;
    public function __construct($entityPersister, $identifier)
    {
        $this->_entityPersister = $entityPersister;
        $this->_identifier = $identifier;
    }
    private function _load()
    {
        if (!$this->__isInitialized__ && $this->_entityPersister) {
            $this->__isInitialized__ = true;
            if ($this->_entityPersister->load($this->_identifier, $this) === null) {
                throw new \Doctrine\ORM\EntityNotFoundException();
            }
            unset($this->_entityPersister, $this->_identifier);
        }
    }

    
    public function setAlpha2($alpha2)
    {
        $this->_load();
        return parent::setAlpha2($alpha2);
    }

    public function getAlpha2()
    {
        $this->_load();
        return parent::getAlpha2();
    }

    public function setAlpha3($alpha3)
    {
        $this->_load();
        return parent::setAlpha3($alpha3);
    }

    public function getAlpha3()
    {
        $this->_load();
        return parent::getAlpha3();
    }

    public function setOrd($ord)
    {
        $this->_load();
        return parent::setOrd($ord);
    }

    public function getOrd()
    {
        $this->_load();
        return parent::getOrd();
    }

    public function addLocales(\Cx\Core\Locale\Model\Entity\Locale $locales)
    {
        $this->_load();
        return parent::addLocales($locales);
    }

    public function getLocales()
    {
        $this->_load();
        return parent::getLocales();
    }

    public function __get($name)
    {
        $this->_load();
        return parent::__get($name);
    }

    public function getComponentController()
    {
        $this->_load();
        return parent::getComponentController();
    }

    public function setVirtual($virtual)
    {
        $this->_load();
        return parent::setVirtual($virtual);
    }

    public function isVirtual()
    {
        $this->_load();
        return parent::isVirtual();
    }

    public function validate()
    {
        $this->_load();
        return parent::validate();
    }

    public function __toString()
    {
        $this->_load();
        return parent::__toString();
    }


    public function __sleep()
    {
        return array('__isInitialized__', 'alpha2', 'alpha3', 'ord', 'locales');
    }

    public function __clone()
    {
        if (!$this->__isInitialized__ && $this->_entityPersister) {
            $this->__isInitialized__ = true;
            $class = $this->_entityPersister->getClassMetadata();
            $original = $this->_entityPersister->load($this->_identifier);
            if ($original === null) {
                throw new \Doctrine\ORM\EntityNotFoundException();
            }
            foreach ($class->reflFields AS $field => $reflProperty) {
                $reflProperty->setValue($this, $reflProperty->getValue($original));
            }
            unset($this->_entityPersister, $this->_identifier);
        }
        
    }
}