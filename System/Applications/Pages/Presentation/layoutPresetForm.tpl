<script language="javascript">{literal}

function check(){
var editForm = document.getElementById('createLayoutPreset');
var element = document.getElementsByName('asset[]');
var flag = 'false';
for(i=0;i<element.length;i++){
    if(element[i].checked){
        flag= 'true';
    }
  }
if(editForm.layoutpresetname.value==''){
alert ('please enter the presetname');
alert (element);
editForm.layoutpresetname.focus();
return false;
}
elseif(flag==false){
  alert("Please check at least one box!");
  return false;
}
else 
return true;

}

{/literal}</script>

<div id="work-area">

<h3>Create a Page Preset</h3>

<a name="top"></a>

<div class="instruction">Click the checkbox corresponding to each asset to add it to preset</div>

<form id="createLayoutPreset" action="{$domain}{$section}/createLayoutPreset" method="post" style="margin:0px">
  
<input type="hidden" name="page_id" value="{$page.id}" />
{if $item}<input type="hidden" name="item_id" value="{$item.id}" />{/if}

<div class="instruction">Enter a name for this preset: <input type="text" name="preset_name" value="Untitled Preset" /></div>

<div style="margin-bottom:10px"><input type="checkbox" name="preset_shared" value="true" id="preset_shared" /><label for="preset_shared">Make this preset available to all sites</label></div>

<table width="100%" cellpadding="0" cellspacing="2" style="width:100%" border="0">

{if !empty($elements)}

{foreach from=$elements item="element" key="key"}
  
{if $element.info.exists == 'true' && in_array($element.info.type, array("container", "placeholder", "field"))}
  <tr>
    <td style="width:20px">
      <input type="checkbox" name="{$element.info.type}[]" value="{$element.info.assetclass_name}" id="element_{$key}" {if in_array($element.info.defined, array("PUBLISHED", "DRAFT")) }checked="checked"{else}disabled="disabled"{/if} />
    </td>
    
    <td>{$element.info.type}
{if $element.info.defined == "PUBLISHED"}
		  <img border="0" style="width:16px;height:16px;" src="{$domain}Resources/Icons/published_{$element.info.type|lower}.gif" />
{elseif  $element.info.defined == "DRAFT"}
      <img border="0" style="width:16px;height:16px;" title="This {$element.info.type} is only defined in the draft version of the page" src="{$domain}Resources/Icons/draftonly_{$element.info.type|lower}.gif" />
{else}
		  <img border="0" style="width:16px;height:16px;" title="This {$element.info.type} is only defined in the draft version of the page" src="{$domain}Resources/Icons/undefined_{$element.info.type|lower}.gif" />
{/if}
		  <label for="element_{$key}"><strong>{$element.info.assetclass_name}</strong></label>
	  </td>
	</tr>
{/if}

{/foreach}

{/if}

</table>

<div class="edit-form-row">
  <div class="buttons-bar">
    <input type="button" value="Cancel" onclick="cancelForm();" />
    <input type="submit" name="action" onclick= "return check();" value="Save" />
  </div>
</div>

</form>

</div>