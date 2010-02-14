ALTER TABLE `Settings` ADD  `setting_application_id` VARCHAR( 128 ) NOT NULL AFTER  `setting_user_id`;
ALTER TABLE `Settings` ADD  `setting_type` VARCHAR( 64 ) NOT NULL AFTER  `setting_application_id`;
ALTER TABLE `Settings` ADD  `setting_parent_id` INT( 11 ) NOT NULL DEFAULT  '0' AFTER  `setting_id`;
ALTER TABLE `Settings` CHANGE `setting_user_id` `setting_user_id` INT(11) NOT NULL DEFAULT '0';
INSERT INTO `Settings` (`setting_type`, `setting_name`, `setting_value`) VALUES ('SM_SETTINGTYPE_SYSTEM_META', 'database_minimum_revision', '334'), ('SM_SETTINGTYPE_SYSTEM_META', 'database_version', '14');
ALTER TABLE  `Sites` ADD  `site_internal_label` VARCHAR( 255 ) NOT NULL AFTER  `site_name`;
UPDATE  `Sites` SET  `site_internal_label` =  `site_name`;