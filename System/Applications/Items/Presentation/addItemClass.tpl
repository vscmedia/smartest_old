<script src="{$domain}Resources/Javascript/livesearch.js" type="text/javascript"></script> 

<script language="javascript">

{literal}
var setPlural = true;

function suggestPluralName(){
	// alert(document.getElementById('plural').value);
	
	
	if(setPlural == true){
		document.getElementById('plural').value = document.getElementById('livesearch').value+"s";
	}
}

function turnOffAutoPlural(){
	setPlural = false;
}

{/literal}
</script>

<div id="work-area">

<h3><a href="{$domain}{$section}">Data Manager</a> &gt; Build a New Model</h3>

<div class="instruction">Please enter the name of your new model.</div>

<form name="searchform" onsubmit="return liveSearchSubmit()" method="post" action="{$domain}{$section}/insertItemClass">
<input type="hidden" name="stage" value="2" />
    
<div class="edit-form-row">
  <div class="form-section-label">Model Name:</div>
  <input id="livesearch" onkeyup="suggestPluralName()" type="text" name="itemclass_name" style="width:200px" />
  <div class="form-section-label">Model Plural Name:</div>
  <input id="plural" onkeyup="turnOffAutoPlural()" type="text" name="itemclass_plural_name" style="width:200px" />     
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
    
<div class="edit-form-row">
    <div class="buttons-bar"><input type="submit" value="Next &gt;&gt;" /></div>
</div>

</form>

</div>