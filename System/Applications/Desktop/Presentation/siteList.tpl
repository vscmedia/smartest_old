<div id="work-area">
    
<h3>Your Websites</h3>

{if $num_sites > 0}

<div class="instruction" style="margin-bottom:10px">Select a site to work with:</div>

<ul class="basic-list">
{foreach from=$sites item="site" key="key"}
{if isset($site.name) }
<li><a href="{$domain}{$section}/openSite?site_id={$site.id}">{$site.name} ({$site.domain})</a></li>
{/if}
{/foreach}
</ul>

{else}

<div class="instruction" style="margin-bottom:10px">You haven't yet been granted access to any sites.</div>

{/if}

{if $show_create_button}<a href="{$domain}{$section}/createSite">Create a new site</a>{/if}

</div>