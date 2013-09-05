<div class="edit-form-row">
  <div class="form-section-label">Choose a Model:</div>

  <select name="foreign_key_filter" id="model-selector">
    <option value="">Choose...</option>
    {foreach from=$foreign_key_filter_options item="option"}
    <option value="{$option.id}">{$option.plural_name}</option>
    {/foreach}
  </select>
</div>

<div class="edit-form-row" style="display:none" id="item-set-selector-outer">
  <div class="form-section-label">Restrict choices to data a set:</div>
  <div id="item-set-selector-inner">
    <img src="{$domain}Resources/System/Images/ajax-loader.gif" alt="" />
  </div>
</div>

<script type="text/javascript">
var model_id = {$model.id};
var waitingHTML = '<img src="{$domain}Resources/System/Images/ajax-loader.gif" alt="" />';
{literal}
  $('model-selector').observe('change', function(e){
    if($('model-selector').value){
      
      if($('item-set-selector-outer').getStyle('display') != 'block'){
        new Effect.BlindDown('item-set-selector-outer', {duration: 0.3});
      }
      
      $('item-set-selector-inner').innerHTML = waitingHTML;
      
      new Ajax.Updater('item-set-selector-inner', sm_domain+'ajax:datamanager/getItemClassSetSelectorForNewPropertyForm', {
          parameters: {class_id: $('model-selector').value},
          onComplete: function(){
            // alert('test');
          }
      });
      
    }else{
      new Effect.BlindUp('item-set-selector-outer', {duration: 0.3});
    }
  });
{/literal}
</script>

{if $request_parameters.itemproperty_datatype == "SM_DATATYPE_CMS_ITEM"}
<div class="edit-form-row">
  <div class="form-section-label">Create corresponding property</div> 
  <input type="checkbox" name="create_aq_property" value="1" id="create-corresponding-auto-query" checked="checked" /> <label for="create-corresponding-auto-query">Create an auto-query property on the selected model that corresponds to this property</label>
</div>

<div class="edit-form-row">
  <div class="form-section-label">Corresponding property name</div>
  <input type="text" name="aq_property_name" value="{$model.plural_name}" />
</div>
{/if}