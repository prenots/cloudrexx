ALTER TABLE contrexx_module_shop_categories CHANGE parent_id parent_id INT UNSIGNED DEFAULT NULL;
UPDATE `contrexx_module_shop_categories` SET `parent_id`=NULL WHERE `parent_id`= 0;

ALTER TABLE contrexx_module_shop_categories
  ADD CONSTRAINT FK_A9242624727ACA70 FOREIGN KEY (parent_id)
  REFERENCES contrexx_module_shop_categories (id);

CREATE INDEX IDX_A9242624727ACA70 ON contrexx_module_shop_categories (parent_id);


/** Merge Data **/
INSERT INTO contrexx_module_shop_rel_category_product SELECT
    c.id AS category_id, d.id AS product_id
FROM
    contrexx_module_shop_categories c
    JOIN contrexx_module_shop_products d
        ON d.category_id REGEXP CONCAT('[[:<:]]', c.id, '[[:>:]]');

INSERT INTO contrexx_module_shop_rel_category_pricelist SELECT
    c.id AS category_id, d.id AS pricelist_id
FROM
    contrexx_module_shop_categories c
    JOIN contrexx_module_shop_pricelists d
        ON d.categories REGEXP CONCAT('[[:<:]]', c.id, '[[:>:]]') OR d.categories = '*';

INSERT INTO contrexx_module_shop_rel_product_user_group SELECT
    c.group_id AS usergroup_id, d.id AS product_id
FROM
    contrexx_access_user_groups c
    JOIN contrexx_module_shop_products d
        ON d.usergroup_ids REGEXP CONCAT('[[:<:]]', c.group_id, '[[:>:]]');

/** Zones **/
UPDATE contrexx_module_shop_zones AS z
	INNER JOIN contrexx_core_text AS t ON z.id = t.id
  SET z.name = t.text
  WHERE t.section = 'Shop' AND t.key = 'zone_name' AND t.lang_id = 1;

/** Shipper **/
UPDATE contrexx_module_shop_shipper AS z
	INNER JOIN contrexx_core_text AS t ON z.id = t.id
  SET z.name = t.text
  WHERE t.section = 'Shop' AND t.key = 'shipper_name' AND t.lang_id = 1;

/** Products **/
UPDATE contrexx_module_shop_products AS z
	INNER JOIN contrexx_core_text AS t ON z.id = t.id
  SET z.uri = t.text
  WHERE t.section = 'Shop' AND t.key = 'product_uri' AND t.lang_id = 1;

UPDATE contrexx_module_shop_products AS z
	INNER JOIN contrexx_core_text AS t ON z.id = t.id
  SET z.short = t.text
  WHERE t.section = 'Shop' AND t.key = 'product_short' AND t.lang_id = 1;

UPDATE contrexx_module_shop_products AS z
	INNER JOIN contrexx_core_text AS t ON z.id = t.id
  SET z.long = t.text
  WHERE t.section = 'Shop' AND t.key = 'product_long' AND t.lang_id = 1;

UPDATE contrexx_module_shop_products AS z
	INNER JOIN contrexx_core_text AS t ON z.id = t.id
  SET z.name = t.text
  WHERE t.section = 'Shop' AND t.key = 'product_name' AND t.lang_id = 1;

UPDATE contrexx_module_shop_products AS z
	INNER JOIN contrexx_core_text AS t ON z.id = t.id
  SET z.keys = t.text
  WHERE t.section = 'Shop' AND t.key = 'product_keys' AND t.lang_id = 1;

UPDATE contrexx_module_shop_products AS z
	INNER JOIN contrexx_core_text AS t ON z.id = t.id
  SET z.code = t.text
  WHERE t.section = 'Shop' AND t.key = 'product_code' AND t.lang_id = 1;

/** Attribute **/
UPDATE contrexx_module_shop_attribute AS z
	INNER JOIN contrexx_core_text AS t ON z.id = t.id
  SET z.name = t.text
  WHERE t.section = 'Shop' AND t.key = 'attribute_name' AND t.lang_id = 1;

/** Option **/
UPDATE contrexx_module_shop_option AS z
	INNER JOIN contrexx_core_text AS t ON z.id = t.id
  SET z.name = t.text
  WHERE t.section = 'Shop' AND t.key = 'option_name' AND t.lang_id = 1;

/** Category **/
UPDATE contrexx_module_shop_categories AS z
	INNER JOIN contrexx_core_text AS t ON z.id = t.id
  SET z.description = t.text
  WHERE t.section = 'Shop' AND t.key = 'category_description' AND t.lang_id = 1;

UPDATE contrexx_module_shop_categories AS z
	INNER JOIN contrexx_core_text AS t ON z.id = t.id
  SET z.name = t.text
  WHERE t.section = 'Shop' AND t.key = 'category_name' AND t.lang_id = 1;

/** Manufacturer **/
UPDATE contrexx_module_shop_manufacturer AS z
	INNER JOIN contrexx_core_text AS t ON z.id = t.id
  SET z.uri = t.text
  WHERE t.section = 'Shop' AND t.key = 'manufacturer_uri' AND t.lang_id = 1;

UPDATE contrexx_module_shop_manufacturer AS z
	INNER JOIN contrexx_core_text AS t ON z.id = t.id
  SET z.name = t.text
  WHERE t.section = 'Shop' AND t.key = 'manufacturer_name' AND t.lang_id = 1;

/** Payment **/
UPDATE contrexx_module_shop_payment AS z
	INNER JOIN contrexx_core_text AS t ON z.id = t.id
  SET z.name = t.text
  WHERE t.section = 'Shop' AND t.key = 'payment_name' AND t.lang_id = 1;

/** Currency **/
UPDATE contrexx_module_shop_currencies AS z
	INNER JOIN contrexx_core_text AS t ON z.id = t.id
  SET z.name = t.text
  WHERE t.section = 'Shop' AND t.key = 'currency_name' AND t.lang_id = 1;

/** Vat **/
UPDATE contrexx_module_shop_vat AS z
	INNER JOIN contrexx_core_text AS t ON z.id = t.id
  SET z.class = t.text
  WHERE t.section = 'Shop' AND t.key = 'vat_class' AND t.lang_id = 1;

/** Discountgroup Count Name **/
UPDATE contrexx_module_shop_discountgroup_count_name AS z
	INNER JOIN contrexx_core_text AS t ON z.id = t.id
  SET z.unit = t.text
  WHERE t.section = 'Shop' AND t.key = 'discount_group_unit' AND t.lang_id = 1;
UPDATE contrexx_module_shop_discountgroup_count_name AS z
	INNER JOIN contrexx_core_text AS t ON z.id = t.id
  SET z.name = t.text
  WHERE t.section = 'Shop' AND t.key = 'discount_group_name' AND t.lang_id = 1;

/** Article Group **/
UPDATE contrexx_module_shop_article_group AS z
	INNER JOIN contrexx_core_text AS t ON z.id = t.id
  SET z.name = t.text
  WHERE t.section = 'Shop' AND t.key = 'discount_group_article' AND t.lang_id = 1;

/** Customer Group **/
UPDATE contrexx_module_shop_customer_group AS z
	INNER JOIN contrexx_core_text AS t ON z.id = t.id
  SET z.name = t.text
  WHERE t.section = 'Shop' AND t.key = 'discount_group_customer' AND t.lang_id = 1;


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