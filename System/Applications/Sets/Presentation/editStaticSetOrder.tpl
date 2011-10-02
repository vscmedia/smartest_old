<div id="work-area">
  
  {load_interface file="edit_set_tabs.tpl"}
  
  <h3><a href="{$domain}smartest/models">Items</a> &gt; {if $model.id}<a href="{$domain}datamanager/getItemClassMembers?class_id={$model.id}">{$model.plural_name}</a> &gt; <a href="{$domain}sets/getItemClassSets?class_id={$model.id}">Sets</a>{else}<a href="{$domain}smartest/sets">Sets</a>{/if} &gt; <a href="{$domain}sets/editSet?set_id={$set.id}">{$set.label}</a> &gt; Set Order</h3>
  
  {if count($items)}
  
  <div class="special-box">
    Show as <select name="col_count" id="col-count">
      <option value="1"{if $num_cols == "1"} selected="selected"{/if}>One column</option>
      <option value="2"{if $num_cols == "2"} selected="selected"{/if}>Two columns</option>
      <option value="3"{if $num_cols == "3"} selected="selected"{/if}>Three columns</option>
      <option value="4"{if $num_cols == "4"} selected="selected"{/if}>Four columns</option>
      <option value="5"{if $num_cols == "5"} selected="selected"{/if}>Five columns</option>
    </select>
  </div>
  
  <ul class="re-orderable-list div{$num_cols}" id="static-set-order" style="padding-top:10px">
    {foreach from=$items item="item" key="key"}
    <li id="item_{$item.id}">{$item.name}
      {* <div class="buttons">{if $key > 0}<a href="{$domain}sets/moveItemInStaticSet?set_id={$set.id}&amp;item_id={$item.id}&amp;direction=up"><img src="{$domain}Resources/Icons/arrow_up.png" alt="up" /></a>{/if}
      {if $key < count($items)-1}<a href="{$domain}sets/moveItemInStaticSet?set_id={$set.id}&amp;item_id={$item.id}&amp;direction=down"><img src="{$domain}Resources/Icons/arrow_down.png" alt="down" /></a>{/if}</div> *}
    </li>
    {/foreach}
  </ul>
  
  <script type="text/javascript">
    var currentColStyle = 'div{$num_cols}';
    {literal}
    $('col-count').observe('change', function(){
        $('static-set-order').removeClassName(currentColStyle);
        $('static-set-order').addClassName('div'+$('col-count').value);
        currentColStyle = 'div'+$('col-count').value;
        PREFS.setApplicationPreference('reorder_static_set_num_cols', $('col-count').value);
    });
    {/literal}
  </script>
  
  <div class="breaker"></div>
  
  <div class="buttons-bar"><input type="button" value="Drag items to change order" id="submit-ajax" disabled="disabled" /></div>
  
  <script type="text/javascript" src="{$domain}Resources/System/Javascript/scriptaculous/src/dragdrop.js"></script>
    
    <script type="text/javascript">
      var url = sm_domain+'ajax:sets/updateStaticSetOrder';
      var setId = {$set.id};
    {literal}
      var IDs;
      var IDs_string;
      
      // Position.includeScrollOffsets = true;
      var itemsList = Sortable.create('static-set-order', {
          
          onChange: function(){
            IDs = Sortable.sequence('static-set-order');
            IDs_string = IDs.join(',');
            $('submit-ajax').value = 'Save new order';
            $('submit-ajax').disabled=false;
            // $('tester').update('Set ID:'+setId+' IDs:'+IDs_string);
            // alert(typeof IDs);
          },
          
          constraint: false,
          scroll: window,
          scrollSensitivity: 35
          
      });
      
      $('submit-ajax').observe('click', function(){
          
          $('submit-ajax').disabled = true;
          $('submit-ajax').value = 'Updating...';
          // ;
          new Ajax.Request(url, {
              method: 'post',
              parameters: {item_ids: IDs_string, set_id: setId},
              onSuccess: function(){
                  // alert('The order of this set has been updated');
                  $('submit-ajax').value = 'Drag items to change order';
                  // alert('IDs submitted: '+IDs);
              }
          });
      });
      
     {/literal}
    </script>
  
  {else}
  <div class="warning">There are currently no items in this set. <a href="{$domain}{$section}/editSet?set_id={$set.id}">Click here</a> to add some.</div>
  {/if}
  
</div>

<div id="actions-area">
    
</div>