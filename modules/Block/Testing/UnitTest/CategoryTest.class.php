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
 * Cx\Modules\Block\Testing\UnitTest\CategoryTest
 *
 * @copyright   CLOUDREXX CMS - CLOUDREXX AG
 * @author      Manuel Schenk <manuel.schenk@comvation.com>
 * @version     1.0.0
 * @package     cloudrexx
 * @subpackage  module_block
 */

namespace Cx\Modules\Block\Testing\UnitTest;

/**
 * Cx\Modules\Block\Testing\UnitTest\CategoryTest
 *
 * @copyright   CLOUDREXX CMS - CLOUDREXX AG
 * @author      Manuel Schenk <manuel.schenk@comvation.com>
 * @version     1.0.0
 * @package     cloudrexx
 * @subpackage  module_block
 */
class CategoryTest extends \Cx\Core\Test\Model\Entity\DoctrineTestCase
{
    /**
     * Tests category creation without enough arguments
     *
     * @expectedException \Cx\Modules\Block\Controller\NotEnoughArgumentsException
     */
    public function testCreateCategoryNotEnoughArguments()
    {
        $this->loginUser();
        $blockLibrary = $this->getBlockLibrary();
        $blockLibrary->_saveCategory(
            0,
            0,
            'Test Category',
            null,
            1,
            1
        );
    }

    /**
     * Tests category creation
     */
    public function testCreateCategory()
    {
        $this->loginUser();
        $blockLibrary = $this->getBlockLibrary();
        $id = $blockLibrary->_saveCategory(
            0,
            0,
            'Test Category',
            '[separator]',
            1,
            1
        );
        $this->assertNotFalse($id);

        $em = \Cx\Core\Core\Controller\Cx::instanciate()->getDb()->getEntityManager();
        $categoryRepo = $em->getRepository('\Cx\Modules\Block\Model\Entity\Category');
        $category = $categoryRepo->findOneBy(array('id' => $id));
        $this->assertEquals('Test Category', $category->getName());
    }

    /**
     * Tests update on non existing category
     *
     * @expectedException \Cx\Modules\Block\Controller\NoCategoryFoundException
     */
    public function testUpdateCategoryNoCategoryFound()
    {
        $this->loginUser();
        $blockLibrary = $this->getBlockLibrary();
        $blockLibrary->_saveCategory(
            1,
            0,
            'Test Category updated',
            '[separator updated]',
            1,
            1
        );
    }

    /**
     * Tests block saving
     */
    public function testUpdateCategory()
    {
        $this->loginUser();
        $blockLibrary = $this->getBlockLibrary();
        $id = $blockLibrary->_saveCategory(
            2,
            0,
            'Test Category updated',
            '[separator updated]',
            1,
            1
        );
        $this->assertNotFalse($id);

        $em = \Cx\Core\Core\Controller\Cx::instanciate()->getDb()->getEntityManager();
        $categoryRepo = $em->getRepository('\Cx\Modules\Block\Model\Entity\Category');
        $category = $categoryRepo->findOneBy(array('id' => 2));
        $this->assertEquals('Test Category updated', $category->getName());
    }

    /**
     * Test category deleting without enough arguments
     *
     * @covers \Cx\Modules\Block\Controller\BlockLibrary::_deleteCategory
     * @expectedException \Cx\Modules\Block\Controller\NotEnoughArgumentsException
     */
    public function testDeleteCategoryNotEnoughArguments()
    {
        $this->loginUser();
        $blockLibrary = $this->getBlockLibrary();
        $blockLibrary->_deleteCategory();
    }

    /**
     * Tests deleting on non existing category
     *
     * @covers \Cx\Modules\Block\Controller\BlockLibrary::_deleteCategory
     * @expectedException \Cx\Modules\Block\Controller\NoCategoryFoundException
     */
    public function testDeleteCategoryNoCategoryFound()
    {
        $this->loginUser();
        $blockLibrary = $this->getBlockLibrary();
        $blockLibrary->_deleteCategory(1);
    }

    /**
     * Tests category deleting
     *
     * @covers \Cx\Modules\Block\Controller\BlockLibrary::_deleteCategory
     */
    public function testDeleteCategory()
    {
        $this->loginUser();
        $blockLibrary = $this->getBlockLibrary();
        $return = $blockLibrary->_deleteCategory(2);
        $this->assertTrue($return);

        $em = \Cx\Core\Core\Controller\Cx::instanciate()->getDb()->getEntityManager();
        $categoryRepo = $em->getRepository('\Cx\Modules\Block\Model\Entity\Category');
        $category = $categoryRepo->findOneBy(array('id' => 2));
        $this->assertNull($category);
    }

    /**
     * Gets block library using repository
     *
     * @return \Cx\Modules\Block\Controller\BlockLibrary
     */
    public function getBlockLibrary()
    {
        return new \Cx\Modules\Block\Controller\BlockLibrary();
    }

    /**
     * Login a user for testing
     */
    protected function loginUser()
    {
        $sessionObj = self::$cx->getComponent('Session')->getSession();
        $user = \FWUser::getFWUserObject()->objUser->getUser(1);
        \FWUser::loginUser($user);
    }
}
