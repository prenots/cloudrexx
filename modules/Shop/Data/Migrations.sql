/** Create Tables **/
CREATE TABLE contrexx_module_shop_rel_category_pricelist (
  category_id INT UNSIGNED NOT NULL,
  pricelist_id INT UNSIGNED NOT NULL,
  PRIMARY KEY(category_id, pricelist_id)
) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;

CREATE TABLE contrexx_module_shop_rel_category_product (
  category_id INT UNSIGNED NOT NULL,
  product_id INT UNSIGNED NOT NULL,
  PRIMARY KEY(category_id, product_id)
) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;

CREATE TABLE contrexx_module_shop_rel_product_user_group (
  product_id INT UNSIGNED NOT NULL,
  usergroup_id INT NOT NULL,
  PRIMARY KEY(product_id, usergroup_id)
) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;

ALTER TABLE contrexx_module_shop_pricelists ADD all_categories TINYINT NOT NULL DEFAULT 0;

/** Core Text **/
ALTER TABLE contrexx_module_shop_manufacturer
  ADD `uri` VARCHAR(255) DEFAULT '' NOT NULL,
  ADD `name` VARCHAR(255) DEFAULT '' NOT NULL;

ALTER TABLE contrexx_module_shop_products
  ADD `uri` VARCHAR(255) DEFAULT '' NOT NULL,
  ADD `short` TEXT NOT NULL,
  ADD `long` TEXT NOT NULL,
  ADD `name` VARCHAR(255) DEFAULT '' NOT NULL,
  ADD `keys` TEXT NOT NULL,
  ADD `code` VARCHAR(255) DEFAULT '' NOT NULL;

ALTER TABLE contrexx_module_shop_categories
  ADD `name` VARCHAR(255) DEFAULT '' NOT NULL,
  ADD `description` TEXT NOT NULL;

ALTER TABLE contrexx_module_shop_discountgroup_count_name
  ADD `unit` VARCHAR(255) DEFAULT '' NOT NULL,
  ADD `name` VARCHAR(255) DEFAULT '' NOT NULL;

ALTER TABLE contrexx_module_shop_currencies ADD `name` VARCHAR(255) DEFAULT '' NOT NULL;

ALTER TABLE contrexx_module_shop_option ADD `name` VARCHAR(255) DEFAULT '' NOT NULL;

ALTER TABLE contrexx_module_shop_payment ADD `name` VARCHAR(255) DEFAULT '' NOT NULL;

ALTER TABLE contrexx_module_shop_shipper ADD `name` VARCHAR(255) DEFAULT '' NOT NULL;

ALTER TABLE contrexx_module_shop_vat ADD `class` VARCHAR(255) NOT NULL;

ALTER TABLE contrexx_module_shop_article_group ADD `name` VARCHAR(255) DEFAULT '' NOT NULL;

ALTER TABLE contrexx_module_shop_attribute ADD `name` VARCHAR(255) DEFAULT '' NOT NULL;

ALTER TABLE contrexx_module_shop_zones ADD `name` VARCHAR(255) DEFAULT '' NOT NULL;

ALTER TABLE contrexx_module_shop_customer_group ADD `name` VARCHAR(255) DEFAULT '' NOT NULL;

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

UPDATE contrexx_module_shop_pricelists
	SET all_categories = (
      SELECT (
          CASE
          WHEN categories = '*' THEN 1
          ELSE 0 END
      ) AS all_categories
  );

ALTER TABLE contrexx_module_shop_products DROP category_id, DROP usergroup_ids;
ALTER TABLE contrexx_module_shop_pricelists DROP categories;