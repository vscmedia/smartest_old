<script type="text/javascript">
  var groups = new Smartest.UI.OptionSet('pageViewForm', 'item_id_input', 'option', 'options_grid');
</script>

<form id="pageViewForm" method="get" action="">
  <input type="hidden" name="group_id" id="item_id_input" value="" />
</form>

<div id="work-area">
  <h3>Page groups</h3>
  <ul class="{if count($groups) > 10}options-list{else}options-grid{/if}" id="options_grid">
{foreach from=$groups item="group"}
    <li ondblclick="window.location='{$domain}{$section}/browseAssetGroup?group_id={$group.id}'">
      <a href="#" class="option" id="group_{$group.id}" onclick="return groups.setSelectedItem('{$group.id}', 'group');" >
        <img border="0" src="{$domain}Resources/Icons/folder.png"> {$group.label}</a></li>
{/foreach}
  </ul>
</div>

<div id="actions-area">
  
  <ul class="actions-list" id="group-specific-actions" style="display:none">
    <li><b>Selected Group</b></li>
    <li class="permanent-action"><a href="#" onclick="return groups.workWithItem('editPageGroup');"><img border="0" src="{$domain}Resources/Icons/folder_edit.png"> Edit group</a></li>
    <li class="permanent-action"><a href="#" onclick="return groups.workWithItem('deletePageGroupConfirm');" ><img border="0" src="{$domain}Resources/Icons/folder_delete.png"> Delete group</a></li>
  </ul>
  
  <ul class="actions-list" id="non-specific-actions">
    <li><b>Page groups</b></li>
    <li class="permanent-action"><a href="{$domain}smartest/pagegroup/new" class="right-nav-link"><img src="{$domain}Resources/Icons/add.png" border="0" alt=""> Add page group</a></li>
    <li class="permanent-action"><a href="{$domain}smartest/pages" class="right-nav-link"><img src="{$domain}Resources/Icons/page.png" border="0" alt=""> Back to pages</a></li>
  </ul>
  
</div>