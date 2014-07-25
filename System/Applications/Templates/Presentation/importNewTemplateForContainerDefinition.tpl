<div id="work-area">
  
  <h3>Import new template to container</h3>
  
  <form action="{$domain}templates/finishNewTemplateImportToContainerDefinition" method="post" id="template-import-form">
    
    <input type="hidden" name="page_id" value="{$page.id}" />
    <input type="hidden" name="container_id" value="{$container.id}" />
    {if $item_id}<input type="hidden" name="item_id" value="{$item_id}" />{/if}
    
    <div class="edit-form-row">
      <div class="form-section-label">Template name</div>
      <input type="text" name="new_template_label" id="new-template-label" value="New container template" class="unfilled" />
    </div>
  
    <div class="edit-form-row">
      <div class="form-section-label">Which file would you like to import?</div>
      <div style="padding:5px 0 10px 0">Unimported template files in <code>Presentation/Layouts/</code></div>
      <div style="height:250px;overflow:scroll;border:1px solid #ccc;padding:5px">
    {foreach from=$potential_templates item="potential_template"}
    <input type="radio" name="chosen_file" value="{$potential_template}" class="select-template-radio" id="{$potential_template|slug}" />&nbsp;<label for="{$potential_template|slug}"><code>{$potential_template}</code></label><br />
    {/foreach}
      </div>
    </div>
    
    <div class="buttons-bar">
      <input type="submit" value="Import selected template" disabled="disabled" id="submit-button" />
    </div>
  
  </form>
  
  <script type="text/javascript">
  
  var itemNameFieldDefaultValue = 'New container template';
  var preventDefaultValue = true;
{literal}
  document.observe('dom:loaded', function(){

      $('new-template-label').observe('focus', function(){
          if(($('new-template-label').getValue() == itemNameFieldDefaultValue)|| $('new-template-label').getValue() == ''){
              $('new-template-label').removeClassName('unfilled');
              $('new-template-label').setValue('');
          }
      });

      $('new-template-label').observe('blur', function(){
          if(($('new-template-label').getValue() == itemNameFieldDefaultValue) || $('new-template-label').getValue() == ''){
              $('new-template-label').addClassName('unfilled');
              $('new-template-label').setValue(itemNameFieldDefaultValue);
          }else{
              $('new-template-label').removeClassName('error');
          }
      });

      $('template-import-form').observe('submit', function(e){

          if(($('new-template-label').getValue() == itemNameFieldDefaultValue) || $('new-template-label').getValue() == ''){
              $('new-template-label').addClassName('error');
              e.stop();
          }

      });
      
      $$('input.select-template-radio').each(function(radio){
        radio.observe('click', function(){
          $('submit-button').disabled = false;
        });
      });

  });
{/literal}
  </script>
  
</div>