<h3 id="pageName">Page Details: {$page.static_title}</h3>

<form id="getForm" method="get" action="">
  <input type="hidden" name="page_id" value="{$page.id}">
  <input type="hidden" name="page_webid" value="{$page.webid}">
  <input type="hidden" name="current_url" value="{$pageurl.pageurl_url}">
</form>

<div class="instruction">Edit page meta information.</div>

{if $show_deleted_warning}
  <div class="warning">Warning: This page is currently in the trash.</div>
{/if}

<form id="updatePage" name="updatePage" action="{$domain}{$section}/updatePage" method="post" style="margin:0px">
  
  <input type="hidden" name="page_id" value="{$page.id}">
  <input type="hidden" name="page_webid" value="{$page.webid}">

<div id="edit-form-layout">
  
  <div class="edit-form-row">
    <div class="form-section-label">Type</div>
    {if $page.type == "ITEMCLASS"}Object Meta-Page{else}Regular Web-Page{/if}
  </div>
  
  {if $page.type == "ITEMCLASS"}
  {* <div class="edit-form-row">
    <div class="form-section-label">Data Set</div>
    &quot;{$page.set_name}&quot;
  </div> *}
  
  <div class="edit-form-row">
    <div class="form-section-label">Object Model</div>
    &quot;{$page.model.plural_name}&quot;
  </div>
  {/if}
  
  <div class="edit-form-row">
    <div class="form-section-label">Title</div>
    	<input type="text" name="page_title" value="{$page.static_title}" />
    	{if $page.type == "ITEMCLASS"}
    	  <span class="form-hint">This is only visible to the public if you check "Always use page name" below.</span>
    	{else}
    	  {if !$page.title}<div>You must have a title! </div>{/if}
    	{/if}
  </div>
  
  {if $page.type == "ITEMCLASS"}
  <div class="edit-form-row">
    <div class="form-section-label">Always use page name</div>
    <input type="checkbox" name="page_force_static_title" id="page_force_static_title" value="true"{if $page.force_static_title=='1'} checked="checked"{/if} />
    <label for="page_force_static_title">{if $page.force_static_title=='1'}Un-tick this box to make this meta-page have the title of the {$page.model.name|lower} that is being displayed.{else}Tick this box to make sure this meta-page keeps the title above, instead of the {$page.model.name|lower} that is being displayed.{/if}</label>
  </div>
  {/if}
  
  <div class="edit-form-row">
    <div class="form-section-label">Short Name</div>
    {if $allow_edit_page_name}<input type="text" name="page_name" value="{$page.name}" /><span class="form-hint">Numbers, lowercase letters and hyphens only, please</span>{else}{$page.name}{/if}
  </div>
  
  <div class="edit-form-row">
      <div class="form-section-label">Link code</div>
      <code>{$page.link_code}</code>
    </div>
  
  <div class="edit-form-row">
    <div class="form-section-label">Status</div>
      <div style="display:inline" class="text">
    	{if $page.is_published == "TRUE"}
    	  <strong>Live</strong> - Last Published {convert_timestamp format="h:i a, l jS F, Y" time=$page.last_published}
    	  <input type="button" onclick="window.location='{$domain}{$section}/unPublishPage?page_id={$page.webid}'" value="Un-Publish">
    	{else}
    	  {if $page.last_published == 0 }
    	  	<strong>Never Published</strong>
    	  {else}
    	    <strong>Not Published</strong> <a href="{$domain}{$section}/pageAssets?page_id={$page.webid}">Go To Page Tree</a>
    	  {/if}
    	{/if}</div>
  </div>
  
  {if $page.type == "NORMAL"}
  <div class="edit-form-row">
    <div class="form-section-label">Section</div>
    <input type="checkbox" name="page_is_section" id="page_is_section" value="true"{if $page.is_section=='1'} checked="checked"{/if} />
    <label for="page_is_section">{if $page.is_section=='1'}Un-tick this box to make this page no longer a section{else}Tick this box to make this page a section{/if}</label>
  </div>
  {/if}
  
  <div class="edit-form-row">
    <div class="form-section-label">Cache as Static HTML</div>
    <input type="radio" name="page_cache_as_html" id="page_cache_as_html_on" value="TRUE"{if $page.cache_as_html == "TRUE"} checked="checked"{/if} />&nbsp;<label for="page_cache_as_html_on">Yes please</label>
    <input type="radio" name="page_cache_as_html" id="page_cache_as_html_off" value="FALSE"{if $page.cache_as_html == "FALSE"} checked="checked"{/if} />&nbsp;<label for="page_cache_as_html_off">No, thanks</label>
  </div>
  
  {if $page.cache_as_html == "TRUE"}
  <div class="edit-form-row">
    <div class="form-section-label">Cache How Often?</div>
    	<select name="page_cache_interval" style="width:300px">
    	  <option value="PERMANENT"{if $page.cache_interval=='PERMANENT'} selected="selected"{/if}>Stay Cached Until Re-Published</option>
    	  <option value="MONTHLY"{if $page.cache_interval=='MONTHLY'} selected="selected"{/if}>Every Month</option>
    	  <option value="DAILY"{if $page.cache_interval=='DAILY'} selected="selected"{/if}>Every Day</option>
    	  <option value="HOURLY"{if $page.cache_interval=='HOURLY'} selected="selected"{/if}>Every Hour</option>
    	  <option value="MINUTE"{if $page.cache_interval=='MINUTE'} selected="selected"{/if}>Every Minute</option>
    	  <option value="SECOND"{if $page.cache_interval=='SECOND'} selected="selected"{/if}>Every Second</option>
    	</select>
  </div>
  {/if}
  
  {if $page.id > 0}
  <div class="edit-form-row">
    <div class="form-section-label">URL</div>
		
	  <table width="100%" style="border:1px solid #ccc;padding:2px;" cellpadding="0" cellspacing="0">
  	  
  	  {if $ishomepage == "true"}
    	<tr style="background-color:#{cycle values="ddd,fff"};height:20px">
    	  <td>
    		  <div style="display:inline" id="siteDomainField_0">
    		    <a href="http://{$site.domain}{$domain}" target="_blank">http://{$site.domain}{$domain}</a></div></td>
    	  <td>&nbsp;</td>
      </tr>
      {/if}
      
  	  {if count($page.urls)}
  	  
  	  {foreach from=$page.urls item=pageurl}
  	    {capture name="pageUrl" assign="pageUrl"}http://{$site.domain}{$domain}{$pageurl.url}{/capture}
  	  <tr style="background-color:#{cycle values="ddd,fff"};height:20px">
  	    <td>
  		    <div style="display:inline" id="siteDomainField_{$pageurl.id}">
  		      {if $page.is_published == "TRUE"}<a href="{$pageUrl}" target="_blank">{$pageUrl|truncate:100:"..."}</a>{else}{$pageUrl|truncate:100:"..."}{/if}</div></td>
  	    <td>
  		    <input type="button" name="edit" value="Edit" onclick="window.location='{$domain}{$section}/editPageUrl?url_id={$pageurl.id}'" />
  		    <input type="button" name="mkdefault" value="Make Default" onclick="window.location='{$domain}{$section}/setPageDefaultUrl?page_id={$page.webid}&amp;url={$pageurl.id}'"{if $pageurl.is_default == 1 || $pageurl.type == 'SM_PAGEURL_INTERNAL_FORWARD'} disabled="disabled"{/if} />
  		    {if count($page.urls) > 1 || $ishomepage == "true"}<input type="button" name="delete" value="Delete" onclick="if(confirm('Are you sure you want to delete this URL?')) window.location='{$domain}{$section}/deletePageUrl?page_id={$page.webid}&amp;url={$pageurl.id}&amp;ishomepage={$ishomepage};'"/>{/if}</td></tr> 
      {/foreach}
      
	    {else}
	    
      <tr style="background-color:#{cycle values="ddd,fff"};height:20px">
        <td>
          <div style="display:inline" id="siteDomainField">
          {if $page.is_published == "TRUE"}<a href="http://{$site.domain}{$domain}{$page.fallback_url}" target="_blank">http://{$site.domain}{$domain}{$page.fallback_url|truncate:100:"..."}</a>{else}http://{$site.domain}{$domain}{$page.fallback_url|truncate:100:"..."}{/if}</div></td>
  	    <td></td></tr>
  	    
  	  {/if}
  	  
  	</table>
	
  	<a href="{$domain}{$section}/addPageUrl?page_id={$page.webid}&amp;ishomepage={$ishomepage}">{if count($page.urls)}Add Another Url{else}Give This Page A Nicer Url{/if}</a><br />
  	<img src="{$domain}Resources/Images/spacer.gif" width="1" height="10" />
  </div>
  
  <div class="edit-form-row">
    <div class="form-section-label">Page Icon</div>
    <span>This image can be used when referring to a page from another page.</span><br />
    <select name="page_icon_image">
      <option value="">None</option>
      {foreach from=$available_icons item="icon"}
      <option value="{$icon}"{if $page.icon_image == $icon} selected="selected"{/if}>{$icon}</option>
      {/foreach}
    </select>
  </div>
  
  {if !$ishomepage}
  <div class="edit-form-row">
    <div class="form-section-label">Parent Page</div>
    <select name="page_parent">
      {foreach from=$parent_pages item="p_page"}
        {if $p_page.id != $page.id}
        <option value="{$p_page.info.id}"{if $page.parent == $p_page.info.id} selected="selected"{/if}>+{section name="dashes" loop=$p_page.treeLevel}-{/section} {$p_page.info.title}</option>
        {/if}
      {/foreach}
    </select>
  </div>
  
    {if $show_parent_meta_page_property_control}
  <div class="edit-form-row">
    <div class="form-section-label">Parent Meta-Page Item Source</div>
    {if $parent_mpp_control_type == 'dropdown'}
    <select name="page_parent_data_source">
      {if $show_self_option}<option value="_SELF"{if $parent_data_source_property_id == '_SELF'} selected="selected"{/if}>The same {$model.name} as on this page</option>{/if}
      {foreach from=$parent_meta_page_property_options item="parent_meta_page_property"}
      <option value="{$parent_meta_page_property.id}"{if $parent_data_source_property_id == $parent_meta_page_property.id} selected="selected"{/if}>Property: {$parent_meta_page_property.name} ({$parent_meta_page_property.selected_item_name})</option>
      {/foreach}
    </select>
    {elseif $parent_mpp_control_type == 'text'}
      {if $parent_meta_page_property == '_SELF'}
      Parent meta-page will use the same items as this page.
      {else}
      <input type="hidden" name="page_parent_data_source" value="{$parent_meta_page_property.id}" />
      Property: {$parent_meta_page_property.name} ({$parent_meta_page_property.selected_item_name})
      {/if}
    {/if}
  </div>
  
    {/if}
  
  {/if}
  
  {/if}
  
  {if $page.type == 'NORMAL'}
  
  <div class="edit-form-row">
    <div class="form-section-label">Search terms</div>
      <textarea name="page_search_field" style="width:500px;height:60px">{$page.search_field}</textarea>
  </div>
  
  <div class="edit-form-row">
    <div class="form-section-label">Page Description</div>
      <textarea name="page_description" style="width:500px;height:60px">{$page.description}</textarea>
  </div>
  
  <div class="edit-form-row">
    <div class="form-section-label">Meta Description</div>
      <textarea name="page_meta_description" style="width:500px;height:60px">{$page.meta_description}</textarea>
  </div>
    
  <div class="edit-form-row">
      <div class="form-section-label">Meta Keywords</div>
      <textarea name="page_keywords" style="width:500px;height:100px">{$page.keywords}</textarea>
    </div>
    
  {/if}
    
  <div class="edit-form-row">
    <div class="buttons-bar">
      <input type="button" value="Cancel" onclick="cancelForm();" />
      <input type="submit" name="action" value="Save Changes" />
    </div>
  </div>
  
</div>

</form>