<div id="work-area">
  <h3>Sets to which {$model.plural_name|lower} are automatically added</h3>
  {if count($sets)}
  <div class="instruction">New {$model.plural_name|lower} will be added automatically to static sets chosen here</div>
  <form action="{$domain}{$section}/updateModelAutomaticSets" method="post" id="update-model-auto-static-sets">
    <input type="hidden" name="model_id" value="{$model.id}" />
    <ul class="modal-basic-list" style="border:1px solid #ccc">
    {foreach from=$sets item="set"}
      <li><input type="checkbox" name="sets[{$set.id}]" id="set_{$set.id}" value="1"{if in_array($set.id, $selected_set_ids)} checked="checked"{/if} /> <label for="set_{$set.id}">{$set.label}</label></li>
    {/foreach}
    </ul>
    <div class="buttons-bar"><img src="{$domain}Resources/System/Images/ajax-loader.gif" alt="" id="saving-gif" style="display:none" /><input type="button" value="Save" id="update-model-auto-static-sets-button" /></div>
  </form>
  <script type="text/javascript">
  var model_id = {$model.id};
{literal}
  $('update-model-auto-static-sets-button').observe('click', function(){
    $('saving-gif').show();
    $('update-model-auto-static-sets').request({
      onComplete: function(){
        new Ajax.Updater('model-set-auto-add-ajax-field', sm_domain+'ajax:datamanager/modelAutomaticSetsInfo', {
          parameters: {'model_id': model_id},
        });
        MODALS.hideViewer();
      }
    });
  });
{/literal}
  </script>
  {else}
  <div class="special-box">There are no static sets set up for {$model.plural_name|lower}.</div>
  {/if}

</div>