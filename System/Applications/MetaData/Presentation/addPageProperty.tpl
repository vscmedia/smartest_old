<div id="work-area">
<h3>Add a new page field</h3>

  <div id="edit-form-layout">
    
    <form id="definePageProperty" name="definePageProperty" action="{$domain}{$section}/insertPageProperty" method="post" style="margin:0px">
      
      <input type="hidden" name="site_id" value="{$site_id}" />
      
      <div class="edit-form-row">
        <div class="form-section-label">Field Name: </div>
        {if $field_name}<input type="hidden" name="property_name" value="{$field_name}" />{$field_name}{else}<input type="text" name="property_name" value="unnamed_property" id="property-name" class="unfilled" />{/if}
      </div>
      
      <div class="edit-form-row">
        <div class="form-section-label">Field Type: </div>
        <select name="pageproperty_type" id="pageproperty_type">
          <option value="">Select A Type</option>
        	{foreach from=$property_types item="type"}
          <option value="{$type.id}"{if $type.id==$selected_type} selected="selected"{/if}>{$type.label}</option>
          {/foreach}
        </select>
      </div>
      
      <div id="foreign-key-filter-selector-container"></div>
      
      <div class="edit-form-row">
        <div class="form-section-label">Global: </div>
        <input type="checkbox" name="property_sitewide" id="property-sitewide" value="1" /> <label for="property-sitewide">Making a field global means the definition will be the same on all pages</label>
      </div>
      
      <div class="edit-form-row">
        <div class="buttons-bar">
          <input type="button" value="Cancel" onclick="cancelForm();" />
          <input type="submit" name="action" value="Save" />
        </div>
      </div>
  
    </form>
    
    <script type="text/javascript">
{literal}
      
      $('pageproperty_type').observe('change', function(e){
        new Ajax.Updater('foreign-key-filter-selector-container', sm_domain+'ajax:metadata/getForeignKeyFilterSelector', {
          parameters: {type: $('pageproperty_type').value}
        });
      });
      
      $('definePageProperty').observe('submit', function(e){
        
        if($('property-name')){
            
            if(!$('property-name').value || $('property-name').value == 'unnamed_property'){
                $('property-name').addClassName('error');
                e.stop();
            }
            
        }
        
        if(!$('pageproperty_type').value){
            $('pageproperty_type').addClassName('error');
            e.stop();
        }
        
      });
      
      if($('property-name')){
          
          $('property-name').observe('focus', function(){
              if($('property-name').value == 'unnamed_property'){
                  $('property-name').value = '';
              }
              $('property-name').removeClassName('unfilled');
          });
          
          $('property-name').observe('blur', function(){
              if($('property-name').value == ''){
                  $('property-name').value = 'unnamed_property';
                  $('property-name').addClassName('unfilled');
              }else if($('property-name').value && $('property-name').value != 'unnamed_property'){
                  $('property-name').removeClassName('error');
              }
          });
          
      }
      
      $('pageproperty_type').observe('change', function(){
          if($('pageproperty_type').value){
              $('pageproperty_type').removeClassName('error');
          }
      });
      
{/literal}    
    </script>

  </div>

</div>
