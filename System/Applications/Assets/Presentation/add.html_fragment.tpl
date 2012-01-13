  <div style="display:none;margin-top:8px;margin-bottom:8px" id="uploader" class="special-box">
    Upload file: <input type="file" name="new_file" /><br /><a href="javascript:hideUploader()">never mind</a>
  </div>
  
  <div style="width:100%" id="text_window" class="textarea-holder">
    <div class="textarea-holder">
      <textarea name="content" id="tpl_textArea" wrap="virtual"></textarea>
    </div>
  </div>

  <div id="uploader_link" class="special-box">or, alternatively, <a href="javascript:showUploader();">upload a file</a>.</div>
  
  <script src="{$domain}Resources/System/Javascript/CodeMirror-0.65/js/codemirror.js" type="text/javascript"></script>

  <script type="text/javascript">
  {literal}  var editor = new CodeMirror.fromTextArea('tpl_textArea', {{/literal}
    parserfile: 'parsexml.js',
    stylesheet: "{$domain}Resources/System/Javascript/CodeMirror-0.65/css/xmlcolors.css",
    continuousScanning: 500,
    height: '400px',
    path: "{$domain}Resources/System/Javascript/CodeMirror-0.65/js/"
  {literal}  }); {/literal}
  </script>