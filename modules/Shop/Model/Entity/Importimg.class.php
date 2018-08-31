<?php

namespace Cx\Modules\Shop\Model\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Importimg
 */
class Importimg extends \Cx\Model\Base\EntityBase {
    /**
     * @var integer
     */
    protected $imgId;

    /**
     * @var string
     */
    protected $imgName;

    /**
     * @var string
     */
    protected $imgCats;

    /**
     * @var string
     */
    protected $imgFieldsFile;

    /**
     * @var string
     */
    protected $imgFieldsDb;


    /**
     * Get imgId
     *
     * @return integer 
     */
    public function getImgId()
    {
        return $this->imgId;
    }

    /**
     * Set imgName
     *
     * @param string $imgName
     * @return Importimg
     */
    public function setImgName($imgName)
    {
        $this->imgName = $imgName;

        return $this;
    }

    /**
     * Get imgName
     *
     * @return string 
     */
    public function getImgName()
    {
        return $this->imgName;
    }

    /**
     * Set imgCats
     *
     * @param string $imgCats
     * @return Importimg
     */
    public function setImgCats($imgCats)
    {
        $this->imgCats = $imgCats;

        return $this;
    }

    /**
     * Get imgCats
     *
     * @return string 
     */
    public function getImgCats()
    {
        return $this->imgCats;
    }

    /**
     * Set imgFieldsFile
     *
     * @param string $imgFieldsFile
     * @return Importimg
     */
    public function setImgFieldsFile($imgFieldsFile)
    {
        $this->imgFieldsFile = $imgFieldsFile;

        return $this;
    }

    /**
     * Get imgFieldsFile
     *
     * @return string 
     */
    public function getImgFieldsFile()
    {
        return $this->imgFieldsFile;
    }

    /**
     * Set imgFieldsDb
     *
     * @param string $imgFieldsDb
     * @return Importimg
     */
    public function setImgFieldsDb($imgFieldsDb)
    {
        $this->imgFieldsDb = $imgFieldsDb;

        return $this;
    }

    /**
     * Get imgFieldsDb
     *
     * @return string 
     */
    public function getImgFieldsDb()
    {
        return $this->imgFieldsDb;
    }
}
