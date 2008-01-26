<div id="work-area">
  
<h3><a href="{$domain}datamanager">Data Manager</a> &gt; <a href="{$domain}smartest/sets">Sets</a> &gt; Create a new set</h3>
  
  <form id="pageViewForm" method="post" action="{$domain}{$section}/insertSet">
  
    <div class="edit-form-layout">
    
			<div class="edit-form-row">
				<div class="form-section-label">Set Name:</div>
				<input type="text" name="set_name" id="set_name" value="Untitled Set" />
			</div>
				
			<div class="edit-form-row">
				<div class="form-section-label">With items from model:</div>
				<select name="set_model_id" id="model_select" {* onchange="window.location='{$domain}{$section}/addSet?set_name='+document.getElementById('set_name').value+'&model_id=' + document.getElementById('model_select').value+'&type='+document.getElementById('set_type').value" *}>
			    <option value="">Please Choose...</option>
			    {foreach from=$models key=key item="model"}
				  <option {if $model.itemclass_id == $content.model_id} selected{/if} value="{$model.itemclass_id}">{$model.itemclass_plural_name}</option>
				  {/foreach}
				</select>
			</div>
				
			<div class="edit-form-row">
				<div class="form-section-label">Set Type</div>
				<select  name="set_type" id="set_type" {* onchange="window.location='{$domain}{$section}/addSet?set_name='+document.getElementById('set_name').value+'&type='+document.getElementById('set_type').value" *}>
					  <option value="">Please Choose...</option>
					  <option value="STATIC" {if $content.type == 'STATIC'} selected{/if}>Static (Folder)</option>
					  <option value="DYNAMIC" {if $content.type == 'DYNAMIC' } selected{/if} >Smart (Dynamic Saved Query)</option>
				</select>
			</div>
			
			<div class="edit-form-row">
			  <div class="form-section-label">Share this Set?</div>
			  <input type="checkbox" name="set_shared" /> Check here to make this set available to all sites.
			</div>
				
			<div class="edit-form-row">
				<div class="buttons-bar">
				  <input type="button" value="Cancel" />
					<input type="submit" value="Continue" />
				</div>
			</div>
		
		</div>

	</form>