<form action="{$domain}{$section}/updateAsset" method="post" name="newHtml" enctype="multipart/form-data">

    <input type="hidden" name="asset_id" value="{$asset.id}" />
    <input type="hidden" name="asset_type" value="{$asset.type}" />
    
    {foreach from=$asset._editor_parameters key="parameter_name" item="parameter"}
    <div class="edit-form-row">
      <div class="form-section-label">{$parameter.label}</div>
      <input type="text" name="params[{$parameter_name}]" value="{$parameter.value}" style="width:250px" />
    </div>
    {/foreach}
    
    <div id="textarea-holder" style="width:100%">
        <textarea name="asset_content" id="tpl_textArea" wrap="virtual" style="width:100%;padding:0;font-family:monospace">{$textfragment_content}</textarea>
        <div class="buttons-bar">
            {save_buttons}
        </div>
    <div>
        
</form>