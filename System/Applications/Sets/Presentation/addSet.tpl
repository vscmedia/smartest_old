<script language="javascript">

// var acceptable_suffixes = {$suffixes};
var input_mode = '{$starting_mode}';
var show_params_holder = false;
var itemNameFieldDefaultValue = '{$start_name}';

{literal}

document.observe('dom:loaded', function(){
    
    $('new-set-name').observe('focus', function(){
        if(($('new-set-name').getValue() == itemNameFieldDefaultValue)|| $('new-set-name').getValue() == ''){
            $('new-set-name').removeClassName('unfilled');
            $('new-set-name').setValue('');
        }
    });
    
    $('new-set-name').observe('blur', function(){
        if(($('new-set-name').getValue() == itemNameFieldDefaultValue) || $('new-set-name').getValue() == ''){
            $('new-set-name').addClassName('unfilled');
            $('new-set-name').setValue(itemNameFieldDefaultValue);
        }else{
            $('new-set-name').removeClassName('error');
        }
    });
    
    $('new-set-form').observe('submit', function(e){
        
        if(($('new-set-name').getValue() == itemNameFieldDefaultValue) || $('new-set-name').getValue() == ''){
            $('new-set-name').addClassName('error');
            e.stop();
        }
        
    });
    
});

{/literal}

</script>

<div id="work-area">
  
<h3><a href="{$domain}smartest/models">Items</a>  {if !$allow_choose_model} &gt; <a href="{$domain}datamanager/getItemClassMembers?class_id={$model.id}">{$model.plural_name}</a> &gt; <a href="{$domain}{$section}/getItemClassSets?class_id={$model.id}">Sets</a>{else} &gt; <a href="{$domain}smartest/sets">Sets</a>{/if} &gt; Create a new set</h3>
  
  <form id="new-set-form" method="post" action="{$domain}{$section}/insertSet">
      {if $add_item_id}<input type="hidden" name="add_item_id" value="{$add_item_id}" />{/if}
  
    <div class="edit-form-layout">
    
			<div class="edit-form-row">
				<div class="form-section-label">Set Name:</div>
				<input type="text" name="set_name" id="new-set-name" value="{$start_name}" class="unfilled" />
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