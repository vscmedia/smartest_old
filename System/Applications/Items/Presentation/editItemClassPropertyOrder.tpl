<div id="work-area">
  
  {load_interface file="model_list_tabs.tpl"}
  
  <h3><a href="{$domain}smartest/models">Items</a> &gt; <a href="{$domain}{$section}/getItemClassMembers?class_id={$model.id}">{$model.plural_name}</a> &gt; Property order</h3>
  
  <ul class="re-orderable-list div1" id="property-order" style="padding-top:10px">
      {foreach from=$properties item="property" key="key"}
      <li id="property_{$property.id}">{$property.name}</li>
      {/foreach}
    </ul>
    
    <div class="breaker"></div>

      <div class="buttons-bar"><input type="button" value="Drag properties to change order" id="submit-ajax" disabled="disabled" /></div>
      <script type="text/javascript" src="{$domain}Resources/System/Javascript/scriptaculous/src/dragdrop.js"></script>
    
    <script type="text/javascript">
    
      var url = sm_domain+'ajax:datamanager/updateItemClassPropertyOrder';
      var modelId = {$model.id};
    {literal}
      var IDs;
      var IDs_string;
      
      var itemsList = Sortable.create('property-order', {

            onChange: function(){
              IDs = Sortable.sequence('property-order');
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
                  parameters: {property_ids: IDs_string, class_id: modelId},
                  onSuccess: function(){
                      $('submit-ajax').value = 'Drag items to change order';
                  }
              });
          });

         {/literal}
          
      </script>
    
</div>