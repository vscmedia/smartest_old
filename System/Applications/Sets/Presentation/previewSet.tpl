<div id="work-area">

<h3><a href="{$domain}datamanager">Data Manager</a> &gt; <a href="{$domain}{$section}">Sets</a> &gt; {$set.name} </h3>
<a name="top"></a>

<div class="instruction">Items in this set:</div>

<form id="pageViewForm" method="get" action="">
  <input type="hidden" name="set_id"  value="{$set.id}" />
  <input type="hidden" name="item_id" id="item_id_input" value="" />
</form>

<div class="instruction">Found {$count} item{if $count != 1}s{/if} in this data set</div>

View as:
<a href="{dud_link}" onclick="setView('list', '{if $content.count > 10}options_list{else}options_grid{/if}')">List</a> /
<a href="{dud_link}" onclick="setView('grid', '{if $content.count > 10}options_list{else}options_grid{/if}')">Icons</a>
  
  <ul class="{if $content.count > 10}options-list{else}options-grid{/if}" id="{if $content.count > 10}options_list{else}options_grid{/if}">
  {foreach from=$items key="key" item="item"}
    <li>
      <a href="{dud_link}" class="option" id="item_{$item.id}" onclick="setSelectedItem('{$item.id}', '{$item.name|escape:quotes}');">
        <img border="0" src="{$domain}Resources/Icons/item.png">{$item.name}</a></li>
  {/foreach}
  </ul>

</div>

<div id="actions-area">
    
    <ul class="actions-list" id="item-specific-actions" style="display:none">
      <li><b>Selected Item</b></li>
      <li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$domain}datamanager/editItem?item_id='+selectedPage"><img border="0" src="{$domain}Resources/Icons/model.png" style="width:16px;height:18px"> Edit Properties</a></li>	
    </ul>
    
    <ul class="actions-list">
      <li><b>Options</b></li>
      <li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$domain}smartest/sets'"><img border="0" src="{$domain}Resources/Icons/folder.png" style="width:16px;height:18px"> Back to Data Sets</a></li>
    	<li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$domain}smartest/models'"><img border="0" src="{$domain}Resources/Icons/model.png" style="width:16px;height:18px"> Go to Models</a></li>	
    </ul>    
</div>