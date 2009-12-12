<div id="work-area">

<h3>Page Master Templates</h3>

<form id="pageViewForm" method="get" action="">
  <input type="hidden" name="type" value="SM_PAGE_MASTER_TEMPLATE" />
  <input type="hidden" name="asset_type" value="SM_ASSETTYPE_MASTER_TEMPLATE" />
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
  <a href="javascript:nothing()" class="option" id="item_{$template.url}" onclick="setSelectedItem('{$template.url}', '{$template.url}', '{if $template.status == 'imported'}imported-template{else}unimported-template{/if}');">
    <img border="0" src="{$domain}Resources/Icons/{if $template.status == 'imported'}blank{else}mystery{/if}_page.png" />{$template.url}</a>
</li>
{/foreach}
</ul>

</div>

<div id="actions-area">

<ul class="actions-list" id="imported-template-specific-actions" style="display:none">
    
  <li><b>Selected Template:</b></li>
	<li class="disabled-action"><a href="javascript:nothing()" onclick="{literal}if(selectedPage){ workWithItem('editTemplate'); }{/literal}" class="right-nav-link"><img src="{$domain}Resources/Icons/page_edit.png" border="0" alt=""> Edit this template</a></li>
	<li class="disabled-action"><a href="javascript:nothing()" onclick="{literal}if(selectedPage && confirm('Really delete this template?')){ workWithItem('deleteTemplate'); }{/literal}" class="right-nav-link"><img src="{$domain}Resources/Icons/page_delete.png" border="0" alt=""> Delete this template</a></li>
	<li class="disabled-action"><a href="javascript:nothing()" onclick="{literal}if(selectedPage){ workWithItem('downloadTemplate'); }{/literal}" class="right-nav-link"><img src="{$domain}Resources/Icons/page_edit.png" border="0" alt=""> Download this template</a></li>
</ul>

<ul class="actions-list" id="unimported-template-specific-actions" style="display:none">
    
  <li><b>Unimported Template:</b></li>
	<li class="disabled-action"><a href="javascript:nothing()" onclick="{literal}if(selectedPage){ workWithItem('importSingleTemplate'); }{/literal}" class="right-nav-link"><img src="{$domain}Resources/Icons/page_add.png" border="0" alt=""> Import this template</a></li>
	<li class="disabled-action"><a href="javascript:nothing()" onclick="{literal}if(selectedPage){ workWithItem('editTemplate'); }{/literal}" class="right-nav-link"><img src="{$domain}Resources/Icons/page_edit.png" border="0" alt=""> Edit as-is</a></li>
	<li class="disabled-action"><a href="javascript:nothing()" onclick="{literal}if(selectedPage && confirm('Really delete this template?')){ workWithItem('deleteTemplate'); }{/literal}" class="right-nav-link"><img src="{$domain}Resources/Icons/page_delete.png" border="0" alt=""> Delete this template</a></li>
	<li class="disabled-action"><a href="javascript:nothing()" onclick="{literal}if(selectedPage){ workWithItem('duplicateTemplate'); }{/literal}" class="right-nav-link"><img src="{$domain}Resources/Icons/page_edit.png" border="0" alt=""> Duplicate this template</a></li>
	<li class="disabled-action"><a href="javascript:nothing()" onclick="{literal}if(selectedPage){ workWithItem('downloadTemplate'); }{/literal}" class="right-nav-link"><img src="{$domain}Resources/Icons/page_edit.png" border="0" alt=""> Download this template</a></li>
</ul>

<ul class="actions-list" id="non-specific-actions">
  <li><b>Template Options</b></li>
	<li class="disabled-action"><a href="javascript:nothing()" onclick="window.location='{$domain}{$section}/addTemplate?type=SM_PAGE_MASTER_TEMPLATE';" class="right-nav-link"><img src="{$domain}Resources/Icons/page_add.png" border="0" alt=""> Add Another Master Template</a></li>
</ul>

</div>