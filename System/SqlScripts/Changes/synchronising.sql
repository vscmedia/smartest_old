ALTER TABLE AssetClasses ADD COLUMN `assetclass_shared` tinyint(1) NOT NULL default '1' AFTER assetclass_site_id;

DROP TABLE AssetTypeCategories, AssetTypes;

ALTER TABLE DropDowns ADD COLUMN `dropdown_name` varchar(64) NOT NULL default '' AFTER dropdown_id;
ALTER TABLE DropDownValues ADD COLUMN `dropdownvalue_value` varchar(255) NOT NULL default '' AFTER dropdownvalue_label;

ALTER TABLE ItemClasses DROP COLUMN itemclass_default_metapage_id;
ALTER TABLE ItemClasses ADD COLUMN `itemclass_default_description_property_id` int(11) unsigned NOT NULL default '0' AFTER itemclass_varname;

ALTER TABLE Items CHANGE COLUMN item_webid `item_webid` varchar(32) character set utf8 NOT NULL default '',
 CHANGE COLUMN item_name `item_name` varchar(64) character set utf8 NOT NULL default '',
 CHANGE COLUMN item_slug `item_slug` varchar(127) character set utf8 NOT NULL default '',
 CHANGE COLUMN item_public `item_public` enum('FALSE','TRUE') character set utf8 NOT NULL default 'FALSE';

ALTER TABLE Items ADD COLUMN `item_site_id` int(11) unsigned NOT NULL default '0',
  ADD COLUMN `item_shared` tinyint(1) unsigned NOT NULL default '0' AFTER item_itemclass_id;

ALTER TABLE Items ADD COLUMN `item_metapage_id` int(11) unsigned NOT NULL default '0',
  ADD COLUMN `item_search_field` text character set utf8 NOT NULL AFTER item_public;

ALTER TABLE PageLayoutPresetDefinitions ADD COLUMN `plpd_element_type` varchar(32) NOT NULL default '' AFTER plpd_preset_id;

ALTER TABLE PageLayoutPresetDefinitions CHANGE COLUMN plpd_assetclass_id `plpd_element_id` mediumint(9) NOT NULL default '0',
  CHANGE COLUMN plpd_asset_id `plpd_element_value` varchar(255) NOT NULL default '0';

ALTER TABLE PageLayoutPresets ADD COLUMN  `plp_site_id` int(11) unsigned NOT NULL default '0',
  ADD COLUMN `plp_shared` tinyint(1) NOT NULL default '0' AFTER plp_id;

ALTER TABLE PageProperties ADD COLUMN `pageproperty_foreign_key_filter` varchar(64) NOT NULL default '' AFTER pageproperty_type;

ALTER TABLE Pages ADD COLUMN `page_icon_image` varchar(64) character set utf8 NOT NULL default '' AFTER page_title;
ALTER TABLE Pages DROP COLUMN page_destination;
ALTER TABLE Pages ADD COLUMN  `page_order_index` int(9) unsigned NOT NULL default '1',
  ADD COLUMN `page_search_field` text character set utf8 NOT NULL AFTER page_url;

ALTER TABLE Pages CHANGE COLUMN page_type `page_type` enum('NORMAL','SUBPAGE','ITEMCLASS','TAG') character set utf8 NOT NULL default 'NORMAL';
 
ALTER TABLE Pages ADD COLUMN `page_meta_description` varchar(255) character set utf8 NOT NULL default '' AFTER page_keywords;

ALTER TABLE Sets ADD COLUMN `set_site_id` int(11) unsigned NOT NULL default '0',
 ADD COLUMN  `set_shared` int(11) unsigned NOT NULL default '0',
 ADD COLUMN `set_data_source_site_id` varchar(16) NOT NULL default 'ALL'  AFTER set_itemclass_id;

ALTER TABLE Sites ADD COLUMN `site_tag_page_id` int(11) unsigned NOT NULL default '0',
  ADD COLUMN `site_search_page_id` int(11) unsigned NOT NULL default '0' AFTER site_top_page_id;
 
  

