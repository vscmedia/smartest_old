<div id="work-area">
<h3 id="definePageProperty">Define Page Property: {$field.name}</h3>


<form id="defineProperty" name="defineProperty" action="{$domain}{$section}/updatePagePropertyValue" method="POST" style="margin:0px">
<input type="hidden" name="page_id" value="{$page_id}">
<input type="hidden" name="field_id" value="{$field.id}">

<div id="edit-form-layout">
    
    {if $field.is_sitewide}<div class="special-box">This field is global, meaning the value you enter on any page will be the same for all pages</div>{/if}
    
    <div class="edit-form-row">
      <div class="form-section-label">Property Value:</div>
      {if $field_type == 'SM_DATATYPE_DROPDOWN_MENU'}
      <select name="field_content">
        {foreach from=$options item="option"}
        <option value="{$option.value}"{if $option.value == $value} selected="selected"{/if}>{$option.label}</option>
        {/foreach}
      </select>
      {else}
      <input type="text" name="field_content" value="{$value}" />
      {/if}
    </div>
  
    <div class="buttons-bar">
      <input type="submit" name="action" value="Save" />
      <input type="button" value="Cancel" onclick="cancelForm();" />
    </div>
  
</div>

</form>

</div>