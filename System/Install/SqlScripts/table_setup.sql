-- phpMyAdmin SQL Dump
-- version 3.1.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Feb 23, 2010 at 07:59 PM
-- Server version: 5.0.75
-- PHP Version: 5.2.8-0.dotdeb.1
-- Smartest Database Schema version 14. Minimum Code revision 334.

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Database: `smartest_setup`
--

-- --------------------------------------------------------

--
-- Table structure for table `AssetClasses`
--

CREATE TABLE `AssetClasses` (
  `assetclass_id` int(9) NOT NULL AUTO_INCREMENT,
  `assetclass_name` varchar(32) NOT NULL DEFAULT '',
  `assetclass_label` varchar(32) NOT NULL DEFAULT 'Untitled Asset Class',
  `assetclass_site_id` int(11) NOT NULL DEFAULT '1',
  `assetclass_is_sitewide` tinyint(1) NOT NULL DEFAULT '0',
  `assetclass_shared` tinyint(1) NOT NULL DEFAULT '1',
  `assetclass_type` varchar(64) NOT NULL DEFAULT '',
  `assetclass_info` text NOT NULL,
  `assetclass_parent_id` int(11) unsigned NOT NULL,
  `assetclass_update_on_page_publish` tinyint(1) NOT NULL DEFAULT '1',
  `assetclass_filter_type` varchar(64) CHARACTER SET ascii NOT NULL DEFAULT 'SM_ASSETCLASS_FILTERTYPE_NONE',
  `assetclass_filter_value` varchar(64) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `assetclass_is_system` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`assetclass_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `AssetIdentifiers`
--

CREATE TABLE `AssetIdentifiers` (
  `assetidentifier_id` int(20) unsigned NOT NULL AUTO_INCREMENT,
  `assetidentifier_draft_asset_id` int(9) unsigned NOT NULL DEFAULT '0',
  `assetidentifier_live_asset_id` mediumint(9) unsigned NOT NULL DEFAULT '0',
  `assetidentifier_assetclass_id` int(9) unsigned NOT NULL DEFAULT '0',
  `assetidentifier_instance_name` varchar(64) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'default',
  `assetidentifier_page_id` mediumint(9) unsigned NOT NULL DEFAULT '0',
  `assetidentifier_item_id` mediumint(9) unsigned DEFAULT NULL,
  `assetidentifier_site_id` mediumint(9) DEFAULT NULL,
  `assetidentifier_draft_render_data` text COLLATE utf8_unicode_ci NOT NULL,
  `assetidentifier_live_render_data` text COLLATE utf8_unicode_ci NOT NULL,
  `assetidentifier_language` varchar(8) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'eng',
  PRIMARY KEY (`assetidentifier_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `Assets`
--

CREATE TABLE `Assets` (
  `asset_id` int(9) unsigned NOT NULL AUTO_INCREMENT,
  `asset_webid` varchar(36) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `asset_label` varchar(255) NOT NULL,
  `asset_stringid` varchar(64) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `asset_url` varchar(255) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `asset_type` varchar(64) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `asset_language` varchar(8) NOT NULL DEFAULT 'eng',
  `asset_site_id` mediumint(9) NOT NULL DEFAULT '1',
  `asset_user_id` int(9) unsigned NOT NULL DEFAULT '1',
  `asset_created` int(11) NOT NULL DEFAULT '0',
  `asset_modified` int(11) NOT NULL DEFAULT '0',
  `asset_shared` tinyint(1) NOT NULL DEFAULT '0',
  `asset_deleted` tinyint(1) NOT NULL DEFAULT '0',
  `asset_fragment_id` mediumint(9) unsigned DEFAULT '0',
  `asset_parent_id` int(11) NOT NULL,
  `asset_variant_id` int(9) NOT NULL,
  `asset_model_id` int(11) NOT NULL,
  `asset_thumbnail_id` int(11) NOT NULL,
  `asset_parameter_defaults` varchar(255) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `asset_info` text NOT NULL,
  `asset_is_held` tinyint(1) NOT NULL DEFAULT '0',
  `asset_held_by` mediumint(9) NOT NULL DEFAULT '0',
  `asset_is_approved` tinyint(1) NOT NULL DEFAULT '0',
  `asset_is_subbed` tinyint(1) NOT NULL,
  `asset_is_archived` tinyint(1) NOT NULL DEFAULT '0',
  `asset_submitted_from_public` tinyint(1) NOT NULL DEFAULT '0',
  `asset_public_status_trusted` tinyint(1) NOT NULL DEFAULT '0',
  `asset_is_system` tinyint(1) NOT NULL DEFAULT '0',
  `asset_is_hidden` tinyint(1) NOT NULL,
  `asset_search_field` text NOT NULL,
  PRIMARY KEY (`asset_id`),
  UNIQUE KEY `Asset_webid` (`asset_webid`,`asset_stringid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `Comments`
--

CREATE TABLE `Comments` (
  `comment_id` mediumint(11) NOT NULL AUTO_INCREMENT,
  `comment_object_id` mediumint(11) NOT NULL DEFAULT '0',
  `comment_type` varchar(32) NOT NULL DEFAULT '',
  `comment_status` varchar(32) NOT NULL DEFAULT '',
  `comment_author_user_id` mediumint(11) NOT NULL DEFAULT '0',
  `comment_author_name` varchar(128) NOT NULL DEFAULT '',
  `comment_author_website` varchar(128) NOT NULL DEFAULT '',
  `comment_content` text NOT NULL,
  `comment_language` varchar(8) NOT NULL DEFAULT 'eng',
  `comment_posted_at` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`comment_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `DropDowns`
--

CREATE TABLE `DropDowns` (
  `dropdown_id` mediumint(9) NOT NULL AUTO_INCREMENT,
  `dropdown_name` varchar(64) NOT NULL DEFAULT '',
  `dropdown_label` varchar(64) NOT NULL DEFAULT '',
  `dropdown_datatype` varchar(64) NOT NULL,
  `dropdown_language` varchar(8) NOT NULL DEFAULT 'eng',
  `dropdown_is_system` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`dropdown_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `DropDownValues`
--

CREATE TABLE `DropDownValues` (
  `dropdownvalue_id` mediumint(9) NOT NULL AUTO_INCREMENT,
  `dropdownvalue_dropdown_id` mediumint(9) NOT NULL DEFAULT '0',
  `dropdownvalue_order` mediumint(9) NOT NULL DEFAULT '0',
  `dropdownvalue_label` varchar(64) NOT NULL DEFAULT '',
  `dropdownvalue_value` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`dropdownvalue_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `ItemClasses`
--

CREATE TABLE `ItemClasses` (
  `itemclass_id` int(9) unsigned NOT NULL AUTO_INCREMENT,
  `itemclass_type` varchar(32) NOT NULL DEFAULT 'SM_ITEMCLASS_MODEL',
  `itemclass_webid` varchar(36) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `itemclass_parent_id` int(9) NOT NULL DEFAULT '0',
  `itemclass_name` varchar(40) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `itemclass_plural_name` varchar(64) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `itemclass_site_id` int(11) NOT NULL DEFAULT '0',
  `itemclass_shared` tinyint(1) NOT NULL,
  `itemclass_varname` varchar(64) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `itemclass_item_webid_format` varchar(32) NOT NULL,
  `itemclass_class_file_checksum` varchar(32) NOT NULL,
  `itemclass_default_description_property_id` int(11) unsigned NOT NULL DEFAULT '0',
  `itemclass_default_sort_property_id` int(9) NOT NULL,
  `itemclass_default_thumbnail_property_id` int(11) NOT NULL,
  `itemclass_primary_property_id` int(11) NOT NULL,
  `itemclass_settings` text NOT NULL,
  `itemclass_blog_mode` tinyint(1) NOT NULL DEFAULT '0',
  `itemclass_uses_draft_properties` tinyint(1) NOT NULL DEFAULT '1',
  `itemclass_userid` int(11) NOT NULL DEFAULT '0',
  `itemclass_rating_max_score` smallint(3) NOT NULL DEFAULT '5',
  `itemclass_is_system` tinyint(1) NOT NULL DEFAULT '0',
  `itemclass_is_hidden` tinyint(1) NOT NULL DEFAULT '0',
  `itemclass_created_from_buildkit` varchar(64) NOT NULL,
  PRIMARY KEY (`itemclass_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `ItemProperties`
--

CREATE TABLE `ItemProperties` (
  `itemproperty_id` int(9) unsigned NOT NULL AUTO_INCREMENT,
  `itemproperty_webid` varchar(36) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `itemproperty_name` varchar(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `itemproperty_varname` varchar(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `itemproperty_required` varchar(16) CHARACTER SET utf8 NOT NULL DEFAULT 'FALSE',
  `itemproperty_datatype` varchar(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'SM_DATATYPE_SL_TEXT',
  `itemproperty_foreign_key_filter` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,
  `itemproperty_itemclass_id` int(9) NOT NULL DEFAULT '0',
  `itemproperty_defaultvalue` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `itemproperty_defaultformat` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `itemproperty_info` text COLLATE utf8_unicode_ci NOT NULL,
  `itemproperty_share_values_autocomplete` tinyint(1) NOT NULL,
  `itemproperty_option_set_type` varchar(64) CHARACTER SET ascii NOT NULL DEFAULT 'SM_PROPERTY_FILTERTYPE_NONE',
  `itemproperty_option_set_id` int(11) NOT NULL DEFAULT '0',
  `itemproperty_order_index` int(11) NOT NULL,
  `itemproperty_storage_migrated` tinyint(1) NOT NULL DEFAULT '0',
  `itemproperty_last_regularized` int(11) NOT NULL,
  PRIMARY KEY (`itemproperty_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ItemPropertyValues`
--

CREATE TABLE `ItemPropertyValues` (
  `itempropertyvalue_id` int(20) unsigned NOT NULL AUTO_INCREMENT,
  `itempropertyvalue_item_id` int(9) NOT NULL DEFAULT '0',
  `itempropertyvalue_property_id` int(9) NOT NULL DEFAULT '0',
  `itempropertyvalue_name` varchar(64) NOT NULL,
  `itempropertyvalue_parent_value_id` int(20) NOT NULL,
  `itempropertyvalue_draft_content` text CHARACTER SET utf8 NOT NULL,
  `itempropertyvalue_content` text CHARACTER SET utf8 NOT NULL,
  `itempropertyvalue_draft_info` text NOT NULL,
  `itempropertyvalue_live_info` text NOT NULL,
  `itempropertyvalue_language` varchar(8) NOT NULL DEFAULT 'eng',
  PRIMARY KEY (`itempropertyvalue_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `Items`
--

CREATE TABLE `Items` (
  `item_id` int(11) NOT NULL AUTO_INCREMENT,
  `item_webid` varchar(36) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `item_itemclass_id` int(9) unsigned NOT NULL DEFAULT '0',
  `item_site_id` int(11) unsigned NOT NULL DEFAULT '0',
  `item_shared` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `item_name` varchar(255) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `item_slug` varchar(255) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `item_type` varchar(64) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'SM_ITEMTYPE_NORMAL',
  `item_alt_title_tag` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `item_use_alt_title_tag` tinyint(1) NOT NULL DEFAULT '0',
  `item_public` enum('FALSE','TRUE') CHARACTER SET utf8 NOT NULL DEFAULT 'FALSE',
  `item_metapage_id` int(11) unsigned NOT NULL DEFAULT '0',
  `item_search_field` text CHARACTER SET utf8 NOT NULL,
  `item_is_held` tinyint(1) NOT NULL DEFAULT '0',
  `item_held_by` int(10) NOT NULL DEFAULT '0',
  `item_deleted` tinyint(1) NOT NULL DEFAULT '0',
  `item_created` int(11) NOT NULL DEFAULT '0',
  `item_modified` int(11) NOT NULL DEFAULT '0',
  `item_last_published` int(11) NOT NULL DEFAULT '0',
  `item_changes_approved` tinyint(1) NOT NULL DEFAULT '0',
  `item_createdby_userid` int(11) NOT NULL DEFAULT '0',
  `item_is_archived` tinyint(1) NOT NULL DEFAULT '0',
  `item_submitted_from_public` tinyint(1) NOT NULL DEFAULT '0',
  `item_public_status_trusted` tinyint(1) NOT NULL DEFAULT '0',
  `item_language` varchar(8) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `item_num_hits` bigint(20) NOT NULL DEFAULT '0',
  `item_num_ratings` int(11) NOT NULL DEFAULT '0',
  `item_average_rating` float NOT NULL DEFAULT '0',
  PRIMARY KEY (`item_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `Lists`
--

CREATE TABLE `Lists` (
  `list_id` int(9) NOT NULL AUTO_INCREMENT,
  `list_name` varchar(32) NOT NULL DEFAULT '',
  `list_title` varchar(64) CHARACTER SET utf8 DEFAULT '',
  `list_type` varchar(32) NOT NULL DEFAULT 'SM_LIST_ARTCULATED',
  `list_draft_set_id` int(9) unsigned NOT NULL DEFAULT '0',
  `list_live_set_id` int(9) unsigned NOT NULL DEFAULT '0',
  `list_draft_template_file` varchar(64) CHARACTER SET utf8 NOT NULL DEFAULT 'default_list.tpl',
  `list_live_template_file` varchar(64) CHARACTER SET utf8 NOT NULL DEFAULT 'default_list.tpl',
  `list_draft_header_template` varchar(64) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `list_draft_footer_template` varchar(64) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `list_live_header_template` varchar(64) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `list_live_footer_template` varchar(64) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `list_maximum_length` int(2) NOT NULL DEFAULT '0',
  `list_page_id` mediumint(9) NOT NULL DEFAULT '0',
  `list_item_id` mediumint(9) NOT NULL,
  `list_global` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`list_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `ManyToManyLookups`
--

CREATE TABLE `ManyToManyLookups` (
  `mtmlookup_id` int(20) unsigned NOT NULL AUTO_INCREMENT,
  `mtmlookup_type` varchar(64) NOT NULL DEFAULT '',
  `mtmlookup_instance_name` varchar(64) NOT NULL DEFAULT '',
  `mtmlookup_context_data` text NOT NULL,
  `mtmlookup_order_index` mediumint(11) DEFAULT NULL,
  `mtmlookup_status_flag` varchar(64) NOT NULL DEFAULT 'SM_MTMLOOKUPSTATUS_LIVE',
  `mtmlookup_entity_1_foreignkey` mediumint(11) NOT NULL DEFAULT '0',
  `mtmlookup_entity_2_foreignkey` mediumint(11) NOT NULL DEFAULT '0',
  `mtmlookup_entity_3_foreignkey` mediumint(11) NOT NULL DEFAULT '0',
  `mtmlookup_entity_4_foreignkey` mediumint(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`mtmlookup_id`),
  KEY `mtmlookup_type` (`mtmlookup_type`),
  KEY `mtmlookup_entity_1_foreignkey` (`mtmlookup_entity_1_foreignkey`),
  KEY `mtmlookup_entity_2_foreignkey` (`mtmlookup_entity_2_foreignkey`),
  KEY `mtmlookup_entity_3_foreignkey` (`mtmlookup_entity_3_foreignkey`),
  KEY `mtmlookup_entity_4_foreignkey` (`mtmlookup_entity_4_foreignkey`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `PageLayoutPresetDefinitions`
--

CREATE TABLE `PageLayoutPresetDefinitions` (
  `plpd_id` mediumint(9) NOT NULL AUTO_INCREMENT,
  `plpd_preset_id` mediumint(9) NOT NULL DEFAULT '0',
  `plpd_element_type` varchar(32) NOT NULL DEFAULT '',
  `plpd_element_id` mediumint(9) NOT NULL DEFAULT '0',
  `plpd_element_value` varchar(255) NOT NULL DEFAULT '0',
  `plpd_template_id` int(11) NOT NULL,
  PRIMARY KEY (`plpd_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `PageLayoutPresets`
--

CREATE TABLE `PageLayoutPresets` (
  `plp_id` mediumint(9) NOT NULL AUTO_INCREMENT,
  `plp_site_id` int(11) unsigned NOT NULL DEFAULT '0',
  `plp_shared` tinyint(1) NOT NULL DEFAULT '0',
  `plp_label` varchar(64) NOT NULL DEFAULT '',
  `plp_master_template_name` varchar(64) NOT NULL DEFAULT '',
  `plp_created_by_user_id` mediumint(9) NOT NULL DEFAULT '0',
  `plp_orig_from_page_id` mediumint(9) NOT NULL DEFAULT '0',
  PRIMARY KEY (`plp_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `PageProperties`
--

CREATE TABLE `PageProperties` (
  `pageproperty_id` mediumint(9) NOT NULL AUTO_INCREMENT,
  `pageproperty_site_id` mediumint(9) NOT NULL DEFAULT '0',
  `pageproperty_is_sitewide` tinyint(1) NOT NULL DEFAULT '0',
  `pageproperty_name` varchar(64) NOT NULL DEFAULT '',
  `pageproperty_label` varchar(64) NOT NULL DEFAULT '',
  `pageproperty_type` varchar(64) NOT NULL DEFAULT '',
  `pageproperty_foreign_key_filter` varchar(64) NOT NULL DEFAULT '',
  PRIMARY KEY (`pageproperty_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `PagePropertyValues`
--

CREATE TABLE `PagePropertyValues` (
  `pagepropertyvalue_id` mediumint(9) unsigned NOT NULL AUTO_INCREMENT,
  `pagepropertyvalue_page_id` mediumint(9) unsigned NOT NULL DEFAULT '0',
  `pagepropertyvalue_item_id` int(9) NOT NULL,
  `pagepropertyvalue_site_id` mediumint(9) DEFAULT NULL,
  `pagepropertyvalue_pageproperty_id` mediumint(9) unsigned NOT NULL DEFAULT '0',
  `pagepropertyvalue_live_value` varchar(255) NOT NULL DEFAULT '',
  `pagepropertyvalue_draft_value` varchar(255) NOT NULL DEFAULT '',
  `pagepropertyvalue_language` varchar(3) NOT NULL,
  PRIMARY KEY (`pagepropertyvalue_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `Pages`
--

CREATE TABLE `Pages` (
  `page_id` int(11) NOT NULL AUTO_INCREMENT,
  `page_webid` varchar(36) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `page_site_id` mediumint(9) unsigned NOT NULL DEFAULT '0',
  `page_dataset_id` mediumint(9) DEFAULT NULL,
  `page_name` varchar(64) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `page_title` varchar(48) CHARACTER SET utf8 NOT NULL DEFAULT 'Untitled Page',
  `page_icon_image` varchar(64) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `page_icon_image_id` int(11) unsigned NOT NULL,
  `page_parent` int(11) NOT NULL DEFAULT '0',
  `page_order_index` int(9) unsigned NOT NULL DEFAULT '1',
  `page_search_field` text CHARACTER SET utf8 NOT NULL,
  `page_is_held` tinyint(1) NOT NULL DEFAULT '0',
  `page_held_by` int(9) DEFAULT NULL,
  `page_is_section` tinyint(4) NOT NULL DEFAULT '0',
  `page_live_template` varchar(60) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `page_draft_template` varchar(60) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `page_type` enum('NORMAL','SUBPAGE','ITEMCLASS','TAG') CHARACTER SET utf8 NOT NULL DEFAULT 'NORMAL',
  `page_force_static_title` tinyint(1) NOT NULL DEFAULT '0',
  `page_deleted` enum('TRUE','FALSE') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'FALSE',
  `page_cache_as_html` varchar(5) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'TRUE',
  `page_cache_interval` enum('PERMANENT','MONTHLY','DAILY','HOURLY','MINUTE','SECOND') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'PERMANENT',
  `page_created` int(11) NOT NULL DEFAULT '0',
  `page_modified` int(11) NOT NULL DEFAULT '0',
  `page_changes_approved` tinyint(1) NOT NULL DEFAULT '0',
  `page_last_built` int(11) NOT NULL,
  `page_last_published` int(11) NOT NULL DEFAULT '0',
  `page_is_published` enum('FALSE','TRUE') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'FALSE',
  `page_keywords` text CHARACTER SET utf8 NOT NULL,
  `page_meta_description` varchar(255) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `page_description` varchar(255) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `page_createdby_userid` int(11) NOT NULL DEFAULT '0',
  `page_pdf_version_asset_id` int(11) NOT NULL,
  PRIMARY KEY (`page_id`),
  KEY `page_webid` (`page_webid`),
  KEY `page_name` (`page_name`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `PageUrls`
--

CREATE TABLE `PageUrls` (
  `pageurl_id` mediumint(9) NOT NULL AUTO_INCREMENT,
  `pageurl_name` varchar(64) NOT NULL,
  `pageurl_page_id` mediumint(9) NOT NULL DEFAULT '0',
  `pageurl_item_id` int(11) NOT NULL,
  `pageurl_site_id` int(11) NOT NULL,
  `pageurl_asset_id` int(11) NOT NULL,
  `pageurl_url` varchar(255) NOT NULL DEFAULT '',
  `pageurl_type` varchar(64) NOT NULL DEFAULT 'SM_PAGEURL_NORMAL',
  `pageurl_redirect_type` varchar(32) NOT NULL,
  `pageurl_destination` varchar(255) NOT NULL DEFAULT '',
  `pageurl_is_default` tinyint(1) NOT NULL DEFAULT '0',
  `pageurl_num_hits` int(11) unsigned NOT NULL,
  `pageurl_language` varchar(3) NOT NULL,
  `pageurl_expires` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`pageurl_id`),
  KEY `pageurl_page_id` (`pageurl_page_id`),
  KEY `pageurl_url` (`pageurl_url`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `Roles`
--

CREATE TABLE `Roles` (
  `role_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `role_label` varchar(64) NOT NULL DEFAULT '',
  PRIMARY KEY (`role_id`),
  UNIQUE KEY `usergroup_name` (`role_label`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `RolesTokensLookup`
--

CREATE TABLE `RolesTokensLookup` (
  `rtlookup_id` int(9) NOT NULL AUTO_INCREMENT,
  `rtlookup_token_id` int(9) NOT NULL DEFAULT '0',
  `rtlookup_role_id` int(9) NOT NULL DEFAULT '0',
  PRIMARY KEY (`rtlookup_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `SetRules`
--

CREATE TABLE `SetRules` (
  `setrule_id` int(11) NOT NULL AUTO_INCREMENT,
  `setrule_set_id` int(11) NOT NULL DEFAULT '0',
  `setrule_label` varchar(64) NOT NULL DEFAULT '',
  `setrule_itemproperty_id` varchar(32) NOT NULL DEFAULT '0',
  `setrule_operator` varchar(64) NOT NULL DEFAULT '',
  `setrule_value` text NOT NULL,
  PRIMARY KEY (`setrule_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `Sets`
--

CREATE TABLE `Sets` (
  `set_id` int(11) NOT NULL AUTO_INCREMENT,
  `set_webid` varchar(36) NOT NULL,
  `set_name` varchar(64) NOT NULL DEFAULT '',
  `set_label` varchar(64) NOT NULL DEFAULT '',
  `set_cover_asset_id` int(11) NOT NULL,
  `set_itemclass_id` int(11) NOT NULL DEFAULT '0',
  `set_site_id` int(11) unsigned NOT NULL DEFAULT '0',
  `set_shared` int(11) unsigned NOT NULL DEFAULT '0',
  `set_is_system` tinyint(1) NOT NULL,
  `set_is_hidden` tinyint(1) NOT NULL,
  `set_data_source_site_id` varchar(16) NOT NULL DEFAULT 'ALL',
  `set_type` varchar(32) NOT NULL DEFAULT 'DYNAMIC',
  `set_sort_field` varchar(32) NOT NULL DEFAULT '',
  `set_sort_direction` varchar(4) NOT NULL DEFAULT 'ASC',
  `set_feed_sort_field` varchar(32) NOT NULL,
  `set_feed_sort_direction` varchar(4) NOT NULL DEFAULT 'DESC',
  `set_feed_nonce` varchar(16) NOT NULL,
  `set_varname` varchar(64) NOT NULL DEFAULT '',
  `set_lookup_source` varchar(32) NOT NULL DEFAULT '',
  `set_filter_type` varchar(64) NOT NULL DEFAULT '',
  `set_filter_value` varchar(64) NOT NULL DEFAULT '',
  PRIMARY KEY (`set_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `SetsItemsLookup`
--

CREATE TABLE `SetsItemsLookup` (
  `setlookup_id` int(20) unsigned NOT NULL AUTO_INCREMENT,
  `setlookup_set_id` mediumint(9) NOT NULL DEFAULT '0',
  `setlookup_item_id` mediumint(9) NOT NULL DEFAULT '0',
  `setlookup_order` mediumint(9) NOT NULL DEFAULT '0',
  PRIMARY KEY (`setlookup_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `Settings`
--

CREATE TABLE `Settings` (
  `setting_id` int(9) unsigned NOT NULL AUTO_INCREMENT,
  `setting_parent_id` int(11) NOT NULL DEFAULT '0',
  `setting_site_id` int(11) NOT NULL DEFAULT '0',
  `setting_user_id` int(11) NOT NULL DEFAULT '0',
  `setting_application_id` varchar(128) NOT NULL,
  `setting_type` varchar(64) NOT NULL,
  `setting_name` varchar(50) NOT NULL DEFAULT '',
  `setting_value` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`setting_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `Sites`
--

CREATE TABLE `Sites` (
  `site_id` int(9) unsigned NOT NULL AUTO_INCREMENT,
  `site_unique_id` varchar(23) NOT NULL,
  `site_name` varchar(128) NOT NULL DEFAULT '',
  `site_internal_label` varchar(255) NOT NULL,
  `site_is_enabled` tinyint(1) NOT NULL DEFAULT '0',
  `site_title_format` varchar(255) NOT NULL DEFAULT '$site | $page',
  `site_domain` varchar(128) NOT NULL DEFAULT '',
  `site_url_prefix` varchar(32) NOT NULL,
  `site_directory_name` varchar(64) NOT NULL DEFAULT '',
  `site_admin_email` varchar(64) NOT NULL DEFAULT '',
  `site_top_page_id` mediumint(9) NOT NULL DEFAULT '0',
  `site_tag_page_id` int(11) unsigned NOT NULL DEFAULT '0',
  `site_search_page_id` int(11) unsigned NOT NULL DEFAULT '0',
  `site_error_page_id` int(11) NOT NULL DEFAULT '0',
  `site_logo_image_asset_id` int(11) NOT NULL,
  PRIMARY KEY (`site_id`),
  UNIQUE KEY `site_domain` (`site_domain`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `Tags`
--

CREATE TABLE `Tags` (
  `tag_id` int(9) unsigned NOT NULL AUTO_INCREMENT,
  `tag_name` varchar(64) NOT NULL DEFAULT '',
  `tag_label` varchar(64) NOT NULL DEFAULT '',
  `tag_site_id` int(11) NOT NULL,
  `tag_language` varchar(8) NOT NULL DEFAULT 'eng',
  `tag_type` varchar(64) NOT NULL DEFAULT 'SM_TAGTYPE_TAG',
  PRIMARY KEY (`tag_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `TagsObjectsLookup`
--

CREATE TABLE `TagsObjectsLookup` (
  `taglookup_id` int(20) unsigned NOT NULL AUTO_INCREMENT,
  `taglookup_tag_id` int(9) unsigned NOT NULL DEFAULT '0',
  `taglookup_object_id` int(9) unsigned NOT NULL DEFAULT '0',
  `taglookup_type` varchar(32) NOT NULL DEFAULT 'SM_PAGE_TAG_LINK',
  `taglookup_metapage_id` int(9) unsigned NOT NULL DEFAULT '0',
  `taglookup_order_index` int(9) NOT NULL,
  PRIMARY KEY (`taglookup_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `TextFragments`
--

CREATE TABLE `TextFragments` (
  `textfragment_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `textfragment_webid` varchar(36) NOT NULL DEFAULT '',
  `textfragment_asset_id` mediumint(9) NOT NULL DEFAULT '0',
  `textfragment_content` text NOT NULL,
  `textfragment_file` varchar(128) NOT NULL DEFAULT '',
  `textfragment_created` int(10) NOT NULL DEFAULT '0',
  `textfragment_modified` int(10) unsigned NOT NULL DEFAULT '0',
  `textfragment_is_approved` tinyint(1) NOT NULL DEFAULT '0',
  `textfragment_type` varchar(64) NOT NULL DEFAULT 'SM_TEXTFRAGMENTTYPE_CURRENT_VERSION',
  PRIMARY KEY (`textfragment_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `TodoItems`
--

CREATE TABLE `TodoItems` (
  `todoitem_id` mediumint(9) NOT NULL AUTO_INCREMENT,
  `todoitem_assigning_user_id` int(10) NOT NULL DEFAULT '0',
  `todoitem_receiving_user_id` int(10) NOT NULL DEFAULT '0',
  `todoitem_type` varchar(32) NOT NULL DEFAULT 'SM_TODOITEMTYPE_PERSONAL',
  `todoitem_token` varchar(64) DEFAULT NULL,
  `todoitem_foreign_object_type` varchar(32) DEFAULT NULL,
  `todoitem_foreign_object_id` int(10) DEFAULT NULL,
  `todoitem_time_assigned` int(11) NOT NULL DEFAULT '0',
  `todoitem_time_completed` int(11) NOT NULL DEFAULT '0',
  `todoitem_priority` int(1) NOT NULL DEFAULT '2',
  `todoitem_is_complete` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `todoitem_ignore` tinyint(1) NOT NULL DEFAULT '0',
  `todoitem_description` text NOT NULL,
  `todoitem_size` int(1) NOT NULL DEFAULT '2',
  PRIMARY KEY (`todoitem_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `Users`
--

CREATE TABLE `Users` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(64) NOT NULL DEFAULT '',
  `password` varchar(32) NOT NULL DEFAULT '',
  `user_password_salt` varchar(40) NOT NULL,
  `user_password_last_changed` int(11) NOT NULL,
  `user_password_change_required` tinyint(1) NOT NULL,
  `user_firstname` varchar(64) NOT NULL DEFAULT '',
  `user_lastname` varchar(64) NOT NULL DEFAULT '',
  `user_invert_name_order` tinyint(1) NOT NULL DEFAULT '0',
  `user_email` varchar(64) NOT NULL DEFAULT '',
  `user_website` varchar(64) NOT NULL DEFAULT '',
  `user_twitter_handle` varchar(32) NOT NULL,
  `user_bio` text CHARACTER SET utf8 NOT NULL,
  `user_bio_asset_id` int(9) NOT NULL,
  `user_birthday` date NOT NULL DEFAULT '0000-00-00',
  `user_profile_pic_asset_id` int(9) unsigned NOT NULL,
  `user_register_date` int(11) NOT NULL DEFAULT '0',
  `user_last_visit` int(11) NOT NULL DEFAULT '0',
  `user_activated` tinyint(1) NOT NULL DEFAULT '1',
  `user_is_smartest_account` tinyint(1) NOT NULL DEFAULT '0',
  `user_info` text CHARACTER SET utf8 NOT NULL,
  `user_oauth_consumer_token` varchar(255) CHARACTER SET utf8 NOT NULL,
  `user_oauth_consumer_secret` varchar(255) CHARACTER SET utf8 NOT NULL,
  `user_oauth_access_token` varchar(255) CHARACTER SET utf8 NOT NULL,
  `user_oauth_access_token_secret` varchar(255) CHARACTER SET utf8 NOT NULL,
  `user_oauth_service_id` varchar(64) CHARACTER SET utf8 NOT NULL,
  `user_type` varchar(64) CHARACTER SET utf8 NOT NULL DEFAULT 'SM_USERTYPE_SYSTEM_USER',
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `UsersTokensLookup`
--

CREATE TABLE `UsersTokensLookup` (
  `utlookup_id` mediumint(9) NOT NULL AUTO_INCREMENT,
  `utlookup_user_id` mediumint(9) NOT NULL DEFAULT '0',
  `utlookup_token_id` mediumint(9) NOT NULL DEFAULT '0',
  `utlookup_site_id` int(11) DEFAULT NULL,
  `utlookup_is_global` tinyint(1) NOT NULL DEFAULT '0',
  `utlookup_order_index` int(9) NOT NULL DEFAULT '0',
  `utlookup_granted_timestamp` int(11) NOT NULL DEFAULT '0',
  `utlookup_granted_by_user_id` int(11) NOT NULL,
  PRIMARY KEY (`utlookup_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;


INSERT INTO `Settings` (`setting_id`, `setting_parent_id`, `setting_site_id`, `setting_user_id`, `setting_application_id`, `setting_type`, `setting_name`, `setting_value`) VALUES
(1, 0, 0, 0, '', 'SM_SETTINGTYPE_SYSTEM_META', 'database_minimum_revision', '584'),
(2, 0, 0, 0, '', 'SM_SETTINGTYPE_SYSTEM_META', 'database_version', '21');

INSERT INTO `AssetClasses` (`assetclass_name`, `assetclass_label`, `assetclass_site_id`, `assetclass_shared`, `assetclass_type`, `assetclass_is_system`) VALUES
('page_specific_stylesheet', 'Page-specific Stylesheet', 1, 1, 'SM_ASSETCLASS_STYLESHEET', 1),
('page_specific_javascript', 'Page-specific Javascript', 1, 1, 'SM_ASSETCLASS_JAVASCRIPT', 1),
('page_layout', 'Page layout', 1, 1, 'SM_ASSETCLASS_CONTAINER', 1);