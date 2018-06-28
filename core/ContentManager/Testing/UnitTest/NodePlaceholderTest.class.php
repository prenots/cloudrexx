<?php declare(strict_types=1);

/**
 * Cloudrexx
 *
 * @link      http://www.cloudrexx.com
 * @copyright Cloudrexx AG 2007-2015
 *
 * According to our dual licensing model, this program can be used either
 * under the terms of the GNU Affero General Public License, version 3,
 * or under a proprietary license.
 *
 * The texts of the GNU Affero General Public License with an additional
 * permission and of our proprietary license can be found at and
 * in the LICENSE file you have received along with this program.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * "Cloudrexx" is a registered trademark of Cloudrexx AG.
 * The licensing of the program under the AGPLv3 does not imply a
 * trademark license. Therefore any rights, title and interest in
 * our trademarks remain entirely with us.
 */

/**
 * NodePlaceholderTest
 *
 * @copyright   CLOUDREXX CMS - CLOUDREXX AG
 * @author      Michael Ritter <michael.ritter@cloudrexx.com>
 * @version     1.0.0
 * @package     cloudrexx
 * @subpackage  core_contentmanager
 */

namespace Cx\Core\ContentManager\Testing\UnitTest;

/**
 * NodeTest
 *
 * @copyright   CLOUDREXX CMS - CLOUDREXX AG
 * @author      Michael Ritter <michael.ritter@cloudrexx.com>
 * @version     1.0.0
 * @package     cloudrexx
 * @subpackage  core_contentmanager
 */
class NodePlaceholderTest extends \Cx\Core\Test\Model\Entity\DoctrineTestCase
{
    /**
     */
    public function testPlaceholderResolving() {
        $nodeRepo = \Env::get('em')->getRepository('Cx\Core\ContentManager\Model\Entity\Node');

        $node = new \Cx\Core\ContentManager\Model\Entity\Node();
        $node->setParent($nodeRepo->getRoot());
        $nodeRepo->getRoot()->addChildren($node);
        $node2 = new \Cx\Core\ContentManager\Model\Entity\Node();
        $node2->setParent($node);
        $node->addChildren($node2);

        \Env::get('em')->persist($node);
        \Env::get('em')->persist($node2);
        \Env::get('em')->flush();

        $p1 = new \Cx\Core\ContentManager\Model\Entity\Page();
        $p2 = new \Cx\Core\ContentManager\Model\Entity\Page();
        $p3 = new \Cx\Core\ContentManager\Model\Entity\Page();

        $p1->setNode($node);
        $node->addPage($p1);
        $p2->setNode($node);
        $node->addPage($p2);
        $p3->setNode($node2);
        $node2->addPage($p3);

        $p1->setLang(1);
        $p1->setTitle('testpage');
        $p1->setNodeIdShadowed($node->getId());
        $p1->setUseCustomContentForAllChannels('');
        $p1->setUseCustomApplicationTemplateForAllChannels('');
        $p1->setUseSkinForAllChannels('');
        $p1->setType(\Cx\Core\ContentManager\Model\Entity\Page::TYPE_APPLICATION);
        $p1->setModule('Test');
        $p1->setCmd('');
        $p1->setActive(1);

        $p2->setLang(2);
        $p2->setTitle('testpage2');
        $p2->setNodeIdShadowed($node->getId());
        $p2->setUseCustomContentForAllChannels('');
        $p2->setUseCustomApplicationTemplateForAllChannels('');
        $p2->setUseSkinForAllChannels('');
        $p2->setType(\Cx\Core\ContentManager\Model\Entity\Page::TYPE_APPLICATION);
        $p2->setModule('Test');
        $p2->setCmd('7');
        $p2->setActive(1);

        $p3->setLang(1);
        $p3->setTitle('testpage3');
        $p3->setNodeIdShadowed($node->getId());
        $p3->setUseCustomContentForAllChannels('');
        $p3->setUseCustomApplicationTemplateForAllChannels('');
        $p3->setUseSkinForAllChannels('');
        $p3->setType(\Cx\Core\ContentManager\Model\Entity\Page::TYPE_APPLICATION);
        $p3->setModule('Test');
        $p3->setCmd('7');
        $p3->setActive(1);

        \Env::get('em')->persist($node);
        \Env::get('em')->persist($node2);
        \Env::get('em')->persist($p1);
        \Env::get('em')->persist($p2);
        \Env::get('em')->persist($p3);

        \Env::get('em')->flush();
        \Env::get('em')->refresh($node); // Refreshes the state of the given entity from the database, overwriting local changes.
        \Env::get('em')->refresh($node2); // Refreshes the state of the given entity from the database, overwriting local changes.

        // func_node(CALENDAR) / func_node(CALENDAR,DETAIL)
        // func_node(CALENDAR,2) / func_node(CALENDAR,DETAIL,2)
        // func_node(CALENDAR,2,0)

        // Ensure env is configured
        if (!defined('FRONTEND_LANG_ID')) {
            define('FRONTEND_LANG_ID', 1);
        }
        static::$cx->setPage($p1);

        $nodePlaceholders = array(
            // Node ID
            'func_node(' . $node->getId() . ')'                 => \Cx\Core\Routing\Url::fromNode($node),
            // Legacy Node ID
            '[[NODE_' . $node->getId() . ']]'                   => \Cx\Core\Routing\Url::fromNode($node),
            // Node ID and lang
            'func_node(' . $node->getId() . ',2)'               => \Cx\Core\Routing\Url::fromNode($node, 2),
            // Module and lang
            'func_node(' . $p2->getModule() . ',7)'             => \Cx\Core\Routing\Url::fromModuleAndCmd('Error'),
            // Module and cmd
            'func_node(' . $p2->getModule() . ',7,0)'           => \Cx\Core\Routing\Url::fromPage($p3),
            // Module, cmd and lang
            'func_node(' . $p2->getModule() . ',7,2)'           => \Cx\Core\Routing\Url::fromPage($p2),
            // Legacy module and cmd
            '[[NODE_' . strtoupper($p2->getModule()) . '_7]]'   => \Cx\Core\Routing\Url::fromPage($p3),
            // Legacy module and cmd
            '[[NODE_' . strtoupper($p2->getModule()) . '_2]]'   => \Cx\Core\Routing\Url::fromModuleAndCmd('Error'),
            // Module
            'func_node(' . $p1->getModule() . ')'               => \Cx\Core\Routing\Url::fromPage($p1),
            // Module
            'func_node(asdf)'                                   => \Cx\Core\Routing\Url::fromModuleAndCmd('Error'),
            // Module and lang
            'func_node(asdf,2)'                                 => \Cx\Core\Routing\Url::fromModuleAndCmd('Error'),
        );

        foreach ($nodePlaceholders as $nodePlaceholder=>$target) {
            $template = new \Cx\Core\Html\Sigma();
            $template->setTemplate($nodePlaceholder);
            static::$cx->getComponent('Widget')->parseWidgets(
                $template,
                '',
                '',
                ''
            );
            //echo $template->get() . ' / ' . $target->toString() . PHP_EOL;
            $this->assertEquals(
                $template->get(),
                $target->toString(),
                $nodePlaceholder . ': ' . $template->get()
            );
        }
    }
}

