<script type="text/javascript">
    var itemNameFieldDefaultValue = 'Unnamed page group';
    var nameFieldFocussed = false;
{literal}
    document.observe('dom:loaded', function(){
        
        $('page-group-label').observe('focus', function(){
            if($('page-group-label').getValue() == itemNameFieldDefaultValue || $('page-group-label').getValue() == ''){
                $('page-group-label').removeClassName('unfilled');
                $('page-group-label').setValue('');
            }
            nameFieldFocussed = true;
        });
        
        $('page-group-label').observe('blur', function(){
            if($('page-group-label').getValue() == itemNameFieldDefaultValue || $('page-group-label').getValue() == ''){
                $('page-group-label').addClassName('unfilled');
                $('page-group-label').setValue(itemNameFieldDefaultValue);
            }else{
                $('page-group-label').removeClassName('error');
            }
            nameFieldFocussed = false;
        });
        
        $('new-group-form').observe('submit', function(e){
            
            if($('page-group-label').value == 'Unnamed template group' || $('page-group-label').value == itemNameFieldDefaultValue){
                $('page-group-label').addClassName('error');
                e.stop();
            }
            
        });
        
        document.observe('keypress', function(e){
            
            if(e.keyCode == 13){
            
                if(nameFieldFocussed && ($('page-group-label').value == 'Unnamed page group' || $('page-group-label').value == itemNameFieldDefaultValue || !$('page-group-label').value.charAt(0))){
                    $('page-group-label').addClassName('error');
                    e.stop();
                }
            
            }
            
        });
        
    });
    
{/literal}
</script>

<div id="work-area">
  
  <h3>Add a page group</h3>
  <form method="post" action="{$domain}{$section}/insertPageGroup" id="new-group-form">

    <div class="edit-form-layout">

      <div class="edit-form-row">
        <div class="form-section-label">Label</div>
        <input type="text" name="pagegroup_label" id="page-group-label" class="unfilled" value="Unnamed page group" />			
      </div>

      <div class="edit-form-row">
        <div class="buttons-bar">
          <input type="checkbox" name="continue_to_pages" value="1" id="continue-to-pages" checked="checked" />
          <label for="continue-to-pages">Add pages after saving</label>
          <input type="button" value="Cancel" onclick="cancelForm();">
          <input type="submit" value="Next &gt;&gt;" />
        </div>
      </div>

    </div>

  </form>
</div>

<div id="actions-area">
  
</div>