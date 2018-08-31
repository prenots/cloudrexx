<?php

namespace Cx\Modules\Shop\Model\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Attribute
 */
class Attribute extends \Cx\Model\Base\EntityBase {
    /**
     * @var integer
     */
    protected $id;

    /**
     * @var boolean
     */
    protected $type;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    protected $options;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->options = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set type
     *
     * @param boolean $type
     * @return Attribute
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return boolean 
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Add options
     *
     * @param \Cx\Modules\Shop\Model\Entity\Option $options
     * @return Attribute
     */
    public function addOption(\Cx\Modules\Shop\Model\Entity\Option $options)
    {
        $this->options[] = $options;

        return $this;
    }

    /**
     * Remove options
     *
     * @param \Cx\Modules\Shop\Model\Entity\Option $options
     */
    public function removeOption(\Cx\Modules\Shop\Model\Entity\Option $options)
    {
        $this->options->removeElement($options);
    }

    /**
     * Get options
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getOptions()
    {
        return $this->options;
    }
}
