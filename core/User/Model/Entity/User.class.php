<?php

namespace Cx\Core\User\Model\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * User
 */
class User extends \Cx\Model\Base\EntityBase {
    /**
     * @var integer
     */
    protected $id;

    /**
     * @var boolean
     */
    protected $isAdmin = false;

    /**
     * @var string
     */
    protected $username;

    /**
     * @var string
     */
    protected $password;

    /**
     * @var string
     */
    protected $authToken;

    /**
     * @var integer
     */
    protected $authTokenTimeout = 0;

    /**
     * @var integer
     */
    protected $regdate = 0;

    /**
     * @var integer
     */
    protected $expiration = 0;

    /**
     * @var integer
     */
    protected $validity = 0;

    /**
     * @var integer
     */
    protected $lastAuth = 0;

    /**
     * @var integer
     */
    protected $lastAuthStatus = 1;

    /**
     * @var integer
     */
    protected $lastActivity = 0;

    /**
     * @var string
     */
    protected $email;

    /**
     * @var enum_user_user_emailaccess
     */
    protected $emailAccess = 'nobody';

    /**
     * @var integer
     */
    protected $frontendLangId = 0;

    /**
     * @var integer
     */
    protected $backendLangId = 0;

    /**
     * @var boolean
     */
    protected $active = false;

    /**
     * @var boolean
     */
    protected $verified = true;

    /**
     * @var integer
     */
    protected $primaryGroup = 0;

    /**
     * @var enum_user_user_profileaccess
     */
    protected $profileAccess = 'members_only';

    /**
     * @var string
     */
    protected $restoreKey = '';

    /**
     * @var integer
     */
    protected $restoreKeyTime = 0;

    /**
     * @var enum_user_user_u2uactive
     */
    protected $u2uActive = '1';

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    protected $group;

    /**
     * @var \Cx\Core\User\Model\Entity\UserAttributeValue
     */
    protected $userAttributeValue;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->group = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set isAdmin
     *
     * @param boolean $isAdmin
     * @return User
     */
    public function setIsAdmin($isAdmin)
    {
        $this->isAdmin = $isAdmin;

        return $this;
    }

    /**
     * Get isAdmin
     *
     * @return boolean 
     */
    public function getIsAdmin()
    {
        return $this->isAdmin;
    }

    /**
     * Set username
     *
     * @param string $username
     * @return User
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Get username
     *
     * @return string 
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set password
     *
     * @param string $password
     * @return User
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get password
     *
     * @return string 
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set authToken
     *
     * @param string $authToken
     * @return User
     */
    public function setAuthToken($authToken)
    {
        $this->authToken = $authToken;

        return $this;
    }

    /**
     * Get authToken
     *
     * @return string 
     */
    public function getAuthToken()
    {
        return $this->authToken;
    }

    /**
     * Set authTokenTimeout
     *
     * @param integer $authTokenTimeout
     * @return User
     */
    public function setAuthTokenTimeout($authTokenTimeout)
    {
        $this->authTokenTimeout = $authTokenTimeout;

        return $this;
    }

    /**
     * Get authTokenTimeout
     *
     * @return integer 
     */
    public function getAuthTokenTimeout()
    {
        return $this->authTokenTimeout;
    }

    /**
     * Set regdate
     *
     * @param integer $regdate
     * @return User
     */
    public function setRegdate($regdate)
    {
        $this->regdate = $regdate;

        return $this;
    }

    /**
     * Get regdate
     *
     * @return integer 
     */
    public function getRegdate()
    {
        return $this->regdate;
    }

    /**
     * Set expiration
     *
     * @param integer $expiration
     * @return User
     */
    public function setExpiration($expiration)
    {
        $this->expiration = $expiration;

        return $this;
    }

    /**
     * Get expiration
     *
     * @return integer 
     */
    public function getExpiration()
    {
        return $this->expiration;
    }

    /**
     * Set validity
     *
     * @param integer $validity
     * @return User
     */
    public function setValidity($validity)
    {
        $this->validity = $validity;

        return $this;
    }

    /**
     * Get validity
     *
     * @return integer 
     */
    public function getValidity()
    {
        return $this->validity;
    }

    /**
     * Set lastAuth
     *
     * @param integer $lastAuth
     * @return User
     */
    public function setLastAuth($lastAuth)
    {
        $this->lastAuth = $lastAuth;

        return $this;
    }

    /**
     * Get lastAuth
     *
     * @return integer 
     */
    public function getLastAuth()
    {
        return $this->lastAuth;
    }

    /**
     * Set lastAuthStatus
     *
     * @param integer $lastAuthStatus
     * @return User
     */
    public function setLastAuthStatus($lastAuthStatus)
    {
        $this->lastAuthStatus = $lastAuthStatus;

        return $this;
    }

    /**
     * Get lastAuthStatus
     *
     * @return integer 
     */
    public function getLastAuthStatus()
    {
        return $this->lastAuthStatus;
    }

    /**
     * Set lastActivity
     *
     * @param integer $lastActivity
     * @return User
     */
    public function setLastActivity($lastActivity)
    {
        $this->lastActivity = $lastActivity;

        return $this;
    }

    /**
     * Get lastActivity
     *
     * @return integer 
     */
    public function getLastActivity()
    {
        return $this->lastActivity;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return User
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set emailAccess
     *
     * @param enum_user_user_emailaccess $emailAccess
     * @return User
     */
    public function setEmailAccess($emailAccess)
    {
        $this->emailAccess = $emailAccess;

        return $this;
    }

    /**
     * Get emailAccess
     *
     * @return enum_user_user_emailaccess 
     */
    public function getEmailAccess()
    {
        return $this->emailAccess;
    }

    /**
     * Set frontendLangId
     *
     * @param integer $frontendLangId
     * @return User
     */
    public function setFrontendLangId($frontendLangId)
    {
        $this->frontendLangId = $frontendLangId;

        return $this;
    }

    /**
     * Get frontendLangId
     *
     * @return integer 
     */
    public function getFrontendLangId()
    {
        return $this->frontendLangId;
    }

    /**
     * Set backendLangId
     *
     * @param integer $backendLangId
     * @return User
     */
    public function setBackendLangId($backendLangId)
    {
        $this->backendLangId = $backendLangId;

        return $this;
    }

    /**
     * Get backendLangId
     *
     * @return integer 
     */
    public function getBackendLangId()
    {
        return $this->backendLangId;
    }

    /**
     * Set active
     *
     * @param boolean $active
     * @return User
     */
    public function setActive($active)
    {
        $this->active = $active;

        return $this;
    }

    /**
     * Get active
     *
     * @return boolean 
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * Set verified
     *
     * @param boolean $verified
     * @return User
     */
    public function setVerified($verified)
    {
        $this->verified = $verified;

        return $this;
    }

    /**
     * Get verified
     *
     * @return boolean 
     */
    public function getVerified()
    {
        return $this->verified;
    }

    /**
     * Set primaryGroup
     *
     * @param integer $primaryGroup
     * @return User
     */
    public function setPrimaryGroup($primaryGroup)
    {
        $this->primaryGroup = $primaryGroup;

        return $this;
    }

    /**
     * Get primaryGroup
     *
     * @return integer 
     */
    public function getPrimaryGroup()
    {
        return $this->primaryGroup;
    }

    /**
     * Set profileAccess
     *
     * @param enum_user_user_profileaccess $profileAccess
     * @return User
     */
    public function setProfileAccess($profileAccess)
    {
        $this->profileAccess = $profileAccess;

        return $this;
    }

    /**
     * Get profileAccess
     *
     * @return enum_user_user_profileaccess 
     */
    public function getProfileAccess()
    {
        return $this->profileAccess;
    }

    /**
     * Set restoreKey
     *
     * @param string $restoreKey
     * @return User
     */
    public function setRestoreKey($restoreKey)
    {
        $this->restoreKey = $restoreKey;

        return $this;
    }

    /**
     * Get restoreKey
     *
     * @return string 
     */
    public function getRestoreKey()
    {
        return $this->restoreKey;
    }

    /**
     * Set restoreKeyTime
     *
     * @param integer $restoreKeyTime
     * @return User
     */
    public function setRestoreKeyTime($restoreKeyTime)
    {
        $this->restoreKeyTime = $restoreKeyTime;

        return $this;
    }

    /**
     * Get restoreKeyTime
     *
     * @return integer 
     */
    public function getRestoreKeyTime()
    {
        return $this->restoreKeyTime;
    }

    /**
     * Set u2uActive
     *
     * @param enum_user_user_u2uactive $u2uActive
     * @return User
     */
    public function setU2uActive($u2uActive)
    {
        $this->u2uActive = $u2uActive;

        return $this;
    }

    /**
     * Get u2uActive
     *
     * @return enum_user_user_u2uactive 
     */
    public function getU2uActive()
    {
        return $this->u2uActive;
    }

    /**
     * Add group
     *
     * @param \Cx\Core\User\Model\Entity\Group $group
     * @return User
     */
    public function addGroup(\Cx\Core\User\Model\Entity\Group $group)
    {
        $this->group[] = $group;

        return $this;
    }

    /**
     * Remove group
     *
     * @param \Cx\Core\User\Model\Entity\Group $group
     */
    public function removeGroup(\Cx\Core\User\Model\Entity\Group $group)
    {
        $this->group->removeElement($group);
    }

    /**
     * Get group
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getGroup()
    {
        return $this->group;
    }

    /**
     * Set userAttributeValue
     *
     * @param \Cx\Core\User\Model\Entity\UserAttributeValue $userAttributeValue
     */
    public function setUserAttributeValue(\Cx\Core\User\Model\Entity\UserAttributeValue $userAttributeValue = null)
    {
        $this->userAttributeValue = $userAttributeValue;
    }

    /**
     * Get userAttributeValue
     *
     * @return \Cx\Core\User\Model\Entity\UserAttributeValue
     */
    public function getUserAttributeValue()
    {
        return $this->userAttributeValue;
    }
}
