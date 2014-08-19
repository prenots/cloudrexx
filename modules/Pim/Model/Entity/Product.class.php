<?php

/**
 * Product
 *
 * @copyright   CONTREXX CMS - COMVATION AG
 * @author      COMVATION Development Team <info@comvation.com>
 * @package     contrexx
 * @subpackage  module_pim
 */

namespace Cx\Modules\Pim\Model\Entity;

class ProductException extends \Exception {};

/**
 * Product
 * 
 * @copyright   CONTREXX CMS - COMVATION AG
 * @author      COMVATION Development Team <info@comvation.com>
 * @package     contrexx
 * @subpackage  module_pim
 */
class Product extends \Cx\Model\Base\EntityBase {
    protected $id;
    protected $renewalOptions = array();
    protected $defaultRenewalOption = array();
    protected $entityClass = null;
    protected $entityAttributes = array();
    protected $renewable = false;
    protected $expirable = false;
    protected $upgradable = false;
    protected $expirationUnit = null;
    protected $expirationQuantifier = null;
    protected $upgradableProducts = array();
    protected $price = null;
    protected $subscriptions;

    const UNIT_DAY = 'day';
    const UNIT_MONTH = 'month';
    const UNIT_WEEK = 'week';
    const UNIT_YEAR = 'year';

    public function __construct() {
        $this->initRenewalConfig();
    }

    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function getRenewalOptions() {
        return $this->renewalOptions;
    }

    public function setRenewalOptions($renewalOptions) {
        $this->renewalOptions = $renewalOptions;
    }

    public function getDefaultRenewalOption() {
        return $this->defaultRenewalOption;
    }

    public function setDefaultRenewalOption($defaultRenewalOption) {
        return $this->defaultRenewalOption = $defaultRenewalOption;
    }

    public function getEntityClass() {
        return $this->entityClass;
    }

    public function setEntityClass($entityClass) {
        $this->entityClass = $entityClass;
    }

    public function getEntityAttributes() {
        return $this->entityAttributes;
    }

    public function setEntityAttributes($entityAttributes) {
        $this->entityAttributes = $entityAttributes;
    }

    public function getRenewable() {
        return $this->renewable;
    }

    public function isRenewable() {
        return $this->renewable;
    }

    public function setRenewable($renewable) {
        $this->renewable = $renewable;
    }

    public function getExpirable() {
        return $this->expirable;
    }

    public function isExpirable() {
        return $this->expirable;
    }

    public function setExpirable($expirable) {
        $this->expirable = $expirable;
    }

    public function getUpgradable() {
        return $this->upgradable;
    }

    public function setUpgradable($upgradable) {
        $this->upgradable = $upgradable;
    }

    public function getExpirationUnit() {
        return $this->expirationUnit;
    }

    public function setExpirationUnit($expirationUnit) {
        $this->expirationUnit = $expirationUnit;
    }

    public function getExpirationQuantifier() {
        return $this->expirationQuantifier;
    }

    public function setExpirationQuantifier($expirationQuantifier) {
        $this->expirationQuantifier = $expirationQuantifier;
    }

    public function getUpgradableProducts() {
        return $this->upgradableProducts;
    }

    public function setUpgradableProducts($upgradableProducts) {
        $this->upgradableProducts = $upgradableProducts;
    }

    public function getPrice() {
        return $this->price;
    }

    public function setPrice($price) {
        $this->price = $price;
    }

    public function getSubscriptions() {
        return $this->subscriptions;
    }

    public function setSubscriptions($subscriptions) {
        $this->subscriptions = $subscriptions;
    }

    public function getNewEntityForSale($saleOptions) {
        return \Env::get('em')->getRepository($this->entityClass)->findOneForSale($this->entityAttributes, $saleOptions);
    }

    public function getEntityById($entityId) {
        $entityIdKey = \Env::get('em')->getClassMetadata($this->entityClass)->getSingleIdentifierFieldName(); 
        return \Env::get('em')->getRepository($this->entityClass)->findOneBy(array($entityIdKey => $entityId));
    }

    public function getExpirationDate() {
        if (!$this->expirable) {
            throw new \ProductException('Product is not expirable.');
        }
        $expirationDate = new \DateTime();
        $expirationDate->modify("+$this->expirationQuantifier $this->expirationUnit");
        return $expirationDate;
    }

    public function getRenewalDate($unit, $quantifier) {
        if (!$this->renewable) {
            throw new \ProductException('Product is not renewable.');
        }
        if (!$this->isValidRenewalDefinition($unit, $quantifier)) {
            throw new \ProductException("Invalid renewal definition supplied: $quantifier $unit");
        }

        $renewalDate = new \DateTime();
        $renewalDate->modify("+$quantifier $unit");
        return $renewalDate;
    }

    protected function isValidRenewalDefinition($unit, $quantifier) {
        return    isset($this->renewalOptions[$unit])
               && in_array($quantifier, $this->renewalOptions[$unit]);
    }

    public function getRenewalDefinition($unit, $quantifier) {
        if ($this->isValidRenewalDefinition($unit, $quantifier)) {
            return array($unit, $quantifier);
        }

        return $this->defaultRenewalOption;
    }

    protected function initRenewalConfig() {
        $this->renewalOptions = array(
            self::UNIT_MONTH  => array(1   ),
            self::UNIT_YEAR    => array(1, 2),
        );
        $this->defaultRenewalOption = array(self::UNIT_YEAR, 1);
    }
}

