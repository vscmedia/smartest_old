<script language="javascript">
{literal}

function setMode(mode){

	document.getElementById('transferAction').value=mode;

	if(mode == "add"){
		document.getElementById('add_button').disabled=false;
		document.getElementById('remove_button').disabled=true;
		
	}else if(mode == "remove"){
		document.getElementById('add_button').disabled=true;
		document.getElementById('remove_button').disabled=false;
		formList = document.getElementById('used_items');
	}	
	
}

function executeTransfer(){
	document.transferForm.submit();
}

{/literal}
</script>

<div id="work-area">

{load_interface file="edit_set_tabs.tpl"}

<h3><a href="{$domain}smartest/models">Items</a> &gt; {if $model.id}<a href="{$domain}datamanager/getItemClassMembers?class_id={$model.id}">{$model.plural_name}</a> &gt; <a href="{$domain}sets/getItemClassSets?class_id={$model.id}">Sets</a>{else}<a href="{$domain}smartest/sets">Sets</a>{/if} &gt; <span id="set-label-h3">{$set.label}</span></h3>

<div class="edit-form-row">
  <div class="form-section-label">Set label</div>
  <p class="editable" id="set-label">{$set.label}</p>
  <script type="text/javascript">
  new Ajax.InPlaceEditor('set-label', sm_domain+'ajax:sets/updateSetLabelFromInPlaceEditField', {ldelim}
    callback: function(form, value) {ldelim}
      return 'set_id={$set.id}&new_label='+encodeURIComponent(value);
    {rdelim},
    onComplete: function(t, e){ldelim}
      $('set-label-h3').update($('set-label').innerHTML);
    {rdelim},
    highlightColor: '#ffffff',
    hoverClassName: 'editable-hover',
    savingClassName: 'editable-saving'
  {rdelim});
  </script>
</div>

<div class="instruction">Use the arrow buttons below to move {$model.plural_name|lower} in and out of this set.</div>

<form action="{$domain}sets/transferItem" method="post" name="transferForm">
  
  <input type="hidden" id="transferAction" name="transferAction" value="" /> 
  <input type="hidden" name="set_id" value="{$set.id}" />
  
  <table width="100%" border="0" cellpadding="0" cellspacing="5" style="border:1px solid #ccc">
    <tr>
      <td align="center">
      <div style="text-align:left">{$model.plural_name} that <strong>aren't</strong> in this set</div>

		<select name="available_items[]"  id="available_items" size="2" multiple style="width:270px; height:300px;"  onclick="setMode('add')">
    
    {foreach from=$non_members key="key" item="item"}
		<option value="{$item.id}" >{$item.name}</option>
		{/foreach}
		
		</select>
		
		</td>
     <td valign="middle" style="width:40px">
		<input type="button" value="&gt;&gt;" id="add_button" disabled="disabled" onclick="executeTransfer();" /><br /><br />
    <input type="button" value="&lt;&lt;" id="remove_button" disabled="disabled" onclick="executeTransfer();" />
     </td>
     <td align="center">
        <div style="text-align:left">{$model.plural_name} that <strong>are</strong> in this set</div>
 	<select name="used_items[]"  id='used_items' size="2" multiple style="width:270px; height:300px" onclick="setMode('remove')" >	
	  {foreach from=$members key="key" item="item"}
		<option value="{$item.id}" >{$item.name}</option>
		{/foreach}
        </select>
	</td>
   </tr>
</table>
</form>

</div>

<div id="actions-area">
		
		<ul class="actions-list">
		  <li><b>Options</b></li>
            <li class="permanent-action"><a href="#" onclick="window.location='{$domain}{$section}/previewSet?set_id={$set.id}'"><img border="0" src="{$domain}Resources/Icons/folder_explore.png"> Browse set contents</a></li>
            <li class="permanent-action"><a href="#" onclick="window.location='{$domain}{$section}/deleteSetConfirm?set_id={$set.id}'"><img border="0" src="{$domain}Resources/Icons/cross.png"> Delete this set</a></li>
			<li class="permanent-action"><a href="#" onclick="window.location='{$domain}{$section}/editStaticSetOrder?set_id={$set.id}'"><img border="0" src="{$domain}Resources/Icons/arrow_switch.png"> Change the order of this set</a></li>
			<li class="permanent-action">{if $model.id}<a href="#" onclick="window.location='{$domain}{$section}/getItemClassSets?class_id={$model.id}'"><img border="0" src="{$domain}Resources/Icons/package_add.png"> Browse sets of {$model.plural_name|strtolower}</a>{else}<a href="#" onclick="window.location='{$domain}smartest/sets'"><img border="0" src="{$domain}Resources/Icons/package_add.png"> Back to data sets</a></li>{/if}		
			<li class="permanent-action"><a href="#" onclick="window.location='{$domain}smartest/models'"><img border="0" src="{$domain}Resources/Icons/package_small.png"> Browse all items</a></li>
			{* <li class="permanent-action"><a href="#" onclick="window.location='{$domain}{$section}/chooseSchemaForExport?set_id={$set.id}'"><img border="0" src="{$domain}Resources/Icons/package_add.png"> Export this Set as XML</a></li> *}
		</ul>
		
</div>