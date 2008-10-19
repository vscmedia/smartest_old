<div id="work-area">
<h3 id="definePageProperty">Add a New Page Field</h3>

  <div id="edit-form-layout">
    
    <form id="type_chooser" action="{$domain}{$section}/addPageProperty" method="get" style="margin:0px">
      
      {if $field_name}<input type="hidden" name="name" value="{$field_name}" />{/if}
      
      <div class="edit-form-row">
        <div class="form-section-label">Field Type: </div>
        <select name="type" id="pageproperty_type" onchange="$('type_chooser').submit()">
          <option value="">Select A Type</option>
        	{foreach from=$property_types item="type"}
          <option value="{$type.id}"{if $type.id==$selected_type} selected="selected"{/if}>{$type.label}</option>
          {/foreach}
        </select>
      </div>
  
  {if !$show_full_form}
      <div class="edit-form-row">
        <div class="buttons-bar">
          <input type="button" value="Cancel" onclick="cancelForm();" />
        </div>
      </div>
  {/if}
  
    </form>

  {if $show_full_form}

    <form id="definePageProperty" name="definePageProperty" action="{$domain}{$section}/insertPageProperty" method="post" style="margin:0px">

      <input type="hidden" name="site_id" value="{$site_id}" />
      <input type="hidden" name="pageproperty_type" value="{$selected_type}" />

      <div class="edit-form-row">
        <div class="form-section-label">Field Name: </div>
        {if $field_name}<input type="hidden" name="property_name" value="{$field_name}" />{$field_name}{else}<input type="text" name="property_name" value="" />{/if}
      </div>
      
      {if $foreign_key_filter_select}
          <div class="edit-form-row">
            {include file=$filter_select_template}
          </div>
      {/if}
      
      <div class="edit-form-row">
        <div class="buttons-bar">
          <input type="button" value="Cancel" onclick="cancelForm();" />
          <input type="submit" name="action" value="Save" />
        </div>
      </div>

    </form>

  {/if}

  </div>

</div>
