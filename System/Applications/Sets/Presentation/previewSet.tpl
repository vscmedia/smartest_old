<div id="work-area">

<h3><a href="{$domain}smartest/data">Items</a> &gt; <a href="{$domain}smartest/sets">Sets</a> &gt; <a href="{$domain}sets/editSet?set_id={$set.id}">{$set.name}</a> &gt; Preview</h3>

<div class="instruction">Items in this set:</div>

<form id="pageViewForm" method="get" action="">
  <input type="hidden" name="set_id"  value="{$set.id}" />
  <input type="hidden" name="item_id" id="item_id_input" value="" />
</form>

<div class="instruction">Found {$count} item{if $count != 1}s{/if} in this data set</div>

<form action="{$domain}{$section}/previewSet" method="get" id="mode-form">
  
  <div>Conditions:
    <input type="hidden" name="set_id"  value="{$set.id}" />
    <select name="mode" onchange="$('mode-form').submit();">
      <option value="0"{if $mode == 0} selected="selected"{/if}>All items, using draft property values</option>
      <option value="1"{if $mode == 1} selected="selected"{/if}>All items, using draft property values, but only in archive</option>
      <option value="2"{if $mode == 2} selected="selected"{/if}>All items, using draft property values, excluding those in archive</option>
      <option value="3"{if $mode == 3} selected="selected"{/if}>All items, using live property values</option>
      <option value="4"{if $mode == 4} selected="selected"{/if}>All items, using live property values, but only in archive</option>
      <option value="5"{if $mode == 5} selected="selected"{/if}>All items, using live property values, excluding those in archive</option>
      <option value="6"{if $mode == 6} selected="selected"{/if}>Published items, but using draft property values</option>
      <option value="7"{if $mode == 7} selected="selected"{/if}>Published items, but using draft property values, but only in archive</option>
      <option value="8"{if $mode == 8} selected="selected"{/if}>Published items, but using draft property values, excluding those in archive</option>
      <option value="9"{if $mode == 9} selected="selected"{/if}>Published items, using live property values</option>
      <option value="10"{if $mode == 10} selected="selected"{/if}>Published items, using live property values, but only in archive</option>
      <option value="11"{if $mode == 11} selected="selected"{/if}>Published items, using live property values, excluding those in archive</option>
    </select>
  </div>
</form>

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
      <li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$domain}datamanager/editItem?item_id='+selectedPage+'&amp;from=previewSet'"><img border="0" src="{$domain}Resources/Icons/model.png" style="width:16px;height:18px"> Edit Properties</a></li>	
    </ul>
    
    <ul class="actions-list">
      <li><b>Sets Options</b></li>
      <li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$domain}{$section}/editSet?set_id={$set.id}'"><img border="0" src="{$domain}Resources/Icons/folder.png" style="width:16px;height:18px"> Edit This Set</a></li>
      <li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$domain}smartest/sets'"><img border="0" src="{$domain}Resources/Icons/folder.png" style="width:16px;height:18px"> Back to Data Sets</a></li>
      <li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$domain}datamanager/getItemClassMembers?class_id={$set.model.id}'"><img border="0" src="{$domain}Resources/Icons/model.png" style="width:16px;height:18px"> Browse {$set.model.plural_name}</a></li>
      <li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$domain}smartest/models'"><img border="0" src="{$domain}Resources/Icons/model.png" style="width:16px;height:18px"> Go to Models</a></li>	
    </ul>    
</div>
