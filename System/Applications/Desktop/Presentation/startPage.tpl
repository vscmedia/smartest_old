<div id="work-area">

{if $num_sites > 0 || $show_create_button}

<h3>Welcome to Smartest</h3>

{if $num_sites > 0}<div class="instruction">Select a site to work with.</div>{/if}

<ul class="apps">
{foreach from=$sites item="site" key="key"}
{if isset($site.name) }
  <li><a href="{$domain}smartest/site/open/{$site.id}" class="icon"{if $site.logo_image_asset_id != '0'} style="background-image:url({$site.logo.startpage_glossy.web_path})"{/if}>&nbsp;</a><br /><a class="label" href="{$domain}smartest/site/open/{$site.id}">{$site.internal_label}</a></li>
{/if}
{/foreach}
{* <li><a class="icon" id="new" href="{$domain}smartest/site/new"></a><br /><a class="label" href="{$domain}smartest/site/new">Create a new site</a></li> *}
</ul>

{else}

<h3>Welcome to Smartest</h3>
<div class="special-box" style="margin-bottom:10px">You haven't yet been granted access to any sites yet. Talk to your system administrator about which sites you should be given access to.</div>

{/if}

</div>