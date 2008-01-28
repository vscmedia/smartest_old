TRUNCATE TABLE `Users`;
INSERT INTO `Users` (`user_id`, `username`, `password`, `user_firstname`, `user_lastname`, `user_email`, `user_website`, `user_bio`, `user_birthday`, `user_register_date`, `user_last_visit`, `user_activated`) VALUES (1, '__USERNAME__', '__PASSWORD__', '__FIRSTNAME__', '__LASTNAME__', '__EMAIL__', '', 'Share a little biographical information to fill out your profile. This may be shown publicly.', '0000-00-00', 0, 0, 1);

TRUNCATE TABLE `Pages`;
INSERT INTO `Pages` (`page_id`, `page_webid`, `page_site_id`, `page_dataset_id`, `page_name`, `page_title`, `page_icon_image`, `page_parent`, `page_url`, `page_search_field`, `page_is_held`, `page_held_by`, `page_live_template`, `page_draft_template`, `page_type`, `page_deleted`, `page_cache_as_html`, `page_cache_interval`, `page_created`, `page_modified`, `page_changes_approved`, `page_last_published`, `page_is_published`, `page_keywords`, `page_meta_description`, `page_description`, `page_createdby_userid`, `page_order_index`) VALUES (1, 'jl02c1D042YTWF6w85TO9j808iW7wNjQ', 1, NULL, 'home', 'Home Page', '', 0, '', '', 0, 0, 'default.tpl', 'default.tpl', 'NORMAL', 'FALSE', 'TRUE', 'DAILY', __NOW__, 0, 0, __NOW__, 'TRUE', 'keywords', 'description', 'description', 1, 1),
(2, 'dlq6190fcgq72ibm0m9fr7j4moksbemk', 1, NULL, 'search-results', 'Search Results',            '', 1, '', '', 0, 0, 'default.tpl', 'default.tpl', 'NORMAL', 'FALSE', 'TRUE', 'PERMANENT', __NOW__, 0, 0, __NOW__, 'TRUE', 'keywords', 'description', 'description', 1, 2),
(3, '2nr50x6io6ey8f6lu6rxm38mjo1i07a3', 1, NULL, '404-error',      'Error 404: Page not found', '', 1, '', '', 0, 0, 'default.tpl', 'default.tpl', 'NORMAL', 'FALSE', 'TRUE', 'PERMANENT', __NOW__, 0, 0, __NOW__, 'TRUE', 'keywords', 'description', 'description', 1, 3),
(4, 'qketyznhk4fl9ndn63dgoklilh6hgucj', 1, NULL, 'tagged-content', 'Tagged Content',            '', 1, '', '', 0, 0, 'default.tpl', 'default.tpl', 'NORMAL', 'FALSE', 'TRUE', 'PERMANENT', __NOW__, 0, 0, __NOW__, 'TRUE', 'keywords', 'description', 'description', 1, 4);

TRUNCATE TABLE `Sites`;
INSERT INTO `Sites` (`site_id`, `site_name`, `site_is_enabled`, `site_title_format`, `site_domain`, `site_root`, `site_automatic_urls`, `site_error_title`, `site_error_tpl`, `site_admin_email`, `site_top_page_id`, `site_search_page_id`, `site_error_page_id`, `site_logo_image_file`, `site_tag_page_id`) VALUES (1, '__TITLE__', 1, '$site | $page', '__DOMAIN__', '__SITE_ROOT__', 'OFF', 'Requested Page Not Found', 'default.tpl', '__EMAIL__', 1, 2, 3, 'default.jpg', 4);

TRUNCATE TABLE `UserTokens`;
INSERT INTO `UserTokens` (`token_id`, `token_type`, `token_code`, `token_description`) VALUES (1, 'permission', 'create_remove_settings', 'Create, remove, and edit settings themselves.'),
(2, 'permission', 'modify_system_settings', 'Modify values of system settings.'),
(3, 'permission', 'add_new_pages', 'Add pages to the sitemap.'),
(4, 'permission', 'remove_pages', 'Remove pages from the sitemap.'),
(5, 'permission', 'create_remove_models', 'Create, remove, and edit item classes.'),
(6, 'permission', 'create_remove_properties', 'Create, remove and edit item class properties.'),
(7, 'permission', 'add_items', 'Add items to existing item classes.'),
(8, 'permission', 'delete_items', 'Delete items from existing item classes.'),
(9, 'permission', 'modify_page_properties', 'Modify the properties of pages, such as title, URLs, and meta information.'),
(10, 'permission', 'modify_draft_pages', 'Modify draft versions of pages in the content management system.'),
(11, 'permission', 'approve_page_changes', 'Approve pages marked as ready for approval.'),
(12, 'complexity', 'see_advanced_page_edit_ui', 'If present, the user will see an advanced "tree" page edit interface by default. If absent, the user sees a simplified one.'),
(18, 'permission', 'publish_all_pages', 'Publish changed pages even if they have not been approved.'),
(13, 'permission', 'modify_user_permissions', 'Edit the permissions of other users.'),
(14, 'permission', 'modify_items', 'Modify CMS Items that do not belong to the user.'),
(15, 'permission', 'approve_item_changes', 'Approve changes to items that do not belong to the user.'),
(16, 'permission', 'publish_approved_pages', 'Publish changes to pages that have been modified, and those modifications accepted.'),
(17, 'permission', 'publish_approved_items', 'Publish changes to items that have been modified, and those modifications accepted.'),
(19, 'permission', 'publish_all_items', 'Publish changed items even if they have not been approved.'),
(20, 'permission', 'modify_site_parameters', 'Edit site parameters.'),
(21, 'permission', 'site_access', 'To see the contents of a site; to "open" it in order to work on it.'),
(22, 'permission', 'modify_other_user_details', 'Edit the details of other users.'),
(23, 'permission', 'grant_site_access', 'Grant access to a site to another user.'),
(24, 'permission', 'grant_global_permissions', 'Permission to grant permissions that will persist across all sites.'),
(25, 'permission', 'create_sites', 'Allows the user to create new sites.'),
(26, 'permission', 'modify_assets', 'Allows the user to modify existing media assets.'),
(27, 'permission', 'delete_assets', 'Allows the user to delete media assets.'),
(28, 'permission', 'create_assets', 'Allows the user to add new media to the system.'),
(29, 'permission', 'clear_pages_cache', 'Allows the user to clear the pages cache.');

TRUNCATE TABLE `UsersTokensLookup`;
INSERT INTO `UsersTokensLookup` (`utlookup_id`, `utlookup_user_id`, `utlookup_token_id`, `utlookup_site_id`, `utlookup_is_global`, `utlookup_granted_timestamp`) VALUES (1, 1, 28, 1, 0, __NOW__),
(2, 1, 11, 1, 0, __NOW__),
(3, 1, 10, 1, 0, __NOW__),
(4, 1, 19, 1, 0, __NOW__),
(5, 1, 9, 1, 0, __NOW__),
(6, 1, 8, 1, 0, __NOW__),
(7, 1, 7, 1, 0, __NOW__),
(8, 1, 2, 1, 0, __NOW__),
(9, 1, 5, 1, 0, __NOW__),
(10, 1, 4, 1, 0, __NOW__),
(11, 1, 3, 1, 0, __NOW__),
(12, 1, 1, 1, 0, __NOW__),
(13, 1, 6, 1, 0, __NOW__),
(14, 1, 13, NULL, 1, __NOW__),
(15, 1, 12, 1, 0, __NOW__),
(16, 1, 12, 1, 0, __NOW__),
(17, 1, 16, 1, 0, __NOW__),
(18, 1, 14, 1, 0, __NOW__),
(19, 1, 18, 1, 0, __NOW__),
(20, 1, 23, 1, 0, __NOW__),
(21, 1, 20, 1, 0, __NOW__),
(22, 1, 24, 1, 0, __NOW__),
(23, 1, 22, 1, 0, __NOW__),
(24, 1, 21, 1, 0, __NOW__),
(25, 1, 29, 1, 0, __NOW__),
(26, 1, 17, 1, 0, __NOW__),
(27, 1, 15, 1, 0, __NOW__),
(28, 1, 25, NULL, 1, __NOW__);