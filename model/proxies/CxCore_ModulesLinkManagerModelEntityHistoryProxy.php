<?php

namespace Cx\Model\Proxies;

/**
 * THIS CLASS WAS GENERATED BY THE DOCTRINE ORM. DO NOT EDIT THIS FILE.
 */
class CxCore_ModulesLinkManagerModelEntityHistoryProxy extends \Cx\Core_Modules\LinkManager\Model\Entity\History implements \Doctrine\ORM\Proxy\Proxy
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

    public function getLang()
    {
        $this->_load();
        return parent::getLang();
    }

    public function setLang($lang)
    {
        $this->_load();
        return parent::setLang($lang);
    }

    public function getLinkStatusCode()
    {
        $this->_load();
        return parent::getLinkStatusCode();
    }

    public function setLinkStatusCode($linkStatusCode)
    {
        $this->_load();
        return parent::setLinkStatusCode($linkStatusCode);
    }

    public function getLinkStatus()
    {
        $this->_load();
        return parent::getLinkStatus();
    }

    public function setLinkStatus($linkStatus)
    {
        $this->_load();
        return parent::setLinkStatus($linkStatus);
    }

    public function getDetectedTime()
    {
        $this->_load();
        return parent::getDetectedTime();
    }

    public function updateDetectedTime()
    {
        $this->_load();
        return parent::updateDetectedTime();
    }

    public function setDetectedTime($detectedTime)
    {
        $this->_load();
        return parent::setDetectedTime($detectedTime);
    }

    public function getRequestedPath()
    {
        $this->_load();
        return parent::getRequestedPath();
    }

    public function setRequestedPath($requestedPath)
    {
        $this->_load();
        return parent::setRequestedPath($requestedPath);
    }

    public function getRefererPath()
    {
        $this->_load();
        return parent::getRefererPath();
    }

    public function setRefererPath($refererPath)
    {
        $this->_load();
        return parent::setRefererPath($refererPath);
    }

    public function getLeadPath()
    {
        $this->_load();
        return parent::getLeadPath();
    }

    public function setLeadPath($leadPath)
    {
        $this->_load();
        return parent::setLeadPath($leadPath);
    }

    public function getEntryTitle()
    {
        $this->_load();
        return parent::getEntryTitle();
    }

    public function setEntryTitle($entryTitle)
    {
        $this->_load();
        return parent::setEntryTitle($entryTitle);
    }

    public function getModuleName()
    {
        $this->_load();
        return parent::getModuleName();
    }

    public function setModuleName($moduleName)
    {
        $this->_load();
        return parent::setModuleName($moduleName);
    }

    public function getModuleAction()
    {
        $this->_load();
        return parent::getModuleAction();
    }

    public function setModuleAction($moduleAction)
    {
        $this->_load();
        return parent::setModuleAction($moduleAction);
    }

    public function getModuleParams()
    {
        $this->_load();
        return parent::getModuleParams();
    }

    public function setModuleParams($moduleParams)
    {
        $this->_load();
        return parent::setModuleParams($moduleParams);
    }

    public function getFlagStatus()
    {
        $this->_load();
        return parent::getFlagStatus();
    }

    public function setFlagStatus($flagStatus)
    {
        $this->_load();
        return parent::setFlagStatus($flagStatus);
    }

    public function getLinkRecheck()
    {
        $this->_load();
        return parent::getLinkRecheck();
    }

    public function setLinkRecheck($linkRecheck)
    {
        $this->_load();
        return parent::setLinkRecheck($linkRecheck);
    }

    public function getUpdatedBy()
    {
        $this->_load();
        return parent::getUpdatedBy();
    }

    public function setUpdatedBy($updatedBy)
    {
        $this->_load();
        return parent::setUpdatedBy($updatedBy);
    }

    public function getRequestedLinkType()
    {
        $this->_load();
        return parent::getRequestedLinkType();
    }

    public function setRequestedLinkType($requestedLinkType)
    {
        $this->_load();
        return parent::setRequestedLinkType($requestedLinkType);
    }

    public function getBrokenLinkText()
    {
        $this->_load();
        return parent::getBrokenLinkText();
    }

    public function setBrokenLinkText($brokenLinkText)
    {
        $this->_load();
        return parent::setBrokenLinkText($brokenLinkText);
    }

    public function updateFromArray($newData)
    {
        $this->_load();
        return parent::updateFromArray($newData);
    }


    public function __sleep()
    {
        return array('__isInitialized__', 'id', 'lang', 'requestedPath', 'refererPath', 'leadPath', 'linkStatusCode', 'entryTitle', 'moduleName', 'moduleAction', 'moduleParams', 'detectedTime', 'flagStatus', 'linkStatus', 'linkRecheck', 'updatedBy', 'requestedLinkType', 'brokenLinkText');
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