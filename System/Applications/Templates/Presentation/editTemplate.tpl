<div id="work-area">

<h3>{$interface_title}</h3>

{if $show_form}

{if $is_editable}<form action="{$domain}{$section}/updateTemplate" method="post" name="newTemplate" enctype="multipart/form-data">{/if}
  
  <input type="hidden" name="type" value="{$template_type}" />
  <input type="hidden" name="filename" value="{$template_name}" />
  {if $template_type == "SM_CONTAINER_TEMPLATE"}<input type="hidden" name="template_id" value="{$template_id}" />{/if}
  
  <div class="special-box"><strong>Template</strong>: <code>{$path}</code><strong><code>{$template_name}</code></strong></div>
  {if !$file_is_writable}
    <div class="warning">This file is not currently writable by the web server, so it cannot be edited directly in Smartest.</div>
  {elseif !$dir_is_writable}
    <div class="warning">The directory where this file is stored is not currently writable by the web server, so this file cannot be edited directly in Smartest.</div>
  {/if}
  
  <div style="width:100%" id="editTMPL">
    <textarea name="template_content" id="tpl_textArea" style="display:block">{$template_content}</textarea>
  </div>
  
  <div class="buttons-bar">
    {if $is_editable}
    <input type="submit" value="Save Changes" />
    <input type="button" onclick="cancelForm();" value="Done" />
    {else}
    <input type="button" onclick="cancelForm();" value="Cancel" />
    {/if}
  </div>
  
{if $is_editable}</form>{/if}

{/if}

<script src="{$domain}Resources/System/Javascript/CodeMirror-0.65/js/codemirror.js" type="text/javascript"></script>
<script src="{$domain}Resources/System/Javascript/CodeMirror-0.65/js/mirrorframe.js" type="text/javascript"></script>

<script type="text/javascript">
{literal}  var editor = new CodeMirror.fromTextArea('tpl_textArea', {{/literal}
  parserfile: 'parsexml.js',
  stylesheet: "{$domain}Resources/System/Javascript/CodeMirror-0.65/css/xmlcolors.css",
  continuousScanning: 500,
  path: "{$domain}Resources/System/Javascript/CodeMirror-0.65/js/"
{literal}  }); {/literal}
</script>

</div>

<div id="actions-area">
  <ul class="actions-list" id="non-specific-actions">
    <li><b>Options</b></li>
    {if $template_type == "SM_CONTAINER_TEMPLATE"}<li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$domain}{$section}/containerTemplates'">Back to container temnplates</a></li>{/if}
    {if $template_type == "SM_LIST_ITEM_TEMPLATE"}<li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$domain}{$section}/listItemTemplates'">Back to list item temnplates</a></li>{/if}
    {if $template_type == "SM_PAGE_MASTER_TEMPLATE"}<li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$domain}{$section}/masterTemplates'">Back to master temnplates</a></li>{/if}
  </ul>
</div>