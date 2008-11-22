DROP TABLE SettingCategories;

ALTER TABLE `PageUrls` ADD `pageurl_type` VARCHAR( 64 ) NOT NULL DEFAULT 'SM_PAGEURL_NORMAL' AFTER `pageurl_url` ,
ADD `pageurl_destination` VARCHAR( 255 ) NOT NULL AFTER `pageurl_type` ;