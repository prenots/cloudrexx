<?php

/**
 * This is the FavoriteList component controller
 *
 * @copyright   Comvation AG
 * @author      Manuel Schenk <manuel.schenk@comvation.com>
 * @package     cloudrexx
 * @subpackage  module_favoritelist
 * @version     5.0.0
 */

namespace Cx\Modules\FavoriteList\Controller;

/**
 * This is the FavoriteList component controller
 *
 * @copyright   Comvation AG
 * @author      Manuel Schenk <manuel.schenk@comvation.com>
 * @package     cloudrexx
 * @subpackage  module_favoritelist
 * @version     5.0.0
 */
class ComponentController extends \Cx\Core\Core\Model\Entity\SystemComponentController
{

    /**
     * Returns all Controller class names for this component (except this)
     *
     * Be sure to return all your controller classes if you add your own
     * @return array List of Controller class names (without namespace)
     */
    public function getControllerClasses()
    {
        return array('Backend', 'Frontend', 'Json');
    }

    /**
     * {@inheritdoc}
     */
    public function getControllersAccessableByJson()
    {
        return array('JsonController');
    }

    /**
     * Do something before main template gets parsed
     *
     * USE CAREFULLY, DO NOT DO ANYTHING COSTLY HERE!
     * CALCULATE YOUR STUFF AS LATE AS POSSIBLE
     * @param \Cx\Core\Html\Sigma $template The main template
     */
    public function preFinalize(\Cx\Core\Html\Sigma $template)
    {
        if ($this->cx->getMode() != \Cx\Core\Core\Controller\Cx::MODE_FRONTEND) {
            return;
        }
        $this->getController('Frontend')->getBlock($template);
    }

    public function registerEventListeners()
    {
        $evm = $this->cx->getEvents();
        $catalogSaveListener = new \Cx\Modules\FavoriteList\Model\Event\CatalogSaveEventListener($this->cx);
        $evm->addModelListener(\Doctrine\ORM\Events::prePersist, 'Cx\\Modules\\FavoriteList\\Model\\Entity\\Catalog', $catalogSaveListener);
    }
}
