INSERT INTO `Users` ( `user_id` , `username` , `password` , `user_firstname` , `user_lastname` , `user_email` , `user_website` , `user_bio` , `user_birthday` , `user_register_date` , `user_last_visit` , `user_activated` )
VALUES (
'0', 'smartest', '', 'Smartest', '', '', '', '', '0000-00-00', '0', '0', '1'
);
UPDATE `Users` SET `user_id` = '0' WHERE `username`='smartest' LIMIT 1 ;
ALTER TABLE `AssetClasses` ADD `assetclass_update_on_page_publish` TINYINT( 1 ) NOT NULL DEFAULT '1' AFTER `assetclass_info` ;
UPDATE `AssetClasses` SET `assetclass_update_on_page_publish` = '0' WHERE `assetclass_type` = 'SM_ASSETCLASS_ITEM_SPACE';
ALTER TABLE `ItemPropertyValues` ADD `itempropertyvalue_draft_info` TEXT NOT NULL ,
ADD `itempropertyvalue_live_info` TEXT NOT NULL ;