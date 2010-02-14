<script language="javascript">
var customAssetClassName = false;

{literal}

function updateAssetClassName(){
	if(!customAssetClassName){
		// alert('test');
		document.getElementById("assetclass_name").value = document.getElementById("assetclass_label").value.toSlug();
	}
}
{/literal}
</script>

<div id="work-area">

<h3>Website Manager &gt; Assets &gt; Add a New Container</h3>

<form action="{$domain}{$section}/insertContainer" method="post" style="margin:0px">
  
{if $name}
  <input type="hidden" name="container_name" value="{$name}" />
{/if}

    <div id="edit-form-layout">

      <div class="edit-form-row">
        <div class="form-section-label">Label:</div>
        <input type="text" name="container_label" id="container_label" {if !$name}onkeyup="updateAssetClassName();"{/if} />
      </div>
      
      <div class="edit-form-row">
        <div class="form-section-label">Markup/tag name: </div>
        {if $name}<code>&lt;?sm:container name="{$name}":?&gt;{else}<input type="text" name="container_name" id="container_name" value="{$name}" /><br />
          <span>If you don't enter a tag name, one will be generated for you.</span>{/if}
      </div>

      <div class="edit-form-row">
        <div class="buttons-bar">
          <input type="button" value="Cancel" onclick="cancelForm();" />
          <input type="submit" value="Save" />
        </div>
      </div>
    
    </div>
    
</form>

</div>