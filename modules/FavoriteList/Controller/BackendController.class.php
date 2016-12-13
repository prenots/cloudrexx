<?php

/**
 * Backend controller to create the FavoriteList backend view.
 *
 * @copyright   Comvation AG
 * @author      Manuel Schenk <manuel.schenk@comvation.com>
 * @package     cloudrexx
 * @subpackage  module_favoritelist
 * @version     5.0.0
 */

namespace Cx\Modules\FavoriteList\Controller;

/**
 * Backend controller to create the FavoriteList backend view.
 * @copyright   Comvation AG
 * @author      Manuel Schenk <manuel.schenk@comvation.com>
 * @package     cloudrexx
 * @subpackage  module_favoritelist
 * @version     5.0.0
 */
class BackendController extends \Cx\Core\Core\Model\Entity\SystemComponentBackendController
{

    /**
     * Returns a list of available commands (?act=XY)
     * @return array List of acts
     */
    public function getCommands()
    {
        return array(
            'Catalog',
            'Favorite',
            'Settings' => array(
                'Mailing',
                'FormField',
            ),
        );
    }

    /**
     * Use this to parse your backend page
     *
     * You will get the template located in /View/Template/{CMD}.html
     * You can access Cx class using $this->cx
     * To show messages, use \Message class
     * @param \Cx\Core\Html\Sigma $template Template for current CMD
     * @param array $cmd CMD separated by slashes
     */
    public function parsePage(\Cx\Core\Html\Sigma $template, array $cmd)
    {
        global $_ARRAYLANG;

        try {
            // function group
            \Cx\Core\Setting\Controller\Setting::init($this->getName(), 'function', 'FileSystem');
            if (!\Cx\Core\Setting\Controller\Setting::isDefined('functionMail')
                && !\Cx\Core\Setting\Controller\Setting::add('functionMail', 0, 1,
                    \Cx\Core\Setting\Controller\Setting::TYPE_CHECKBOX, '1', 'function')
            ) {
                throw new \Cx\Lib\Update_DatabaseException("Failed to add Setting entry for " . $this->getName() . " Function Mail");
            }
            if (!\Cx\Core\Setting\Controller\Setting::isDefined('functionPrint')
                && !\Cx\Core\Setting\Controller\Setting::add('functionPrint', 0, 2,
                    \Cx\Core\Setting\Controller\Setting::TYPE_CHECKBOX, '1', 'function')
            ) {
                throw new \Cx\Lib\Update_DatabaseException("Failed to add Setting entry for " . $this->getName() . " Function Print");
            }
            if (!\Cx\Core\Setting\Controller\Setting::isDefined('functionRecommendation')
                && !\Cx\Core\Setting\Controller\Setting::add('functionRecommendation', 0, 3,
                    \Cx\Core\Setting\Controller\Setting::TYPE_CHECKBOX, '1', 'function')
            ) {
                throw new \Cx\Lib\Update_DatabaseException("Failed to add Setting entry for " . $this->getName() . " Function Recommendation");
            }
            if (!\Cx\Core\Setting\Controller\Setting::isDefined('functionInquiry')
                && !\Cx\Core\Setting\Controller\Setting::add('functionInquiry', 0, 4,
                    \Cx\Core\Setting\Controller\Setting::TYPE_CHECKBOX, '1', 'function')
            ) {
                throw new \Cx\Lib\Update_DatabaseException("Failed to add Setting entry for " . $this->getName() . " Function Inquiry");
            }

            // pdf group
            \Cx\Core\Setting\Controller\Setting::init($this->getName(), 'pdf', 'FileSystem');
            if (!\Cx\Core\Setting\Controller\Setting::isDefined('pdfTemplate')
                && !\Cx\Core\Setting\Controller\Setting::add('pdfTemplate', null, 1,
                    \Cx\Core\Setting\Controller\Setting::TYPE_DROPDOWN, '{src:\\' . __CLASS__ . '::getPdfTemplates()}', 'pdf')
            ) {
                throw new \Cx\Lib\Update_DatabaseException("Failed to add Setting entry for " . $this->getName() . " PDF Template");
            }
            if (!\Cx\Core\Setting\Controller\Setting::isDefined('pdfLogo')
                && !\Cx\Core\Setting\Controller\Setting::add('pdfLogo', '', 2,
                    \Cx\Core\Setting\Controller\Setting::TYPE_IMAGE, '', 'pdf')
            ) {
                throw new \Cx\Lib\Update_DatabaseException("Failed to add Setting entry for " . $this->getName() . " PDF Logo");
            }
            if (!\Cx\Core\Setting\Controller\Setting::isDefined('pdfAddress')
                && !\Cx\Core\Setting\Controller\Setting::add('pdfAddress', '', 3,
                    \Cx\Core\Setting\Controller\Setting::TYPE_WYSIWYG, '', 'pdf')
            ) {
                throw new \Cx\Lib\Update_DatabaseException("Failed to add Setting entry for " . $this->getName() . " PDF Address");
            }
            if (!\Cx\Core\Setting\Controller\Setting::isDefined('pdfFooter')
                && !\Cx\Core\Setting\Controller\Setting::add('pdfFooter', '', 4,
                    \Cx\Core\Setting\Controller\Setting::TYPE_WYSIWYG, '', 'pdf')
            ) {
                throw new \Cx\Lib\Update_DatabaseException("Failed to add Setting entry for " . $this->getName() . " PDF Footer");
            }
        } catch (\Exception $e) {
            \DBG::msg($e->getMessage());
        }

        // Parse entity view generation pages
        $entityClassName = $this->getNamespace() . '\\Model\\Entity\\' . current($cmd);
        if (in_array($entityClassName, $this->getEntityClasses())) {
            $this->parseEntityClassPage($template, $entityClassName, current($cmd));
            return;
        }

        // Not an entity, parse overview or settings
        switch (current($cmd)) {
            case 'Settings':
                if (!isset($cmd[1])) {
                    $cmd[1] = '';
                }
                switch ($cmd[1]) {
                    case 'Mailing':
                        if (!$template->blockExists('mailing')) {
                            return;
                        }
                        \Cx\Core\Setting\Controller\Setting::init('Config', 'otherConfigurations');
                        $template->setVariable(
                            'MAILING',
                            \Cx\Core\MailTemplate\Controller\MailTemplate::adminView(
                                $this->getName(),
                                'nonempty',
                                \Cx\Core\Setting\Controller\Setting::getValue('corePagingLimit'),
                                'settings/email'
                            )->get()
                        );
                        break;
                    case 'FormField':
                        // Parse entity view generation pages
                        $entityClassName = $this->getNamespace() . '\\Model\\Entity\\' . $cmd[1];
                        $this->parseEntityClassPage($template, $entityClassName, $cmd[1]);
                        break;
                    default:
                        //save the setting values
                        \Cx\Core\Setting\Controller\Setting::init($this->getName(), null, 'FileSystem', null, \Cx\Core\Setting\Controller\Setting::REPOPULATE);
                        if (!empty($_POST['bsubmit'])) {
                            \Cx\Core\Setting\Controller\Setting::storeFromPost();
                        }

                        \Cx\Core\Setting\Controller\Setting::setEngineType($this->getName(), 'FileSystem', 'function');
                        \Cx\Core\Setting\Controller\Setting::show(
                            $template,
                            'index.php?cmd=' . $this->getName() . '&act=' . current($cmd),
                            $_ARRAYLANG['TXT_' . strtoupper($this->getType()) . '_' . strtoupper($this->getName()) . '_SETTINGS_FUNCTION_DESCRIPTION'],
                            $_ARRAYLANG['TXT_' . strtoupper($this->getType()) . '_' . strtoupper($this->getName()) . '_SETTINGS_FUNCTION'],
                            'TXT_' . strtoupper($this->getType()) . '_' . strtoupper($this->getName()) . '_SETTINGS_'
                        );

                        \Cx\Core\Setting\Controller\Setting::setEngineType($this->getName(), 'FileSystem', 'pdf');
                        \Cx\Core\Setting\Controller\Setting::show(
                            $template,
                            'index.php?cmd=' . $this->getName() . '&act=' . current($cmd),
                            $_ARRAYLANG['TXT_' . strtoupper($this->getType()) . '_' . strtoupper($this->getName()) . '_SETTINGS_PDF_DESCRIPTION'],
                            $_ARRAYLANG['TXT_' . strtoupper($this->getType()) . '_' . strtoupper($this->getName()) . '_SETTINGS_PDF'],
                            'TXT_' . strtoupper($this->getType()) . '_' . strtoupper($this->getName()) . '_SETTINGS_'
                        );
                        break;
                }
                break;
            default:
                if ($template->blockExists('overview')) {
                    $template->touchBlock('overview');
                }
                break;
        }
    }

    /**
     * Return true here if you want the first tab to be an entity view
     * @return boolean True if overview should be shown, false otherwise
     */
    protected function showOverviewPage()
    {
        return false;
    }

    /**
     * This function returns the ViewGeneration options for a given entityClass
     *
     * @access protected
     * @global $_ARRAYLANG
     * @param $entityClassName contains the FQCN from entity
     * @return array with options
     */
    protected function getViewGeneratorOptions($entityClassName, $dataSetIdentifier = '')
    {
        global $_ARRAYLANG;

        $classNameParts = explode('\\', $entityClassName);
        $classIdentifier = end($classNameParts);

        $langVarName = 'TXT_' . strtoupper($this->getType() . '_' . $this->getName() . '_ACT_' . $classIdentifier);
        if (isset($_ARRAYLANG[$langVarName])) {
            $header = $_ARRAYLANG[$langVarName];
        } else {
            $header = $_ARRAYLANG['TXT_' . strtoupper($this->getType()) . '_' . strtoupper($this->getName()) . '_ACT_DEFAULT'];
        }

        $favoriteUrl = \Cx\Core\Routing\Url::fromDocumentRoot();
        $favoriteUrl->setMode(\Cx\Core\Core\Controller\Cx::MODE_BACKEND);
        $favoriteUrl->setPath(substr($this->cx->getBackendFolderName(), 1) . '/' . $this->getName() . '/Favorite');

        switch ($entityClassName) {
            case 'Cx\Modules\FavoriteList\Model\Entity\Catalog':
                // set default order for entries
                if (!isset($_GET['order'])) {
                    $_GET['order'] = 'id';
                }
                return array(
                    'header' => $_ARRAYLANG['TXT_' . strtoupper($this->getType()) . '_' . strtoupper($this->getName()) . '_ACT_CATALOG'],
                    'fields' => array(
                        'id' => array(
                            'header' => $_ARRAYLANG['TXT_' . strtoupper($this->getType()) . '_' . strtoupper($this->getName()) . '_FIELD_ID'],
                        ),
                        'sessionId' => array(
                            'showOverview' => false,
                            'showDetail' => false,
                        ),
                        'name' => array(
                            'header' => $_ARRAYLANG['TXT_' . strtoupper($this->getType()) . '_' . strtoupper($this->getName()) . '_FIELD_NAME'],
                            'table' => array(
                                'parse' => function ($value, $rowData) use ($favoriteUrl) {
                                    return '<a href="' . \Cx\Core\Html\Controller\ViewGenerator::getVgExtendedSearchUrl(
                                        0,
                                        array(
                                            'catalog' => $rowData['id'],
                                        ),
                                        clone $favoriteUrl
                                    )->toString() . '">' . $value . '</a>';
                                },
                            ),
                            'formfield' => function ($name, $type, $length, $value, $options) {
                                $field = new \Cx\Core\Html\Model\Entity\HtmlElement('span');
                                $field->addChild(new \Cx\Core\Html\Model\Entity\TextElement($value));
                                return $field;
                            },
                        ),
                        'date' => array(
                            'header' => $_ARRAYLANG['TXT_' . strtoupper($this->getType()) . '_' . strtoupper($this->getName()) . '_FIELD_DATE'],
                            'showDetail' => false,
                        ),
                        'meta' => array(
                            'header' => $_ARRAYLANG['TXT_' . strtoupper($this->getType()) . '_' . strtoupper($this->getName()) . '_FIELD_META'],
                            'formfield' => function ($name, $type, $length, $value, $options) {
                                $field = new \Cx\Core\Html\Model\Entity\HtmlElement('span');
                                $meta = $this->formatMeta($value);
                                $field->addChild(new \Cx\Core\Html\Model\Entity\TextElement($meta));
                                return $field;
                            },
                            'showOverview' => false,
                        ),
                        'favorites' => array(
                            'showOverview' => false,
                            'showDetail' => false,
                        ),
                    ),
                    'functions' => array(
                        'add' => true,
                        'edit' => true,
                        'delete' => true,
                        'sorting' => true,
                        'paging' => true,
                        'filtering' => false,
                    ),
                );
                break;
            case 'Cx\Modules\FavoriteList\Model\Entity\Favorite':
                // set default order for entries
                if (!isset($_GET['order'])) {
                    $_GET['order'] = 'id';
                }
                return array(
                    'header' => $_ARRAYLANG['TXT_' . strtoupper($this->getType()) . '_' . strtoupper($this->getName()) . '_ACT_FAVORITE'],
                    'fields' => array(
                        'id' => array(
                            'header' => $_ARRAYLANG['TXT_' . strtoupper($this->getType()) . '_' . strtoupper($this->getName()) . '_FIELD_ID'],
                            'table' => array(
                                'parse' => function ($value) {
                                    return $this->returnString($value);
                                },
                            ),
                        ),
                        'catalog' => array(
                            'header' => $_ARRAYLANG['TXT_' . strtoupper($this->getType()) . '_' . strtoupper($this->getName()) . '_FIELD_CATALOG'],
                            'table' => array(
                                'parse' => function ($value, $rowData) use ($favoriteUrl) {
                                    return '<a href="' . \Cx\Core\Html\Controller\ViewGenerator::getVgExtendedSearchUrl(
                                        0,
                                        array(
                                            'catalog' => $rowData['catalog']->getId(),
                                        ),
                                        clone $favoriteUrl
                                    )->toString() . '">' . $this->returnString($value) . '</a>';
                                },
                            ),
                        ),
                        'title' => array(
                            'header' => $_ARRAYLANG['TXT_' . strtoupper($this->getType()) . '_' . strtoupper($this->getName()) . '_FIELD_TITLE'],
                            'table' => array(
                                'parse' => function ($value) {
                                    return $this->returnString($value);
                                },
                            ),
                        ),
                        'link' => array(
                            'header' => $_ARRAYLANG['TXT_' . strtoupper($this->getType()) . '_' . strtoupper($this->getName()) . '_FIELD_LINK'],
                            'table' => array(
                                'parse' => function ($value) {
                                    return $this->returnString($value, '<a target="_blank" href="' . $value . '">' . $value . '</a>');
                                },
                            ),
                        ),
                        'description' => array(
                            'header' => $_ARRAYLANG['TXT_' . strtoupper($this->getType()) . '_' . strtoupper($this->getName()) . '_FIELD_DESCRIPTION'],
                            'table' => array(
                                'parse' => function ($value) {
                                    return $this->returnString($value);
                                },
                            ),
                        ),
                        'message' => array(
                            'header' => $_ARRAYLANG['TXT_' . strtoupper($this->getType()) . '_' . strtoupper($this->getName()) . '_FIELD_MESSAGE'],
                            'table' => array(
                                'parse' => function ($value) {
                                    return $this->returnString($value);
                                },
                            ),
                        ),
                        'price' => array(
                            'header' => $_ARRAYLANG['TXT_' . strtoupper($this->getType()) . '_' . strtoupper($this->getName()) . '_FIELD_PRICE'],
                            'table' => array(
                                'parse' => function ($value) {
                                    return $this->returnString($value);
                                },
                            ),
                        ),
                        'image1' => array(
                            'header' => $_ARRAYLANG['TXT_' . strtoupper($this->getType()) . '_' . strtoupper($this->getName()) . '_FIELD_IMAGE1'],
                            'table' => array(
                                'parse' => function ($value) {
                                    return $this->returnString(
                                        $value,
                                        '<img src="' . $value . '"/>'
                                    );
                                },
                            ),
                        ),
                        'image2' => array(
                            'header' => $_ARRAYLANG['TXT_' . strtoupper($this->getType()) . '_' . strtoupper($this->getName()) . '_FIELD_IMAGE2'],
                            'table' => array(
                                'parse' => function ($value) {
                                    return $this->returnString(
                                        $value,
                                        '<img src="' . $value . '"/>'
                                    );
                                },
                            ),
                        ),
                        'image3' => array(
                            'header' => $_ARRAYLANG['TXT_' . strtoupper($this->getType()) . '_' . strtoupper($this->getName()) . '_FIELD_IMAGE3'],
                            'table' => array(
                                'parse' => function ($value) {
                                    return $this->returnString(
                                        $value,
                                        '<img src="' . $value . '"/>'
                                    );
                                },
                            ),
                        ),
                    ),
                    'functions' => array(
                        'add' => true,
                        'edit' => true,
                        'delete' => true,
                        'sorting' => true,
                        'paging' => true,
                        'filtering' => true,
                    ),
                    'order' => array(
                        'overview' => array(
                            'id',
                            'catalog',
                            'title',
                            'link',
                            'description',
                            'message',
                            'price',
                            'image1',
                            'image2',
                            'image3',
                        ),
                        'form' => array(
                            'id',
                            'catalog',
                            'title',
                            'link',
                            'description',
                            'message',
                            'price',
                            'image1',
                            'image2',
                            'image3',
                        ),
                    ),
                );
                break;
            case 'Cx\Modules\FavoriteList\Model\Entity\FormField':
                return array(
                    'header' => $_ARRAYLANG['TXT_' . strtoupper($this->getType()) . '_' . strtoupper($this->getName()) . '_ACT_FORMFIELD'],
                    'fields' => array(
                        'id' => array(
                            'showOverview' => false,
                        ),
                        'name' => array(
                            'header' => $_ARRAYLANG['TXT_' . strtoupper($this->getType()) . '_' . strtoupper($this->getName()) . '_FIELD_NAME'],
                        ),
                        'type' => array(
                            'header' => $_ARRAYLANG['TXT_' . strtoupper($this->getType()) . '_' . strtoupper($this->getName()) . '_FIELD_TYP'],
                            'type' => 'select',
                            'validValues' => array(
                                'inputtext' => $_ARRAYLANG['TXT_' . strtoupper($this->getType()) . '_' . strtoupper($this->getName()) . '_FIELD_INPUTTEXT'],
                                'textarea' => $_ARRAYLANG['TXT_' . strtoupper($this->getType()) . '_' . strtoupper($this->getName()) . '_FIELD_TEXTAREA'],
                                'select' => $_ARRAYLANG['TXT_' . strtoupper($this->getType()) . '_' . strtoupper($this->getName()) . '_FIELD_SELECT'],
                                'radio' => $_ARRAYLANG['TXT_' . strtoupper($this->getType()) . '_' . strtoupper($this->getName()) . '_FIELD_RADIO'],
                                'checkbox' => $_ARRAYLANG['TXT_' . strtoupper($this->getType()) . '_' . strtoupper($this->getName()) . '_FIELD_CHECKBOX'],
                                'mail' => $_ARRAYLANG['TXT_' . strtoupper($this->getType()) . '_' . strtoupper($this->getName()) . '_FIELD_MAIL'],
                            ),
                        ),
                        'required' => array(
                            'header' => $_ARRAYLANG['TXT_' . strtoupper($this->getType()) . '_' . strtoupper($this->getName()) . '_FIELD_REQUIRED'],
                        ),
                        'order' => array(
                            'header' => $_ARRAYLANG['TXT_' . strtoupper($this->getType()) . '_' . strtoupper($this->getName()) . '_FIELD_ORDER'],
                        ),
                        'values' => array(
                            'header' => $_ARRAYLANG['TXT_' . strtoupper($this->getType()) . '_' . strtoupper($this->getName()) . '_FIELD_VALUES'],
                        ),
                    ),
                    'functions' => array(
                        'add' => true,
                        'edit' => true,
                        'delete' => true,
                        'sorting' => true,
                        'paging' => true,
                        'filtering' => false,
                        'sortBy' => [
                            'field' => ['order' => SORT_ASC]
                        ],
                    ),
                    'order' => array(
                        'overview' => array(
                            'order',
                            'name',
                            'type',
                            'required',
                            'values',
                        ),
                        'form' => array(
                            'order',
                            'name',
                            'type',
                            'required',
                            'values',
                        ),
                    ),
                );
                break;
            default:
                return array(
                    'header' => $header,
                    'functions' => array(
                        'add' => true,
                        'edit' => true,
                        'delete' => true,
                        'sorting' => true,
                        'paging' => true,
                        'filtering' => false,
                    ),
                );
                break;
        }
    }

    /**
     * Returns empty string if value is null
     *
     * @access  public
     * @return  string
     */
    public function returnString($value, $output = null)
    {
        if (is_null($value) || $value == '0' || $value == '') {
            return '';
        }
        if (!is_null($output)) {
            return $output;
        }
        return $value;
    }

    /**
     * Formats the serialized array to output string
     *
     * @access  protected
     * @return  string
     * @global  $_ARRAYLANG
     */
    protected function formatMeta($value)
    {
        global $_ARRAYLANG;

        $meta = unserialize($value);
        $formatedValue['ipaddress'] =
            $_ARRAYLANG['TXT_' . strtoupper($this->getType()) . '_' . strtoupper($this->getName()) . '_FIELD_META_IP_ADDRESS']
            . ': '
            . $meta['ipaddress'];
        $formatedValue['host'] =
            $_ARRAYLANG['TXT_' . strtoupper($this->getType()) . '_' . strtoupper($this->getName()) . '_FIELD_META_HOST']
            . ': '
            . $meta['host'];
        $formatedValue['lang'] =
            $_ARRAYLANG['TXT_' . strtoupper($this->getType()) . '_' . strtoupper($this->getName()) . '_FIELD_META_LANGUAGE']
            . ': '
            . $meta['lang'];
        $formatedValue['browser'] =
            $_ARRAYLANG['TXT_' . strtoupper($this->getType()) . '_' . strtoupper($this->getName()) . '_FIELD_META_BROWSER']
            . ': '
            . $meta['browser'];

        return implode('<br />', $formatedValue);
    }

    /**
     * Returns all PDF Templates
     *
     * @access  public
     * @return  string
     */
    public static function getPdfTemplates()
    {
        $cx = \Cx\Core\Core\Controller\Cx::instanciate();
        $arrOptions = $cx->getComponent('Pdf')->getPdfTemplates();
        $display = array();
        foreach ($arrOptions as $key => $arrOption) {
            array_push($display, $key . ':' . $arrOption);
        }
        return implode(',', $display);
    }
}
