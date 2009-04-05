ALTER TABLE `ManyToManyLookups` ADD `mtmlookup_order_index` MEDIUMINT( 11 ) AFTER `mtmlookup_context_data` ;
ALTER TABLE `Sets` CHANGE `set_type` `set_type` VARCHAR( 32 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'DYNAMIC';
ALTER TABLE `Sets` ADD `set_lookup_source` VARCHAR( 32 ) NOT NULL ;
ALTER TABLE `ItemPropertyValues` ADD `itempropertyvalue_language` VARCHAR( 8 ) DEFAULT 'eng' NOT NULL ;
ALTER TABLE `Items` ADD `item_language` VARCHAR( 8 ) NOT NULL ;
ALTER TABLE `Items` ADD `item_num_hits` BIGINT( 20 ) NOT NULL ;
ALTER TABLE `Items` CHANGE `item_name` `item_name` VARCHAR( 128 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ;
ALTER TABLE `Items` ADD `item_num_ratings` INT( 11 ) NOT NULL ;
ALTER TABLE `Items` ADD `item_average_rating` FLOAT NOT NULL ;
ALTER TABLE `ItemClasses` ADD `itemclass_type` VARCHAR( 32 ) DEFAULT 'SM_ITEMCLASS_MODEL' NOT NULL AFTER `itemclass_id` ;
ALTER TABLE `ItemClasses` ADD `itemclass_rating_max_score` SMALLINT( 3 ) DEFAULT '5' NOT NULL ;
ALTER TABLE `Assets` ADD `asset_language` VARCHAR( 8 ) DEFAULT 'eng' NOT NULL AFTER `asset_type` ;
ALTER TABLE `DropDowns` ADD `dropdown_language` VARCHAR( 8 ) DEFAULT 'eng' NOT NULL ;
ALTER TABLE `AssetIdentifiers` ADD `assetidentifier_language` VARCHAR( 8 ) DEFAULT 'eng' NOT NULL ;

CREATE TABLE `Comments` (
`comment_id` MEDIUMINT( 11 ) NOT NULL AUTO_INCREMENT ,
`comment_type` VARCHAR( 32 ) NOT NULL ,
`comment_status` VARCHAR( 32 ) NOT NULL ,
`comment_author_user_id` MEDIUMINT( 11 ) NOT NULL ,
`comment_author_name` VARCHAR( 128 ) NOT NULL ,
`comment_author_website` VARCHAR( 128 ) NOT NULL ,
`comment_content` TEXT NOT NULL ,
`comment_language` VARCHAR( 8 ) DEFAULT 'eng' NOT NULL ,
`comment_posted_at` INT( 10 ) NOT NULL ,
PRIMARY KEY ( `comment_id` )
) TYPE = MYISAM ;