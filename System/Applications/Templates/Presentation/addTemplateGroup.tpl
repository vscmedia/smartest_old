<script type="text/javascript">
    var itemNameFieldDefaultValue = 'Unnamed template group';
    var nameFieldFocussed = false;
{literal}
    document.observe('dom:loaded', function(){
        
        $('template-group-label').observe('focus', function(){
            if($('template-group-label').getValue() == itemNameFieldDefaultValue || $('template-group-label').getValue() == ''){
                $('template-group-label').removeClassName('unfilled');
                $('template-group-label').setValue('');
            }
            nameFieldFocussed = true;
        });
        
        $('template-group-label').observe('blur', function(){
            if($('template-group-label').getValue() == itemNameFieldDefaultValue || $('template-group-label').getValue() == ''){
                $('template-group-label').addClassName('unfilled');
                $('template-group-label').setValue(itemNameFieldDefaultValue);
            }else{
                $('template-group-label').removeClassName('error');
            }
            nameFieldFocussed = false;
        });
        
        $('new-group-form').observe('submit', function(e){
            
            if($('template-group-label').value == 'Unnamed template group' || $('template-group-label').value == itemNameFieldDefaultValue){
                $('template-group-label').addClassName('error');
                e.stop();
            }
            
            if($('template-group-type').value == ''){
                $('template-group-type').addClassName('error');
                e.stop();
            }
            
        });
        
        document.observe('keypress', function(e){
            
            if(e.keyCode == 13){
            
                if(nameFieldFocussed && ($('template-group-label').value == 'Unnamed template group' || $('template-group-label').value == itemNameFieldDefaultValue || !$('template-group-label').value.charAt(0))){
                    $('template-group-label').addClassName('error');
                    e.stop();
                }
            
            }
            
        });
        
    });
    
{/literal}
</script>

<div id="work-area">
  
  <h3>Create a template group</h3>
  
  <form action="{$domain}{$section}/insertTemplateGroup" method="post" id="new-group-form">
  
    <div id="edit-form-layout">
      
      <div class="edit-form-row">
        <div class="form-section-label">Name this group</div>
        <input type="text" id="template-group-label" name="template_group_label" value="Unnamed template group" class="unfilled" />
      </div>
      
      <div class="edit-form-row">
        <div class="form-section-label">Which templates can go in this group?</div>
        <select name="template_group_type" id="template-group-type">
            <option value=""></option>
          {* <option value="ALL">Any type of template</option> *}

{foreach from=$template_types item="type"}
            <option value="{$type.id}"{if $filter_type == $type.id} selected="selected"{/if}>{$type.label}</option>
{/foreach}

        </select>
      </div>
      
      <div class="edit-form-row">
          <div class="form-section-label">Cross-site usage</div>
          <input type="checkbox" name="template_group_shared" value="1" id="template_group_shared" />
          <label for="template_group_shared">Share this group</label>
          <div class="form-hint">Makes this template group available to other sites you create in this Smartest install, although files within the group will not be automatically shared with other sites</div>
        </div>
      
      <div class="edit-form-row">
        <div class="buttons-bar">
          <input type="submit" value="Save" />
          <input type="button" onclick="cancelForm();" value="Cancel" />
        </div>
      </div>

    </div>
    
  </form>
  
</div>

<div id="actions-area">

</div>