<div id="admin-menu">
  <ul>
    {if $show_left_nav_options}
    <li class="site-top-level{if $section == "desktop" && $method != 'aboutSmartest'} on{else} off{/if}"><a href="{$domain}smartest" style="float:left">{$sm_currentSite.internal_label|summary:"17"}</a> <a href="{$domain}desktop/closeCurrentSite" id="site-exit-button"></a></li>
    <li class="top-level{if $section == "websitemanager"} on{else} off{/if}"><a href="{$domain}smartest/pages">Pages</a></li>
    <li class="top-level{if $section == "datamanager" || $section == "sets"} on{else} off{/if}"><a href="{$domain}smartest/models">Items</a></li>
    <li class="top-level{if $section == "assets"} on{else} off{/if}"><a href="{$domain}smartest/files">Files</a></li>
    <li class="top-level{if $section == "templates"} on{else} off{/if}"><a href="{$domain}smartest/templates">Templates</a></li>
    <li class="top-level{if $section == "users"} on{else} off{/if}"><a href="{$domain}smartest/users">Users</a></li>
    <li class="top-level{if $section == "metadata" || $section == "dropdowns"} on{else} off{/if}"><a href="{$domain}smartest/metadata">Meta-data</a></li>
    {else}
    <li class="site-top-level{if $section == "desktop" && $method != 'aboutSmartest'} on{else} off{/if}"><a href='{$domain}smartest' >Your websites</a></li>
    {/if}
    <li class="break top-level{if $section == "desktop" && $method == 'aboutSmartest'} on{else} off{/if}"><a href='{$domain}smartest/about'>About Smartest</a></li>
    <li class="top-level off"><a href='{$domain}smartest/logout'>Sign out</a></li>
  </ul>
</div>