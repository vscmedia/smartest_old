<div id="work-area">

<h3>{$interface_title}</h3>

{if $show_form}

<form action="{$domain}{$section}/updateTemplate" method="post" name="newTemplate" enctype="multipart/form-data">
  
  <input type="hidden" name="type" value="{$template_type}" />
  <input type="hidden" name="filename" value="{$template_name}" />
  {if $template_type == "SM_CONTAINER_TEMPLATE"}<input type="hidden" name="template_id" value="{$template_id}" />{/if}
  
  <div style="width:100%" id="editTMPL">
    Template Filename: {$template_name}
    <textarea name="template_content" id="tpl_textArea" style="display:block">{$template_content}</textarea>
  </div>
  
  <div class="buttons-bar">
    <input type="submit" value="Save Changes" />
    <input type="button" onclick="cancelForm();" value="Done" />
  </div>
  
</form>

{/if}

</div>

<div id="actions-area">
  <ul class="actions-list" id="non-specific-actions">
    <li><b>Options</b></li>
    {if $template_type == "SM_CONTAINER_TEMPLATE"}<li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$domain}{$section}/containerTemplates'">Back to container temnplates</a></li>{/if}
    {if $template_type == "SM_LISTITEM_TEMPLATE"}<li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$domain}{$section}/listItemTemplates'">Back to list item temnplates</a></li>{/if}
    {if $template_type == "SM_MASTER_TEMPLATE"}<li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$domain}{$section}/masterTemplates'">Back to master temnplates</a></li>{/if}
  </ul>
</div>