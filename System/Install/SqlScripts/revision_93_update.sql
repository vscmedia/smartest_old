INSERT INTO `UserTokens` (  `token_id` ,  `token_type` ,  `token_code` ,  `token_description` ) VALUES (NULL ,  'permission',  'create_users',  'Allow the user to create other user accounts');
ALTER TABLE `TodoItems` ADD  `todoitem_priority` INT( 1 ) NOT NULL DEFAULT  '2' AFTER  `todoitem_time_completed` ;
ALTER TABLE `TodoItems` ADD  `todoitem_size` INT( 1 ) NOT NULL DEFAULT  '2' ;
ALTER TABLE `AssetClasses` ADD `assetclass_info` TEXT NOT NULL ;
ALTER TABLE `Assets` ADD `asset_is_approved` TINYINT( 1 ) NOT NULL DEFAULT '0';
INSERT INTO `UserTokens` ( `token_id` , `token_type` , `token_code` , `token_description` ) VALUES (NULL , 'permission', 'approve_assets', 'Allow the user to approve files');
ALTER TABLE `Assets` ADD `asset_is_archived` TINYINT( 1 ) NOT NULL DEFAULT '0';
ALTER TABLE `Items` ADD `item_is_archived` TINYINT( 1 ) NOT NULL DEFAULT '0';