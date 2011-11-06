<script src="{$domain}Resources/Javascript/livesearch.js" type="text/javascript"></script> 

<script language="javascript">

{literal}
var setPlural = true;

function suggestPluralName(){
	if(setPlural == true){
		$('plural').value = $('modelname').value+"s";
	}
}

function turnOffAutoPlural(){
	setPlural = false;
}

{/literal}
</script>

<div id="work-area">

<h3><a href="{$domain}smartest/models">Items</a> &gt; Build a new model</h3>

<div class="special-box">Unsure about what "models" are? {help id="datamanager:models"}click here{/help} before you go any further.</div>

<form name="searchform" onsubmit="return liveSearchSubmit()" method="post" action="{$domain}{$section}/insertItemClass">
<input type="hidden" name="stage" value="2" />
    
<div class="edit-form-row">
  <div class="form-section-label">Model Name:</div>
  <input id="modelname" onkeyup="suggestPluralName()" type="text" name="itemclass_name" style="width:200px" /><span class="form-hint">ie "Article", "Car", "Person"</span>
</div>

<div class="edit-form-row">
  <div class="form-section-label">Model Plural Name:</div>
  <input id="plural" onkeyup="turnOffAutoPlural()" type="text" name="itemclass_plural_name" style="width:200px" /><span class="form-hint">ie "Articles", "Cars", "People"</span>
</div>

<div class="edit-form-row">
  <div class="form-section-label">Shared</div>
  <input id="shared" type="checkbox" name="itemclass_shared" checked="checked" value="1" /><label for="shared">Make this model available to all sites</label>
</div>

<div class="special-box">
  
  <input type="checkbox" name="create_meta_page" id="create-meta-page" value="1" onchange="toggleFormAreaVisibilityBasedOnCheckbox('create-meta-page', 'extra-form-options');"{if $cmp} checked="checked"{/if} /><label for="create-meta-page">Create meta-page now for this model</label>
  
  <div style="display:{if $cmp}block{else}none{/if}" id="extra-form-options">
    <div class="edit-form-row">
      <div class="form-section-label">Meta-page template</div>
      <select name="meta_page_template">
        {foreach from=$templates item="template"}
        <option value"{$template.url}">{$template.url}</option>
        {/foreach}
      </select>
    </div>
    <div class="edit-form-row">
      <div class="form-section-label">Meta-page parent</div>
      <select name="meta_page_parent">
        {foreach from=$pages item="page"}
          <option value="{$page.info.id}"{if $pageInfo.parent == $page.info.id} selected="selected"{/if}>+{section name="dashes" loop=$page.treeLevel}-{/section} {$page.info.title}</option>
        {/foreach}
      </select>
    </div>
  </div>
  
</div>
    
{* <div class="edit-form-row">
  <div class="form-section-label">Model Template (optional)</div>
      <select name="itemclass_schema_id" style="width:180px">
        <option value="">None (Custom Model)</option>  
        {foreach from=$content.schemas key=key item=item }
          <option value="{$item.schema_id}">{$item.schema_name}</option>  
        {/foreach}
      </select>
</div> *}

{* <div class="edit-form-row">
    <div class="form-section-label">How many properties?</div>
    <input id="plural" type="text" name="num_properties" style="width:200px" />
</div> *}
    

<div class="buttons-bar"><input type="submit" value="Next &gt;&gt;" /></div>


</form>

</div>