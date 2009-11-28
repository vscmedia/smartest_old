<form action="{$domain}{$section}/updateAsset" method="post" name="newJscr" enctype="multipart/form-data">
    
    <input type="hidden" name="asset_id" value="{$asset.id}" />
    <input type="hidden" name="asset_type" value="{$asset.type}" />
    
    <div class="special-box">You are editing file: <code>Public/Resources/Javascript/</code><strong><code>{$asset.url}</code></strong></div>
    
    <div class="special-box">
      <span class="heading">Language</span>
      <select name="asset_language">
        <option value="">{$lang.label}</option>
    {foreach from=$_languages item="lang" key="langcode"}
        <option value="{$langcode}"{if $asset.language == $langcode} selected="selected"{/if}>{$lang.label}</option>
    {/foreach}
      </select>
    </div>
    
    {foreach from=$asset.default_parameters key="parameter_name" item="parameter_value"}
    <div class="edit-form-row">
      <div class="form-section-label">{$parameter_name}</div>
      <input type="text" name="params[{$parameter_name}]" value="{$parameter_value}" style="width:250px" />
    </div>
    {/foreach}
    
    <div class="edit-form-row">
      <div class="form-section-label">File contents</div>
      <div id="editTMPL">
        <textarea name="asset_content" id="tpl_textArea" wrap="virtual" >{$textfragment_content}</textarea>
      </div>
    </div>
    
    <div class="buttons-bar">
      <input type="submit" value="Save Changes" />
      <input type="button" onclick="cancelForm();" value="Done" />
    </div>
    
    <script src="{$domain}Resources/System/Javascript/CodeMirror-0.65/js/codemirror.js" type="text/javascript"></script>

    <script type="text/javascript">
    {literal}  var editor = new CodeMirror.fromTextArea('tpl_textArea', {{/literal}
      parserfile: ["tokenizejavascript.js", "parsejavascript.js"],
      stylesheet: "{$domain}Resources/System/Javascript/CodeMirror-0.65/css/jscolors.css",
      continuousScanning: 500,
      path: "{$domain}Resources/System/Javascript/CodeMirror-0.65/js/"
    {literal}  }); {/literal}
    </script>

</form>