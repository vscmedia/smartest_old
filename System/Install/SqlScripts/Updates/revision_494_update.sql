ALTER TABLE  `Pages` ADD  `page_icon_image_id` INT( 11 ) NOT NULL AFTER  `page_icon_image`;
ALTER TABLE  `Assets` ADD  `asset_info` TEXT NOT NULL AFTER  `asset_parameter_defaults`;
ALTER TABLE  `PageUrls` ADD  `pageurl_site_id` INT( 11 ) NOT NULL AFTER  `pageurl_item_id`;
ALTER TABLE  `PageUrls` ADD  `pageurl_num_hits` INT( 11 ) UNSIGNED NOT NULL;
UPDATE `Settings` SET `setting_value` = '494' WHERE `Settings`.`setting_type` ='SM_SETTINGTYPE_SYSTEM_META' AND `Settings`.`setting_name`='database_minimum_revision' LIMIT 1;
UPDATE `Settings` SET `setting_value` = '19' WHERE `Settings`.`setting_type` ='SM_SETTINGTYPE_SYSTEM_META' AND `Settings`.`setting_name`='database_version' LIMIT 1;