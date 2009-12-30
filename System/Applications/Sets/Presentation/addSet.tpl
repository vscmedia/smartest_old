<div id="work-area">
  
<h3><a href="{$domain}smartest/models">Items</a>  {if !$allow_choose_model} &gt; <a href="{$domain}datamanager/getItemClassMembers?class_id={$model.id}">{$model.plural_name}</a> &gt; <a href="{$domain}{$section}/getItemClassSets?class_id={$model.id}">Sets</a>{else} &gt; <a href="{$domain}smartest/sets">Sets</a>{/if} &gt; Create a new set</h3>
  
  <form id="pageViewForm" method="post" action="{$domain}{$section}/insertSet">
  
    <div class="edit-form-layout">
    
			<div class="edit-form-row">
				<div class="form-section-label">Set Name:</div>
				<input type="text" name="set_name" id="set_name" value="Untitled Set" />
			</div>
				
			<div class="edit-form-row">
				<div class="form-section-label">With items from model:</div>
				{if $allow_choose_model}
				<select name="set_model_id" id="model_select">
			    <option value="">Please Choose...</option>
			    {foreach from=$models key="key" item="model"}
				  <option {if $model.id == $content.model_id} selected{/if} value="{$model.id}">{$model.plural_name}</option>
				  {/foreach}
				</select>
				{else}
				<input type="hidden" name="set_model_id" value="{$model.id}" />
				{$model.plural_name}
				{/if}
			</div>
				
			<div class="edit-form-row">
				<div class="form-section-label">Set Type</div>
				<select  name="set_type" id="set_type" >
					  <option value="STATIC" {if $content.type == 'STATIC'} selected{/if}>Normal</option>
					  <option value="DYNAMIC" {if $content.type == 'DYNAMIC' } selected{/if} >Saved Query</option>
				</select>
			</div>
			
			<div class="edit-form-row">
			  <div class="form-section-label">Share this Set?</div>
			  <input type="checkbox" name="set_shared" /> Check here to make this set available to all sites.
			</div>
				
			<div class="edit-form-row">
				<div class="buttons-bar">
				  <input type="button" value="Cancel" onclick="cancelForm();" />
				  <input type="submit" value="Continue" />
				</div>
			</div>
		
		</div>

	</form>