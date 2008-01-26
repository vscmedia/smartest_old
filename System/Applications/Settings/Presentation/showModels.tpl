<h3>Models created by {$content.username} </h3>
<a name="top"></a>
<div id="work-area">

<div id="options-view-chooser">
View: <a href="#" onclick="setView('list', 'options_grid')">List</a> /
<a href="#" onclick="setView('grid', 'options_grid')">Icon</a>
</div>
{if $content.itemClassCount!=0}
<ul class="options-grid" id="options_grid">
{foreach from=$models key=key item=itemClass}
  <li ondblclick="window.location='{$domain}{$section}/getItemClassMembers?class_id={$itemClass.itemclass_id}'">
    <a id="item_{$itemClass.itemclass_id}" class="option" href="#" onclick="setSelectedItem('{$itemClass.itemclass_id}','{$itemClass.itemclass_schema_id}', '{$itemClass.itemclass_plural_name|escape:quotes}', 'fff');">
      <img border="0" src="{$domain}Resources/Icons/package.png">
      {$itemClass.itemclass_plural_name}</a>
	{if $itemClass.number_properties < 1}{*(<a class="normal" href="{$domain}{$section}/addPropertyToClass?class_id={$itemClass.itemclass_id}">No Properties</a>)*}{/if}</li>
{/foreach}

{else}<font color="Red">No Models are creted by this User!</font>{/if}
</ul>
</div>






