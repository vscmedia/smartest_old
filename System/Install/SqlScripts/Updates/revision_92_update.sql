ALTER TABLE  `Assets` ADD  `asset_user_id` INT( 9 ) UNSIGNED NOT NULL DEFAULT  '1' AFTER  `asset_site_id` ;
ALTER TABLE  `TodoItems` ADD  `todoitem_is_complete` TINYINT( 1 ) UNSIGNED NOT NULL AFTER  `todoitem_time_completed` ;
ALTER TABLE  `Assets` ADD  `asset_is_held` TINYINT( 1 ) NOT NULL DEFAULT  '0';
ALTER TABLE  `Assets` ADD `asset_held_by` MEDIUMINT( 9 ) NOT NULL DEFAULT  '0';
ALTER TABLE  `Assets` DROP  `asset_assettype_id` ;
ALTER TABLE  `Assets` DROP  `asset_href`;
ALTER TABLE  `Assets` ADD  `asset_created` INT( 11 ) NOT NULL DEFAULT  '0' AFTER  `asset_user_id` ,
ADD  `asset_modified` INT( 11 ) NOT NULL DEFAULT  '0' AFTER  `asset_created` ;
ALTER TABLE  `TodoItems` ADD  `todoitem_ignore` TINYINT( 1 ) NOT NULL DEFAULT  '0' AFTER  `todoitem_is_complete` ;