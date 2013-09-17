<div id="work-area">
  
  <h3>Page group order</h3>
  
  {load_interface file="page_group_tabs.tpl"}
  
  <div class="instruction">Drag the pages in this group into the order you would like</div>
  
  {if count($members)}
  
  <ul class="re-orderable-list div1" id="page-group-order" style="padding-top:10px">
    {foreach from=$members item="membership" key="key"}
    <li id="page_{$membership.page.id}">{$membership.page.title}</li>
    {/foreach}
  </ul>
  
  <div class="breaker"></div>
  
  <div class="buttons-bar"><input type="button" value="Drag pages to change order" id="submit-ajax" disabled="disabled" /></div>
  
  <script type="text/javascript" src="{$domain}Resources/System/Javascript/scriptaculous/src/dragdrop.js"></script>
    
    <script type="text/javascript">
    
      var url = sm_domain+'ajax:websitemanager/updatePageGroupOrder';
      var groupId = {$group.id};
    {literal}
      var IDs;
      var IDs_string;
      
      // Position.includeScrollOffsets = true;
      var pagesList = Sortable.create('page-group-order', {
          
          onUpdate: function(){
            IDs = Sortable.sequence('page-group-order');
            IDs_string = IDs.join(',');
            $('submit-ajax').value = 'Save new order';
            $('submit-ajax').disabled=false;
          },
          
          constraint: false,
          scroll: window,
          scrollSensitivity: 35
          
      });
      
      $('submit-ajax').observe('click', function(){
          
          $('submit-ajax').disabled = true;
          $('submit-ajax').value = 'Updating...';
          
          new Ajax.Request(url, {
              method: 'post',
              parameters: {page_ids: IDs_string, group_id: groupId},
              onSuccess: function(){
                  $('submit-ajax').value = 'Drag pages to change order';
              }
          });
      });
      
     {/literal}
    </script>
  
  {else}
  <div class="warning">There are currently no pages in this group. <a href="{$domain}{$section}/editPageGroup?group_id={$group.id}">Click here</a> to add some.</div>
  {/if}
  
</div>

<div id="actions-area">
  <ul class="actions-list" id="non-specific-actions">
    <li><b>Page groups</b></li>
    <li class="permanent-action"><a href="{$domain}smartest/pagegroup/new" class="right-nav-link"><img src="{$domain}Resources/Icons/add.png" border="0" alt=""> Create another page group</a></li>
    <li class="permanent-action"><a href="{$domain}smartest/pagegroups" class="right-nav-link"><img src="{$domain}Resources/Icons/page.png" border="0" alt=""> Back to page groups</a></li>
    <li class="permanent-action"><a href="{$domain}smartest/pages" class="right-nav-link"><img src="{$domain}Resources/Icons/page.png" border="0" alt=""> Back to pages</a></li>
  </ul>
</div>