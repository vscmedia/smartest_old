  <h3>Create a New Page</h3>
  
  <div class="instruction">Step 2 of 3: Please fill out the details below</div>
  
  <form action="{$domain}{$section}/addPage" method="post">
  
    <input type="hidden" name="page_parent" value="{$page_parent}" />
    <input type="hidden" name="stage" value="3">
    <input type="hidden" name="page_type" value="NORMAL">
    
    <div id="edit-form-layout">
        
  	  <div class="edit-form-row">
  	    <div class="form-section-label">Title:</div>
  	    <input type="text" name="page_title" id="page_title" value="{$newPage.title}" />
  	  </div>
  	  
  	  <div class="edit-form-row">
  	    <div class="form-section-label">Address</div>
  	    http://{$siteInfo.domain}{$domain}<input type="text" name="page_url" id="page_url" value="{$newPage.url}" />
  	    {if $newPage.type == "ITEMCLASS"}<input type="button" value="&lt;&lt; Item URL Name" onclick="addField('page_url', 'name');" />{/if}
  	    {if $newPage.type == "ITEMCLASS"}<input type="button" value="&lt;&lt; Item Short ID" onclick="addField('page_url', 'id');" />{/if}
  	  </div>
  	
  	{if $newPage.type == "ITEMCLASS"}
  	
      <div class="edit-form-row">
  	    <div class="form-section-label">Select a Model</div>
  	    <select name="page_model">
  	      {foreach from=$models item="model"}
  	      <option value="{$model.id}"{if $newPage.dataset_id == $model.id} selected="selected"{/if}>{$model.plural_name}</option>
  	      {/foreach}
  	    </select>
  	  </div>
  	
  	{/if}
  	
  	{if $newPage.type == "TAG"}
  	
  	<div class="edit-form-row">
  	    <div class="form-section-label">Select a Tag</div>
  	    <select name="page_tag">
  	      {foreach from=$tags item="tag"}
  	      <option value="{$tag.id}"{if $newPage.dataset_id == $tag.id} selected="selected"{/if}>{$tag.label}</option>
  	      {/foreach}
  	    </select>
  	</div>
  	
  	{/if}
  	
  	<div class="edit-form-row">
      <div class="form-section-label">Cache as Static HTML</div>
      <input type="radio" name="page_cache_as_html" id="page_cache_as_html_on" value="TRUE"{if $newPage.cache_as_html == 'TRUE'} checked="checked"{/if} />&nbsp;<label for="page_cache_as_html_on">Yes please</label>
      <input type="radio" name="page_cache_as_html" id="page_cache_as_html_off" value="FALSE"{if $newPage.cache_as_html == 'FALSE'} checked="checked"{/if} />&nbsp;<label for="page_cache_as_html_off">No, thanks</label>
    </div>
  	
    <div class="edit-form-row">
      <div class="form-section-label">Cache How Often?</div>
      <select name="page_cache_interval" style="width:300px">
        <option value="PERMANENT"{if $newPage.cache_interval == 'PERMANENT'} selected="selected"{/if}>Stay Cached Until Re-Published</option>
        <option value="MONTHLY"{if $newPage.cache_interval == 'MONTHLY'} selected="selected"{/if}>Every Month</option>
        <option value="DAILY"{if $newPage.cache_interval == 'DAILY'} selected="selected"{/if}>Every Day</option>
        <option value="HOURLY"{if $newPage.cache_interval == 'HOURLY'} selected="selected"{/if}>Every Hour</option>
        <option value="MINUTE"{if $newPage.cache_interval == 'MINUTE'} selected="selected"{/if}>Every Minute</option>
        <option value="SECOND"{if $newPage.cache_interval == 'SECOND'} selected="selected"{/if}>Every Second</option>
      </select>
    </div>
  	
  	{if count($presets)}
  	  <div class="edit-form-row">
  	    <div class="form-section-label">Use Preset Layout</div>
  	    <select name="page_preset" onchange="{literal}if(this.value){document.getElementById('page_draft_template').disabled=true;}else{document.getElementById('page_draft_template').disabled=false;}{/literal}">
  	      {foreach from=$presets item="preset"}
  	      <option value="{$preset.plp_id}"{if $newPage.preset == $preset.plp_id} selected="selected"{/if}>{$preset.plp_label}</option>
  	      {/foreach}
  	      <option value="">No preset</option>
  	    </select>
  	  </div>
  	{/if}
  	
  	  <div class="edit-form-row">
  	    <div class="form-section-label">Main Template</div>
  	    <select name="page_draft_template" id="page_draft_template"{if $newPage.preset} disabled="true"{/if}>
  	      {foreach from=$templates item="template"}
  	      <option value="{$template.filename}"{if $newPage.draft_template == $template.filename} selected="selected"{/if}>{$template.filename}</option>
  	      {/foreach}
  	    </select>
  	  </div>
  	
    	<div class="edit-form-row">
        <div class="form-section-label">Search terms</div>
        <textarea name="page_search_field" style="width:500px;height:60px">{$newPage.search_field}</textarea>
      </div>
  	
    	<div class="edit-form-row">
        <div class="form-section-label">Page Description</div>
        <textarea name="page_description" style="width:500px;height:60px">{$newPage.meta_description}</textarea>
      </div>
  	
    	<div class="edit-form-row">
        <div class="form-section-label">Meta Description</div>
        <textarea name="page_meta_description" style="width:500px;height:60px">{$newPage.meta_description}</textarea>
      </div>
    
      <div class="edit-form-row">
        <div class="form-section-label">Meta Keywords</div>
        <textarea name="page_keywords" style="width:500px;height:100px">{$newPage.keywords}</textarea>
      </div>
    
      <div class="edit-form-row">
        <div class="buttons-bar"><input type="submit" value="Next &gt;&gt;" /></div>
      </div>
      
    </div>
  </form>