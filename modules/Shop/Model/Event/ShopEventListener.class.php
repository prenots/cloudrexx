<?php

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
 * EventListener for Shop
 *
 * @copyright   Cloudrexx AG
 * @author      Project Team SS4U <info@cloudrexx.com>
 * @package     cloudrexx
 * @subpackage  module_shop
 */

namespace Cx\Modules\Shop\Model\Event;
use Cx\Core\Core\Controller\Cx;
use Cx\Core\MediaSource\Model\Entity\MediaSourceManager;
use Cx\Core\MediaSource\Model\Entity\MediaSource;
use Cx\Core\Event\Model\Entity\DefaultEventListener;

/**
 * Class ShopEventListener
 * EventListener for Shop
 *
 * @copyright   Cloudrexx AG
 * @author      Project Team SS4U <info@cloudrexx.com>
 * @package     cloudrexx
 * @subpackage  module_shop
 */
class ShopEventListener extends DefaultEventListener {

    protected $mappedAttributes =
        array(
            'category_description' => array(
                'entity' => 'Categories',
                'attr' => 'description'
            ),
            'category_name' => array(
                'entity' => 'Categories',
                'attr' => 'name'
            ),
            'currency_name' => array(
                'entity' => 'Currencies',
                'attr' => 'name'
            ),
            'discount_group_article' => array(
                'entity' => 'ArticleGroup',
                'attr' => 'name'
            ),
            'discount_group_customer' => array(
                'entity' => 'CustomerGroup',
                'attr' => 'name'
            ),
            'discount_group_name' => array(
                'entity' => 'DiscountgroupCountName',
                'attr' => 'name'
            ),
            'discount_group_unit' => array(
                'entity' => 'DiscountgroupCountName',
                'attr' => 'name'
            ),
            'product_code' => array(
                'entity' => 'Products',
                'attr' => 'code'
            ),
            'product_keys' => array(
                'entity' => 'Products',
                'attr' => 'keys'
            ),
            'product_long' => array(
                'entity' => 'Products',
                'attr' => 'long'
            ),
            'product_name' => array(
                'entity' => 'Products',
                'attr' => 'name'
            ),
            'product_short' => array(
                'entity' => 'Products',
                'attr' => 'short'
            ),
            'product_uri' => array(
                'entity' => 'Products',
                'attr' => 'uri'
            ),
        );


    public function SearchFindContent($search) {
        $term_db = $search->getTerm();

        $flagIsReseller = false;
        $objUser = \FWUser::getFWUserObject()->objUser;

        if ($objUser->login()) {
            $objCustomer = \Cx\Modules\Shop\Controller\Customer::getById($objUser->getId());
            \Cx\Core\Setting\Controller\Setting::init('Shop', 'config');
            if ($objCustomer && $objCustomer->is_reseller()) {
                $flagIsReseller = true;
            }
        }

        $querySelect = $queryCount = $queryOrder = null;
        list($querySelect, $queryCount, $queryTail, $queryOrder) = \Cx\Modules\Shop\Controller\Products::getQueryParts(null, null, null, $term_db, false, false, '', $flagIsReseller);
        $query = $querySelect . $queryTail . $queryOrder;//Search query
        $parseSearchData = function(&$searchData) {
                                $searchData['title']   = $searchData['name'];
                                $searchData['content'] = $searchData['long'] ? $searchData['long'] : $searchData['short'];
                                $searchData['score']   = $searchData['score1'] + $searchData['score2'] + $searchData['score3'];
                            };
        $result = new \Cx\Core_Modules\Listing\Model\Entity\DataSet($search->getResultArray($query, 'Shop', 'details', 'productId=', $search->getTerm(), $parseSearchData));
        $search->appendResult($result);
    }

    public function mediasourceLoad(MediaSourceManager $mediaBrowserConfiguration)
    {
        global $_ARRAYLANG;
        \Env::get('init')->loadLanguageData('MediaBrowser');
        $mediaType = new MediaSource('shop',$_ARRAYLANG['TXT_FILEBROWSER_SHOP'],array(
            $this->cx->getWebsiteImagesShopPath(),
            $this->cx->getWebsiteImagesShopWebPath(),
        ));
        $mediaType->setAccessIds(array(13));
        $mediaBrowserConfiguration->addMediaType($mediaType);
    }

    /**
     * Update translatable entities with doctrine
     *
     * @param $replaceInfo array info includes id of entity, key and value of
     *                           \Text
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function textReplace($replaceInfo)
    {
        $id = $replaceInfo[0];
        $key = $replaceInfo[1];
        $value = $replaceInfo[2];

        $entityAndAttr = $this->getEntityNameAndAttr($key);
        $setter = 'set' . $entityAndAttr['attrName'];

        $em = $this->cx->getDb()->getEntityManager();
        $repo = $em->getRepository(
            '\\'.$entityAndAttr['entityName']
        );
        // Save old translatable locale to set it after updating the attribute
        $oldLocale = $this->cx->getDb()->getTranslationListener()
            ->getTranslatableLocale();
        // Set translatable locale by frontend lang id
        $this->cx->getDb()->getTranslationListener()->setTranslatableLocale(
            \FWLanguage::getLanguageCodeById(FRONTEND_LANG_ID)
        );
        // Only entities with a identifier named id affected
        $entity = $repo->find($id);
        $entity->$setter($value);

        $this->cx->getDb()->getTranslationListener()->setTranslatableLocale(
            $oldLocale
        );

        $em->persist($entity);
        $em->flush();
    }

    /**
     * Get the entity namespace and attribute name from key
     *
     * @param $key array include key of \Text
     * @return array
     */
    protected function getEntityNameAndAttr($key)
    {
        if (array_key_exists($key, $this->mappedAttributes)) {
            $entityName = 'Cx\Modules\Shop\Model\Entity\\'.
                $this->mappedAttributes[$key]['entity'];
            $attrName = ucfirst($this->mappedAttributes[$key]['attr']);
        } else {
            $keyFragments = explode('_', $key);
            $entityName = 'Cx\Modules\Shop\Model\Entity\\'. ucfirst(
                $keyFragments[0]
            );
            $attrName = ucfirst($keyFragments[1]);
        }

        return array('entityName' => $entityName, 'attrName' => $attrName);
    }
}
