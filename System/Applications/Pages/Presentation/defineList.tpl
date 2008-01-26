<div id="work-area">

<h3>Define List</h3>

<div class="text" style="margin-bottom:10px">Choose a data set and templates to use in {$list_name}</div>

<form id="editForm" method="post" action="saveList">
  
  <input type="hidden" name="page_id" value="{$page.id}" />
  <input type="hidden" name="list_name" value="{$list_name}" />

  <div class="edit-form-layout">
    
    <div class="edit-form-row">
      <div class="form-section-label">Data Set</div>
      <select name="dataset_id" onchange="">
        {foreach from=$sets item="set"}
        <option value="{$set.id}"{if $list.draft_set_id == $set.id && strlen($set.id)} selected="selected"{/if}>{$set.name} ({$set.type|lower})</option>
        {/foreach}
      </select>
    </div>
    
    <div class="edit-form-row">
      <div class="form-section-label">Header Template (Optional)</div>
      <select name="header_template" onchange="">
        <option></option>
        {foreach from=$templates item="ht"}
        <option value="{$ht}" {if $header_template == $ht} selected="selected"{/if}>{$ht}</option>
        {/foreach}
      </select>
    </div>
    
    <div class="edit-form-row">
      <div class="form-section-label">Repeating Template</div>
      <select name="main_template" onchange="">
        {foreach from=$templates item="rt"}
        <option value="{$rt}" {if $main_template == $rt} selected="selected"{/if}>{$rt}</option>
        {/foreach}
      </select>
    </div>
     
    <div class="edit-form-row">
      <div class="form-section-label">Footer Template (Optional)</div>
      <select name="footer_template" onchange="">
        <option></option>
        {foreach from=$templates item="ft"}
        <option value="{$ft}" {if $footer_template == $ft} selected="selected"{/if}>{$ft}</option>
        {/foreach}
      </select>
    </div>
    
    <div class="edit-form-row">
       <div class="buttons-bar">
         <input type="button" onclick="cancelForm();" value="Cancel">
         <input type="submit" value="Save" />
       </div>
    </div>

</div>

</form>

</div>
<div id="actions-area">

<ul class="actions-list" id="non-specific-actions">
  <li><b>Options</b></li>
  <li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$domain}smartest/sets'" class="right-nav-link"><img src="{$domain}Resources/Icons/folder_picture.png" border="0" alt=""> View data sets</a></li>
  <li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$domain}templates/listItemTemplates'" class="right-nav-link"><img src="{$domain}Resources/Icons/page_go.png" border="0" alt=""> Browse list item templates</a></li>
</ul>

</div>