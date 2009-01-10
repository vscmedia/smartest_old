<h3 id="pageName">Page Details: {$pageInfo.static_title}</h3>

<form id="getForm" method="get" action="">
  <input type="hidden" name="page_id" value="{$pageInfo.id}">
  <input type="hidden" name="page_webid" value="{$pageInfo.webid}">
  <input type="hidden" name="current_url" value="{$pageurl.pageurl_url}">
</form>

<div class="instruction">Edit page meta information.</div>

<form id="updatePage" name="updatePage" action="{$domain}{$section}/updatePage" method="post" style="margin:0px">
  
  <input type="hidden" name="page_id" value="{$pageInfo.id}">
  <input type="hidden" name="page_webid" value="{$pageInfo.webid}">

<div id="edit-form-layout">
  
  <div class="edit-form-row">
    <div class="form-section-label">Type</div>
    {if $pageInfo.type == "ITEMCLASS"}Object Meta-Page{else}Regular Web-Page{/if}
  </div>
  
  {if $pageInfo.type == "ITEMCLASS"}
  {* <div class="edit-form-row">
    <div class="form-section-label">Data Set</div>
    &quot;{$pageInfo.set_name}&quot;
  </div> *}
  
  <div class="edit-form-row">
    <div class="form-section-label">Object Model</div>
    &quot;{$pageInfo.model_name}&quot;
  </div>
  {/if}
  
  <div class="edit-form-row">
    <div class="form-section-label">Title</div>
    	<input type="text" name="page_title" value="{$pageInfo.static_title}" style="width:200px" />
    	{if !$pageInfo.title}<div>You must have a title! </div>{/if}
  </div>
  
  {if $pageInfo.type == "ITEMCLASS"}
  <div class="edit-form-row">
    <div class="form-section-label">Always use page name</div>
    <input type="checkbox" name="page_force_static_title" id="page_force_static_title" value="true"{if $pageInfo.force_static_title=='1'} checked="checked"{/if} />
    <label for="page_force_static_title">{if $pageInfo.force_static_title=='1'}Un-tick this box to make this meta-page have the title of the {$pageInfo.model_name} that is being displayed.{else}Tick this box to make sure this meta-page keeps the title above, instead of the {$pageInfo.model_name} that is being displayed.{/if}</label>
  </div>
  {/if}
  
  <div class="edit-form-row">
    <div class="form-section-label">Name</div>
    {$pageInfo.name}
  </div>
  
  <div class="edit-form-row">
    <div class="form-section-label">Status</div>
      <div style="display:inline" class="text">
    	{if $pageInfo.is_published == "TRUE"}
    	  <strong>Live</strong> - Last Published {convert_timestamp format="h:i a, l jS F, Y" time=$pageInfo.last_published}
    	  <input type="button" onclick="window.location='{$domain}{$section}/unPublishPage?page_id={$pageInfo.webid}'" value="Un-Publish">
    	{else}
    	  {if $pageInfo.last_published == 0 }
    	  	<strong>Never Published</strong>
    	  {else}
    	    <strong>Not Published</strong> <a href="{$domain}{$section}/pageAssets?page_id={$pageInfo.webid}">Go To Page Tree</a>
    	  {/if}
    	{/if}</div>
  </div>
  
  {if $pageInfo.type == "NORMAL"}
  <div class="edit-form-row">
    <div class="form-section-label">Section</div>
    <input type="checkbox" name="page_is_section" id="page_is_section" value="true"{if $pageInfo.is_section=='1'} checked="checked"{/if} />
    <label for="page_is_section">{if $pageInfo.is_section=='1'}Un-tick this box to make this page no longer a section{else}Tick this box to make this page a section{/if}</label>
  </div>
  {/if}
  
  <div class="edit-form-row">
    <div class="form-section-label">Cache as Static HTML</div>
    <input type="radio" name="page_cache_as_html" id="page_cache_as_html_on" value="TRUE"{if $pageInfo.cache_as_html == "TRUE"} checked="checked"{/if} />&nbsp;<label for="page_cache_as_html_on">Yes please</label>
    <input type="radio" name="page_cache_as_html" id="page_cache_as_html_off" value="FALSE"{if $pageInfo.cache_as_html == "FALSE"} checked="checked"{/if} />&nbsp;<label for="page_cache_as_html_off">No, thanks</label>
  </div>
  
  {if $pageInfo.cache_as_html == "TRUE"}
  <div class="edit-form-row">
    <div class="form-section-label">Cache How Often?</div>
    	<select name="page_cache_interval" style="width:300px">
    	  <option value="PERMANENT"{if $pageInfo.cache_as_html=='PERMANENT'} selected="selected"{/if}>Stay Cached Until Re-Published</option>
    	  <option value="MONTHLY"{if $pageInfo.cache_as_html=='MONTHLY'} selected="selected"{/if}>Every Month</option>
    	  <option value="DAILY"{if $pageInfo.cache_as_html=='DAILY'} selected="selected"{/if}>Every Day</option>
    	  <option value="HOURLY"{if $pageInfo.cache_as_html=='HOURLY'} selected="selected"{/if}>Every Hour</option>
    	  <option value="MINUTE"{if $pageInfo.cache_as_html=='MINUTE'} selected="selected"{/if}>Every Minute</option>
    	  <option value="SECOND"{if $pageInfo.cache_as_html=='SECOND'} selected="selected"{/if}>Every Second</option>
    	</select>
  </div>
  {/if}
  
  {if $pageInfo.id > 0}
  <div class="edit-form-row">
    <div class="form-section-label">Address</div>
		
	  <table width="100%" style="border:1px solid #ccc;padding:2px;" cellpadding="0" cellspacing="0">
  	  {if $ishomepage == "true"}
    	<tr style="background-color:#{cycle values="ddd,fff"};height:20px"><td>
    		<div style="display:inline" id="siteDomainField_0">
    		  <a href="http://{$site.domain}/" target="_blank">http://{$site.domain}/</a></div>
    	</td>
    	<td>&nbsp;</td>
      </tr>{/if}
      
  	  {if !empty($pageurls)}
  	  {foreach from=$pageurls item=pageurl}
  	  {capture name="pageUrl" assign="pageUrl"}http://{$site.domain}/{$pageurl.url}{/capture}
  	  <tr style="background-color:#{cycle values="ddd,fff"};height:20px">
  	    <td>
  		    <div style="display:inline" id="siteDomainField_{$pageurl.id}">
  		      {if $pageInfo.is_published == "TRUE"}<a href="{$pageUrl}" target="_blank">{$pageUrl|truncate:100:"..."}</a>{else}{$pageUrl|truncate:100:"..."}{/if}</div></td>
  	    <td>
  		    <input type="button" name="edit" value="Edit" onclick="window.location='{$domain}{$section}/editPageUrl?page_id={$pageInfo.webid}&amp;url={$pageurl.id}&amp;ishomepage={$ishomepage}'" />
  		    <input type="button" name="mkdefault" value="Make Default" onclick="window.location='{$domain}{$section}/setPageDefaultUrl?page_id={$pageInfo.webid}&amp;url={$pageurl.id}'"{if $pageurl.is_default == 1} disabled="disabled"{/if} />
  		    {if $count > 1 || $ishomepage == "true"}<input type="button" name="delete" value="Delete" onclick="if(confirm('Are you sure you want to delete this URL?')) window.location='{$domain}{$section}/deletePageUrl?page_id={$pageInfo.webid}&amp;url={$pageurl.id}&amp;ishomepage={$ishomepage};'"/>{/if}</td></tr> 
      {/foreach}
	    {else}
      {capture name="defaultUrl" assign="defaultUrl"}http://{$site.domain}/website/renderPageFromId?page_id={$pageInfo.webid}{/capture}
      <tr style="background-color:#{cycle values="ddd,fff"};height:20px">
        <td>
          <div style="display:inline" id="siteDomainField">
          {if $pageInfo.is_published == "TRUE"}<a href="{$defaultUrl}" target="_blank">{$defaultUrl|truncate:100:"..."}</a>{else}{$defaultUrl|truncate:100:"..."}{/if}</div></td>
  	    <td></td></tr>{/if}
  	</table>
	
  	<a href="{$domain}{$section}/addPageUrl?page_id={$pageInfo.webid}&amp;ishomepage={$ishomepage}">{if !empty($pageurls)}Add Another Url{else}Give This Page A Nicer Url{/if}</a><br />
  	<img src="{$domain}Resources/Images/spacer.gif" width="1" height="10" />
  </div>
  
  <div class="edit-form-row">
    <div class="form-section-label">Page Icon</div>
    <span>This image can be used when referring to a page from another page.</span><br />
    <select name="page_icon_image">
      <option value="">None</option>
      {foreach from=$available_icons item="icon"}
      <option value="{$icon}"{if $pageInfo.icon_image == $icon} selected="selected"{/if}>{$icon}</option>
      {/foreach}
    </select>
  </div>
  
  {if $ishomepage != "true"}
  <div class="edit-form-row">
    <div class="form-section-label">Parent Page</div>
    <select name="page_parent" style="width:300px">
      {foreach from=$parent_pages item="page"}
        {if $page.id != $pageInfo.id}
        <option value="{$page.info.id}"{if $pageInfo.parent == $page.info.id} selected="selected"{/if}>+{section name="dashes" loop=$page.treeLevel}-{/section} {$page.info.title}</option>
        {/if}
      {/foreach}
    </select>
  </div>
  
    {if $show_parent_meta_page_property_control}
  
  <div class="edit-form-row">
    <div class="form-section-label">Parent Meta-Page Item Source</div>
    {if $parent_mpp_control_type == 'dropdown'}
    <select name="page_parent_data_source" style="width:300px">
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
  
  {if $pageInfo.type == 'NORMAL'}
  
  <div class="edit-form-row">
    <div class="form-section-label">Search terms</div>
      <textarea name="page_search_field" style="width:500px;height:60px">{$pageInfo.search_field}</textarea>
  </div>
  
  <div class="edit-form-row">
    <div class="form-section-label">Page Description</div>
      <textarea name="page_description" style="width:500px;height:60px">{$pageInfo.description}</textarea>
  </div>
  
  <div class="edit-form-row">
    <div class="form-section-label">Meta Description</div>
      <textarea name="page_meta_description" style="width:500px;height:60px">{$pageInfo.meta_description}</textarea>
  </div>
    
  <div class="edit-form-row">
      <div class="form-section-label">Meta Keywords</div>
      <textarea name="page_keywords" style="width:500px;height:100px">{$pageInfo.keywords}</textarea>
    </div>
    
  {/if}
    
  <div class="edit-form-row">
    <div class="buttons-bar">
      <input type="button" value="Cancel" onclick="cancelForm();" />
      <input type="submit" name="action" value="Save Changes" />
    	{* <input type="button" value="Done" onclick="cancelForm();" />
		  <input type="button" value="Done" onclick="window.location='{$domain}{$section}/getSite" />
    	<input type="submit" name="action" value="Publish" disabled /> *}
    </div>
  </div>
  
</div>

</form>