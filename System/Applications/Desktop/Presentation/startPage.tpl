<div id="work-area">

{if $display == "desktop"}

{* <table cellpadding="0" cellspacing="0" width="100%" border="0" style="margin-bottom:10px">
  <tr style="height:60px">
    <td style="width:60px"><img src="{$domain}Resources/Images/SiteLogos/{$site.logo_image_file}" /></td>
    <td valign="top"></td>
  </tr>
</table> *}

<h3>Welcome to the {$site.name} administration interface</h3>

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

{if $num_sites > 0}

<h3>Welcome to Smartest!</h3>

<div class="instruction">Select a site to work with:</div>

<ul class="basic-list">
{foreach from=$sites item="site" key="key"}
{if isset($site.name) }
<li><a href="{$domain}{$section}/openSite?site_id={$site.id}">{$site.name} ({$site.domain})</a></li>
{/if}
{/foreach}
</ul>

{if $show_create_button}<a href="{$domain}{$section}/createSite">Create a new site</a>{/if}

{else}

{if $show_create_button}

<a href="{$domain}{$section}/createSite">Create a new site</a>

{else}

<div class="instruction" style="margin-bottom:10px">You haven't yet been granted access to any sites yet. Talk to your system administrator about which sites you should be given access to.</div>

{/if}

{/if}

{/if}

</div>