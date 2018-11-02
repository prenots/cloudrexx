/** Drop **/
ALTER TABLE contrexx_module_shop_pricelists DROP categories;
ALTER TABLE contrexx_module_shop_products DROP category_id, DROP usergroup_ids;

DELETE FROM `contrexx_core_text` WHERE `key` = 'zone_name';
DELETE FROM `contrexx_core_text` WHERE `key` = 'shipper_name';
DELETE FROM `contrexx_core_text` WHERE `key` = 'product_uri';
DELETE FROM `contrexx_core_text` WHERE `key` = 'product_short';
DELETE FROM `contrexx_core_text` WHERE `key` = 'product_long';
DELETE FROM `contrexx_core_text` WHERE `key` = 'product_name';
DELETE FROM `contrexx_core_text` WHERE `key` = 'product_keys';
DELETE FROM `contrexx_core_text` WHERE `key` = 'product_code';
DELETE FROM `contrexx_core_text` WHERE `key` = 'attribute_name';
DELETE FROM `contrexx_core_text` WHERE `key` = 'option_name';
DELETE FROM `contrexx_core_text` WHERE `key` = 'category_description';
DELETE FROM `contrexx_core_text` WHERE `key` = 'category_name';
DELETE FROM `contrexx_core_text` WHERE `key` = 'manufacturer_uri';
DELETE FROM `contrexx_core_text` WHERE `key` = 'manufacturer_name';
DELETE FROM `contrexx_core_text` WHERE `key` = 'payment_name';
DELETE FROM `contrexx_core_text` WHERE `key` = 'currency_name';
DELETE FROM `contrexx_core_text` WHERE `key` = 'vat_class';
DELETE FROM `contrexx_core_text` WHERE `key` = 'discount_group_unit';
DELETE FROM `contrexx_core_text` WHERE `key` = 'discount_group_name';
DELETE FROM `contrexx_core_text` WHERE `key` = 'discount_group_article';
DELETE FROM `contrexx_core_text` WHERE `key` = 'discount_group_customer';