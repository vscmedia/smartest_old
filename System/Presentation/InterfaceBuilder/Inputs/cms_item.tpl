<div id="{$_input_data.id}-container{if $_input_data.show_new_field}-{$_input_data.host_item_id}{/if}">
  <select name="{$_input_data.name}" id="{$_input_data.id}">
      {if !$_input_data.required}<option value="0"></option>{/if}
  {foreach from=$_input_data.options item="foreign_item"}
      <option value="{$foreign_item.id}"{if $_input_data.value.id==$foreign_item.id} selected="selected"{/if}>{$foreign_item.name}</option>
  {/foreach}
      {if $_input_data.show_new_field}<option value="NEW">New...</option>{/if}
  </select>{if $_input_data.show_new_field}<span id="{$_input_data.id}-container-{$_input_data.host_item_id}-loading"></span>{/if}
  
  {if $_input_data.show_new_field}<div id="{$_input_data.id}-new-item-form-holder" style="padding-top:5px;display:none">
    <input type="text" name="{$_input_data.id}_new_item_name" id="{$_input_data.id}-new-item-name" />
    <input type="button" id="{$_input_data.id}-new-item-save-button" value="save" disabled="disabled" />
  </div>{/if}
  
</div>

{if $_input_data.show_new_field}<script type="text/javascript">
  $('{$_input_data.id}').observe('change', function(){ldelim}
    if(this.value == 'NEW'){ldelim}
      $('{$_input_data.id}-new-item-form-holder').show();
      $('{$_input_data.id}-new-item-name').activate();
    {rdelim}else{ldelim}
      $('{$_input_data.id}-new-item-form-holder').hide();
    {rdelim}
  {rdelim});
  
  $('{$_input_data.id}-new-item-save-button').observe('click', function(){ldelim}
    // submit
    new Smartest.IPVItemCreator({ldelim}name: $('{$_input_data.id}-new-item-name').value, property_id: '{$_input_data.property_id}', host_item_id: '{$_input_data.host_item_id}'{rdelim});
  {rdelim});
  
  $('{$_input_data.id}-new-item-name').observe('keyup', function(e){ldelim}
    
    if(this.value.charAt(1)){ldelim}
      $('{$_input_data.id}-new-item-save-button').disabled = false;
    {rdelim}else{ldelim}
      $('{$_input_data.id}-new-item-save-button').disabled = true;
    {rdelim}
      
  {rdelim});
  
  $('{$_input_data.id}-new-item-name').observe('keypress', function(e){ldelim}
    
    if(e.keyCode == 13){ldelim}
      if(this.value.charAt(1)){ldelim}
        // submit
        new Smartest.IPVItemCreator({ldelim}name: $('{$_input_data.id}-new-item-name').value, property_id: '{$_input_data.property_id}', host_item_id: '{$_input_data.host_item_id}'{rdelim});
        e.stop();
      {rdelim}else{ldelim}
        // do nothing
        e.stop();
      {rdelim}
    {rdelim}
  {rdelim});
  
</script>{/if}