<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Code\Reflection\DocBlock;

use Zend\Code\Generic\Prototype\PrototypeClassFactory;
use Zend\Code\Reflection\DocBlock\Tag\TagInterface;

class TagManager extends PrototypeClassFactory
{
    /**
     * @return void
     */
    public function initializeDefaultTags()
    {
        $this->addPrototype(new \Zend\Code\Reflection\DocBlock\Tag\ParamTag());
        $this->addPrototype(new \Zend\Code\Reflection\DocBlock\Tag\ReturnTag());
        $this->addPrototype(new \Zend\Code\Reflection\DocBlock\Tag\MethodTag());
        $this->addPrototype(new \Zend\Code\Reflection\DocBlock\Tag\PropertyTag());
        $this->addPrototype(new \Zend\Code\Reflection\DocBlock\Tag\AuthorTag());
        $this->addPrototype(new \Zend\Code\Reflection\DocBlock\Tag\LicenseTag());
        $this->addPrototype(new \Zend\Code\Reflection\DocBlock\Tag\ThrowsTag());
        $this->setGenericPrototype(new \Zend\Code\Reflection\DocBlock\Tag\GenericTag());
    }

    /**
     * @param string $tagName
     * @param string $content
     * @return TagInterface
     */
    public function createTag($tagName, $content = null)
    {
        /* @var TagInterface $newTag */
        $newTag = $this->getClonedPrototype($tagName);

        if ($content) {
            $newTag->initialize($content);
        }

        return $newTag;
    }
}
