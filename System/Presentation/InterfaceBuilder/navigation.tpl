<div id="admin-menu">
  <ul>
    {if $show_left_nav_options}
    <li class="site-top-level{if $section == "desktop" && $method != 'aboutSmartest'} on{else} off{/if}"><a href="{$domain}smartest" style="float:left">{$sm_currentSite.title|summary:"17"}</a> <a href="{$domain}desktop/closeCurrentSite" style="float:right;padding-right:3px">(exit)</a></li>
    <li class="top-level{if $section == "websitemanager"} on{else} off{/if}"><a href="{$domain}smartest/pages">Pages</a></li>
    <li class="top-level{if $section == "datamanager" || $section == "sets"} on{else} off{/if}"><a href="{$domain}smartest/models">Items</a></li>
    <li class="top-level{if $section == "metadata" || $section == "dropdowns"} on{else} off{/if}"><a href="{$domain}smartest/metadata">Meta-data</a></li>
    <li class="top-level{if $section == "assets"} on{else} off{/if}"><a href="{$domain}smartest/assets">Files</a></li>
    <li class="top-level{if $section == "templates"} on{else} off{/if}"><a href="{$domain}smartest/templates">Templates</a></li>
    {else}
    <li class="site-top-level{if $section == "desktop" && $method != 'aboutSmartest'} on{else} off{/if}"><a href='{$domain}smartest' >Your websites</a></li>
    {/if}
    <li class="break top-level{if $section == "desktop" && $method == 'aboutSmartest'} on{else} off{/if}"><a href='{$domain}desktop/aboutSmartest'>About Smartest</a></li>
    <li class="top-level off"><a href='{$domain}smartest/logout'>Sign out</a></li>
  </ul>
</div>

{* <!--
<div class="top-nav">
  <ul class="top-nav-buttons">
    <li><a href="{$domain}smartest" class="top-nav-link"><img src="{$domain}Resources/Icons/nav-home.gif" border="0" alt="Home" class="top-nav-icon" /></a></li>
    <li><a href="{$domain}smartest/logout" class="top-nav-link"><img src="{$domain}Resources/Icons/nav-home.gif" border="0" alt="Log Out" class="top-nav-icon" /></a></li>
  </ul>
  <img src="http://universal.visudo.com/images/logos/smartest-small.gif" id="smartest_logo" alt="Smartest" style="float:right;margin:10px;" border="0" />
</div>
--> *}
