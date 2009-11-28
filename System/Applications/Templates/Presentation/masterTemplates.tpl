<div id="work-area">

<h3>Page Master Templates</h3>

<form id="pageViewForm" method="get" action="">
  <input type="hidden" name="type" value="SM_PAGE_MASTER_TEMPLATE" />
  <input type="hidden" name="template_name" id="item_id_input"  value="" />
</form>



<div id="options-view-chooser">
Found templates. View as:
<a href="javascript:nothing()" onclick="setView('list', 'options_grid')">List</a> /
<a href="javascript:nothing()" onclick="setView('grid', 'options_grid')">Icons</a>
</div>

<ul class="options-grid" style="margin-top:0px" id="options_grid">
{foreach from=$templateList item="template"}
<li>
  <a href="javascript:nothing()" class="option" id="item_{$template}" onclick="setSelectedItem('{$template}');">
    <img border="0" src="{$domain}Resources/Icons/blank_page.png" />{$template}</a>
</li>
{/foreach}
</ul>

{if $error}{$error}{/if}
</div>

<div id="actions-area">

<ul class="actions-list" id="item-specific-actions" style="display:none">
    
  <li><b>Selected Template:</b></li>
	<li class="disabled-action"><a href="javascript:nothing()" onclick="{literal}if(selectedPage){ workWithItem('editTemplate'); }{/literal}" class="right-nav-link"><img src="{$domain}Resources/Icons/page_edit.png" border="0" alt=""> Edit This Template</a></li>
	<li class="disabled-action"><a href="javascript:nothing()" onclick="{literal}if(selectedPage && confirm('Really delete this template?')){ workWithItem('deleteTemplate'); }{/literal}" class="right-nav-link"><img src="{$domain}Resources/Icons/page_delete.png" border="0" alt=""> Delete This Template</a></li>
	<li class="disabled-action"><a href="javascript:nothing()" onclick="{literal}if(selectedPage){ workWithItem('duplicateTemplate'); }{/literal}" class="right-nav-link"><img src="{$domain}Resources/Icons/page_edit.png" border="0" alt=""> Duplicate This Template</a></li>
	<li class="disabled-action"><a href="javascript:nothing()" onclick="{literal}if(selectedPage){ workWithItem('downloadTemplate'); }{/literal}" class="right-nav-link"><img src="{$domain}Resources/Icons/page_edit.png" border="0" alt=""> Download This Template</a></li>
</ul>

<ul class="actions-list" id="non-specific-actions">
  <li><b>Template Options</b></li>
	<li class="disabled-action"><a href="javascript:nothing()" onclick="window.location='{$domain}{$section}/addTemplate?type=SM_PAGE_MASTER_TEMPLATE';" class="right-nav-link"><img src="{$domain}Resources/Icons/page_add.png" border="0" alt=""> Add Another Master Template</a></li>
</ul>

</div>