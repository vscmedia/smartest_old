<script type="text/javascript">
    var itemNameFieldDefaultValue = '{$start_name}';
    var itemNameFieldName = '{$model.item_name_field_name}';
    var modelName = '{$model.name}';
{literal}
    document.observe('dom:loaded', function(){
        
        $('item-name').observe('focus', function(){
            if($('item-name').getValue() == itemNameFieldDefaultValue || $('item-name').getValue() == ''){
                $('item-name').removeClassName('unfilled');
                $('item-name').setValue('');
            }
        });
        
        $('item-name').observe('blur', function(){
            if($('item-name').getValue() == itemNameFieldDefaultValue || $('item-name').getValue() == ''){
                $('item-name').addClassName('unfilled');
                $('item-name').setValue(itemNameFieldDefaultValue);
            }else{
                $('item-name').removeClassName('error');
            }
        });
        
        $('new-item-form').observe('submit', function(e){
            
            document.fire('smartest:newItemFormSubmit');
            e.stop();
            
        });
        
        document.observe('smartest:newItemFormSubmit', function(){
            if($('item-name').getValue() == itemNameFieldDefaultValue || $('item-name').getValue() == ''){
                $('item-name').addClassName('error');
                e.stop();
            }else{
                $('new-item-form').submit();
            }
        });
        
    });
    
{/literal}
</script>

<div id="work-area">

{* <h3><a href="{$domain}smartest/models">Items</a> &gt; <a href="{$domain}{$section}/getItemClassMembers?class_id={$model.id}">{$model.plural_name}</a> &gt; Add a new {$model.name|strtolower}</h3> *}
<h3>Add a new {$model.name|strtolower}</h3>

{if $request_parameters.for == 'ipv'}
<div class="special-box">
  The {$model.name|strtolower} you create here will be added as the value for property <strong>{$parent_property.name|strtolower}</strong> on the {$parent_item._model.name|strtolower} '{$parent_item.name}'.
</div>
{/if}

<form action="{$domain}{$section}/insertItem" method="post" id="new-item-form">

<input type="hidden" name="class_id" value="{$model.id}" />

{if $request_parameters.for == 'ipv'}
<input type="hidden" name="for" value="ipv" />
<input type="hidden" name="property_id" value="{$request_parameters.property_id}" />
<input type="hidden" name="item_id" value="{$request_parameters.item_id}" />
{else}
<input type="hidden" name="nextAction" id="next-action" value="" />
<input type="hidden" name="property_id" id="property-id" value="" />
{/if}

{if $model.item_name_field_visible}
<div class="edit-form-row">
  <div class="form-section-label">{$model.name} {$model.item_name_field_name}</div>
  <input type="text" name="item[_name]" value="{$start_name}" id="item-name" class="unfilled" />
  <div class="form-hint">Enter a {$model.item_name_field_name|lower} for this {$model.name|lower}</div>
</div>{/if}

<div class="edit-form-row">
  <div class="form-section-label">Add tags</div>
  <input type="text" name="item_tags" value="" id="item-tags" />
  <span class="form-hint">Separate tags with commas</span>
</div>

{foreach from=$properties key="pid" item="property"}

<div class="edit-form-row">
  <div class="form-section-label">{if $property.required == 'TRUE'}<strong>{/if}{$property.name}{if $property.required == 'TRUE'}</strong> *{/if}{if $can_edit_properties}<a style="float:left" title="Edit this property" href="{$domain}datamanager/editItemClassProperty?from=item_edit&amp;item_id={$item.id}&amp;itemproperty_id={$property.id}"><img src="{$domain}Resources/System/Images/edit_setting_minimal.png" alt="Edit this property" /></a>{/if}</div>
  {item_field property=$property value=$property.default_value}
</div>
{foreachelse}
<div class="warning">
  There are no properties yet, so this model will likely be of only limited use. Why not try <a href="{$domain}{$section}/addPropertyToClass?class_id={$model.id}">adding some properties</a> and then come back here?
</div>
{/foreach}

<div class="edit-form-row">
  <div class="form-section-label">Language</div>
  <select name="_language">
{foreach from=$_languages item="lang" key="langcode"}
    <option value="{$langcode}"{if $langcode == $site_language} selected="selected"{/if}>{$lang.label}</option>
{/foreach}
  </select>
</div>

<div class="buttons-bar">
  <input type="button" onclick="window.location='{$domain}{$section}/getItemClassMembers?class_id={$model.id}';" value="Cancel" />
  <input type="submit" value="Save Changes" />
</div>

</form>

</div>

<div id="actions-area">


  
</div>