<div id="work-area">

{load_interface file="cms_elements_tabs.tpl"}

<h3>Page Fields</h3>

<div class="text" style="margin-bottom:10px">Double click a field to see how and where it's defined.</div>

<form id="pageViewForm" method="get" action="">
<input type="hidden" name="field_id" id="item_id_input" value="" />
</form>

{if count($fields)}

<ul class="options-list" id="tree-root">
       {foreach from=$fields item="element" }
    <li>
       <a id="item_{$element.pageproperty_id}" class="option" href="#" onclick="setSelectedItem('{$element.pageproperty_id}','{$element.pageproperty_name}');" ondblclick="workWithItem('viewPageFieldDefinitions')">		 
        <img border="0" src="{$domain}Resources/Icons/page_code.png" />
        {$element.pageproperty_name}
      </a>
     
    </li>
    {/foreach}
</ul>

{else}

<div class="special-box">There are no page fields set up on this site yet. <a href="{$domain}{$section}/addPageProperty?site_id={$site_id}">Click here</a> to add one.</div>

{/if}

</div>

<div id="actions-area">

<ul class="actions-list" id="item-specific-actions" style="display:none">
	<li><b>Selected Field</b></li>
	<li class="permanent-action"><a href="#" onclick="workWithItem('viewPageFieldDefinitions');" class="right-nav-link"> <img src="{$domain}Resources/Icons/eye.png" border="0" alt=""> View definitions</a></li>
	<li class="permanent-action"><a href="#" onclick="workWithItem('clearFieldOnAllPages');" class="right-nav-link"> <img src="{$domain}Resources/Icons/cross.png" border="0" alt=""> Clear on all pages</a></li>
	<li class="permanent-action"><a href="#" onclick="{literal}if(selectedPage && confirm('Are you sure you want to delete this property?')){workWithItem('deletePageProperty');}{/literal}" class="right-nav-link"> <img src="{$domain}Resources/Icons/delete.png" border="0" alt="">&nbsp;Delete</a></li>
</ul>

<ul class="actions-list" id="non-specific-actions">
  <li><b>Options</b></li>
  <li class="permanent-action"><a href="#" onclick="window.location='{$domain}{$section}/addPageProperty?site_id={$site_id}'"><img src="{$domain}Resources/Icons/add.png" border="0" alt=""> Add A New Field</a></li>
</ul>

</div>
