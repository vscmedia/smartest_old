<div id="work-area">

<h3><a href="{$domain}smartest/data">Items</a> &gt; <a href="{$domain}smartest/models">Models</a> &gt; <a href="{$domain}{$section}/getItemClassMembers?class_id={$item._model.id}">{$item._model.plural_name}</a> &gt; Add a new {$item._model.name|strtolower}</h3>

<div id="instruction">You are submitting the draft property values of the new {$item._model.name|lower}.</div>

<form action="{$domain}{$section}/addItem" enctype="multipart/form-data" method="post">

<input type="hidden" name="class_id" value="{$item._model.id}" />
<input type="hidden" name="item_id" value="{$item.id}" />
<input type="hidden" name="save_item" value="{$item.id}" />

<div class="edit-form-row">
  <div class="form-section-label">{$item._model.name} Name</div>
  <input type="text" name="item[_name]" value="Untitled {$item._model.name}" style="width:250px" />
</div>

{foreach from=$item._properties key="pid" item="property"}

<div class="edit-form-row">
  {item_field property=$property value=$item[$pid]}
</div>

{/foreach}

<div class="edit-form-row">
  <div class="buttons-bar">
    <input type="button" onclick="window.location='{$domain}{$section}/getItemClassMembers?class_id={$item._model.id}';" value="Cancel" />
    <input type="submit" value="Save Changes" />
  </div>
</div>

</form>

</div>

<div id="actions-area">


  
</div>