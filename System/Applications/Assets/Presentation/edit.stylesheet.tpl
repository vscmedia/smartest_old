{if $allow_save}<form action="{$domain}{$section}/updateAsset" method="post" name="newCss" enctype="multipart/form-data">{/if}
  
  <input type="hidden" name="asset_type" value="{$asset.type}" />
  <input type="hidden" name="asset_id" value="{$asset.id}" />
  
  <div class="special-box">
    <span class="heading">Language</span>
    <select name="asset_language">
      <option value="">{$lang.label}</option>
  {foreach from=$_languages item="lang" key="langcode"}
      <option value="{$langcode}"{if $asset.language == $langcode} selected="selected"{/if}>{$lang.label}</option>
  {/foreach}
    </select>
  </div>
    
  {foreach from=$asset._editor_parameters key="parameter_name" item="parameter"}
  <div class="edit-form-row">
    <div class="form-section-label">{$parameter.label}</div>
    <input type="text" name="params[{$parameter_name}]" value="{$parameter.value}" style="width:250px" />
  </div>
  {/foreach}
    
  <div class="edit-form-row">
    <div class="form-section-label">File contents</div>
    <div class="textarea-holder">
      <textarea name="asset_content" id="tpl_textArea" wrap="virtual" >{$textfragment_content}</textarea>
    </div>
  </div>
  
  <div class="buttons-bar">
    {if $allow_save}
    {save_buttons}
    {else}
    <input type="button" onclick="cancelForm();" value="Cancel" />
    {/if}
  </div>
  
  <script src="{$domain}Resources/System/Javascript/CodeMirror-0.65/js/codemirror.js" type="text/javascript"></script>

  <script type="text/javascript">
  {literal}  var editor = new CodeMirror.fromTextArea('tpl_textArea', {{/literal}
    parserfile: "parsecss.js",
    stylesheet: "{$domain}Resources/System/Javascript/CodeMirror-0.65/css/csscolors.css",
    continuousScanning: 500,
    height: '300px',
    path: "{$domain}Resources/System/Javascript/CodeMirror-0.65/js/"
  {literal}  }); {/literal}
  </script>
  
{if $allow_save}</form>{/if}