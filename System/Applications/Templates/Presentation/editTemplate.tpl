<div id="work-area">

{load_interface file="template_edit_tabs.tpl"}

<h3>{$interface_title}</h3>

{if $show_form}

{if $is_editable}<form action="{$domain}{$section}/updateTemplate" method="post" name="newTemplate" enctype="multipart/form-data">{/if}
  
  {if $template.status == "imported"}
  <input type="hidden" name="edit_type" value="imported" />
  <input type="hidden" name="template_id" value="{$template.id}" />
  {else}
  <input type="hidden" name="edit_type" value="unimported" />
  <input type="hidden" name="type" value="{$template.type}" />
  <input type="hidden" name="filename" value="{$template.url}" />
  {/if}
  
  <div class="special-box"><strong>Template</strong>: <code>{$template.storage_location}</code><strong><code>{$template.url}</code></strong></div>
  {if !$file_is_writable}
    <div class="warning">This file is not currently writable by the web server, so it cannot be edited directly in Smartest.</div>
  {elseif !$dir_is_writable}
    <div class="warning">The directory where this file is stored is not currently writable by the web server, so this file cannot be edited directly in Smartest.</div>
  {/if}
  
  <div style="width:100%" id="editTMPL" class="textarea-holder">
    <textarea name="template_content" id="tpl_textArea" style="display:block">{$template_content}</textarea>
    <div style="height:14px"><span class="form-hint">Editor powered by CodeMirror</span></div>
  </div>
  
  <div class="buttons-bar">
    {if $is_editable}
    {save_buttons}
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
  height: '400px',
  path: "{$domain}Resources/System/Javascript/CodeMirror-0.65/js/"
{literal}  }); {/literal}
</script>

</div>

<div id="actions-area">
  <ul class="actions-list" id="non-specific-actions">
    <li><b>Options</b></li>
    {if $is_convertable}<li class="permanent-action"><a href="javascript:nothing()" onclick="window.location='{$domain}{$section}/convertTemplateType?template_id={$template.id}'" class="right-nav-link"><img src="{$domain}Resources/Icons/wrench_orange.png" border="0" alt="" /> Convert to another type</a></li>{/if}
    <li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$domain}{$section}/listByType?type={$template.type}'"><img src="{$domain}Resources/Icons/page_white_stack.png" border="0" alt="" /> Back to {$type_info.label|lower}s</a></li>
    {if $template_type == "SM_LIST_ITEM_TEMPLATE"}<li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$domain}{$section}/listItemTemplates'">Back to list item temnplates</a></li>{/if}
    {if $template_type == "SM_PAGE_MASTER_TEMPLATE"}<li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$domain}{$section}/masterTemplates'">Back to master temnplates</a></li>{/if}
  </ul>
  
{if !empty($stylesheets)}
  <ul class="actions-list" id="non-specific-actions">
    <li><b>Stylesheets in this template</b></li>
{foreach from=$stylesheets item="stylesheet"}
    <li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$domain}assets/editAsset?asset_id={$stylesheet.id}'"><img src="{$stylesheet.small_icon}" border="0" alt="" /> {$stylesheet.label}</a></li>
{/foreach}
  </ul>
{/if}

{if !empty($recently_edited)}
<ul class="actions-list" id="non-specific-actions">
  <li><b>Recently edited</b></li>
  {foreach from=$recently_edited item="recent_template"}
	<li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$recent_template.action_url}'"><img border="0" src="{$recent_template.small_icon}" /> {$recent_template.label|summary:"30"}</a></li>
  {/foreach}
</ul>
{/if}
  
</div>