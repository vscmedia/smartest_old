<div id="work-area">

<h3><a href="{$domain}smartest/data">Items</a> &gt; <a href="{$domain}smartest/models">Models</a> &gt; <a href="{$domain}{$section}/getItemClassMembers?class_id={$model.id}">{$model.plural_name}</a> &gt; Add a new {$item._model.name|strtolower}</h3>

<div id="instruction">You are submitting the draft property values of the new {$model.name|lower}.</div>

<form action="{$domain}{$section}/addItem" enctype="multipart/form-data" method="post">

<input type="hidden" name="class_id" value="{$model.id}" />
<input type="hidden" name="save_item" value="{$item.id}" />

<div class="edit-form-row">
  <div class="form-section-label">{$model.name} Name</div>
  <input type="text" name="item[_name]" value="Untitled {$model.name}" />
</div>

{foreach from=$properties key="pid" item="property"}

<div class="edit-form-row">
  {item_field property=$property value=$property.default_value}
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

<div class="edit-form-row">
  <div class="buttons-bar">
    <input type="button" onclick="window.location='{$domain}{$section}/getItemClassMembers?class_id={$model.id}';" value="Cancel" />
    <input type="submit" value="Save Changes" />
  </div>
</div>

</form>

</div>

<div id="actions-area">


  
</div>