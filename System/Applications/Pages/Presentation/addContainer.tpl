<script language="javascript">
var customAssetClassName = false;

{if !$name}

{literal}

document.observe('dom:loaded', function(){
    
    $('new-container-form').observe('submit', function(e){

        if($('container_name').value == 'Unnamed container' || $('container_name').value == 'unnamed_container' || $('container_name').value == ''){
            $('container_name').addClassName('error');
            e.stop();
        }

    });
    
}

/* function updateAssetClassName(){
	if(!customAssetClassName){
		// alert('test');
		document.getElementById("assetclass_name").value = document.getElementById("assetclass_label").value.toSlug();
	}
} */

{/literal}

{/if}



</script>

<div id="work-area">

<h3>Add a New Container</h3>

<form action="{$domain}{$section}/insertContainer" method="post" style="margin:0px" id="new-container-form">
  
{if $name}
  <input type="hidden" name="container_name" value="{$name}" />
{/if}

    <div id="edit-form-layout">
    
      <div class="edit-form-row">
        <div class="form-section-label">Markup/tag name</div>
        {if $name}<code>&lt;?sm:container name="{$name}":?&gt;{else}<input type="text" name="container_name" id="container_name" value="unnamed_container" class="unfilled" />{/if}
      </div>
    
      <div class="edit-form-row">
        <div class="form-section-label">Label</div>
        <input type="text" name="container_label" id="container_label"{if $name} value="{$label}"{/if} />
      </div>
      
{if $groups._count > 0}

      <div class="edit-form-row">
        <div class="form-section-label">Template group</div>
        <select name="container_group">
          <option value="NONE">Do not limit - Allow all container templates</option>
          {foreach from=$groups item="group"}
          <option value="{$group.id}">{$group.label}</option>
          {/foreach}
        </select>
      </div>

{/if}
      
      <div class="edit-form-row">
        <div class="buttons-bar">
          <input type="button" value="Cancel" onclick="cancelForm();" />
          <input type="submit" value="Save" />
        </div>
      </div>
    
    </div>
    
</form>

</div>