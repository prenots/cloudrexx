<?php

namespace Cx\Model\Proxies;

/**
 * THIS CLASS WAS GENERATED BY THE DOCTRINE ORM. DO NOT EDIT THIS FILE.
 */
class GedmoLoggableEntityLogEntryProxy extends \Gedmo\Loggable\Entity\LogEntry implements \Doctrine\ORM\Proxy\Proxy
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

    
    public function getAction()
    {
        $this->_load();
        return parent::getAction();
    }

    public function setAction($action)
    {
        $this->_load();
        return parent::setAction($action);
    }

    public function getObjectClass()
    {
        $this->_load();
        return parent::getObjectClass();
    }

    public function setObjectClass($objectClass)
    {
        $this->_load();
        return parent::setObjectClass($objectClass);
    }

    public function getObjectId()
    {
        $this->_load();
        return parent::getObjectId();
    }

    public function setObjectId($objectId)
    {
        $this->_load();
        return parent::setObjectId($objectId);
    }

    public function getUsername()
    {
        $this->_load();
        return parent::getUsername();
    }

    public function setUsername($username)
    {
        $this->_load();
        return parent::setUsername($username);
    }

    public function getLoggedAt()
    {
        $this->_load();
        return parent::getLoggedAt();
    }

    public function setLoggedAt()
    {
        $this->_load();
        return parent::setLoggedAt();
    }

    public function getData()
    {
        $this->_load();
        return parent::getData();
    }

    public function setData($data)
    {
        $this->_load();
        return parent::setData($data);
    }

    public function setVersion($version)
    {
        $this->_load();
        return parent::setVersion($version);
    }

    public function getVersion()
    {
        $this->_load();
        return parent::getVersion();
    }


    public function __sleep()
    {
        return array('__isInitialized__', 'action', 'loggedAt', 'version', 'id', 'objectId', 'objectClass', 'data', 'username');
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