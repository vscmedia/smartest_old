<script language="javascript">

var acceptable_suffixes = {$suffixes};
var input_mode = '{$starting_mode}';
var show_params_holder = false;

{literal}

function insertAssetClass(){
	var assetClassName = prompt("Enter the asset class name");
	var html = '{assetclass get="'+assetClassName+'"}';
	insertElement(html);
}

function insertElement(){
	var field = document.getElementById("tpl_textArea");
	field.focus();
	alert(field.value);
}

function toggleParamsHolder(){
  if(show_params_holder){
    new Effect.BlindUp('params-holder', {duration: 0.6});
    show_params_holder = false;
    $('params-holder-toggle-link').innerHTML = "Show Parameters";
  }else{
    new Effect.BlindDown('params-holder', {duration: 0.6});
    show_params_holder = true;
    $('params-holder-toggle-link').innerHTML = "Hide Parameters";
  }
}

function showUploader(){
	$('uploader').style.display = 'block';
	$('uploader_link').style.display = 'none';
	$('text_window').style.display = 'none';
	input_mode = 'upload';
	$('input_mode').value = input_mode;
	
}

function hideUploader(){
	$('uploader').style.display = 'none';
	$('uploader_link').style.display = 'block';
	$('text_window').style.display = 'block';
	input_mode = 'direct';
	$('input_mode').value = input_mode;
	$('tpl_textArea').disabled = false;
}

function validateUploadSuffix(){
	
  if(input_mode == 'upload'){
    
  }else{
    return true;
  }

}

{/literal}
</script>


<div id="work-area">
  
  <h3>Create a new {$new_asset_type_info.label|strtolower} file</h3>
  
  {if $require_type_selection}
  
    <div class="instruction">Please choose which type of file you would like to add{if $for=='placeholder'} to define this placeholder with{elseif $for=='ipv'} to define this property with{/if}</div>
    
    <form action="{$domain}smartest/file/new" method="get" id="file-type-form">
      <div class="edit-form-row">
        <select name="asset_type" id="file-type-select">
{foreach from=$types item="type"}
          <option value="{$type.id}">{$type.label}</option>
{/foreach}
        </select>
      </div>
    {if $for}
      {if $for=='placeholder'}
        <input type="hidden" name="for" value="placeholder" />
        <input type="hidden" name="placeholder_id" value="{$placeholder.id}" />
        <input type="hidden" name="page_id" value="{$page.id}" />
      {else if $for=='ipv'}
        <input type="hidden" name="for" value="ipv" />
        <input type="hidden" name="property_id" value="{$property.id}" />
        {if $item_id}<input type="hidden" name="item_id" value="{$item_id}" />{/if}
      {/if}
    {/if}
    
    <div class="buttons-bar"><input type="submit" value="Continue" /></div>
    
    </form>
  
  {else}
  
    {if $allow_save}
  
  <form action="{$domain}{$section}/saveNewAsset" method="post" name="newAsset" enctype="multipart/form-data">
    
    <input type="hidden" name="asset_type" value="{$type_code}" />
    <input type="hidden" name="MAX_FILE_SIZE" value="8000000" />
    <input type="hidden" name="input_mode" id="input_mode" value="{$starting_mode}" />
    
    {if $for=='placeholder'}
      <input type="hidden" name="for" value="placeholder" />
      <input type="hidden" name="placeholder_id" value="{$placeholder.id}" />
      <input type="hidden" name="page_id" value="{$page.id}" />
    {elseif $for=='ipv'}
      <input type="hidden" name="for" value="ipv" />
      <input type="hidden" name="property_id" value="{$property.id}" />
      {if $item}<input type="hidden" name="item_id" value="{$item.id}" />{/if}
    {/if}
    
      {if count($possible_groups)}
      <div id="groups" class="special-box">
    
            <div>
              Add this file to group:
                <select name="initial_group_id">
                  <option value="">None (for now)</option>
{foreach from=$possible_groups item="possible_group"}
                  <option value="{$possible_group.id}">{$possible_group.label}</option>
{/foreach}
                </select>
            </div>
    
      </div>
      {/if}
      
      {if $for=='placeholder'}
        <div class="instruction">The {$new_asset_type_info.label|strtolower} file you are creating will be used to define placeholder '{$placeholder.label}'.</div>
      {elseif $for=='ipv'}
        {if $item}
          <div class="instruction">The {$new_asset_type_info.label|strtolower} file you are creating will be used to define property '{$property.name}' of {$property._model.name|strtolower} '{$item.name}'.</div>
        {else}
          <div class="instruction">The {$new_asset_type_info.label|strtolower} file you are creating will be used as the value for property '{$property.name}' for a new {$property._model.name|strtolower}.</div>
        {/if}
      {/if}
    
      {load_interface file=$form_include}
    
      {if !empty($params)}<a id="params-holder-toggle-link" href="javascript:toggleParamsHolder()">Show Parameters</a>{/if}
    
    <div id="params-holder" style="display:none">
{foreach from=$params key="parameter_name" item="parameter_value"}
    <div class="edit-form-row">
      <div class="form-section-label">{$parameter_name}</div>
      <input type="text" name="params[{$parameter_name}]" />
    </div>
{/foreach}
    </div>
    
    <div class="edit-form-row">
      <div class="form-section-label">Language</div>
      <select name="asset_language">
        <option value="">{$lang.label}</option>
{foreach from=$_languages item="lang" key="langcode"}
        <option value="{$langcode}">{$lang.label}</option>
{/foreach}
      </select>
    </div>
    
    <div class="edit-form-row">
      <div class="form-section-label">Share this asset with other sites?</div>
      <input type="checkbox" name="asset_shared" /> Check here to allow all sites to use this file.
    </div>
    
    <div class="edit-form-row">
      <div class="buttons-bar">
        <input type="button" value="Cancel" onclick="cancelForm();">
        <input type="submit" value="Save" />
      </div>
    </div>
    
  </form>
  
    {else}
  
  <div class="warning">
    The directory <strong><code>{$path}</code></strong> is not writable by the web server, so <strong>{$new_asset_type_info.label}</strong> files cannot currently be created or uploaded via Smartest.
  </div>
  
    {/if}
    
  {/if}
  
</div>

<div id="actions-area">

</div>