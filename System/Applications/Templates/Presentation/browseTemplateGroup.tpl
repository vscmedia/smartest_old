<script type="text/javascript">
  var templates = new Smartest.UI.OptionSet('pageViewForm', 'item_id_input', 'option', 'options_grid');
</script>

<div id="work-area">

{load_interface file="template_group_edit_tabs.tpl"}

<h3>Templates in group "{$group.label}"</h3>

<form id="pageViewForm" method="get" action="">
  <input type="hidden" name="template" id="item_id_input" value="" />
</form>

<div id="options-view-chooser">
{$templates._count} template{if $templates._count != 1}s{/if} in this group. View as:
<a href="javascript:templates.setView('list', 'list_by_type_view')">List</a> /
<a href="javascript:templates.setView('grid', 'list_by_type_view')">Icons</a>
</div>



<ul class="options-{$list_style}" style="margin-top:0px" id="options_grid">

{foreach from=$templates item="tpl"}

<li>
    <a href="#" class="option" id="item_{$tpl.id}" onclick="return templates.setSelectedItem('{$tpl.id}', 'item');" ondblclick="workWithItem('editTemplate')" >
    <img border="0" src="{$domain}Resources/Icons/blank_page.png" />{$tpl.label}</a>
</li>

{/foreach}
</ul>

</div>

<div id="actions-area">

<ul class="actions-list" id="item-specific-actions" style="display:none">
  <li><b>Selected template</b></li>
  <li class="permanent-action"><a href="{dud_link}" onclick="templates.workWithItem('templateInfo');" class="right-nav-link"><img src="{$domain}Resources/Icons/information.png" border="0" alt="" /> About this template...</a></li>
	<li class="permanent-action"><a href="{dud_link}" onclick="templates.workWithItem('editTemplate');" class="right-nav-link"><img src="{$domain}Resources/Icons/pencil.png" border="0" alt=""> Edit this template</a></li>
	<li class="permanent-action"><a href="{dud_link}" onclick="templates.workWithItem('toggleAssetArchived');" class="right-nav-link"><img src="{$domain}Resources/Icons/folder.png" style="width:16px;height:16px" border="0" alt="" /> Archive/unarchive this template</a></li>
	<li class="permanent-action"><a href="{dud_link}" onclick="templates.workWithItem('deleteAssetConfirm');" class="right-nav-link"><img src="{$domain}Resources/Icons/page_delete.png" border="0" alt="" /> Delete this template</a></li>
	{* <li class="permanent-action"><a href="{dud_link}" onclick="templates.workWithItem('duplicateAsset');" class="right-nav-link"><img src="{$domain}Resources/Icons/page_copy.png" border="0" alt="" /> Duplicate this template</a></li> *}
	<li class="permanent-action"><a href="{dud_link}" onclick="templates.workWithItem('downloadTemplate');" class="right-nav-link"><img src="{$domain}Resources/Icons/disk.png" border="0" alt="" /> Download this template</a></li>
</ul>

<ul class="actions-list" id="non-specific-actions">
  <li><b>Template group options</b></li>
	<li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$domain}{$section}/editTemplateGroupContents?group_id={$group.id}'" class="right-nav-link"><img src="{$domain}Resources/Icons/folder_edit.png" border="0" alt="" /> Edit this group</a></li>
	<li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$domain}{$section}/addTemplateGroup'" class="right-nav-link"><img src="{$domain}Resources/Icons/folder_add.png" border="0" alt="" /> Create a new template group</a></li>
</ul>

<ul class="actions-list" id="non-specific-actions">
  <li><b>Other options</b></li>
	<li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$domain}smartest/templates/groups'" class="right-nav-link"><img src="{$domain}Resources/Icons/folder_old.png" border="0" alt="" /> View all file groups</a></li>
	<li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$domain}smartest/templates/types'" class="right-nav-link"><img src="{$domain}Resources/Icons/folder_old.png" border="0" alt="" /> View all files by type</a></li>
</ul>

</div>