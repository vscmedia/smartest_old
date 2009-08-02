<div id="admin-menu">
  <ul>
    {if $show_left_nav_options}
    <li{if $section == "desktop"} class="on"{else} class="off"{/if}><a href='{$domain}smartest' >Site Desktop</a></li>
    <li{if $section == "websitemanager"} class="on"{else} class="off"{/if}><a href='{$domain}smartest/pages' >Pages</a></li>
    <li{if $section == "datamanager" || $section == "sets"} class="on"{else} class="off"{/if}><a href='{$domain}smartest/models'>Items</a></li>
    <li{if $section == "metadata" || $section == "dropdowns"} class="on"{else} class="off"{/if}><a href='{$domain}smartest/metadata'>Meta-data</a></li>
    <li{if $section == "assets"} class="on"{else} class="off"{/if}><a href='{$domain}smartest/assets'>Files</a></li>
  	<li{if $section == "templates"} class="on"{else} class="off"{/if}><a href='{$domain}smartest/templates'>Templates</a></li>
    {else}
    <li{if $section == "desktop" && $method != 'editSite'} class="on"{else} class="off"{/if}><a href='{$domain}smartest' >Sites Menu</a></li>
    {/if}
    <li class="off"><a href='{$domain}smartest/logout'>Sign Out</a></li>
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
