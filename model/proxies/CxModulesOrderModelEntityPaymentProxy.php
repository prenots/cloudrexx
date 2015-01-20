<?php

namespace Cx\Model\Proxies;

/**
 * THIS CLASS WAS GENERATED BY THE DOCTRINE ORM. DO NOT EDIT THIS FILE.
 */
class CxModulesOrderModelEntityPaymentProxy extends \Cx\Modules\Order\Model\Entity\Payment implements \Doctrine\ORM\Proxy\Proxy
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

    
    public function getId()
    {
        $this->_load();
        return parent::getId();
    }

    public function setId($id)
    {
        $this->_load();
        return parent::setId($id);
    }

    public function getTransactionReference()
    {
        $this->_load();
        return parent::getTransactionReference();
    }

    public function setTransactionReference($transactionReference)
    {
        $this->_load();
        return parent::setTransactionReference($transactionReference);
    }

    public function getAmount()
    {
        $this->_load();
        return parent::getAmount();
    }

    public function setAmount($amount)
    {
        $this->_load();
        return parent::setAmount($amount);
    }

    public function getDate()
    {
        $this->_load();
        return parent::getDate();
    }

    public function setDate($date)
    {
        $this->_load();
        return parent::setDate($date);
    }

    public function getInvoice()
    {
        $this->_load();
        return parent::getInvoice();
    }

    public function setInvoice(\Cx\Modules\Order\Model\Entity\Invoice $invoice)
    {
        $this->_load();
        return parent::setInvoice($invoice);
    }

    public function getHandler()
    {
        $this->_load();
        return parent::getHandler();
    }

    public function setHandler($handler)
    {
        $this->_load();
        return parent::setHandler($handler);
    }

    public function setTransactionData($transactionData)
    {
        $this->_load();
        return parent::setTransactionData($transactionData);
    }

    public function getTransactionData()
    {
        $this->_load();
        return parent::getTransactionData();
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
        return array('__isInitialized__', 'id', 'date', 'amount', 'transactionReference', 'handler', 'transactionData', 'invoice');
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