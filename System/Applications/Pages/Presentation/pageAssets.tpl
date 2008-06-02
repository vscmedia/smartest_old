<script language="javascript" type="text/javascript">

var selectedPage = null;
var selectedPageName = null;
var lastRow;
var lastRowColor;
var treeNodes = new Array();
var pageWebId = '{$page.webid}';

{literal}

function viewDraftPage(){

	var pageURL = sm_domain+'website/renderEditableDraftPage?page_id='+pageWebId;
	window.location=pageURL;

}

function viewLivePage(){

	var pageURL = sm_domain+'website/renderPageFromId?page_id='+pageWebId;
	window.open(pageURL);

}

{/literal}
</script>

<div id="work-area">

{if $allow_edit}

  {load_interface file="edit_tabs.tpl"}

  {if $require_item_select}
    <h3>Page Elements</h3>
    {load_interface file="choose_item.tpl"}
  {else}
    {load_interface file=$sub_template}
  {/if}

{else}

<h3>Page Elements</h3>

{/if}

</div>

{if !$require_item_select}

<div id="actions-area">

<!--Navigation Bar-->

<ul class="invisible-actions-list" id="placeholder-specific-actions" style="display:none">
  <li><b>Placeholder Options</b></li>
  <li class="permanent-action"><a href="#" onclick="workWithItem('definePlaceholder');" class="right-nav-link"><img src="{$domain}Resources/Icons/layout_edit.png" border="0" alt=""> Define Placeholder</a></li>
  <li class="permanent-action"><a href="#" onclick="workWithItem('undefinePlaceholder');" class="right-nav-link"><img src="{$domain}Resources/Icons/layout_edit.png" border="0" alt=""> Clear Placeholder</a></li>
</ul>

<ul class="invisible-actions-list" id="container-specific-actions" style="display:none">
  <li><b>Container Options</b></li>
  <li class="permanent-action"><a href="#" onclick="workWithItem('defineContainer');" class="right-nav-link"><img src="{$domain}Resources/Icons/layout_edit.png" border="0" alt=""> Define Container</a></li>
</ul>

<ul class="invisible-actions-list" id="list-specific-actions" style="display:none">
  <li><b>List Options</b></li>
  <li class="permanent-action"><a href="#" onclick="workWithItem('defineList');" class="right-nav-link"><img src="{$domain}Resources/Icons/layout_edit.png" border="0" alt=""> Define List Parameters</a></li>
</ul>

<ul class="invisible-actions-list" id="attachment-specific-actions" style="display:none">
  <li><b>Attachment Options</b></li>
  <li class="permanent-action"><a href="#" onclick="workWithItem('editAttachment');" class="right-nav-link"><img src="{$domain}Resources/Icons/layout_edit.png" border="0" alt=""> Edit Attachment Settings</a></li>
</ul>

<ul class="invisible-actions-list" id="asset-specific-actions" style="display:none">
  <li><b>File Options</b></li>
  <li class="permanent-action"><a href="#" onclick="workWithItem('editFile');" class="right-nav-link"><img src="{$domain}Resources/Icons/layout_edit.png" border="0" alt=""> Edit This File</a></li>
</ul>

<ul class="invisible-actions-list" id="template-specific-actions" style="display:none">
  <li><b>Template Options</b></li>
  <li class="permanent-action"><a href="#" onclick="workWithItem('editTemplate');" class="right-nav-link"><img src="{$domain}Resources/Icons/layout_edit.png" border="0" alt=""> Edit This Template</a></li>
</ul>

<ul class="invisible-actions-list" id="itemspace-specific-actions" style="display:none">
  <li><b>Itemspace Options</b></li>
  <li class="permanent-action"><a href="#" onclick="workWithItem('defineItemspace');" class="right-nav-link"><img src="{$domain}Resources/Icons/layout_edit.png" border="0" alt=""> Define This Itemspace</a></li>
</ul>

<ul class="invisible-actions-list" id="item-specific-actions" style="display:none">
  <li><b>Item Options</b></li>
  <li class="permanent-action"><a href="#" onclick="workWithItem('openItem');" class="right-nav-link"><img src="{$domain}Resources/Icons/pencil.png" border="0" alt=""> Edit This Item</a></li>
</ul>

<ul class="invisible-actions-list" id="field-specific-actions" style="display:none">
  <li><b>Field Options</b></li>
  <li class="permanent-action"><a href="#" onclick="workWithItem('editField');" class="right-nav-link"><img src="{$domain}Resources/Icons/layout_edit.png" border="0" alt=""> Define This Field</a></li>
  <li class="permanent-action">
    <a href="#" onclick="if(confirm('Are you sure you want to set the draft value as live?')) workWithItem('setLiveProperty')" class="right-nav-link">
      <img src="{$domain}Resources/Icons/page_lightning.png" border="0" alt=""> Publish this field</a></li>
  <li class="permanent-action">
    <a href="#" onclick="if(confirm('Are you sure you want to undefine this field?')) workWithItem('undefinePageProperty')" class="right-nav-link">
      <img src="{$domain}Resources/Icons/page_delete.png" border="0" alt=""> Undefine this field</a></li>
</ul>

<ul class="actions-list" id="non-specific-actions">
  <li><b>Page Options</b></li>
  {if $template.filename}<li class="permanent-action"><a href="{$domain}templates/editTemplate?type=SM_PAGE_MASTER_TEMPLATE&amp;template_name={$templateMenuField}" value="Edit"><img src="{$domain}Resources/Icons/page_edit.png" border="0" alt="" /> Edit Page Template</a></li>{/if}
  {if $version == "draft"}<li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$domain}{$section}/publishPageConfirm?page_id={$page.webid}'" class="right-nav-link"><img src="{$domain}Resources/Icons/page_lightning.png" border="0" alt=""> Publish this page</a></li>{/if}
  {* <li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$domain}{$section}/publishPageContainersConfirm?page_id={$page.webid}';" class="right-nav-link"><img src="{$domain}Resources/Icons/layout_edit.png" border="0" alt=""> Publish Containers</a></li> *}
  {* {if $version == "draft"}<li class="permanent-action"><a href="{dud_link}" onclick="workWithItem('publishPagePlaceholdersConfirm');" class="right-nav-link"><img src="{$domain}Resources/Icons/layout_edit.png" border="0" alt=""> Publish Placeholders Only</a></li>{/if} *}
  <li class="permanent-action"><a href="#" onclick="viewLivePage();" class="right-nav-link"><img src="{$domain}Resources/Icons/page_go.png" border="0" alt=""> Go to this page</a></li>

  <li class="permanent-action"><a href="{$domain}{$section}/layoutPresetForm?page_id={$page.webid}" class="right-nav-link"><img src="{$domain}Resources/Icons/page_edit.png" border="0" alt=""> Create preset from this page</a></li>

  {if $draftAsset.asset_id && $draftAsset.asset_id != $liveAsset.asset_id}<li class="permanent-action"><a href="#" onclick="{literal}if(confirm('Are you sure you want to publish your changes right now?')){workWithItem('setLiveAsset');}{/literal}" class="right-nav-link"><img src="{$domain}Resources/Icons/page_delete.png" border="0" alt=""> Publish This Asset Class</a>{/if}
  <li class="permanent-action"><a href="{$domain}smartest/assets/types" class="right-nav-link"><img src="{$domain}Resources/Icons/page_add.png" border="0" alt=""> Browse Assets Library</a></li>
  <li class="permanent-action"><a href="{$domain}{$section}/closeCurrentPage" class="right-nav-link"><img src="{$domain}Resources/Icons/tick.png" border="0" alt=""> Finish working with this page</a></li>
  {if $allow_release}<li class="permanent-action"><a href="{$domain}{$section}/releasePage?page_id={$page.webid}" class="right-nav-link"><img src="{$domain}Resources/Icons/tick.png" border="0" alt=""> Release this page</a></li>{/if}
</ul>

</div>

{/if}