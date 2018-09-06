ALTER TABLE contrexx_access_user_attribute ADD is_default ENUM('0','1') DEFAULT '0' NOT NULL;
ALTER TABLE contrexx_access_user_attribute_value DROP FOREIGN KEY FK_B0DEA323A76ED395;
ALTER TABLE contrexx_access_user_attribute_value ADD CONSTRAINT FK_B0DEA323B6E62EFA FOREIGN KEY (attribute_id) REFERENCES contrexx_access_user_attribute (id);
ALTER TABLE contrexx_access_user_attribute_value ADD CONSTRAINT FK_B0DEA323A76ED395A76ED395A76ED395A76ED395 FOREIGN KEY (user_id) REFERENCES contrexx_access_users (id);
CREATE INDEX IDX_B0DEA323B6E62EFA ON contrexx_access_user_attribute_value (attribute_id);

/*Migrate core attribute to user attribute*/
ALTER TABLE contrexx_access_user_core_attribute ADD is_default ENUM('0','1') DEFAULT '1' NOT NULL;
INSERT INTO `contrexx_access_user_attribute`(`mandatory`, `sort_type`, `order_id`, `access_special`, `access_id`, `read_access_id`, `is_default`) SELECT `mandatory`, `sort_type`, `order_id`, `access_special`, `access_id`, `read_access_id`, `is_default` FROM `contrexx_access_user_core_attribute`;

/*Migrate user profile to user attribute*/
SHOW COLUMNS FROM `contrexx_access_user_profile`;
ALTER TABLE contrexx_access_user_attribute ADD tmp_name TEXT;

INSERT INTO contrexx_access_user_attribute (tmp_name)
SELECT COLUMN_NAME
FROM INFORMATION_SCHEMA.COLUMNS
WHERE table_name = 'contrexx_access_user_profile';

DELETE FROM `contrexx_access_user_attribute` WHERE `contrexx_access_user_attribute`.`tmp_name` = 'user_id'; 

INSERT INTO `contrexx_access_user_attribute_value`(`user_id`) SELECT `user_id` FROM `contrexx_access_user_profile`;

INSERT INTO `contrexx_access_user_attribute_NAME`(`attribute_id`, `name`) SELECT `id`, `tmp_name` FROM `contrexx_access_user_attribute`;

INSERT INTO `contrexx_access_user_attribute_value`(`attribute_id`, `user_id`, `value`) VALUES((SELECT `id` FROM `contrexx_access_user_attribute` WHERE `tmp_name` = 'gender'), (SELECT user_id FROM `contrexx_access_user_profile`), (SELECT gender FROM `contrexx_access_user_profile`));

INSERT INTO `contrexx_access_user_attribute_value`(`attribute_id`, `user_id`, `value`) VALUES((SELECT `id` FROM `contrexx_access_user_attribute` WHERE `tmp_name` = 'title'), (SELECT user_id FROM `contrexx_access_user_profile`), (SELECT title FROM `contrexx_access_user_profile`));

INSERT INTO `contrexx_access_user_attribute_value`(`attribute_id`, `user_id`, `value`) VALUES((SELECT `id` FROM `contrexx_access_user_attribute` WHERE `tmp_name` = 'designation'), (SELECT user_id FROM `contrexx_access_user_profile`), (SELECT designation FROM `contrexx_access_user_profile`));

INSERT INTO `contrexx_access_user_attribute_value`(`attribute_id`, `user_id`, `value`) VALUES((SELECT `id` FROM `contrexx_access_user_attribute` WHERE `tmp_name` = 'firstname'), (SELECT user_id FROM `contrexx_access_user_profile`), (SELECT firstname FROM `contrexx_access_user_profile`));

INSERT INTO `contrexx_access_user_attribute_value`(`attribute_id`, `user_id`, `value`) VALUES((SELECT `id` FROM `contrexx_access_user_attribute` WHERE `tmp_name` = 'lastname'), (SELECT user_id FROM `contrexx_access_user_profile`), (SELECT lastname FROM `contrexx_access_user_profile`));

INSERT INTO `contrexx_access_user_attribute_value`(`attribute_id`, `user_id`, `value`) VALUES((SELECT `id` FROM `contrexx_access_user_attribute` WHERE `tmp_name` = 'company'), (SELECT user_id FROM `contrexx_access_user_profile`), (SELECT company FROM `contrexx_access_user_profile`));

INSERT INTO `contrexx_access_user_attribute_value`(`attribute_id`, `user_id`, `value`) VALUES((SELECT `id` FROM `contrexx_access_user_attribute` WHERE `tmp_name` = 'address'), (SELECT user_id FROM `contrexx_access_user_profile`), (SELECT address FROM `contrexx_access_user_profile`));

INSERT INTO `contrexx_access_user_attribute_value`(`attribute_id`, `user_id`, `value`) VALUES((SELECT `id` FROM `contrexx_access_user_attribute` WHERE `tmp_name` = 'city'), (SELECT user_id FROM `contrexx_access_user_profile`), (SELECT city FROM `contrexx_access_user_profile`));

INSERT INTO `contrexx_access_user_attribute_value`(`attribute_id`, `user_id`, `value`) VALUES((SELECT `id` FROM `contrexx_access_user_attribute` WHERE `tmp_name` = 'zip'), (SELECT user_id FROM `contrexx_access_user_profile`), (SELECT zip FROM `contrexx_access_user_profile`));

INSERT INTO `contrexx_access_user_attribute_value`(`attribute_id`, `user_id`, `value`) VALUES((SELECT `id` FROM `contrexx_access_user_attribute` WHERE `tmp_name` = 'country'), (SELECT user_id FROM `contrexx_access_user_profile`), (SELECT country FROM `contrexx_access_user_profile`));

INSERT INTO `contrexx_access_user_attribute_value`(`attribute_id`, `user_id`, `value`) VALUES((SELECT `id` FROM `contrexx_access_user_attribute` WHERE `tmp_name` = 'phone_office'), (SELECT user_id FROM `contrexx_access_user_profile`), (SELECT phone_office FROM `contrexx_access_user_profile`));

INSERT INTO `contrexx_access_user_attribute_value`(`attribute_id`, `user_id`, `value`) VALUES((SELECT `id` FROM `contrexx_access_user_attribute` WHERE `tmp_name` = 'phone_private'), (SELECT user_id FROM `contrexx_access_user_profile`), (SELECT phone_private FROM `contrexx_access_user_profile`));

INSERT INTO `contrexx_access_user_attribute_value`(`attribute_id`, `user_id`, `value`) VALUES((SELECT `id` FROM `contrexx_access_user_attribute` WHERE `tmp_name` = 'phone_mobile'), (SELECT user_id FROM `contrexx_access_user_profile`), (SELECT phone_mobile FROM `contrexx_access_user_profile`));

INSERT INTO `contrexx_access_user_attribute_value`(`attribute_id`, `user_id`, `value`) VALUES((SELECT `id` FROM `contrexx_access_user_attribute` WHERE `tmp_name` = 'phone_fax'), (SELECT user_id FROM `contrexx_access_user_profile`), (SELECT phone_fax FROM `contrexx_access_user_profile`));

INSERT INTO `contrexx_access_user_attribute_value`(`attribute_id`, `user_id`, `value`) VALUES((SELECT `id` FROM `contrexx_access_user_attribute` WHERE `tmp_name` = 'birthday'), (SELECT user_id FROM `contrexx_access_user_profile`), (SELECT birthday FROM `contrexx_access_user_profile`));

INSERT INTO `contrexx_access_user_attribute_value`(`attribute_id`, `user_id`, `value`) VALUES((SELECT `id` FROM `contrexx_access_user_attribute` WHERE `tmp_name` = 'website'), (SELECT user_id FROM `contrexx_access_user_profile`), (SELECT website FROM `contrexx_access_user_profile`));

INSERT INTO `contrexx_access_user_attribute_value`(`attribute_id`, `user_id`, `value`) VALUES((SELECT `id` FROM `contrexx_access_user_attribute` WHERE `tmp_name` = 'profession'), (SELECT user_id FROM `contrexx_access_user_profile`), (SELECT profession FROM `contrexx_access_user_profile`));

INSERT INTO `contrexx_access_user_attribute_value`(`attribute_id`, `user_id`, `value`) VALUES((SELECT `id` FROM `contrexx_access_user_attribute` WHERE `tmp_name` = 'interests'), (SELECT user_id FROM `contrexx_access_user_profile`), (SELECT interests FROM `contrexx_access_user_profile`));

INSERT INTO `contrexx_access_user_attribute_value`(`attribute_id`, `user_id`, `value`) VALUES((SELECT `id` FROM `contrexx_access_user_attribute` WHERE `tmp_name` = 'signature'), (SELECT user_id FROM `contrexx_access_user_profile`), (SELECT signature FROM `contrexx_access_user_profile`));

INSERT INTO `contrexx_access_user_attribute_value`(`attribute_id`, `user_id`, `value`) VALUES((SELECT `id` FROM `contrexx_access_user_attribute` WHERE `tmp_name` = 'picture'), (SELECT user_id FROM `contrexx_access_user_profile`), (SELECT picture FROM `contrexx_access_user_profile`));

INSERT INTO `contrexx_access_user_attribute_name`(`attribute_id`, `name`) VALUES((SELECT `id` FROM `contrexx_access_user_attribute` WHERE `tmp_name` = 'gender'), 'gender');

INSERT INTO `contrexx_access_user_attribute_name`(`attribute_id`, `name`) VALUES((SELECT `id` FROM `contrexx_access_user_attribute` WHERE `tmp_name` = 'title'), 'title');

INSERT INTO `contrexx_access_user_attribute_name`(`attribute_id`, `name`) VALUES((SELECT `id` FROM `contrexx_access_user_attribute` WHERE `tmp_name` = 'designation'), 'designation');

INSERT INTO `contrexx_access_user_attribute_name`(`attribute_id`, `name`) VALUES((SELECT `id` FROM `contrexx_access_user_attribute` WHERE `tmp_name` = 'firstname'), 'firstname');

INSERT INTO `contrexx_access_user_attribute_name`(`attribute_id`, `name`) VALUES((SELECT `id` FROM `contrexx_access_user_attribute` WHERE `tmp_name` = 'lastname'), 'lastname');

INSERT INTO `contrexx_access_user_attribute_name`(`attribute_id`, `name`) VALUES((SELECT `id` FROM `contrexx_access_user_attribute` WHERE `tmp_name` = 'company'), 'company');

INSERT INTO `contrexx_access_user_attribute_name`(`attribute_id`, `name`) VALUES((SELECT `id` FROM `contrexx_access_user_attribute` WHERE `tmp_name` = 'address'), 'address');

INSERT INTO `contrexx_access_user_attribute_name`(`attribute_id`, `name`) VALUES((SELECT `id` FROM `contrexx_access_user_attribute` WHERE `tmp_name` = 'city'), 'city');

INSERT INTO `contrexx_access_user_attribute_name`(`attribute_id`, `name`) VALUES((SELECT `id` FROM `contrexx_access_user_attribute` WHERE `tmp_name` = 'country'), 'zip');

INSERT INTO `contrexx_access_user_attribute_name`(`attribute_id`, `name`) VALUES((SELECT `id` FROM `contrexx_access_user_attribute` WHERE `tmp_name` = 'phone_office'), 'phone_office');

INSERT INTO `contrexx_access_user_attribute_name`(`attribute_id`, `name`) VALUES((SELECT `id` FROM `contrexx_access_user_attribute` WHERE `tmp_name` = 'phone_private'), 'phone_private');

INSERT INTO `contrexx_access_user_attribute_name`(`attribute_id`, `name`) VALUES((SELECT `id` FROM `contrexx_access_user_attribute` WHERE `tmp_name` = 'phone_mobile'), 'phone_mobile');

INSERT INTO `contrexx_access_user_attribute_name`(`attribute_id`, `name`) VALUES((SELECT `id` FROM `contrexx_access_user_attribute` WHERE `tmp_name` = 'phone_fax'), 'phone_fax');

INSERT INTO `contrexx_access_user_attribute_name`(`attribute_id`, `name`) VALUES((SELECT `id` FROM `contrexx_access_user_attribute` WHERE `tmp_name` = 'birthday'), 'birthday');

INSERT INTO `contrexx_access_user_attribute_name`(`attribute_id`, `name`) VALUES((SELECT `id` FROM `contrexx_access_user_attribute` WHERE `tmp_name` = 'website'), 'website');

INSERT INTO `contrexx_access_user_attribute_name`(`attribute_id`, `name`) VALUES((SELECT `id` FROM `contrexx_access_user_attribute` WHERE `tmp_name` = 'profession'), 'profession');

INSERT INTO `contrexx_access_user_attribute_name`(`attribute_id`, `name`) VALUES((SELECT `id` FROM `contrexx_access_user_attribute` WHERE `tmp_name` = 'interests'), 'interests');

INSERT INTO `contrexx_access_user_attribute_name`(`attribute_id`, `name`) VALUES((SELECT `id` FROM `contrexx_access_user_attribute` WHERE `tmp_name` = 'signature'), 'signature');

INSERT INTO `contrexx_access_user_attribute_name`(`attribute_id`, `name`) VALUES((SELECT `id` FROM `contrexx_access_user_attribute` WHERE `tmp_name` = 'picture'), 'picture');

UPDATE `contrexx_access_user_attribute_value` SET `value` = 'Sehr geehrte Frau' WHERE `value` = 1;

UPDATE `contrexx_access_user_attribute_value` SET `value` = 'Sehr geehrter Herr' WHERE `value` = 2;

UPDATE `contrexx_access_user_attribute_value` SET `value` = 'Dear Ms' WHERE `value` = 3;

UPDATE `contrexx_access_user_attribute_value` SET `value` = 'Dear Mr' WHERE `value` = 4;

UPDATE `contrexx_access_user_attribute_value` SET `value` = 'Madame' WHERE `value` = 5;

UPDATE `contrexx_access_user_attribute_value` SET `value` = 'Monsieur' WHERE `value` = 6;


/*Drop tables*/
ALTER TABLE `contrexx_access_user_attribute` DROP `tmp_name`;

DROP TABLE contrexx_access_user_profile;
DROP TABLE contrexx_access_user_title;
DROP TABLE contrexx_access_user_core_attribute;

/*View for user profile*/
CREATE VIEW `contrexx_access_user_profile` AS SELECT `value`.`value` 
FROM `contrexx_access_user_attribute_name` AS `name` 
JOIN `contrexx_access_user_attribute_value` AS `value` 
ON `name`.`attribute_id`=`value`.`attribute_id` WHERE `name`.`name` IN ('gender', 'title', 'designation', 'firstname', 'lastname', 'company', 'address', 'city', 'zip', 'country', 'phone_office', 'phone_private', 'phone_mobile', 'phone_fax', 'birthday', 'website', 'profession', 'interests', 'signature', 'picture');

/*View for user title*/
CREATE VIEW `contrexx_access_user_title` AS SELECT `value`.`value` 
FROM `contrexx_access_user_attribute_name` AS `name` 
JOIN `contrexx_access_user_attribute_value` AS `value` 
ON `name`.`attribute_id`=`value`.`attribute_id` WHERE `name`.`name` = 'title';

/*View for user core attribute*/
CREATE VIEW `contrexx_access_user_core_attribute` AS SELECT `mandatory`, `sort_type`, `order_id`, `access_special`, `access_id`, `read_access_id`
FROM `contrexx_access_user_attribute`
WHERE `is_default` = '1';
