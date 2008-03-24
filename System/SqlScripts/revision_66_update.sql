CREATE TABLE `ManyToManyLookups` (
  `mtmlookup_id` mediumint(11) NOT NULL auto_increment,
  `mtmlookup_type` varchar(64) NOT NULL default '',
  `mtmlookup_instance_name` varchar(64) NOT NULL default '',
  `mtmlookup_context_data` text NOT NULL,
  `mtmlookup_entity_1_foreignkey` mediumint(11) NOT NULL default '0',
  `mtmlookup_entity_2_foreignkey` mediumint(11) NOT NULL default '0',
  `mtmlookup_entity_3_foreignkey` mediumint(11) NOT NULL default '0',
  `mtmlookup_entity_4_foreignkey` mediumint(11) NOT NULL default '0',
  PRIMARY KEY  (`mtmlookup_id`),
  KEY `mtmlookup_type` (`mtmlookup_type`),
  KEY `mtmlookup_entity_1_foreignkey` (`mtmlookup_entity_1_foreignkey`),
  KEY `mtmlookup_entity_2_foreignkey` (`mtmlookup_entity_2_foreignkey`),
  KEY `mtmlookup_entity_3_foreignkey` (`mtmlookup_entity_3_foreignkey`),
  KEY `mtmlookup_entity_4_foreignkey` (`mtmlookup_entity_4_foreignkey`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

ALTER TABLE `TextFragments` ADD `textfragment_webid` VARCHAR( 16 ) NOT NULL AFTER `textfragment_id` ;
ALTER TABLE `TextFragments` ADD `textfragment_file` VARCHAR( 128 ) NOT NULL AFTER `textfragment_content` ;
