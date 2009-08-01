<div id="work-area">
  <h3>File groups</h3>
  
  <form id="pageViewForm" method="get" action="">
    <input type="hidden" name="group_id" id="item_id_input" value="" />
  </form>


  <ul class="{if count($groups) > 10}options-list{else}options-grid{/if}" id="{if count($groups) > 10}options_list{else}options_grid{/if}">
  {foreach from=$groups key="key" item="set"}
    <li style="list-style:none;" 
  			ondblclick="window.location='{$domain}{$section}/editSet?set_id={$set.id}'">
  			<a class="option" id="item_{$set.id}" onclick="setSelectedItem('{$set.id}', 'fff');" >
  			  <img border="0" src="{$domain}Resources/Icons/folder.png">
  			  {$set.label}</a></li>
  {/foreach}
  </ul>
  
</div>

<div id="actions-area">
  <ul class="actions-list" id="item-specific-actions" style="display:none">
    <li><b>Selected Group</b></li>
    <li class="permanent-action"><a href="#" onclick="{literal}if(selectedPage){workWithItem('editAssetGroupContents');}{/literal}"><img border="0" src="{$domain}Resources/Icons/folder_edit.png"> Edit Contents</a></li>
    <li class="permanent-action"><a href="#" onclick="{literal}if(selectedPage){workWithItem('browseAssetGroup');}{/literal}" ><img border="0" src="{$domain}Resources/Icons/folder_go.png"> View Contents</a></li>
  </ul>
  
  <ul class="actions-list" id="non-specific-actions">
    <li><b>Options</b></li>
    <li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$domain}{$section}/newAssetGroup'" class="right-nav-link"><img src="{$domain}Resources/Icons/page_add.png" border="0" alt="" /> Add a new file group</a></li>
  	<li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$domain}smartest/assets'" class="right-nav-link"><img src="{$domain}Resources/Icons/folder_old.png" border="0" alt="" style="width:16px;height:16px" /> View all files by type</a></li>
  </ul>
  
</div>