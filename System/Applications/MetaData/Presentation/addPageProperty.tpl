<div id="work-area">
<h3 id="definePageProperty">Add Property</h3>

<form id="definePageProperty" name="definePageProperty" action="{$domain}{$section}/insertPageProperty" method="POST" style="margin:0px">
<input type="hidden" name="site_id" value="{$content.site_id}">

<div id="edit-form-layout">
  <div class="edit-form-row">
    <div class="form-section-label">Property Name: </div>
    <input type="text" name="property_name" value="{$content.name}" />
  </div>
{*<div class="edit-form-row">
    <div class="form-section-label">Property Type: </div>
    <select name="pageproperty_type" id='pageproperty_type'>
      <option value="">Select One</option>
	  {foreach from=$propertytypes item=propertyType}
      <option value="{$propertyType.propertytype_id}" {if $propertyType.propertytype_id==$content.type} selected{/if}>{$propertyType.propertytype_name}</option>
      {/foreach}
    </select>
  </div>*}
  <div class="edit-form-row">
    <div class="buttons-bar">
      <input type="button" value="Cancel" onclick="cancelForm();" />
      <input type="submit" name="action" value="Save" />
    </div>
  </div>
</div>

</form>

</div>
