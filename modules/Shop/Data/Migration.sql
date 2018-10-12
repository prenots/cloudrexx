/** Create Tables **/
CREATE TABLE contrexx_module_shop_rel_category_pricelist (
  category_id INT UNSIGNED NOT NULL,
  pricelist_id INT UNSIGNED NOT NULL,
  INDEX IDX_B56E91A112469DE2 (category_id),
  INDEX IDX_B56E91A189045958 (pricelist_id),
  PRIMARY KEY(category_id, pricelist_id)
) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;

CREATE TABLE contrexx_module_shop_rel_category_product (
  category_id INT UNSIGNED NOT NULL,
  product_id INT UNSIGNED NOT NULL,
  INDEX IDX_DA4CA51112469DE2 (category_id),
  INDEX IDX_DA4CA5114584665A (product_id),
  PRIMARY KEY(category_id, product_id)
) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;

CREATE TABLE contrexx_module_shop_rel_product_user_group (
  product_id INT UNSIGNED NOT NULL,
  usergroup_id INT NOT NULL,
  INDEX IDX_32A4494A4584665A (product_id),
  INDEX IDX_32A4494AD2112630 (usergroup_id),
  PRIMARY KEY(product_id, usergroup_id)
) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;


/** Structural adjustments  **/
ALTER TABLE contrexx_module_shop_orders
  CHANGE customer_id customer_id INT DEFAULT 0 NOT NULL,
  CHANGE date_time date_time DATETIME DEFAULT '0000-00-00 00:00:00' NOT NULL,
  CHANGE lang_id lang_id INT DEFAULT 0 NOT NULL,
  CHANGE modified_on modified_on DATETIME DEFAULT NULL;

ALTER TABLE contrexx_module_shop_manufacturer
  ADD uri VARCHAR(55) DEFAULT '' NOT NULL,
  ADD name VARCHAR(35) DEFAULT '' NOT NULL;

ALTER TABLE contrexx_module_shop_currencies
  ADD name VARCHAR(35) DEFAULT '' NOT NULL;

ALTER TABLE contrexx_module_shop_option
  ADD name VARCHAR(45) DEFAULT '' NOT NULL;

ALTER TABLE contrexx_module_shop_products
  ADD uri VARCHAR(55) DEFAULT '' NOT NULL,
  ADD short LONGTEXT NOT NULL,
  ADD `long` LONGTEXT NOT NULL,
  ADD name VARCHAR(45) DEFAULT '' NOT NULL,
  ADD `keys` LONGTEXT NOT NULL,
  ADD code VARCHAR(45) DEFAULT '' NOT NULL,
  CHANGE date_start date_start DATETIME DEFAULT '0000-00-00 00:00:00' NOT NULL,
  CHANGE date_end date_end DATETIME DEFAULT '0000-00-00 00:00:00' NOT NULL;

ALTER TABLE contrexx_module_shop_payment ADD name VARCHAR(45) DEFAULT '' NOT NULL;

ALTER TABLE contrexx_module_shop_shipper ADD name VARCHAR(30) DEFAULT '' NOT NULL;

ALTER TABLE contrexx_module_shop_vat ADD class VARCHAR(35) NOT NULL;

ALTER TABLE contrexx_module_shop_article_group ADD name VARCHAR(45) DEFAULT '' NOT NULL;

ALTER TABLE contrexx_module_shop_categories
  ADD name VARCHAR(45) DEFAULT '' NOT NULL,
  ADD description LONGTEXT NOT NULL;

ALTER TABLE contrexx_module_shop_pricelists CHANGE lang_id lang_id INT DEFAULT 0 NOT NULL;

ALTER TABLE contrexx_module_shop_rel_customer_coupon CHANGE customer_id customer_id INT DEFAULT 0 NOT NULL;

ALTER TABLE contrexx_module_shop_discountgroup_count_name
  ADD unit VARCHAR(30) DEFAULT '' NOT NULL,
  ADD name VARCHAR(45) DEFAULT '' NOT NULL;
ALTER TABLE contrexx_module_shop_attribute ADD name VARCHAR(35) DEFAULT '' NOT NULL;

ALTER TABLE contrexx_module_shop_zones ADD name VARCHAR(45) DEFAULT '' NOT NULL;
ALTER TABLE contrexx_module_shop_rel_payment
  CHANGE zone_id zone_id INT UNSIGNED NOT NULL,
  CHANGE payment_id payment_id INT UNSIGNED NOT NULL;

ALTER TABLE contrexx_module_shop_rel_shipper DROP PRIMARY KEY;
ALTER TABLE contrexx_module_shop_rel_shipper
  CHANGE shipper_id shipper_id INT UNSIGNED NOT NULL,
  CHANGE zone_id zone_id INT UNSIGNED NOT NULL;

ALTER TABLE contrexx_module_shop_customer_group ADD name VARCHAR(45) DEFAULT '' NOT NULL;

ALTER TABLE contrexx_module_shop_categories CHANGE description description LONGTEXT DEFAULT '' NOT NULL;


/** Drop Primary Keys **/
ALTER TABLE contrexx_module_shop_discount_coupon DROP PRIMARY KEY;
ALTER TABLE contrexx_module_shop_rel_countries DROP PRIMARY KEY;


/** To insert relations without problems **/
ALTER TABLE contrexx_module_shop_discount_coupon
  CHANGE customer_id customer_id INT NULL,
  CHANGE payment_id payment_id INT UNSIGNED DEFAULT NULL,
  CHANGE product_id product_id INT UNSIGNED DEFAULT NULL;
UPDATE `contrexx_module_shop_discount_coupon` SET `payment_id`=NULL WHERE `payment_id`= 0;
UPDATE `contrexx_module_shop_discount_coupon` SET `product_id`=NULL WHERE `product_id`= 0;
UPDATE `contrexx_module_shop_discount_coupon` SET `customer_id`=NULL WHERE `customer_id`= 0;

UPDATE `contrexx_module_shop_products` SET `article_id`=NULL WHERE `article_id`= 0;
UPDATE `contrexx_module_shop_products` SET `group_id`=NULL WHERE `group_id`= 0;

ALTER TABLE contrexx_module_shop_categories CHANGE parent_id parent_id INT UNSIGNED DEFAULT NULL;
UPDATE `contrexx_module_shop_categories` SET `parent_id`=NULL WHERE `parent_id`= 0;

/** Constraints **/
ALTER TABLE contrexx_module_shop_rel_category_pricelist ADD CONSTRAINT FK_B56E91A112469DE2 FOREIGN KEY (category_id) REFERENCES contrexx_module_shop_categories (id);
ALTER TABLE contrexx_module_shop_rel_category_pricelist ADD CONSTRAINT FK_B56E91A189045958 FOREIGN KEY (pricelist_id) REFERENCES contrexx_module_shop_pricelists (id);
ALTER TABLE contrexx_module_shop_rel_category_product ADD CONSTRAINT FK_DA4CA51112469DE2 FOREIGN KEY (category_id) REFERENCES contrexx_module_shop_categories (id);
ALTER TABLE contrexx_module_shop_rel_category_product ADD CONSTRAINT FK_DA4CA5114584665A FOREIGN KEY (product_id) REFERENCES contrexx_module_shop_products (id);

ALTER TABLE contrexx_module_shop_orders ADD CONSTRAINT FK_DA286BB1B213FA4 FOREIGN KEY (lang_id) REFERENCES contrexx_core_locale_locale (id);
ALTER TABLE contrexx_module_shop_orders ADD CONSTRAINT FK_DA286BB138248176 FOREIGN KEY (currency_id) REFERENCES contrexx_module_shop_currencies (id);
ALTER TABLE contrexx_module_shop_orders ADD CONSTRAINT FK_DA286BB17BE036FC FOREIGN KEY (shipment_id) REFERENCES contrexx_module_shop_shipper (id);
ALTER TABLE contrexx_module_shop_orders ADD CONSTRAINT FK_DA286BB14C3A3BB FOREIGN KEY (payment_id) REFERENCES contrexx_module_shop_payment (id);
ALTER TABLE contrexx_module_shop_orders ADD CONSTRAINT FK_DA286BB19395C3F3 FOREIGN KEY (customer_id) REFERENCES contrexx_access_users (id);

ALTER TABLE contrexx_module_shop_order_attributes ADD CONSTRAINT FK_273F59F6126F525E FOREIGN KEY (item_id) REFERENCES contrexx_module_shop_order_items (id);

ALTER TABLE contrexx_module_shop_option ADD CONSTRAINT FK_658196EFB6E62EFA FOREIGN KEY (attribute_id) REFERENCES contrexx_module_shop_attribute (id);

ALTER TABLE contrexx_module_shop_products ADD CONSTRAINT FK_97F512B7A23B42D FOREIGN KEY (manufacturer_id) REFERENCES contrexx_module_shop_manufacturer (id);
ALTER TABLE contrexx_module_shop_products ADD CONSTRAINT FK_97F512B7FE54D947 FOREIGN KEY (group_id) REFERENCES contrexx_module_shop_discountgroup_count_name (id);
ALTER TABLE contrexx_module_shop_products ADD CONSTRAINT FK_97F512B77294869C FOREIGN KEY (article_id) REFERENCES contrexx_module_shop_article_group (id);
ALTER TABLE contrexx_module_shop_products ADD CONSTRAINT FK_97F512B7B5B63A6B FOREIGN KEY (vat_id) REFERENCES contrexx_module_shop_vat (id);

ALTER TABLE contrexx_module_shop_payment ADD CONSTRAINT FK_96C3CFFE37BAC19A FOREIGN KEY (processor_id) REFERENCES contrexx_module_shop_payment_processors (id);

ALTER TABLE contrexx_module_shop_rel_product_attribute ADD CONSTRAINT FK_E17E240B4584665A FOREIGN KEY (product_id) REFERENCES contrexx_module_shop_products (id);
ALTER TABLE contrexx_module_shop_rel_product_attribute ADD CONSTRAINT FK_E17E240BA7C41D6F FOREIGN KEY (option_id) REFERENCES contrexx_module_shop_option (id);

ALTER TABLE contrexx_module_shop_order_items ADD CONSTRAINT FK_1D79476B8D9F6D38 FOREIGN KEY (order_id) REFERENCES contrexx_module_shop_orders (id);
ALTER TABLE contrexx_module_shop_order_items ADD CONSTRAINT FK_1D79476B4584665A FOREIGN KEY (product_id) REFERENCES contrexx_module_shop_products (id);

ALTER TABLE contrexx_module_shop_discount_coupon ADD CONSTRAINT FK_7E70AB1A4C3A3BB FOREIGN KEY (payment_id) REFERENCES contrexx_module_shop_payment (id);
ALTER TABLE contrexx_module_shop_discount_coupon ADD CONSTRAINT FK_7E70AB1A4584665A FOREIGN KEY (product_id) REFERENCES contrexx_module_shop_products (id);
ALTER TABLE contrexx_module_shop_discount_coupon ADD CONSTRAINT FK_7E70AB1A9395C3F3 FOREIGN KEY (customer_id) REFERENCES contrexx_access_users (id);

ALTER TABLE contrexx_module_shop_discountgroup_count_rate ADD CONSTRAINT FK_3F3DD477FE54D947 FOREIGN KEY (group_id)
  REFERENCES contrexx_module_shop_discountgroup_count_name (id);

ALTER TABLE contrexx_module_shop_pricelists ADD CONSTRAINT FK_BB867D48B213FA4 FOREIGN KEY (lang_id) REFERENCES contrexx_core_locale_locale (id);

ALTER TABLE contrexx_module_shop_lsv ADD CONSTRAINT FK_889921958D9F6D38 FOREIGN KEY (order_id) REFERENCES contrexx_module_shop_orders (id);

ALTER TABLE contrexx_module_shop_rel_customer_coupon ADD CONSTRAINT FK_6A7FBE248D9F6D38 FOREIGN KEY (order_id) REFERENCES contrexx_module_shop_orders (id);
ALTER TABLE contrexx_module_shop_rel_customer_coupon ADD CONSTRAINT FK_6A7FBE249395C3F3 FOREIGN KEY (customer_id) REFERENCES contrexx_access_users (id);

ALTER TABLE contrexx_module_shop_rel_discount_group ADD CONSTRAINT FK_93D6FD61D2919A68 FOREIGN KEY (customer_group_id) REFERENCES contrexx_module_shop_customer_group (id);
ALTER TABLE contrexx_module_shop_rel_discount_group ADD CONSTRAINT FK_93D6FD61ABBC2D2C FOREIGN KEY (article_group_id) REFERENCES contrexx_module_shop_article_group (id);

ALTER TABLE contrexx_module_shop_rel_payment ADD CONSTRAINT FK_43EB87989F2C3FAB FOREIGN KEY (zone_id) REFERENCES contrexx_module_shop_zones (id);
ALTER TABLE contrexx_module_shop_rel_payment ADD CONSTRAINT FK_43EB87984C3A3BB FOREIGN KEY (payment_id) REFERENCES contrexx_module_shop_payment (id);

ALTER TABLE contrexx_module_shop_rel_shipper ADD CONSTRAINT FK_87E5C9689F2C3FAB FOREIGN KEY (zone_id) REFERENCES contrexx_module_shop_zones (id);
ALTER TABLE contrexx_module_shop_rel_shipper ADD CONSTRAINT FK_87E5C96838459F23 FOREIGN KEY (shipper_id) REFERENCES contrexx_module_shop_shipper (id);

ALTER TABLE contrexx_module_shop_shipment_cost ADD CONSTRAINT FK_2329A4538459F23 FOREIGN KEY (shipper_id) REFERENCES contrexx_module_shop_shipper (id);

ALTER TABLE contrexx_module_shop_categories ADD CONSTRAINT FK_A9242624727ACA70 FOREIGN KEY (parent_id) REFERENCES contrexx_module_shop_categories (id);

ALTER TABLE contrexx_module_shop_rel_product_user_group ADD CONSTRAINT FK_32A4494A4584665A FOREIGN KEY (product_id) REFERENCES contrexx_module_shop_products (id);
ALTER TABLE contrexx_module_shop_rel_product_user_group ADD CONSTRAINT FK_32A4494AD2112630 FOREIGN KEY (usergroup_id) REFERENCES contrexx_access_user_groups (group_id);

ALTER TABLE contrexx_module_shop_rel_countries ADD CONSTRAINT FK_C859EA8B9F2C3FAB FOREIGN KEY (zone_id) REFERENCES contrexx_module_shop_zones (id);


/** Index **/
CREATE INDEX IDX_DA286BB1B213FA4 ON contrexx_module_shop_orders (lang_id);
CREATE INDEX IDX_DA286BB138248176 ON contrexx_module_shop_orders (currency_id);
CREATE INDEX IDX_DA286BB17BE036FC ON contrexx_module_shop_orders (shipment_id);
CREATE INDEX IDX_DA286BB14C3A3BB ON contrexx_module_shop_orders (payment_id);
CREATE INDEX IDX_DA286BB19395C3F3 ON contrexx_module_shop_orders (customer_id);

CREATE INDEX IDX_658196EFB6E62EFA ON contrexx_module_shop_option (attribute_id);

CREATE INDEX IDX_97F512B7A23B42D ON contrexx_module_shop_products (manufacturer_id);
CREATE INDEX IDX_97F512B7B5B63A6B ON contrexx_module_shop_products (vat_id);

CREATE INDEX IDX_96C3CFFE37BAC19A ON contrexx_module_shop_payment (processor_id);

CREATE INDEX IDX_E17E240B4584665A ON contrexx_module_shop_rel_product_attribute (product_id);
CREATE INDEX IDX_E17E240BA7C41D6F ON contrexx_module_shop_rel_product_attribute (option_id);

CREATE INDEX IDX_1D79476B4584665A ON contrexx_module_shop_order_items (product_id);

CREATE INDEX IDX_7E70AB1A4C3A3BB ON contrexx_module_shop_discount_coupon (payment_id);
CREATE INDEX IDX_7E70AB1A4584665A ON contrexx_module_shop_discount_coupon (product_id);
CREATE INDEX IDX_7E70AB1A9395C3F3 ON contrexx_module_shop_discount_coupon (customer_id);

CREATE INDEX IDX_3F3DD477FE54D947 ON contrexx_module_shop_discountgroup_count_rate (group_id);
CREATE INDEX fk_contrexx_module_shop_discountgroup_count_rate_contrexx_m_idx ON contrexx_module_shop_discountgroup_count_rate (count);

CREATE INDEX IDX_BB867D48B213FA4 ON contrexx_module_shop_pricelists (lang_id);

CREATE INDEX IDX_6A7FBE248D9F6D38 ON contrexx_module_shop_rel_customer_coupon (order_id);
CREATE INDEX IDX_6A7FBE249395C3F3 ON contrexx_module_shop_rel_customer_coupon (customer_id);

CREATE INDEX IDX_93D6FD61D2919A68 ON contrexx_module_shop_rel_discount_group (customer_group_id);
CREATE INDEX IDX_93D6FD61ABBC2D2C ON contrexx_module_shop_rel_discount_group (article_group_id);

CREATE INDEX IDX_43EB87989F2C3FAB ON contrexx_module_shop_rel_payment (zone_id);
CREATE INDEX IDX_43EB87984C3A3BB ON contrexx_module_shop_rel_payment (payment_id);

CREATE INDEX IDX_87E5C9689F2C3FAB ON contrexx_module_shop_rel_shipper (zone_id);
CREATE INDEX IDX_87E5C96838459F23 ON contrexx_module_shop_rel_shipper (shipper_id);

CREATE INDEX IDX_2329A4538459F23 ON contrexx_module_shop_shipment_cost (shipper_id);

CREATE INDEX IDX_A9242624727ACA70 ON contrexx_module_shop_categories (parent_id);

CREATE INDEX IDX_C859EA8B9F2C3FAB ON contrexx_module_shop_rel_countries (zone_id);


/** Add Primary Keys **/
ALTER TABLE contrexx_module_shop_rel_shipper ADD PRIMARY KEY (zone_id, shipper_id);
ALTER TABLE contrexx_module_shop_rel_countries ADD PRIMARY KEY (zone_id, country_id);

/** customer_id is no longer a primary key, because it can also be null.**/
ALTER TABLE contrexx_module_shop_discount_coupon ADD PRIMARY KEY (code);


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