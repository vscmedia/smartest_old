ALTER TABLE `Pages` ADD INDEX ( `page_name` );
ALTER TABLE `Pages` ADD `page_force_static_title` TINYINT( 1 ) NOT NULL DEFAULT '0' AFTER `page_type` ;