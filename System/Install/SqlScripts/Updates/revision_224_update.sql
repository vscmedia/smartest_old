ALTER TABLE  `Assets` CHANGE  `asset_stringid`  `asset_stringid` VARCHAR( 64 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
ALTER TABLE  `Sets` ADD  `set_filter_type` VARCHAR( 32 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
ALTER TABLE  `Sets` ADD  `set_filter_value` VARCHAR( 32 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
ALTER TABLE  `AssetClasses` ADD  `assetclass_filter_type` VARCHAR( 64 ) CHARACTER SET ASCII COLLATE ascii_general_ci NOT NULL DEFAULT 'SM_ASSETCLASS_FILTERTYPE_NONE' ;
ALTER TABLE  `AssetClasses` ADD  `assetclass_filter_value` VARCHAR( 64 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ;
ALTER TABLE  `ItemProperties` DROP  `itemproperty_model_id` ;
ALTER TABLE  `ItemProperties` ADD  `itemproperty_option_set_type` VARCHAR( 64 ) CHARACTER SET ASCII COLLATE ascii_general_ci NOT NULL DEFAULT 'SM_PROPERTY_FILTERTYPE_NONE' AFTER  `itemproperty_defaultvalue` ;