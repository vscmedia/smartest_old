ALTER TABLE  `ItemProperties` DROP `itemproperty_setting`;
ALTER TABLE  `ItemProperties` DROP `itemproperty_setting_value`;
ALTER TABLE  `ItemClasses` ADD `itemclass_default_sort_property_id` INT( 11 ) NOT NULL AFTER  `itemclass_default_description_property_id`;
ALTER TABLE  `ItemProperties` ADD  `itemproperty_info` TEXT NOT NULL AFTER  `itemproperty_defaultformat`;
ALTER TABLE  `ItemClasses` ADD  `itemclass_default_thumbnail_property_id` INT( 11 ) NOT NULL AFTER  `itemclass_default_sort_property_id`;
ALTER TABLE  `Assets` ADD  `asset_variant_id` INT( 9 ) UNSIGNED NOT NULL AFTER  `asset_parent_id`;
ALTER TABLE  `PagePropertyValues` ADD  `pagepropertyvalue_item_id` MEDIUMINT( 9 ) UNSIGNED NOT NULL AFTER  `pagepropertyvalue_page_id`;
ALTER TABLE  `TextFragments` CHANGE  `textfragment_type`  `textfragment_type` VARCHAR( 64 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT  'SM_TEXTFRAGMENTTYPE_CURRENT_VERSION';
UPDATE `TextFragments` SET textfragment_type='SM_TEXTFRAGMENTTYPE_CURRENT_VERSION';
ALTER TABLE  `AssetClasses` DROP  `assetclass_assettype_id` ;
ALTER TABLE  `ItemProperties` ADD  `itemproperty_share_values_autocomplete` TINYINT( 1 ) NOT NULL AFTER  `itemproperty_info`;
ALTER TABLE  `Pages` DROP  `page_url`;
ALTER TABLE  `Items` ADD  `item_alt_title_tag` VARCHAR( 255 ) NOT NULL AFTER  `item_slug`;
ALTER TABLE  `Items` ADD  `item_use_alt_title_tag` TINYINT( 1 ) NOT NULL DEFAULT  '0' AFTER  `item_alt_title_tag`;
ALTER TABLE  `PageUrls` ADD  `pageurl_redirect_type` VARCHAR( 32 ) NOT NULL AFTER  `pageurl_type`;
ALTER TABLE  `Users` ADD  `user_twitter_handle` VARCHAR( 32 ) NOT NULL AFTER  `user_website`;
ALTER TABLE  `Users` ADD  `user_bio_asset_id` INT( 9 ) UNSIGNED NOT NULL AFTER  `user_bio`;
ALTER TABLE  `Sites` ADD  `site_logo_image_asset_id` INT( 11 ) UNSIGNED NOT NULL AFTER  `site_logo_image_file`;
ALTER TABLE  `Sites` DROP  `site_automatic_urls`;
ALTER TABLE  `Sites` ADD  `site_unique_id` VARCHAR( 23 ) NOT NULL AFTER  `site_id`;
ALTER TABLE  `Users` ADD  `user_password_salt` VARCHAR( 40 ) NOT NULL AFTER  `password`;
ALTER TABLE  `Users` ADD  `user_invert_name_order` TINYINT( 1 ) NOT NULL DEFAULT '0' AFTER  `user_lastname`;
ALTER TABLE  `Users` ADD  `user_password_last_changed` INT( 11 ) UNSIGNED NOT NULL AFTER  `user_password_salt`;
ALTER TABLE  `Sets` CHANGE  `set_is_system`  `set_is_system` TINYINT( 1 ) NOT NULL DEFAULT  '0';
ALTER TABLE  `Users` ADD  `user_password_change_required` TINYINT( 1 ) NOT NULL DEFAULT  '0' AFTER  `user_password_last_changed`;
ALTER TABLE  `Sets` ADD  `set_is_hidden` TINYINT( 1 ) NOT NULL DEFAULT  '0' AFTER  `set_is_system`;
ALTER TABLE  `Assets` ADD  `asset_is_hidden` TINYINT( 1 ) NOT NULL DEFAULT  '0' AFTER  `asset_is_system`;
ALTER TABLE  `UsersTokensLookup` ADD  `utlookup_order_index` INT( 9 ) UNSIGNED NOT NULL DEFAULT  '0' AFTER  `utlookup_is_global`;
ALTER TABLE  `AssetClasses` ADD  `assetclass_parent_id` INT( 11 ) UNSIGNED NOT NULL AFTER  `assetclass_info`;
ALTER TABLE  `ItemPropertyValues` ADD  `itempropertyvalue_name` VARCHAR( 64 ) NOT NULL AFTER  `itempropertyvalue_property_id`;
ALTER TABLE  `ItemClasses` ADD  `itemclass_is_system` TINYINT( 1 ) NOT NULL DEFAULT  '0';
ALTER TABLE  `ItemClasses` ADD  `itemclass_is_hidden` TINYINT( 1 ) NOT NULL DEFAULT  '0';
ALTER TABLE  `ItemClasses` ADD  `itemclass_created_from_buildkit` VARCHAR( 64 ) NOT NULL;
ALTER TABLE  `ItemProperties` ADD  `itemproperty_last_regularized` INT( 11 ) NOT NULL AFTER  `itemproperty_storage_migrated`;
ALTER TABLE  `ItemClasses` ADD  `itemclass_uses_draft_properties` TINYINT( 1 ) NOT NULL DEFAULT '1' AFTER  `itemclass_settings`;
ALTER TABLE  `ItemClasses` ADD  `itemclass_item_webid_format` VARCHAR( 32 ) NOT NULL AFTER  `itemclass_varname`;
ALTER TABLE  `Lists` CHANGE  `list_global`  `list_global` TINYINT( 1 ) NOT NULL DEFAULT  '1';
ALTER TABLE  `PageProperties` CHANGE  `pageproperty_type`  `pageproperty_type` VARCHAR( 64 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '';
DROP TABLE  `UserTokens`;