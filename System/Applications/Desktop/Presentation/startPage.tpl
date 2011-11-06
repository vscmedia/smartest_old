<div id="work-area">

{if $display == "desktop"}

{* <table cellpadding="0" cellspacing="0" width="100%" border="0" style="margin-bottom:10px">
  <tr style="height:60px">
    <td style="width:60px"><img src="{$domain}Resources/Images/SiteLogos/{$site.logo_image_file}" /></td>
    <td valign="top"></td>
  </tr>
</table> *}

<h3>Home</h3>

<div class="instruction">Welcome to the "<strong>{$site.internal_label}</strong>" web administration interface.</div>

<ul class="options-grid-no-scroll">
  <li><a href="{$domain}smartest/pages" class="option" id="option_1"><img border="0" src="{$domain}Resources/Icons/package.png" />Create and edit pages</a></li>
  <li><a href="{$domain}smartest/models" class="option" id="option_2"><img border="0" src="{$domain}Resources/Icons/package.png" />Create and edit items</a></li>
  <li><a href="{$domain}smartest/assets" class="option" id="option_3"><img border="0" src="{$domain}Resources/Icons/package.png" />Browse and upload files</a></li>
  <li><a href="{$domain}smartest/metadata" class="option" id="option_2"><img border="0" src="{$domain}Resources/Icons/package.png" />Meta data</a></li>
  <li><a href="{$domain}smartest/templates" class="option" id="option_5"><img border="0" src="{$domain}Resources/Icons/package.png" />Browse &amp; Add Templates</a></li>
  <li><a href="{$domain}smartest/todo" class="option" id="option_4"><img border="0" src="{$domain}Resources/Icons/package.png" />Go to Your Todo List</a></li>
  <li><a href="{$domain}smartest/users" class="option" id="option_6"><img border="0" src="{$domain}Resources/Icons/package.png" />Administer Users</a></li>
  <li><a href="{$domain}smartest/settings" class="option" id="option_7"><img border="0" src="{$domain}Resources/Icons/package.png" />Modify Settings</a></li>
</ul>

{elseif $display == 'sites'}

{if $num_sites > 0 || $show_create_button}

<h3>Welcome to Smartest</h3>

{if $num_sites > 0}<div class="instruction">Select a site to work with.</div>{/if}

<ul class="apps">
{foreach from=$sites item="site" key="key"}
{if isset($site.name) }
  <li><a href="{$domain}smartest/site/open/{$site.id}" class="icon"{if $site.logo_image_asset_id != '0'} style="background-image:url({$site.logo.startpage_glossy.web_path})"{/if}>&nbsp;</a><br /><a class="label" href="{$domain}smartest/site/open/{$site.id}">{$site.internal_label}</a></li>
{/if}
{/foreach}
{if $show_create_button}<li><a class="icon" id="new" href="{$domain}smartest/site/new"></a><br /><a class="label" href="{$domain}smartest/site/new">Create a new site</a></li>{/if}
</ul>

{else}

<h3>Welcome to Smartest</h3>
<div class="special-box" style="margin-bottom:10px">You haven't yet been granted access to any sites yet. Talk to your system administrator about which sites you should be given access to.</div>

{/if}

{/if}

</div>