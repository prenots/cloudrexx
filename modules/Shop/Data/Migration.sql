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


/** Andere Strukturanpassungen **/
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
  ADD short LONGTEXT DEFAULT '' NOT NULL,
  ADD `long` LONGTEXT DEFAULT '' NOT NULL,
  ADD name VARCHAR(45) DEFAULT '' NOT NULL,
  ADD `keys` LONGTEXT DEFAULT '' NOT NULL,
  ADD code VARCHAR(45) DEFAULT '' NOT NULL,
  DROP category_id,
  CHANGE date_start date_start DATETIME DEFAULT '0000-00-00 00:00:00' NOT NULL,
  CHANGE date_end date_end DATETIME DEFAULT '0000-00-00 00:00:00' NOT NULL;

ALTER TABLE contrexx_module_shop_payment ADD name VARCHAR(45) DEFAULT '' NOT NULL;

ALTER TABLE contrexx_module_shop_shipper ADD name VARCHAR(30) DEFAULT '' NOT NULL;

ALTER TABLE contrexx_module_shop_article_group ADD name VARCHAR(45) DEFAULT '' NOT NULL;

ALTER TABLE contrexx_module_shop_categories
  ADD name VARCHAR(45) DEFAULT '' NOT NULL,
  ADD description LONGTEXT DEFAULT '' NOT NULL;

ALTER TABLE contrexx_module_shop_discount_coupon
  CHANGE customer_id customer_id INT NOT NULL,
  CHANGE payment_id payment_id INT UNSIGNED DEFAULT NULL,
  CHANGE product_id product_id INT UNSIGNED DEFAULT NULL;

ALTER TABLE contrexx_module_shop_pricelists
  DROP categories,
  CHANGE lang_id lang_id INT DEFAULT 0 NOT NULL;

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


/** Damit die Beziehungen ohne Probleme eingefügt werden können· **/
UPDATE `contrexx_module_shop_products` SET `article_id`=NULL WHERE `article_id`= 0;
UPDATE `contrexx_module_shop_products` SET `group_id`=NULL WHERE `group_id`= 0;

UPDATE `contrexx_module_shop_discount_coupon` SET `payment_id`=NULL WHERE `payment_id`= 0;
UPDATE `contrexx_module_shop_discount_coupon` SET `product_id`=NULL WHERE `product_id`= 0;


/** Drop Primary Keys **/
ALTER TABLE contrexx_module_shop_rel_countries DROP PRIMARY KEY;


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

ALTER TABLE contrexx_module_shop_rel_countries ADD CONSTRAINT FK_C859EA8B9F2C3FAB FOREIGN KEY (zone_id) REFERENCES contrexx_module_shop_zones (id);

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


/** Indexe **/
CREATE INDEX IDX_DA286BB1B213FA4 ON contrexx_module_shop_orders (lang_id);
CREATE INDEX IDX_DA286BB138248176 ON contrexx_module_shop_orders (currency_id);
CREATE INDEX IDX_DA286BB17BE036FC ON contrexx_module_shop_orders (shipment_id);
CREATE INDEX IDX_DA286BB14C3A3BB ON contrexx_module_shop_orders (payment_id);
CREATE INDEX IDX_DA286BB19395C3F3 ON contrexx_module_shop_orders (customer_id);

CREATE INDEX IDX_C859EA8B9F2C3FAB ON contrexx_module_shop_rel_countries (zone_id);

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


/** Add Primary Keys **/
ALTER TABLE contrexx_module_shop_rel_countries ADD PRIMARY KEY (zone_id, country_id);

ALTER TABLE contrexx_module_shop_rel_shipper ADD PRIMARY KEY (zone_id, shipper_id);
