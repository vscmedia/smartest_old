<div id="work-area">

<h3><a href="{$domain}datamanager">Data Manager</a> &gt; <a href="{$domain}smartest/sets">Data Sets</a> &gt; Conditions</h3>
<a name="top"></a>

<div class="instruction">Create conditions to filter your data into a pre-saved set that can be used anywhere.</div>

  <form id="pageViewForm" method="post" action="{$domain}{$section}/updateDynamicSet">
    
    <input type="hidden" name="set_id" value="{$set.id}" />
    
    <table border="0" style="width:100%">
      
      <tr>
        <td style="width:180px">Set Label:</td>
        <td><input type="text"  name="set_name" value="{$set.label}" /> ({$set.name})</td>
      </tr>
      
      <tr>
        <td style="width:180px">Get {$model.plural_name} from:</td>
        <td>
          <select name="set_data_source_site_id">
            {foreach from=$sites item="site"}
				    <option value="{$site.id}" {if $site.id == $set.data_source_site_id} selected="selected"{/if}>{$site.name}</option>
            {/foreach}
            <option value="ALL" {if $set.data_source_site_id == "ALL"} selected="selected"{/if}>All Sites</option>
            <option value="CURRENT"{if $set.data_source_site_id == "CURRENT"} selected="selected"{/if}>The site where it is used (contextual)</option>
			    </select>
        </td>
      </tr>
      
      <tr>
        <td style="width:180px">Sort By Property:</td>
        <td>
          <select name="set_sort_field">
				    {foreach from=$properties item="property"}
				    <option value="{$property.id}" {if $property.id == $set.sort_field} selected="selected"{/if}>{$property.name}</option>
            {/foreach}
			    </select>
			  </td>
      </tr>
      
      <tr>
        <td style="width:180px">Sort Direction:</td>
        <td>
          <select name="set_sort_direction">
				    <option value="ASC" {if $set.sort_direction == "ASC"} selected{/if}>Ascending</option>
				    <option value="DESC" {if $set.sort_direction == "DESC"} selected{/if}>Descending</option>
			    </select>
			  </td>
      </tr>

  {if empty($conditions)}			
{*    <tr>
      <td style="width:60px">Use Items From Model:</td>
      <td>
        <select name="model_select" id="model_select" onchange="window.location='{$domain}{$section}/editSet?set_id=&amp;{$set.set_id}model_id=' + document.getElementById('model_select').value">
  {foreach from=$models key="key" item="item"}
          <option {if $item.itemclass_id == $set.itemclass_id} selected{/if} value="{$item.itemclass_id}">{$item.itemclass_name}</option>
  {/foreach}
        </select>
      </td>
    </tr> *}
    <div>There are no conditions for this data set yet</div>
  {/if}


    <tr>
      <td colspan="2">
  		  <ul class="options-list" id="rules_list">
  		    <li><h4>Conditions:</h4></li>
  				{foreach from=$conditions item="rule" }
  <li id="item_{$rule.itemproperty_id}">
					  
  					  <select name="conditions[{$rule.id}][property_id]">
  						  <option value="_SMARTEST_ITEM_NAME" {if $rule.itemproperty_id == "_SMARTEST_ITEM_NAME"} selected{/if}>{$model.name} Name</option>
  						  <option value="_SMARTEST_ITEM_ID" {if $rule.itemproperty_id == "_SMARTEST_ITEM_ID"} selected{/if}>{$model.name} ID</option>
  					    {foreach from=$properties item="property"}
  						  <option value="{$property.id}" {if $property.id == $rule.itemproperty_id} selected{/if}>{$property.name}</option>
                {/foreach}
  					  </select>
					
  					  <select name="conditions[{$rule.id}][operator]">
  						  <option value="0" {if $rule.operator == "0"} selected="selected" {/if}>Equals</option>
  						  <option value="1" {if $rule.operator == "1"} selected="selected" {/if}>Does Not Equal</option>
  						  <option value="2" {if $rule.operator == "2"} selected="selected" {/if}>Contains</option>
  						  <option value="3" {if $rule.operator == "3"} selected="selected" {/if}>Does Not Contain</option>
  						  <option value="4" {if $rule.operator == "4"} selected="selected" {/if}>Starts With</option>
  						  <option value="5" {if $rule.operator == "5"} selected="selected" {/if}>Ends With</option>
  						  <option value="6" {if $rule.operator == "6"} selected="selected" {/if}>Greater Than</option>
  						  <option value="7" {if $rule.operator == "7"} selected="selected" {/if}>Less Than</option>
  					  </select>
						
  					  <input type="text" value="{$rule.value}" name="conditions[{$rule.id}][value]" />
					  
  					  {* <a href="#" onclick="document.getElementById('form_{$rule.setrule_id}').submit()"><img border="0" src="{$domain}Resources/Icons/package_delete.png"> Delete Rule</a> *}
  					  <input type="button" value="-" onclick="window.location='{$domain}{$section}/removeConditionFromSet?condition_id={$rule.id}'" />

  </li>
  				{/foreach}
  <li>
					    
					    <div style="margin-top:15px">Add a new Condition? <input type="checkbox" name="add_new_condition" value="1" /> Yes</div>
					    
  					  <div>
  					    
  					    <select name="new_condition_property_id">
    					    <option value="_SMARTEST_ITEM_NAME">{$model.name} Name</option>
    					    <option value="_SMARTEST_ITEM_ID">{$model.name} ID</option>
                  {foreach from=$properties item="property"}
                  <option value="{$property.id}">{$property.name}</option>
                  {/foreach}
    					  </select>
					
    					  <select name="new_condition_operator">
    					    <option value="0">Equals</option>
    					    <option value="1">Does Not Equal</option>
    					    <option value="2">Contains</option>
    					    <option value="3">Does Not Contain</option>
    					    <option value="4">Starts With</option>
    					    <option value="5">Ends With</option>
    					    <option value="6">Greater Than</option>
    					    <option value="7">Less Than</option>
    				    </select>
  				    
  						  <input type="text" name="new_condition_value" />
  				    
  				    </div>
					
  				    {* <a href="#" onclick="document.getElementById('form_new').submit()"><img border="0" src="{$domain}Resources/Icons/package_add.png"> Add Rule</a> *}
					
  </li>
  			  </ul>
  			</td>
  		</tr>
    </table>
  
    <div class="edit-form-row">
      <div class="buttons-bar">
        <input type="button" value="cancel" />
        <input type="submit" value="Save Changes" />
      </div>
    </div>
  
  </form>

</div>

<div id="actions-area">
		
		<ul class="actions-list">
		  <li><b>Options</b></li>
			<li class="permanent-action"><a href="#" onclick="window.location='{$domain}{$section}/previewSet?set_id={$set.id}'"><img border="0" src="{$domain}Resources/Icons/package_add.png"> Preview Set</a></li>
			<li class="permanent-action"><a href="#" onclick="window.location='{$domain}{$section}/'">         <img border="0" src="{$domain}Resources/Icons/package_add.png"> Browse Data Sets</a></li>
			<li class="permanent-action"><a href="#" onclick="window.location='{$domain}datamanager/'"><img border="0" src="{$domain}Resources/Icons/package_add.png"> Data Manager</a></li>
			{* <li class="permanent-action"><a href="#" onclick="window.location='{$domain}{$section}/chooseSchemaForExport?set_id={$set.id}'"><img border="0" src="{$domain}Resources/Icons/package_add.png"> Export this Set as XML</a></li> *}
		</ul>
		
</div>


