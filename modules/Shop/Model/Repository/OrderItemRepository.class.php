<?php

namespace Cx\Modules\Shop\Model\Repository;

/**
 * OrderItemsRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class OrderItemRepository extends \Doctrine\ORM\EntityRepository
{
    public function save($values, $order)
    {
        $settersAndIds = $this->getSettersAndIds($values);

        foreach ($settersAndIds['ids'] as $orderItemId) {
            if (empty($values['product_product_id-' . $orderItemId])) {
                continue;
            }

            $orderItem = $this->find($orderItemId);
            if (empty($orderItem)) {
                $orderItem = new $this->_entityName();
            }

            if (empty($values['product_quantity-' . $orderItemId])) {
                $this->_em->remove($orderItem);
                continue;
            }

            $orderItem->setOrder($order);

            foreach ($settersAndIds['setters'] as $key => $setter) {

                $value = $values[$key . '-' . $orderItemId];

                // get product name and set product relation
                if ($key == 'product_product_name') {
                    $product = $this->_em->getRepository(
                        '\Cx\Modules\Shop\Model\Entity\Product'
                    )->findOneBy(
                        array(
                            'id' => $values['product_product_id-'. $orderItemId]
                        )
                    );
                    $orderItem->setProduct($product);
                    $value = $product->getName();
                }
                $orderItem->$setter($value);
            }
            $this->_em->persist($orderItem);
        }
    }

    /**
     * Returns an array with all setters and ids. In this array are only
     * setter that have a matching attribute in the entity.
     *
     * @param $values
     * @return array
     */
    protected function getSettersAndIds($values)
    {
        $settersAndKeys = array();
        $setters = array();
        $ids = array();

        foreach ($values as $key=>$value) {
            if (strpos($key, 'product') === 0) {
                // Explode Keys by _ to get the key without prefix
                $keyParts = explode('_', $key, 2);
                $keyWithoutPrefix = $keyParts[1];
                // Split method name and product id
                $methodAndKey = explode('-', $keyWithoutPrefix);
                $methodName = $methodAndKey[0];
                // Use as new key in settersAndKeys array
                $keyWithoutId = $keyParts[0] . '_' . $methodName;
                // Id of order item
                $orderItemId = $methodAndKey[1];

                // Push id into $ids array if it not already exist
                if (!in_array($orderItemId, $ids)) {
                    array_push($ids, $orderItemId);
                }

                // Push all setters into $setters array if it not already exist
                $setter = $this->getSetter($methodName);
                if (!empty($setter)) {
                    $setters[$keyWithoutId] = $setter;
                }
            }
        }

        $settersAndKeys['setters'] = $setters;
        $settersAndKeys['ids'] = $ids;

        return $settersAndKeys;
    }

    /**
     * Convert a string into an setter if the attribute exists in the entity.
     *
     * @param $attributeName string name of the attribute that should be
     *                              converted.
     * @return string
     */
    protected function getSetter($attributeName)
    {
        $columnNames = $this->_em->getClassMetadata(
            $this->_entityName
        )->getColumnNames();

        if (!in_array($attributeName, $columnNames)) {
            return '';
        }

        // Replace _ and set new word to uppercase, to get the setter name
        $setter = 'set' . str_replace(
            " ",
            "",
            mb_convert_case(
                str_replace(
                    "_",
                    " ",
                    $attributeName
                ),
                MB_CASE_TITLE
            )
        );
        return $setter;
    }
}
