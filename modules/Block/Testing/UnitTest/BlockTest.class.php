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
class BlockTest extends \Cx\Core\Test\Model\Entity\DoctrineTestCase
{
    /**
     * Id of existing Block needed for testing
     */
    const EXISTING_BLOCK_ID = 29;

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
     * Verifies that a version/log exists after saving a new block
     */
    public function testBlockVersionExists()
    {
        // creates a new block
        $block = $this->createNewBlock();

        // gets log entries of block
        $blockLogRepo = static::$cx
            ->getDb()
            ->getEntityManager()
            ->getRepository('Cx\Modules\Block\Model\Entity\LogEntry');
        $logs = $blockLogRepo->getLogs($block);

        // checks if logs are existing and an instance of doctrine collection
        $this->assertInstanceOf('Doctrine\Common\Collections\Collection', $logs);
    }

    /**
     * Creates a new block through block manager
     *
     * @return $block \Cx\Modules\Block\Model\Entity\Block
     */
    protected function createNewBlock()
    {
        // sets post values for creating a new block
        $_POST = array(
            'act' => 'modify',
            'blockId' => '0',
            'globalCachedLang' => 'de',
            'directCachedLang' => 'de',
            'categoryCachedLang' => 'de',
            'globalSelectedPagesList' => '16,13,65',
            'directSelectedPagesList' => '454,64,63',
            'categorySelectedPagesList' => '654,670,62',
            'blockFormLanguages' => array(
                '1' => 'german test content',
                '2' => 'english test content',
            ),
            'page' => array(
                'content' => 'german test content',
            ),
            'blockRandom' => '1',
            'blockRandom3' => '1',
            'inputStartDate' => '0',
            'inputEndDate' => '0',
            'blockGlobal' => '2',
            'pagesLangGlobal' => array(
                'de',
            ),
            'blockDirect' => '1',
            'pagesLangDirect' => array(
                'de',
            ),
            'blockCategory' => '1',
            'pagesLangCategory' => array(
                'de',
            ),
            'targeting_status' => '1',
            'targeting' => array(
                'country' => array(
                    'filter' => 'include',
                    'value' => array(
                        '204'
                    ),
                )
            ),
            'block_save_block' => 'Speichern',
        );

        // instantiates block manager
        $blockManager = new \Cx\Modules\Block\Controller\BlockManager();
        // calls block manager
        $blockManager->getPage();

        // gets id of the previously new created block
        $highestBlockId = static::$cx
            ->getDb()
            ->getEntityManager()
            ->createQueryBuilder()
            ->select('MAX(b.id)')
            ->from('\Cx\Modules\Block\Model\Entity\Block', 'b')
            ->getQuery()
            ->getSingleResult();

        // gets newly created block
        $blockRepo = static::$cx
            ->getDb()
            ->getEntityManager()
            ->getRepository('\Cx\Modules\Block\Model\Entity\Block');
        $block = $blockRepo->findOneBy(
            array(
                'id' => $highestBlockId
            )
        );

        return $block;
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
