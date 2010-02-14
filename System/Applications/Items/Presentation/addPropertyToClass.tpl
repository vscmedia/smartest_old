<script type="text/javascript">
var customVarName = false;

{literal}
function setVarName(){
	if(document.getElementById('itemproperty_varname').value.length < 1){customVarName = false}
	
	var propertyName = document.getElementById('itemproperty_name').value;
	
	if(!customVarName){
		document.getElementById('itemproperty_varname').value = smartest.toVarName(propertyName);
	}
}
{/literal}
</script>

<div id="work-area">

<h3><a href="{$domain}smartest/models">Items</a> &gt; <a href="{$domain}{$section}/getItemClassMembers?class_id={$model.id}">{$model.plural_name}</a> &gt; Add Property</h3>

<div class="instruction">You will be adding a new property to the model "{$model.plural_name}".</div>
  
  <form id="type_chooser" action="{$domain}{$section}/addPropertyToClass" method="get">
    
    <input type="hidden" name="class_id" value="{$model.id}" />
    <input type="hidden" name="continue" value="{$continue}" />
    
    <div class="edit-form-row">
      <div class="form-section-label">Property Type</div>
      <select name="itemproperty_datatype" id='itemproperty_datatype' onchange="$('type_chooser').submit()">
  	    <option value="">Select a type</option>
  	    {foreach from=$data_types item="data_type"}
  	    <option value="{$data_type.id}"{if $data_type.id==$property.datatype} selected="selected"{/if}>{$data_type.label}</option>
  	    {/foreach}
      </select>
    </div>
    
  </form>
  
  {if $show_full_form}
  
  <form action="{$domain}{$section}/insertItemClassProperty" method="post">
    
    <input type="hidden" name="class_id" value="{$model.id}" />
    <input type="hidden" name="itemproperty_datatype" value="{$property.datatype}" />

    <div class="edit-form-row">
      <div class="form-section-label">Property Name</div>
      <input type="text" value="" name="itemproperty_name" id="itemproperty_name" />
      <span class="form-hint">Property names must be three chars or longer and start with a letter.</span>
    </div>
    
{if $foreign_key_filter_select}
    <div class="edit-form-row">
      {include file=$filter_select_template}
    </div>
{/if}
    
    <div class="edit-form-row">
      <div class="form-section-label">Property Required:</div>
      <input type="checkbox" name="itemproperty_required" id="is-required" value="TRUE" /><label for="is-required">Check if this property is required</label>
    </div>
    
    <div class="edit-form-row">
      <div class="buttons-bar">
        Continue to: <select name="continue"><option value="PROPERTIES"{if $continue == "PROPERTIES"} selected="selected"{/if}>View other properties of model {$model.name}</option><option value="NEW_PROPERTY"{if $continue == "NEW_PROPERTY"} selected="selected"{/if}>Add another property to model {$model.name}</option></select>
        <input type="button" value="Cancel" onclick="window.location='{$domain}{$section}/getItemClassProperties?class_id={$model.id}';" />
        <input type="submit" value="Save Property" />
      </div>
    </div>

  </form>
  
  {else}
  
  <div class="edit-form-row">
    <div class="buttons-bar">
      <input type="button" value="Cancel" onclick="window.location='{$domain}{$section}/getItemClassProperties?class_id={$model.id}';" />
    </div>
  </div>
  
  {/if}

</div>