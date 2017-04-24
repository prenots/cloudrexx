<?php

/**
 * Cloudrexx
 *
 * @link      https://www.cloudrexx.com
 * @copyright Cloudrexx AG 2007-2017
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
 * Cx\Modules\Block\Testing\UnitTest\BlockTest
 *
 * @copyright   CLOUDREXX CMS - CLOUDREXX AG
 * @author      Cloudrexx Development Team <info@cloudrexx.com>
 * @author      SS4U <ss4u.comvation@gmail.com>
 * @author      Manuel Schenk <manuel.schenk@comvation.com>
 * @version     1.0.0
 * @package     cloudrexx
 * @subpackage  module_block
 */

namespace Cx\Modules\Block\Testing\UnitTest;

/**
 * Cx\Modules\Block\Testing\UnitTest\BlockTest
 *
 * @copyright   CLOUDREXX CMS - CLOUDREXX AG
 * @author      Cloudrexx Development Team <info@cloudrexx.com>
 * @author      SS4U <ss4u.comvation@gmail.com>
 * @author      Manuel Schenk <manuel.schenk@comvation.com>
 * @version     1.0.0
 * @package     cloudrexx
 * @subpackage  module_block
 */
class BlockTest extends \Cx\Core\Test\Model\Entity\ContrexxTestCase
{
    /**
     * Id of existing Block needed for testing
     */
    protected const EXISTING_BLOCK_ID = 29;

    /**
     * Id of existing Category needed for testing
     */
    protected const EXISTING_CATEGORY_ID = 2;

    /**
     * @covers \Cx\Modules\Block\Controller\JsonBlockController::getBlockContent
     * @expectedException \Cx\Modules\Block\Controller\NotEnoughArgumentsException
     */
    public function testGetBlockContentNotEnoughArguments()
    {
        $jsonBlock = $this->getJsonBlockController();
        $jsonBlock->getBlockContent(array());
    }

    /**
     * @covers \Cx\Modules\Block\Controller\JsonBlockController::getBlockContent
     * @expectedException \Cx\Modules\Block\Controller\NoBlockFoundException
     */
    public function testGetBlockContentNoBlockFound()
    {
        $jsonBlock = $this->getJsonBlockController();
        $jsonBlock->getBlockContent(
            array(
                'get' => array(
                    'block' => 999,
                    'lang' => 'de',
                    'page' => 999,
                )
            )
        );
    }

    /**
     * @covers \Cx\Modules\Block\Controller\JsonBlockController::getBlockContent
     */
    public function testGetBlockContent()
    {
        $jsonBlock = $this->getJsonBlockController();
        $result = $jsonBlock->getBlockContent(
            array(
                'get' => array(
                    'block' => $this::EXISTING_BLOCK_ID,
                    'lang' => 'de',
                    'page' => 1,
                )
            )
        );
        $this->assertArrayHasKey('content', $result);
    }

    /**
     * @covers \Cx\Modules\Block\Controller\JsonBlockController::saveBlockContent
     * @expectedException \Cx\Modules\Block\Controller\NotEnoughArgumentsException
     */
    public function testSaveBlockContentNotEnoughArguments()
    {
        $jsonBlock = $this->getJsonBlockController();
        $jsonBlock->saveBlockContent(array());
    }

    /**
     * @covers \Cx\Modules\Block\Controller\JsonBlockController::saveBlockContent
     */
    public function testSaveBlockContent()
    {
        $jsonBlock = $this->getJsonBlockController();
        $jsonBlock->saveBlockContent(
            array(
                'get' => array(
                    'block' => $this::EXISTING_BLOCK_ID,
                    'lang' => 'de',
                ),
                'post' => array(
                    'content' => 'bla',
                )
            )
        );

        $result = $jsonBlock->getBlockContent(
            array(
                'get' => array(
                    'block' => $this::EXISTING_BLOCK_ID,
                    'lang' => 'de',
                    'page' => 1,
                )
            )
        );
        $this->assertEquals('bla', $result['content']);
    }

    /**
     * Verifies block version creating
     *
     * @covers \Cx\Modules\Block\Model\Repository\BlockLogRepository::getLogs
     */
    public function testCreateBlockVersion()
    {
        // gets Entity Manager
        $em = \Cx\Core\Core\Controller\Cx::instanciate()->getDb()->getEntityManager();

        // starts transaction
        $em->getConnection()->beginTransaction();

        // gets existing category
        $categoryRepo = $em->getRepository('\Cx\Modules\Block\Model\Entity\Category');
        $category = $categoryRepo->findOneBy(array('id' => $this::EXISTING_CATEGORY_ID));

        // creates new block
        $block = new \Cx\Modules\Block\Model\Entity\Block();
        $block->setName('test');
        $block->setCategory($category);
        $block->setStart(0);
        $block->setEnd(0);
        $block->setRandom(0);
        $block->setRandom2(0);
        $block->setRandom3(0);
        $block->setRandom4(0);
        $block->setWysiwygEditor(1);
        $block->setShowInGlobal(0);
        $block->setShowInDirect(0);
        $block->setShowInCategory(0);
        $block->setActive(1);
        $block->setOrder(0);
        $em->persist($block);
        $em->flush();

        // gets log entries of block
        $blockLogRepo = $em->getRepository('Cx\Modules\Block\Model\Entity\LogEntry');
        $logs = $blockLogRepo->getLogs(get_class($block), $block->getId());

        // checks if first log in array is a LogEntry
        $this->assertInstanceOf('Cx\Modules\Block\Model\Entity\LogEntry', $logs[0]);

        // rollback DB changes
        $em->getConnection()->rollback();
    }

    /**
     * Verifies block version reverting
     *
     * @covers \Cx\Modules\Block\Model\Repository\BlockLogRepository::getLogs
     * @covers \Cx\Modules\Block\Model\Repository\BlockLogRepository::revertEntity
     */
    public function testRevertBlockVersion()
    {
        // gets Entity Manager
        $em = \Cx\Core\Core\Controller\Cx::instanciate()->getDb()->getEntityManager();

        // starts transaction
        $em->getConnection()->beginTransaction();

        // gets existing category
        $categoryRepo = $em->getRepository('\Cx\Modules\Block\Model\Entity\Category');
        $category = $categoryRepo->findOneBy(array('id' => $this::EXISTING_CATEGORY_ID));

        // creates new block
        $block = new \Cx\Modules\Block\Model\Entity\Block();
        $block->setName('test');
        $block->setCategory($category);
        $block->setStart(0);
        $block->setEnd(0);
        $block->setRandom(0);
        $block->setRandom2(0);
        $block->setRandom3(0);
        $block->setRandom4(0);
        $block->setWysiwygEditor(1);
        $block->setShowInGlobal(0);
        $block->setShowInDirect(0);
        $block->setShowInCategory(0);
        $block->setActive(1);
        $block->setOrder(0);
        $em->persist($block);
        $em->flush();

        // updates block
        $block->setName('test updated');
        $em->flush();

        // gets log entries of block
        $blockLogRepo = $em->getRepository('Cx\Modules\Block\Model\Entity\LogEntry');
        $logs = $blockLogRepo->getLogs(get_class($block), $block->getId());

        // gets first version of block
        $revertedBlock = $blockLogRepo->revertEntity($block, end($logs)->getVersion());

        // checks if block is correctly reverted by asserting its name
        $this->assertEquals('test', $revertedBlock->getName());

        // rollback DB changes
        $em->getConnection()->rollback();
    }

    /**
     * Verifies block version restoring
     *
     * @covers \Cx\Modules\Block\Model\Repository\BlockLogRepository::getLogs
     * @covers \Cx\Modules\Block\Model\Repository\BlockLogRepository::revertEntity
     */
    public function testRestoreBlockVersion()
    {
        // gets Entity Manager
        $em = \Cx\Core\Core\Controller\Cx::instanciate()->getDb()->getEntityManager();

        // starts transaction
        $em->getConnection()->beginTransaction();

        // gets existing category
        $categoryRepo = $em->getRepository('\Cx\Modules\Block\Model\Entity\Category');
        $category = $categoryRepo->findOneBy(array('id' => $this::EXISTING_CATEGORY_ID));

        // creates new block
        $block = new \Cx\Modules\Block\Model\Entity\Block();
        $block->setName('test');
        $block->setCategory($category);
        $block->setStart(0);
        $block->setEnd(0);
        $block->setRandom(0);
        $block->setRandom2(0);
        $block->setRandom3(0);
        $block->setRandom4(0);
        $block->setWysiwygEditor(1);
        $block->setShowInGlobal(0);
        $block->setShowInDirect(0);
        $block->setShowInCategory(0);
        $block->setActive(1);
        $block->setOrder(0);
        $em->persist($block);
        $em->flush();
        // gets id for restoring
        $id = $block->getId();

        // removes block
        $em->remove($block);
        $em->flush();

        // gets log entries of block
        $blockLogRepo = $em->getRepository('Cx\Modules\Block\Model\Entity\LogEntry');
        $logs = $blockLogRepo->getLogs(get_class($block), $id);

        // gets first version of block
        $block->setId($id);
        $revertedBlock = $blockLogRepo->revertEntity($block, $logs[1]->getVersion());

        // restores reverted block
        $em->persist($revertedBlock);
        $em->flush();

        // checks if block is correctly reverted by asserting its name
        $this->assertEquals('test', $revertedBlock->getName());

        // rollback DB changes
        $em->getConnection()->rollback();
    }

    /**
     * Get json block controller using repository
     *
     * @return \Cx\Modules\Block\Controller\JsonBlockController
     */
    public function getJsonBlockController()
    {
        $componentRepo = static::$cx
            ->getDb()
            ->getEntityManager()
            ->getRepository('Cx\Core\Core\Model\Entity\SystemComponent');
        $componentContoller = $componentRepo->findOneBy(array('name' => 'Block'));
        if (!$componentContoller) {
            return;
        }
        return $componentContoller->getController('JsonBlock');
    }
}
