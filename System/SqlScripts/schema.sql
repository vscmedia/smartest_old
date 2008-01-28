-- phpMyAdmin SQL Dump
-- version 2.8.2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jan 26, 2008 at 05:45 AM
-- Server version: 4.1.20
-- PHP Version: 5.2.5

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Database: `smartest_live`
--

-- --------------------------------------------------------

--
-- Table structure for table `AssetClasses`
--

CREATE TABLE IF NOT EXISTS `AssetClasses` (
  `assetclass_id` int(9) NOT NULL auto_increment,
  `assetclass_name` varchar(32) NOT NULL default '',
  `assetclass_label` varchar(32) NOT NULL default 'Untitled Asset Class',
  `assetclass_assettype_id` int(9) NOT NULL default '1',
  `assetclass_site_id` int(11) NOT NULL default '1',
  `assetclass_shared` tinyint(1) NOT NULL default '1',
  `assetclass_type` varchar(64) NOT NULL default '',
  PRIMARY KEY  (`assetclass_id`),
  UNIQUE KEY `assetclass_name` (`assetclass_name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `AssetIdentifiers`
--

CREATE TABLE IF NOT EXISTS `AssetIdentifiers` (
  `assetidentifier_id` int(9) unsigned NOT NULL auto_increment,
  `assetidentifier_draft_asset_id` int(9) unsigned NOT NULL default '0',
  `assetidentifier_live_asset_id` mediumint(9) unsigned NOT NULL default '0',
  `assetidentifier_assetclass_id` int(9) unsigned NOT NULL default '0',
  `assetidentifier_instance_name` varchar(64) collate utf8_general_ci NOT NULL default 'default',
  `assetidentifier_page_id` mediumint(9) unsigned NOT NULL default '0',
  `assetidentifier_item_id` mediumint(9) unsigned default NULL,
  `assetidentifier_draft_render_data` text collate utf8_general_ci NOT NULL,
  `assetidentifier_live_render_data` text collate utf8_general_ci NOT NULL,
  PRIMARY KEY  (`assetidentifier_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `Assets`
--

CREATE TABLE IF NOT EXISTS `Assets` (
  `asset_id` int(9) unsigned NOT NULL auto_increment,
  `asset_webid` varchar(32) character set utf8 NOT NULL default '',
  `asset_stringid` varchar(32) character set utf8 NOT NULL default '',
  `asset_url` varchar(255) character set utf8 NOT NULL default '',
  `asset_href` varchar(100) character set utf8 NOT NULL default '',
  `asset_assettype_id` mediumint(9) NOT NULL default '0',
  `asset_type` varchar(64) character set utf8 NOT NULL default '',
  `asset_site_id` mediumint(9) NOT NULL default '1',
  `asset_shared` tinyint(1) NOT NULL default '0',
  `asset_deleted` tinyint(1) NOT NULL default '0',
  `asset_fragment_id` mediumint(9) unsigned default '0',
  `asset_parameter_defaults` varchar(255) character set utf8 NOT NULL default '',
  PRIMARY KEY  (`asset_id`),
  UNIQUE KEY `Asset_webid` (`asset_webid`,`asset_stringid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `DataExports`
--

CREATE TABLE IF NOT EXISTS `DataExports` (
  `dataexport_id` int(11) NOT NULL auto_increment,
  `dataexport_name` varchar(100) NOT NULL default '0',
  `dataexport_set_id` int(11) NOT NULL default '0',
  `dataexport_pairing_id` int(11) NOT NULL default '0',
  `dataexport_varname` varchar(64) NOT NULL default '',
  PRIMARY KEY  (`dataexport_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `DropDowns`
--

CREATE TABLE IF NOT EXISTS `DropDowns` (
  `dropdown_id` mediumint(9) NOT NULL auto_increment,
  `dropdown_name` varchar(64) NOT NULL default '',
  `dropdown_label` varchar(64) NOT NULL default '',
  PRIMARY KEY  (`dropdown_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `DropDownValues`
--

CREATE TABLE IF NOT EXISTS `DropDownValues` (
  `dropdownvalue_id` mediumint(9) NOT NULL auto_increment,
  `dropdownvalue_dropdown_id` mediumint(9) NOT NULL default '0',
  `dropdownvalue_order` mediumint(9) NOT NULL default '0',
  `dropdownvalue_label` varchar(64) NOT NULL default '',
  `dropdownvalue_value` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`dropdownvalue_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `ItemClasses`
--

CREATE TABLE IF NOT EXISTS `ItemClasses` (
  `itemclass_id` int(9) unsigned NOT NULL auto_increment,
  `itemclass_webid` varchar(16) character set utf8 NOT NULL default '',
  `itemclass_parent_id` int(9) NOT NULL default '0',
  `itemclass_name` varchar(40) character set utf8 NOT NULL default '',
  `itemclass_plural_name` varchar(64) character set utf8 NOT NULL default '',
  `itemclass_site_id` int(11) NOT NULL default '0',
  `itemclass_varname` varchar(64) character set utf8 NOT NULL default '',
  `itemclass_default_description_property_id` int(11) unsigned NOT NULL default '0',
  `itemclass_userid` int(11) NOT NULL default '0',
  PRIMARY KEY  (`itemclass_id`),
  UNIQUE KEY `itemclass_name` (`itemclass_name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `ItemProperties`
--

CREATE TABLE IF NOT EXISTS `ItemProperties` (
  `itemproperty_id` int(9) unsigned NOT NULL auto_increment,
  `itemproperty_webid` varchar(32) collate utf8_general_ci NOT NULL default '',
  `itemproperty_name` varchar(32) collate utf8_general_ci NOT NULL default '',
  `itemproperty_varname` varchar(32) collate utf8_general_ci NOT NULL default '',
  `itemproperty_required` enum('FALSE','TRUE') collate utf8_general_ci NOT NULL default 'FALSE',
  `itemproperty_datatype` varchar(32) collate utf8_general_ci NOT NULL default 'SM_DATATYPE_SL_TEXT',
  `itemproperty_foreign_key_filter` varchar(128) collate utf8_general_ci default NULL,
  `itemproperty_itemclass_id` int(9) NOT NULL default '0',
  `itemproperty_setting` char(1) collate utf8_general_ci NOT NULL default '',
  `itemproperty_setting_value` varchar(100) collate utf8_general_ci NOT NULL default '',
  `itemproperty_defaultvalue` varchar(100) collate utf8_general_ci NOT NULL default '',
  `itemproperty_option_set_id` int(11) NOT NULL default '0',
  `itemproperty_model_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`itemproperty_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `ItemPropertyValues`
--

CREATE TABLE IF NOT EXISTS `ItemPropertyValues` (
  `itempropertyvalue_id` int(9) NOT NULL auto_increment,
  `itempropertyvalue_item_id` int(9) NOT NULL default '0',
  `itempropertyvalue_property_id` int(9) NOT NULL default '0',
  `itempropertyvalue_draft_content` text character set utf8 NOT NULL,
  `itempropertyvalue_content` text character set utf8 NOT NULL,
  `itempropertyvalue_binary` longblob NOT NULL,
  PRIMARY KEY  (`itempropertyvalue_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `Items`
--

CREATE TABLE IF NOT EXISTS `Items` (
  `item_id` int(11) NOT NULL auto_increment,
  `item_webid` varchar(32) character set utf8 NOT NULL default '',
  `item_itemclass_id` int(9) unsigned NOT NULL default '0',
  `item_site_id` int(11) unsigned NOT NULL default '0',
  `item_shared` tinyint(1) unsigned NOT NULL default '0',
  `item_name` varchar(64) character set utf8 NOT NULL default '',
  `item_slug` varchar(127) character set utf8 NOT NULL default '',
  `item_public` enum('FALSE','TRUE') character set utf8 NOT NULL default 'FALSE',
  `item_metapage_id` int(11) unsigned NOT NULL default '0',
  `item_search_field` text character set utf8 NOT NULL,
  `item_is_held` tinyint(1) NOT NULL default '0',
  `item_held_by` int(10) NOT NULL default '0',
  `item_deleted` tinyint(1) NOT NULL default '0',
  `item_created` int(11) NOT NULL default '0',
  `item_modified` int(11) NOT NULL default '0',
  `item_last_published` int(11) NOT NULL default '0',
  `item_changes_approved` tinyint(1) NOT NULL default '0',
  `item_createdby_userid` int(11) NOT NULL default '0',
  PRIMARY KEY  (`item_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `Lists`
--

CREATE TABLE IF NOT EXISTS `Lists` (
  `list_id` int(9) NOT NULL auto_increment,
  `list_name` varchar(32) NOT NULL default '',
  `list_label` varchar(40) NOT NULL default '3',
  `list_draft_set_id` int(9) unsigned NOT NULL default '0',
  `list_live_set_id` int(9) unsigned NOT NULL default '0',
  `list_draft_template_file` varchar(32) NOT NULL default 'default_list.tpl',
  `list_live_template_file` varchar(32) NOT NULL default 'default_list.tpl',
  `list_draft_header_template` varchar(32) NOT NULL default '',
  `list_draft_footer_template` varchar(32) NOT NULL default '',
  `list_live_header_template` varchar(32) NOT NULL default '',
  `list_live_footer_template` varchar(32) NOT NULL default '',
  `list_minimum_length` smallint(1) default NULL,
  `list_page_id` mediumint(9) NOT NULL default '0',
  `list_global` enum('1','0') NOT NULL default '1',
  PRIMARY KEY  (`list_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `PageLayoutPresetDefinitions`
--

CREATE TABLE IF NOT EXISTS `PageLayoutPresetDefinitions` (
  `plpd_id` mediumint(9) NOT NULL auto_increment,
  `plpd_preset_id` mediumint(9) NOT NULL default '0',
  `plpd_element_type` varchar(32) NOT NULL default '',
  `plpd_element_id` mediumint(9) NOT NULL default '0',
  `plpd_element_value` varchar(255) NOT NULL default '0',
  PRIMARY KEY  (`plpd_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `PageLayoutPresets`
--

CREATE TABLE IF NOT EXISTS `PageLayoutPresets` (
  `plp_id` mediumint(9) NOT NULL auto_increment,
  `plp_site_id` int(11) unsigned NOT NULL default '0',
  `plp_shared` tinyint(1) NOT NULL default '0',
  `plp_label` varchar(64) NOT NULL default '',
  `plp_master_template_name` varchar(64) NOT NULL default '',
  `plp_created_by_user_id` mediumint(9) NOT NULL default '0',
  `plp_orig_from_page_id` mediumint(9) NOT NULL default '0',
  PRIMARY KEY  (`plp_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `PageProperties`
--

CREATE TABLE IF NOT EXISTS `PageProperties` (
  `pageproperty_id` mediumint(9) NOT NULL auto_increment,
  `pageproperty_site_id` mediumint(9) NOT NULL default '0',
  `pageproperty_name` varchar(64) NOT NULL default '',
  `pageproperty_label` varchar(64) NOT NULL default '',
  `pageproperty_type` varchar(32) NOT NULL default '',
  `pageproperty_foreign_key_filter` varchar(64) NOT NULL default '',
  PRIMARY KEY  (`pageproperty_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `PagePropertyValues`
--

CREATE TABLE IF NOT EXISTS `PagePropertyValues` (
  `pagepropertyvalue_id` mediumint(9) unsigned NOT NULL auto_increment,
  `pagepropertyvalue_page_id` mediumint(9) unsigned NOT NULL default '0',
  `pagepropertyvalue_pageproperty_id` mediumint(9) unsigned NOT NULL default '0',
  `pagepropertyvalue_live_value` varchar(255) NOT NULL default '',
  `pagepropertyvalue_draft_value` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`pagepropertyvalue_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `PageUrls`
--

CREATE TABLE IF NOT EXISTS `PageUrls` (
  `pageurl_id` mediumint(9) NOT NULL auto_increment,
  `pageurl_page_id` mediumint(9) NOT NULL default '0',
  `pageurl_url` varchar(255) NOT NULL default '',
  `pageurl_is_default` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`pageurl_id`),
  KEY `pageurl_page_id` (`pageurl_page_id`),
  KEY `pageurl_url` (`pageurl_url`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `Pages`
--

CREATE TABLE IF NOT EXISTS `Pages` (
  `page_id` int(11) NOT NULL auto_increment,
  `page_webid` varchar(32) character set utf8 NOT NULL default '',
  `page_site_id` mediumint(9) unsigned NOT NULL default '0',
  `page_dataset_id` mediumint(9) default NULL,
  `page_name` varchar(64) character set utf8 NOT NULL default '',
  `page_title` varchar(48) character set utf8 NOT NULL default 'Untitled Page',
  `page_icon_image` varchar(64) character set utf8 NOT NULL default '',
  `page_parent` int(11) NOT NULL default '0',
  `page_url` varchar(64) character set utf8 NOT NULL default '',
  `page_order_index` int(9) unsigned NOT NULL default '1',
  `page_search_field` text character set utf8 NOT NULL,
  `page_is_held` tinyint(1) NOT NULL default '0',
  `page_held_by` int(9) default NULL,
  `page_live_template` varchar(60) character set utf8 NOT NULL default '',
  `page_draft_template` varchar(60) collate utf8_general_ci NOT NULL default '',
  `page_type` enum('NORMAL','SUBPAGE','ITEMCLASS','TAG') character set utf8 NOT NULL default 'NORMAL',
  `page_deleted` enum('TRUE','FALSE') collate utf8_general_ci NOT NULL default 'FALSE',
  `page_cache_as_html` varchar(5) collate utf8_general_ci NOT NULL default 'TRUE',
  `page_cache_interval` enum('PERMANENT','MONTHLY','DAILY','HOURLY','MINUTE','SECOND') collate utf8_general_ci NOT NULL default 'PERMANENT',
  `page_created` int(11) NOT NULL default '0',
  `page_modified` int(11) NOT NULL default '0',
  `page_changes_approved` tinyint(1) NOT NULL default '0',
  `page_last_published` int(11) NOT NULL default '0',
  `page_is_published` enum('FALSE','TRUE') collate utf8_general_ci NOT NULL default 'FALSE',
  `page_keywords` text character set utf8 NOT NULL,
  `page_meta_description` varchar(255) character set utf8 NOT NULL default '',
  `page_description` varchar(255) character set utf8 NOT NULL default '',
  `page_createdby_userid` int(11) NOT NULL default '0',
  PRIMARY KEY  (`page_id`),
  KEY `page_webid` (`page_webid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `PairingDetails`
--

CREATE TABLE IF NOT EXISTS `PairingDetails` (
  `id` int(11) NOT NULL auto_increment,
  `paring_id` int(11) NOT NULL default '0',
  `property_id` int(11) NOT NULL default '0',
  `vocabulary_id` int(11) NOT NULL default '0',
  `setting_id` int(11) NOT NULL default '0',
  `setting_value` varchar(100) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `Pairings`
--

CREATE TABLE IF NOT EXISTS `Pairings` (
  `paring_id` int(11) NOT NULL auto_increment,
  `paring_schema_id` int(11) NOT NULL default '0',
  `paring_model_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`paring_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `Roles`
--

CREATE TABLE IF NOT EXISTS `Roles` (
  `role_id` int(10) unsigned NOT NULL auto_increment,
  `role_label` varchar(64) NOT NULL default '',
  PRIMARY KEY  (`role_id`),
  UNIQUE KEY `usergroup_name` (`role_label`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `RolesTokensLookup`
--

CREATE TABLE IF NOT EXISTS `RolesTokensLookup` (
  `rtlookup_id` int(9) NOT NULL auto_increment,
  `rtlookup_token_id` int(9) NOT NULL default '0',
  `rtlookup_role_id` int(9) NOT NULL default '0',
  PRIMARY KEY  (`rtlookup_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `SchemaDefinitions`
--

CREATE TABLE IF NOT EXISTS `SchemaDefinitions` (
  `schemadefinition_id` int(10) unsigned NOT NULL auto_increment,
  `schemadefinition_schema_id` int(11) NOT NULL default '0',
  `schemadefinition_vocabulary_id` int(11) NOT NULL default '0',
  `schemadefinition_parent_id` int(11) NOT NULL default '0',
  `schemadefinition_level` int(11) NOT NULL default '0',
  `schemadefinition_setting` tinyint(1) NOT NULL default '0',
  `schemadefinition_root` tinyint(4) NOT NULL default '0',
  `schemadefinition_required` varchar(10) collate utf8_general_ci NOT NULL default '0',
  PRIMARY KEY  (`schemadefinition_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `Schemas`
--

CREATE TABLE IF NOT EXISTS `Schemas` (
  `schema_id` int(10) unsigned NOT NULL auto_increment,
  `schema_name` varchar(64) collate utf8_general_ci NOT NULL default '',
  `schema_namespace` varchar(64) collate utf8_general_ci NOT NULL default '',
  `schema_label` varchar(64) collate utf8_general_ci NOT NULL default '',
  `schema_description` text collate utf8_general_ci NOT NULL,
  `schema_parent_id` int(11) NOT NULL default '0',
  `schema_encoding` varchar(64) collate utf8_general_ci NOT NULL default '',
  `schema_root_tag` varchar(64) collate utf8_general_ci NOT NULL default '',
  `schema_default_tag` varchar(64) collate utf8_general_ci NOT NULL default '',
  `schema_lang` varchar(8) collate utf8_general_ci NOT NULL default 'en',
  `schema_varname` varchar(64) collate utf8_general_ci NOT NULL default '',
  PRIMARY KEY  (`schema_id`),
  UNIQUE KEY `schema_name` (`schema_name`),
  KEY `datatypeattribute_name` (`schema_name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci PACK_KEYS=0 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `SetRules`
--

CREATE TABLE IF NOT EXISTS `SetRules` (
  `setrule_id` int(11) NOT NULL auto_increment,
  `setrule_set_id` int(11) NOT NULL default '0',
  `setrule_label` varchar(64) NOT NULL default '',
  `setrule_itemproperty_id` varchar(32) NOT NULL default '0',
  `setrule_operator` varchar(64) NOT NULL default '',
  `setrule_value` text NOT NULL,
  PRIMARY KEY  (`setrule_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `Sets`
--

CREATE TABLE IF NOT EXISTS `Sets` (
  `set_id` int(11) NOT NULL auto_increment,
  `set_name` varchar(32) NOT NULL default '',
  `set_label` varchar(64) NOT NULL default '',
  `set_itemclass_id` int(11) NOT NULL default '0',
  `set_site_id` int(11) unsigned NOT NULL default '0',
  `set_shared` int(11) unsigned NOT NULL default '0',
  `set_data_source_site_id` varchar(16) NOT NULL default 'ALL',
  `set_type` enum('DYNAMIC','STATIC') NOT NULL default 'DYNAMIC',
  `set_sort_field` varchar(32) NOT NULL default '',
  `set_sort_direction` varchar(4) NOT NULL default 'ASC',
  `set_varname` varchar(64) NOT NULL default '',
  PRIMARY KEY  (`set_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `SetsItemsLookup`
--

CREATE TABLE IF NOT EXISTS `SetsItemsLookup` (
  `setlookup_id` mediumint(9) NOT NULL auto_increment,
  `setlookup_set_id` mediumint(9) NOT NULL default '0',
  `setlookup_item_id` mediumint(9) NOT NULL default '0',
  `setlookup_order` mediumint(9) NOT NULL default '0',
  PRIMARY KEY  (`setlookup_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `SettingCategories`
--

CREATE TABLE IF NOT EXISTS `SettingCategories` (
  `settingcategory_id` int(9) NOT NULL auto_increment,
  `settingcategory_label` varchar(40) NOT NULL default '',
  PRIMARY KEY  (`settingcategory_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `Settings`
--

CREATE TABLE IF NOT EXISTS `Settings` (
  `setting_id` int(9) unsigned NOT NULL auto_increment,
  `site_id` int(11) NOT NULL default '1',
  `setting_name` varchar(50) NOT NULL default '',
  `setting_value` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`setting_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `Sites`
--

CREATE TABLE IF NOT EXISTS `Sites` (
  `site_id` int(9) unsigned NOT NULL auto_increment,
  `site_name` varchar(64) NOT NULL default '',
  `site_is_enabled` tinyint(1) NOT NULL default '0',
  `site_title_format` varchar(255) NOT NULL default '$site | $page',
  `site_domain` varchar(128) NOT NULL default '',
  `site_root` varchar(100) NOT NULL default '',
  `site_automatic_urls` enum('ON','OFF') character set utf8 collate utf8_general_ci NOT NULL default 'ON',
  `site_error_title` varchar(128) NOT NULL default '',
  `site_error_tpl` varchar(64) NOT NULL default '',
  `site_admin_email` varchar(64) NOT NULL default '',
  `site_top_page_id` mediumint(9) NOT NULL default '0',
  `site_tag_page_id` int(11) unsigned NOT NULL default '0',
  `site_search_page_id` int(11) unsigned NOT NULL default '0',
  `site_error_page_id` int(11) NOT NULL default '0',
  `site_logo_image_file` varchar(64) NOT NULL default '',
  PRIMARY KEY  (`site_id`),
  UNIQUE KEY `site_name` (`site_name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `Tags`
--

CREATE TABLE IF NOT EXISTS `Tags` (
  `tag_id` int(9) unsigned NOT NULL auto_increment,
  `tag_name` varchar(64) NOT NULL default '',
  `tag_label` varchar(64) NOT NULL default '',
  PRIMARY KEY  (`tag_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `TagsObjectsLookup`
--

CREATE TABLE IF NOT EXISTS `TagsObjectsLookup` (
  `taglookup_id` int(9) unsigned NOT NULL auto_increment,
  `taglookup_tag_id` int(9) unsigned NOT NULL default '0',
  `taglookup_object_id` int(9) unsigned NOT NULL default '0',
  `taglookup_type` varchar(32) NOT NULL default 'SM_PAGE_TAG_LINK',
  `taglookup_metapage_id` int(9) unsigned NOT NULL default '0',
  PRIMARY KEY  (`taglookup_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `TextFragments`
--

CREATE TABLE IF NOT EXISTS `TextFragments` (
  `textfragment_id` int(10) unsigned NOT NULL auto_increment,
  `textfragment_asset_id` mediumint(9) NOT NULL default '0',
  `textfragment_content` text NOT NULL,
  `textfragment_created` int(10) NOT NULL default '0',
  `textfragment_modified` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`textfragment_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `TodoItems`
--

CREATE TABLE IF NOT EXISTS `TodoItems` (
  `todoitem_id` mediumint(9) NOT NULL auto_increment,
  `todoitem_assigning_user_id` int(10) NOT NULL default '0',
  `todoitem_receiving_user_id` int(10) NOT NULL default '0',
  `todoitem_type` varchar(32) NOT NULL default 'SM_TODOITEMTYPE_PERSONAL',
  `todoitem_token` varchar(64) default NULL,
  `todoitem_foreign_object_type` varchar(32) default NULL,
  `todoitem_foreign_object_id` int(10) default NULL,
  `todoitem_time_assigned` int(11) NOT NULL default '0',
  `todoitem_time_completed` int(11) NOT NULL default '0',
  `todoitem_description` text NOT NULL,
  PRIMARY KEY  (`todoitem_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `Users`
--

CREATE TABLE IF NOT EXISTS `Users` (
  `user_id` int(11) NOT NULL auto_increment,
  `username` varchar(64) NOT NULL default '',
  `password` varchar(32) NOT NULL default '',
  `user_firstname` varchar(64) NOT NULL default '',
  `user_lastname` varchar(64) NOT NULL default '',
  `user_email` varchar(64) NOT NULL default '',
  `user_website` varchar(64) NOT NULL default '',
  `user_bio` text character set utf8 NOT NULL,
  `user_birthday` date NOT NULL default '0000-00-00',
  `user_register_date` int(11) NOT NULL default '0',
  `user_last_visit` int(11) NOT NULL default '0',
  `user_activated` tinyint(10) NOT NULL default '1',
  PRIMARY KEY  (`user_id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `UsersTokensLookup`
--

CREATE TABLE IF NOT EXISTS `UsersTokensLookup` (
  `utlookup_id` mediumint(9) NOT NULL auto_increment,
  `utlookup_user_id` mediumint(9) NOT NULL default '0',
  `utlookup_token_id` mediumint(9) NOT NULL default '0',
  `utlookup_site_id` int(11) default NULL,
  `utlookup_is_global` tinyint(1) NOT NULL default '0',
  `utlookup_granted_timestamp` int(11) NOT NULL default '0',
  PRIMARY KEY  (`utlookup_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `UserTokens`
--

CREATE TABLE IF NOT EXISTS `UserTokens` (
  `token_id` int(6) unsigned NOT NULL auto_increment,
  `token_type` varchar(10) character set utf8 NOT NULL default '',
  `token_code` varchar(32) character set utf8 NOT NULL default '',
  `token_description` tinytext character set utf8 NOT NULL,
  PRIMARY KEY  (`token_id`),
  UNIQUE KEY `permission_code` (`token_code`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `Vocabulary`
--

CREATE TABLE IF NOT EXISTS `Vocabulary` (
  `vocabulary_id` int(10) unsigned NOT NULL auto_increment,
  `vocabulary_name` varchar(64) collate utf8_general_ci NOT NULL default '',
  `vocabulary_prefix` text collate utf8_general_ci NOT NULL,
  `vocabulary_namespace` varchar(64) collate utf8_general_ci NOT NULL default '',
  `vocabulary_description` text collate utf8_general_ci NOT NULL,
  `vocabulary_type` varchar(32) collate utf8_general_ci NOT NULL default '',
  `vocabulary_max` int(11) NOT NULL default '0',
  `vocabulary_min` int(11) NOT NULL default '0',
  `vocabulary_nested` binary(1) NOT NULL default '1',
  `vocabulary_setting` tinyint(4) NOT NULL default '0',
  `vocabulary_iterates` tinyint(4) NOT NULL default '0',
  `vocabulary_default_content` text collate utf8_general_ci NOT NULL,
  `vocabulary_parent_id` int(11) NOT NULL default '0',
  `vocabulary_definition` varchar(10) collate utf8_general_ci NOT NULL default '',
  PRIMARY KEY  (`vocabulary_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1 ;
