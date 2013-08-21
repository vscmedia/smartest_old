<div id="admin-menu">
  <ul>
    {if $show_left_nav_options}
    {* <li class="site-top-level{if $section == "desktop" && $method != 'aboutSmartest'} on{else} off{/if}"><a href="{$domain}smartest" style="float:left">Home</a> <a href="{$domain}desktop/closeCurrentSite" id="site-exit-button"></a></li> *}
    <li class="top-level{if $section == "websitemanager"} on{else} off{/if}"><a href="{$domain}smartest/pages">{$_l10n_global_strings.main_nav.pages}</a></li>
    <li class="top-level{if $section == "datamanager" || $section == "sets"} on{else} off{/if}"><a href="{$domain}smartest/models">{$_l10n_global_strings.main_nav.items}</a></li>
    <li class="top-level{if $section == "assets"} on{else} off{/if}"><a href="{$domain}smartest/files">{$_l10n_global_strings.main_nav.files}</a></li>
    <li class="top-level{if $section == "templates"} on{else} off{/if}"><a href="{$domain}smartest/templates">{$_l10n_global_strings.main_nav.templates}</a></li>
    <li class="top-level{if $section == "users"} on{else} off{/if}"><a href="{$domain}smartest/users">{$_l10n_global_strings.main_nav.users}</a></li>
    <li class="top-level{if $section == "metadata" || $section == "dropdowns"} on{else} off{/if}"><a href="{$domain}smartest/metadata">{$_l10n_global_strings.main_nav.metadata}</a></li>
    {else}
    <li class="site-top-level{if $section == "desktop" && $method != 'aboutSmartest'} on{else} off{/if}"><a href='{$domain}smartest' >Your websites</a></li>
    {/if}
    <li class="break top-level{if $section == "desktop" && $method == 'aboutSmartest'} on{else} off{/if}"><a href='{$domain}smartest/about'>{$_l10n_global_strings.main_nav.about}</a></li>
    <li class="top-level off"><a href='{$domain}smartest/logout'>{$_l10n_global_strings.main_nav.sign_out}</a></li>
  </ul>
</div>