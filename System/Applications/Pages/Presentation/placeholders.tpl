<div id="work-area">
    
    {load_interface file="cms_elements_tabs.tpl"}
    
  <h3>Placeholders</h3>
  
  <form id="pageViewForm" method="get" action="">
  <input type="hidden" name="placeholder_id" value="" id="item_id_input" />
  </form>

  <ul class="options-list" id="tree-root">
  
{foreach from=$placeholders item="placeholder"}
    <li>
      <a id="item_{$placeholder.id}" class="option" href="javascript:nothing()" onclick="setSelectedItem('{$placeholder.id}', '');" ondblclick="window.location='{$domain}{$section}/editPlaceholder?placeholder_id={$placeholder.id}'">
        <img src="{$domain}Resources/Icons/published_placeholder.gif" alt="" />{$placeholder.name} ({$placeholder.type})</a></li>
{/foreach}
  </ul>
</div>

<div id="actions-area">
    <ul class="actions-list" id="item-specific-actions" style="display:none">
    	<li><b>Selected placeholder</b></li>
    	<li class="permanent-action"><a href="{dud_link}" onclick="workWithItem('editPlaceholder');" class="right-nav-link"><img src="{$domain}Resources/Icons/pencil.png" border="0" alt="" /> Edit this placeholder</a></li>
    </ul>
    
    <ul class="actions-list" id="non-specific-actions">
        <li><b>Options</b></li>
        <li class="permanent-action"><a href="#" onclick="window.location='{$domain}smartest/pages';" class="right-nav-link"> <img src="{$domain}Resources/Icons/page.png" border="0" alt="" /> Go back to pages</a></li>
    </ul>
    
</div>