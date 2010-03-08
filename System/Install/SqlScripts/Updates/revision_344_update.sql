DROP TABLE `DataExports`, `PairingDetails`, `Pairings`, `SchemaDefinitions`, `Schemas`, `Vocabulary`;
ALTER TABLE  `Assets` ADD  `asset_label` VARCHAR( 255 ) NOT NULL AFTER  `asset_webid`;
ALTER TABLE  `Assets` ADD  `asset_group_id` INT( 11 ) NOT NULL AFTER  `asset_fragment_id`;
ALTER TABLE  `Assets` ADD  `asset_model_id` INT( 11 ) NOT NULL AFTER  `asset_group_id`;
UPDATE  `Assets` SET asset_label = asset_stringid;
ALTER TABLE  `Pages` ADD  `page_last_built` INT( 11 ) NOT NULL AFTER  `page_changes_approved`;
UPDATE `Settings` SET `setting_value` = '340' WHERE  `Settings`.`setting_name` = 'database_minimum_revision';
UPDATE `Settings` SET `setting_value` = '15' WHERE  `Settings`.`setting_name` = 'database_version';