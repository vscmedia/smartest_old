<div id="work-area">

{* <h3><a href="{$domain}smartest/models">Items</a> &gt; <a href="{$domain}{$section}/getItemClassMembers?class_id={$model.id}">{$model.plural_name}</a> &gt; Add a new {$model.name|strtolower}</h3> *}
<h3>Add a new {$model.name|strtolower}</h3>

<form action="{$domain}{$section}/insertItem" method="post" id="new-item-form">

<input type="hidden" name="class_id" value="{$model.id}" />

{if $request_parameters.for == 'ipv'}
<input type="hidden" name="for" value="ipv" />
<input type="hidden" name="property_id" value="{$request_parameters.property_id}" />
<input type="hidden" name="item_id" value="{$request_parameters.item_id}" />
{else}
<input type="hidden" name="nextAction" id="next-action" value="" />
<input type="hidden" name="property_id" id="property-id" value="" />
{/if}

<div class="edit-form-row">
  <div class="form-section-label">{$model.name} Name</div>
  <input type="text" name="item[_name]" value="Untitled {$model.name}" id="item-name" />
</div>

{foreach from=$properties key="pid" item="property"}

<div class="edit-form-row">
  <div class="form-section-label">{if $property.required == 'TRUE'}<strong>{/if}{$property.name} ({$property.varname}){if $property.required == 'TRUE'}</strong> *{/if}</div>
  {item_field property=$property}
</div>

{/foreach}

<div class="edit-form-row">
  <div class="form-section-label">Language</div>
  <select name="_language">
{foreach from=$_languages item="lang" key="langcode"}
    <option value="{$langcode}">{$lang.label}</option>
{/foreach}
  </select>
</div>

<div class="buttons-bar">
  <input type="button" onclick="window.location='{$domain}{$section}/getItemClassMembers?class_id={$model.id}';" value="Cancel" />
  <input type="submit" value="Save Changes" />
</div>

</form>

</div>

<div id="actions-area">


  
</div>